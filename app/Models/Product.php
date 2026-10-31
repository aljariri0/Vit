<?php

class ProductModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
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


    public function getPaginatedProducts(array $filters = []): array
    {
        // 6 products per page
        $perPage = 6;

        $page = max(1, (int)($filters["page"] ?? 1));

        // Reuse existing search and filters
        $products = $this->searchProducts($filters);
        
$products = array_values(array_filter(
    $products,
    fn($product) => (int) $product["is_active"] === 1
));

        $sort = $filters["sort"] ?? "newest";

        if (in_array($sort, ["rating_desc", "rating_asc"], true)) {

            // Get average rating for each product
            $stmt = $this->pdo->query("
        SELECT product_id, AVG(star_rating) AS avg_rating
        FROM reviews
        WHERE status = 'approved'
        GROUP BY product_id
    ");

            $ratings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            // Add rating to each product
            foreach ($products as &$product) {
                $product["avg_rating"] =
                    (float) ($ratings[$product["id"]] ?? 0);
            }
            unset($product);

            // Sort all products before pagination
            usort($products, function ($a, $b) use ($sort) {
                $comparison = $a["avg_rating"] <=> $b["avg_rating"];

                if ($sort === "rating_desc") {
                    $comparison = -$comparison;
                }

                return $comparison !== 0
                    ? $comparison
                    : $b["id"] <=> $a["id"];
            });
        }

        // Total matching products
        $totalProducts = count($products);

        // Total pages
        $totalPages = (int) ceil($totalProducts / $perPage);

        // Calculate starting position
        $offset = ($page - 1) * $perPage;

        // Get only 6 products
        $paginatedProducts = array_slice(
            $products,
            $offset,
            $perPage
        );

        return [
            "products" => $paginatedProducts,
            "current_page" => $page,
            "per_page" => $perPage,
            "total_products" => $totalProducts,
            "total_pages" => $totalPages
        ];
    }
}
