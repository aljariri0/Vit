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
   
public function searchProducts(array $filters = []): array
{
    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];

    // 1. Search by name, description, ingredients
    if (!empty($filters["search"])) {
        $sql .= " AND (
            name LIKE :name
            OR description LIKE :description
            OR ingredients LIKE :ingredients
        )";

        $keyword = "%" . $filters["search"] . "%";

        $params["name"] = $keyword;
        $params["description"] = $keyword;
        $params["ingredients"] = $keyword;
    }

    // 2. Filter by brand
    if (!empty($filters["brand_id"])) {
        $sql .= " AND brand_id = :brand_id";
        $params["brand_id"] = $filters["brand_id"];
    }

    // 3. Filter by category
    if (!empty($filters["category_id"])) {
        $sql .= " AND category_id = :category_id";
        $params["category_id"] = $filters["category_id"];
    }

    // 4. Minimum price
    if (isset($filters["min_price"]) && $filters["min_price"] !== "") {
        $sql .= " AND price >= :min_price";
        $params["min_price"] = $filters["min_price"];
    }

    // 5. Maximum price
    if (isset($filters["max_price"]) && $filters["max_price"] !== "") {
        $sql .= " AND price <= :max_price";
        $params["max_price"] = $filters["max_price"];
    }

    // 6. Sorting
    $sortOptions = [
        "price_asc"  => "price ASC",
        "price_desc" => "price DESC",
        "newest"     => "id DESC",
        "oldest"     => "id ASC"
    ];

    $sort = $filters["sort"] ?? "newest";

    $orderBy = $sortOptions[$sort] ?? "id DESC";

    $sql .= " ORDER BY " . $orderBy;

    // 7. Execute query
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
