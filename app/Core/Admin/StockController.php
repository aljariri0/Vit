<?php

require_once __DIR__ . '/../../Models/Stock.php';

class StockController
{
    private Stock $stockModel;

    public function __construct(PDO $pdo)
    {
        $this->stockModel = new Stock($pdo);
    }
    public function index(int $lowStockLimit = 10): array
    {
        $this->requireAdmin();

        if ($lowStockLimit < 0) {
            throw new InvalidArgumentException(
                'The low-stock limit cannot be negative.'
            );
        }

        return $this->stockModel->getInventory($lowStockLimit);
    }
    public function updateStock(int $productId, $quantity): bool
    {
        $this->requireAdmin();
        $quantity = filter_var($quantity, FILTER_VALIDATE_INT);

        if ($productId <= 0 || $quantity === false || $quantity < 0) {
            throw new InvalidArgumentException(
                'Product ID and quantity must be valid and non-negative.'
            );
        }

        if (!$this->stockModel->productExists($productId)) {
            throw new InvalidArgumentException('Product not found.');
        }

        return $this->stockModel->updateQuantity($productId, $quantity);
    }
    public function updateDiscount(int $productId, $discount): bool
    {
        $this->requireAdmin();

        if (
            $productId <= 0 ||
            !is_numeric($discount) ||
            $discount < 0 ||
            $discount > 100
        ) {
            throw new InvalidArgumentException(
                'Product ID must be valid and discount must be from 0 to 100.'
            );
        }

        if (!$this->stockModel->productExists($productId)) {
            throw new InvalidArgumentException('Product not found.');
        }

        return $this->stockModel->updateDiscount(
            $productId,
            (float) $discount
        );
    }

    private function requireAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            throw new RuntimeException('Admin access is required.');
        }
    }
}
