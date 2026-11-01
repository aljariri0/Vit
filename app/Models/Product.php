<?php

class ProductModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function getProductById(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM products WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product === false) {
                return null;
            }

            return $product;
        } catch (PDOException $e) {
            error_log("Get Product Error: " . $e->getMessage());
            throw $e;
        }
    }
}
