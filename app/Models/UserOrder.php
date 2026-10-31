<?php

require_once __DIR__ . '/Order.php';

class UserOrder extends Order
{
    public function createOrder(
        int $userId,
        array $items,
        array $shippingData
    ): int {
        $shippingData = $this->validateShippingData($shippingData);

        try {
            $this->db->beginTransaction();

            if ($userId <= 0 || empty($items)) {
                throw new InvalidArgumentException('Order items are required');
            }

            $subtotal = 0;
            $discountTotal = 0;
            $orderItems = [];

            foreach ($items as $item) {
                if (!is_array($item)) {
                    throw new InvalidArgumentException('Invalid order item');
                }
                $productId = filter_var(
                    $item['product_id'] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );
                $quantity = filter_var(
                    $item['quantity'] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );
                if ($productId === false || $quantity === false) {
                    throw new InvalidArgumentException('Invalid quantity');
                }

                $product = $this->getProduct($productId);

                if (!$product) {
                    throw new Exception('Product not found');
                }

                if ((int) $product['is_active'] !== 1) {
                    throw new Exception('Product is unavailable');
                }

                if ((int) $product['stock'] < $quantity) {
                    throw new Exception('Not enough stock');
                }

                $availableSizes = json_decode($product['sizes'] ?? '[]', true);
                $size = $item['size'] ?? null;
                if ($size !== null && !is_string($size)) {
                    throw new InvalidArgumentException('Invalid product size');
                }
                if (
                    !is_array($availableSizes) ||
                    ($availableSizes && !in_array($size, $availableSizes, true)) ||
                    (!$availableSizes && $size !== null && $size !== '')
                ) {
                    throw new InvalidArgumentException('Invalid product size');
                }

                $unitPrice = $this->calculateUnitPrice($product);

                $subtotal += $unitPrice * $quantity;
                $discountTotal += ((float) $product['price'] - $unitPrice) * $quantity;

                $orderItems[] = [
                    'product_id' => $productId,
                    'product_name' => $product['name'],
                    'size' => $size,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice
                ];
            }

            $city = $shippingData['ship_city'];
            $shippingTotal = $this->calculateShipping($subtotal, $city);
            $total = $subtotal + $shippingTotal;

            $sql = "INSERT INTO orders
                    (
                        user_id,
                        total,
                        discount_total,
                        shipping_total,
                        ship_name,
                        ship_phone,
                        ship_address,
                        ship_city
                    )
                    VALUES
                    (
                        :user_id,
                        :total,
                        :discount_total,
                        :shipping_total,
                        :ship_name,
                        :ship_phone,
                        :ship_address,
                        :ship_city
                    )";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':user_id' => $userId,
                ':total' => $total,
                ':discount_total' => $discountTotal,
                ':shipping_total' => $shippingTotal,
                ':ship_name' => $shippingData['ship_name'],
                ':ship_phone' => $shippingData['ship_phone'],
                ':ship_address' => $shippingData['ship_address'],
                ':ship_city' => $city
            ]);

            $orderId = (int) $this->db->lastInsertId();

            foreach ($orderItems as $item) {
                if (
                    !$this->decreaseStock(
                        (int) $item['product_id'],
                        (int) $item['quantity']
                    )
                ) {
                    throw new Exception('Not enough stock');
                }

                $this->orderItem->addItem(
                    $orderId,
                    (int) $item['product_id'],
                    $item['product_name'],
                    $item['size'],
                    (int) $item['quantity'],
                    (float) $item['unit_price']
                );
            }

            $this->db->commit();

            return $orderId;

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function getUserOrders(int $userId): array
    {
        $sql = "SELECT *
                FROM orders
                WHERE user_id = :user_id
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserOrderById(int $orderId, int $userId): ?array
    {
        $sql = "SELECT *
                FROM orders
                WHERE id = :id AND user_id = :user_id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $orderId,
            ':user_id' => $userId
        ]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        $order['items'] = $this->orderItem->getItemsByOrderId($orderId);

        return $order;
    }

    public function trackOrder(int $orderId, int $userId): ?array
    {
        $sql = "SELECT id, status, created_at, updated_at, shipping_time
                FROM orders
                WHERE id = :id AND user_id = :user_id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $orderId,
            ':user_id' => $userId
        ]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        return $order ?: null;
    }

    public function updateOrder(
        int $orderId,
        int $userId,
        array $items,
        array $shippingData
    ): bool {
        $shippingData = $this->validateShippingData($shippingData);

        try {
            $this->db->beginTransaction();

            $sql = "SELECT *
                    FROM orders
                    WHERE id = :id AND user_id = :user_id
                    FOR UPDATE";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':id' => $orderId,
                ':user_id' => $userId
            ]);

            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order || !in_array($order['status'], ['pending', 'processing'], true)) {
                $this->db->rollBack();
                return false;
            }

            if (empty($items)) {
                throw new InvalidArgumentException('Order items are required');
            }

            $oldItems = $this->orderItem->getItemsByOrderId($orderId);

            foreach ($oldItems as $item) {
                if ($item['product_id'] !== null) {
                    $this->restoreStock(
                        (int) $item['product_id'],
                        (int) $item['quantity']
                    );
                }
            }

            $stmt = $this->db->prepare(
                "DELETE FROM order_items WHERE order_id = :order_id"
            );

            $stmt->execute([':order_id' => $orderId]);

            $subtotal = 0;
            $discountTotal = 0;
            $newItems = [];

            foreach ($items as $item) {
                if (!is_array($item)) {
                    throw new InvalidArgumentException('Invalid order item');
                }
                $productId = filter_var(
                    $item['product_id'] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );
                $quantity = filter_var(
                    $item['quantity'] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );
                if ($productId === false || $quantity === false) {
                    throw new InvalidArgumentException('Invalid quantity');
                }

                $product = $this->getProduct($productId);

                if (!$product) {
                    throw new Exception('Product not found');
                }

                if ((int) $product['is_active'] !== 1) {
                    throw new Exception('Product is unavailable');
                }

                if ((int) $product['stock'] < $quantity) {
                    throw new Exception('Not enough stock');
                }

                $availableSizes = json_decode($product['sizes'] ?? '[]', true);
                $size = $item['size'] ?? null;
                if ($size !== null && !is_string($size)) {
                    throw new InvalidArgumentException('Invalid product size');
                }
                if (
                    !is_array($availableSizes) ||
                    ($availableSizes && !in_array($size, $availableSizes, true)) ||
                    (!$availableSizes && $size !== null && $size !== '')
                ) {
                    throw new InvalidArgumentException('Invalid product size');
                }

                $unitPrice = $this->calculateUnitPrice($product);

                $subtotal += $unitPrice * $quantity;
                $discountTotal += ((float) $product['price'] - $unitPrice) * $quantity;

                $newItems[] = [
                    'product_id' => $productId,
                    'product_name' => $product['name'],
                    'size' => $size,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice
                ];
            }

            foreach ($newItems as $item) {
                if (
                    !$this->decreaseStock(
                        (int) $item['product_id'],
                        (int) $item['quantity']
                    )
                ) {
                    throw new Exception('Not enough stock');
                }

                $this->orderItem->addItem(
                    $orderId,
                    (int) $item['product_id'],
                    $item['product_name'],
                    $item['size'],
                    (int) $item['quantity'],
                    (float) $item['unit_price']
                );
            }

            $city = $shippingData['ship_city'];
            $shippingTotal = $this->calculateShipping($subtotal, $city);
            $total = $subtotal + $shippingTotal;

            $sql = "UPDATE orders
                    SET total = :total,
                        discount_total = :discount_total,
                        shipping_total = :shipping_total,
                        ship_name = :ship_name,
                        ship_phone = :ship_phone,
                        ship_address = :ship_address,
                        ship_city = :ship_city
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':total' => $total,
                ':discount_total' => $discountTotal,
                ':shipping_total' => $shippingTotal,
                ':ship_name' => $shippingData['ship_name'],
                ':ship_phone' => $shippingData['ship_phone'],
                ':ship_address' => $shippingData['ship_address'],
                ':ship_city' => $city,
                ':id' => $orderId
            ]);

            $this->db->commit();

            return true;

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function cancelUserOrder(int $orderId, int $userId): bool
    {
        return $this->cancelOrderProcess($orderId, $userId);
    }

    private function validateShippingData(array $data): array
    {
        $limits = [
            'ship_name' => 100,
            'ship_phone' => 30,
            'ship_address' => 255,
            'ship_city' => 80,
        ];
        $shippingData = [];

        foreach ($limits as $field => $maxLength) {
            $value = trim((string) ($data[$field] ?? ''));
            if ($value === '' || strlen($value) > $maxLength) {
                throw new InvalidArgumentException('Invalid shipping details');
            }
            $shippingData[$field] = $value;
        }

        return $shippingData;
    }
}
