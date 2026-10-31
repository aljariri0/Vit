<?php

require_once __DIR__ . '/../../Models/Product.php';

class ProductController
{
    private $productModel;

    public function __construct($pdo)
    {
        $this->productModel = new ProductModel($pdo);
    }
    public function index()
    {
        $this->requireAdmin();

        try {
            $products = $this->productModel->getAllProducts();

            if ($products === false) {
                http_response_code(500);
                return false;
            }

            return $products;

        } catch (Exception $e) {
            http_response_code(500);
            error_log($e->getMessage());
            return false;
        }
    }
    public function show($id)
    {
        $this->requireAdmin();

        try {
            $product = $this->productModel->getProductById($id);

            if ($product === null) {
                http_response_code(404);
                return false;
            }

            if ($product === false) {
                http_response_code(500);
                return false;
            }

            return $product;

        } catch (Exception $e) {
            http_response_code(500);
            error_log($e->getMessage());
            return false;
        }
    }
    public function create(
        $category_id,
        $name,
        $description,
        $ingredients,
        $warnings,
        $price,
        $discount_percent,
        $stock,
        $sizes,
        $weight,
        $image,
        $brand_id = null
    ) {
        $this->requireAdmin();

        try {
            $product = $this->productModel->createProduct(
                $category_id,
                $name,
                $description,
                $ingredients,
                $warnings,
                $price,
                $discount_percent,
                $stock,
                $sizes,
                $weight,
                $image,
                $brand_id
            );

            if ($product === false) {
                http_response_code(500);
                return false;
            }

            return $product;

        } catch (Exception $e) {
            http_response_code(500);
            error_log($e->getMessage());
            return false;
        }
    }
    public function update(
        $id,
        $category_id,
        $brand_id,
        $name,
        $description,
        $ingredients,
        $warnings,
        $price,
        $discount_percent,
        $stock,
        $sizes,
        $weight,
        $image
    ) {
        $this->requireAdmin();

        try {
            $product = $this->productModel->getProductById($id);

            if ($product === null) {
                http_response_code(404);
                return false;
            }

            if ($product === false) {
                http_response_code(500);
                return false;
            }

            return $this->productModel->updateProduct(
                $id,
                $category_id,
                $brand_id,
                $name,
                $description,
                $ingredients,
                $warnings,
                $price,
                $discount_percent,
                $stock,
                $sizes,
                $weight,
                $image
            );

        } catch (Exception $e) {
            http_response_code(500);
            error_log($e->getMessage());
            return false;
        }
    }
    public function delete($id)
    {
        $this->requireAdmin();

        try {
            $product = $this->productModel->getProductById($id);

            if ($product === null) {
                http_response_code(404);
                return false;
            }

            if ($product === false) {
                http_response_code(500);
                return false;
            }

            return $this->productModel->deleteProductById($id);

        } catch (Exception $e) {
            http_response_code(500);
            error_log($e->getMessage());
            return false;
        }
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

?>
