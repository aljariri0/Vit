<?php

require_once __DIR__ . '/../../Models/ProductModel.php';

class ProductController
{
    private $productModel;

    public function __construct($pdo)
    {
        $this->productModel = new ProductModel($pdo);
    }

    // Get all products
    public function index()
    {
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

    // Get one product by ID
    public function show($id)
    {
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

    // Create product
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

    // Update product
    public function update(
        $id,
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

    // Delete product
    public function delete($id)
    {
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
}

?>