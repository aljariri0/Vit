<?php

class ProductModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Get all products
    public function getAllProducts(): array
    {
        try {
            $sql = "SELECT * FROM products ORDER BY id DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Get All Products Error: " . $e->getMessage());
            throw $e;
        }
    }

    // Get one product by ID
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

    // Create product
    public function createProduct(
        int $category_id,
        string $name,
        ?string $description,
        ?string $ingredients,
        ?string $warnings,
        float $price,
        float $discount_percent,
        int $stock,
        array|string|null $sizes,
        ?float $weight,
        array|string|null $image,
        ?int $brand_id = null
    ): int {
        try {
            $sizes = is_array($sizes)
                ? json_encode($sizes, JSON_THROW_ON_ERROR)
                : $sizes;

            $image = is_array($image)
                ? json_encode($image, JSON_THROW_ON_ERROR)
                : $image;

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
                'image' => $image,

            ]);

            return (int) $this->pdo->lastInsertId();

        } catch (PDOException | JsonException $e) {
            error_log("Create Product Error:" . $e->getMessage());
            throw $e;
        }
    }

    // Update product
    public function updateProduct(
        int $id,
        int $category_id,
        ?int $brand_id,
        string $name,
        ?string $description,
        ?string $ingredients,
        ?string $warnings,
        float $price,
        float $discount_percent,
        int $stock,
        array|string|null $sizes,
        ?float $weight,
        array|string|null $image
    ): bool {
        try {
            $sizes = is_array($sizes)
                ? json_encode($sizes, JSON_THROW_ON_ERROR)
                : $sizes;

            $image = is_array($image)
                ? json_encode($image, JSON_THROW_ON_ERROR)
                : $image;
            $sql = "UPDATE products SET
                        category_id = :category_id,
                        brand_id = :brand_id,
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

            return $stmt->execute([
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
                'image' => $image,
                'category_id' => $category_id,
                'brand_id' => $brand_id,
            ]);



        } catch (PDOException | JsonException $e) {
            error_log("Update Product Error: " . $e->getMessage());
            throw $e;
        }
    }

    // Delete product by ID
    public function deleteProductById(int $id): bool
    {
        try {
            $sql = "DELETE FROM products WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("Delete Product Error: " . $e->getMessage());
            throw $e;
        }
    }
}
