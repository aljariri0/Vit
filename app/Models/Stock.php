<?php

class Stock
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // List products, showing the lowest stock quantities first.
    public function getInventory(int $lowStockLimit = 10): array
    {
        $sql = "SELECT id, name, stock, discount_percent,
                       (stock <= :low_stock_limit) AS is_low_stock
                FROM products
                ORDER BY stock ASC, id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':low_stock_limit', $lowStockLimit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function productExists(int $productId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM products WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$productId]);

        return (bool) $stmt->fetchColumn();
    }

    // Set a product's available quantity.
    public function updateQuantity(int $productId, int $quantity): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE products SET stock = ? WHERE id = ?'
        );

        return $stmt->execute([$quantity, $productId]);
    }

    // Set a discount on one product.
    public function updateDiscount(int $productId, float $discount): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE products SET discount_percent = ? WHERE id = ?'
        );

        return $stmt->execute([$discount, $productId]);
    }
}
