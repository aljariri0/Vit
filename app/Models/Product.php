<?php

class ProductModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Get all products
    public function getAllProducts()
    {
        try {
            $sql = "SELECT * FROM products";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Get one product by ID
    public function getProductById($id)
    {
        try {
            $sql = "SELECT id, category_id, brand_id, name, description,
                       ingredients, warnings, price, discount_percent,
                       stock, sizes, weight, image
                FROM products
                WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product === false) {
                return null;
            }

            return $product;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Create product
    public function createProduct(
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
            if (is_array($sizes)) {
                $sizes = json_encode($sizes);
            }

            if (is_array($image)) {
                $image = json_encode($image);
            }

            $sql = "INSERT INTO products (
                        category_id,
                        brand_id,
                        name,
                        description,
                        ingredients,
                        warnings,
                        price,
                        discount_percent,
                        stock,
                        sizes,
                        weight,
                        image
                    ) VALUES (
                        :category_id,
                        :brand_id,
                        :name,
                        :description,
                        :ingredients,
                        :warnings,
                        :price,
                        :discount_percent,
                        :stock,
                        :sizes,
                        :weight,
                        :image
                    )";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'category_id' => $category_id,
                'brand_id' => $brand_id,
                'name' => $name,
                'description' => $description,
                'ingredients' => $ingredients,
                'warnings' => $warnings,
                'price' => $price,
                'discount_percent' => $discount_percent,
                'stock' => $stock,
                'sizes' => $sizes,
                'weight' => $weight,
                'image' => $image
            ]);

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Update product
    public function updateProduct(
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
            if (is_array($sizes)) {
                $sizes = json_encode($sizes);
            }

            if (is_array($image)) {
                $image = json_encode($image);
            }

            $sql = "UPDATE products SET
                        name = :name,
                        description = :description,
                        ingredients = :ingredients,
                        warnings = :warnings,
                        price = :price,
                        discount_percent = :discount_percent,
                        stock = :stock,
                        sizes = :sizes,
                        weight = :weight,
                        image = :image
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'ingredients' => $ingredients,
                'warnings' => $warnings,
                'price' => $price,
                'discount_percent' => $discount_percent,
                'stock' => $stock,
                'sizes' => $sizes,
                'weight' => $weight,
                'image' => $image
            ]);

            return $stmt->rowCount();

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Delete product by ID
    public function deleteProductById($id)
    {
        try {
            $sql = "DELETE FROM products WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            return $stmt->rowCount();

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
