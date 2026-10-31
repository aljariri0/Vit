<?php

require_once __DIR__ . '/OrderItem.php';

class Order
{
    protected PDO $db;
    protected OrderItem $orderItem;

    protected array $deliveryFees = [
        'Amman' => 2.00,
        'Salt' => 2.00,
        'Zarqa' => 2.00,

        'Madaba' => 3.00,
        'Irbid' => 3.00,
        'Ajloun' => 3.00,
        'Jerash' => 3.00,
        'Mafraq' => 3.00,

        'Aqaba' => 4.00,
        'Ma’an' => 4.00,
        'Karak' => 4.00,
        'Tafilah' => 4.00
    ];

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->orderItem = new OrderItem($db);
    }

    protected function calculateShipping(float $subtotal, string $city): float
    {
        if ($subtotal > 50) {
            return 0.00;
        }

        if (!isset($this->deliveryFees[$city])) {
            throw new InvalidArgumentException('Invalid delivery city');
        }

        return $this->deliveryFees[$city];
    }

    protected function getProduct(int $productId): ?array
    {
        $sql = "SELECT * FROM products WHERE id = :id FOR UPDATE";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $productId]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        return $product ?: null;
    }

    protected function calculateUnitPrice(array $product): float
    {
        $price = (float) $product['price'];
        $discount = (float) $product['discount_percent'];

        return round($price - ($price * $discount / 100), 2);
    }

    protected function decreaseStock(int $productId, int $quantity): bool
    {
        $sql = "UPDATE products
            SET stock = stock - :quantity_to_subtract
            WHERE id = :id AND stock >= :quantity_to_check";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':quantity_to_subtract' => $quantity,
            ':quantity_to_check' => $quantity,
            ':id' => $productId
        ]);

        return $stmt->rowCount() > 0;
    }
    protected function restoreStock(int $productId, int $quantity): bool
    {
        $sql = "UPDATE products
                SET stock = stock + :quantity
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':quantity' => $quantity,
            ':id' => $productId
        ]);
    }

    protected function cancelOrderProcess(
        int $orderId,
        ?int $userId = null,
        ?int $adminId = null
    ): bool {
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM orders WHERE id = :id";

            if ($userId !== null) {
                $sql .= " AND user_id = :user_id";
            }

            $sql .= " FOR UPDATE";

            $stmt = $this->db->prepare($sql);

            $params = [':id' => $orderId];

            if ($userId !== null) {
                $params[':user_id'] = $userId;
            }

            $stmt->execute($params);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order || !in_array($order['status'], ['pending', 'processing'], true)) {
                $this->db->rollBack();
                return false;
            }

            $items = $this->orderItem->getItemsByOrderId($orderId);

            foreach ($items as $item) {
                if ($item['product_id'] !== null) {
                    $this->restoreStock(
                        (int) $item['product_id'],
                        (int) $item['quantity']
                    );
                }
            }

            $sql = "UPDATE orders
                    SET status = 'cancelled',
                        processed_by_admin_id = :admin_id
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':admin_id' => $adminId,
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
}
