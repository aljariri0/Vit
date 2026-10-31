<?php

class OrderItem
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function addItem(
        int $orderId,
        int $productId,
        string $productName,
        ?string $size,
        int $quantity,
        float $unitPrice
    ): bool {
        $sql = "INSERT INTO order_items
                (
                    order_id,
                    product_id,
                    product_name,
                    size,
                    quantity,
                    order_total
                )
                VALUES
                (
                    :order_id,
                    :product_id,
                    :product_name,
                    :size,
                    :quantity,
                    :order_total
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':order_id' => $orderId,
            ':product_id' => $productId,
            ':product_name' => $productName,
            ':size' => $size,
            ':quantity' => $quantity,
            ':order_total' => $unitPrice
        ]);
    }

    public function getItemsByOrderId(int $orderId): array
    {
        $sql = "SELECT *
                FROM order_items
                WHERE order_id = :order_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemById(int $itemId): ?array
    {
        $sql = "SELECT *
                FROM order_items
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $itemId]);

        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        return $item ?: null;
    }

    public function updateItem(
        int $itemId,
        int $quantity,
        ?string $size
    ): bool {
        $sql = "UPDATE order_items
                SET quantity = :quantity,
                    size = :size
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':quantity' => $quantity,
            ':size' => $size,
            ':id' => $itemId
        ]);
    }

    public function deleteItem(int $itemId): bool
    {
        $sql = "DELETE FROM order_items WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $itemId]);
    }
}