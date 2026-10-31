<?php

require_once __DIR__ . '/Order.php';

class UserOrder extends Order
{
    public function createOrder(
        int $userId,
        array $items,
        array $shippingData
    ): int {
        try {
            $this->db->beginTransaction();

            if (empty($items)) {
                throw new InvalidArgumentException('Order items are required');
            }

            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];

                if ($quantity < 1) {
                    throw new InvalidArgumentException('Invalid quantity');
                }

                $product = $this->getProduct($productId);

                if (!$product) {
                    throw new Exception('Product not found');
                }

                if ((int) $product['stock'] < $quantity) {
                    throw new Exception('Not enough stock');
                }

                $unitPrice = $this->calculateUnitPrice($product);

                $subtotal += $unitPrice * $quantity;

                $orderItems[] = [
                    'product_id' => $productId,
                    'product_name' => $product['name'],
                    'size' => $item['size'] ?? null,
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
                        0,
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
            $newItems = [];

            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];

                if ($quantity < 1) {
                    throw new InvalidArgumentException('Invalid quantity');
                }

                $product = $this->getProduct($productId);

                if (!$product) {
                    throw new Exception('Product not found');
                }

                if ((int) $product['stock'] < $quantity) {
                    throw new Exception('Not enough stock');
                }

                $unitPrice = $this->calculateUnitPrice($product);

                $subtotal += $unitPrice * $quantity;

                $newItems[] = [
                    'product_id' => $productId,
                    'product_name' => $product['name'],
                    'size' => $item['size'] ?? null,
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
                        discount_total = 0,
                        shipping_total = :shipping_total,
                        ship_name = :ship_name,
                        ship_phone = :ship_phone,
                        ship_address = :ship_address,
                        ship_city = :ship_city
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':total' => $total,
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
}