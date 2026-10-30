
<?php

require_once __DIR__ . "/../../Models/Product.php";

class ShopController
{
    private ProductModel $productModel;

    public function __construct(PDO $pdo)
    {
        $this->productModel = new ProductModel($pdo);
    }

    public function getProducts(array $filters = []): array
    {
        // Validate search
        $search = $filters["search"] ?? "";

        if (!is_string($search)) {
            throw new InvalidArgumentException("Invalid search");
        }

        $filters["search"] = trim($search);

        // Validate brand and category
        foreach (["brand_id", "category_id"] as $field) {
            if (isset($filters[$field]) && $filters[$field] !== "") {
                $id = filter_var(
                    $filters[$field],
                    FILTER_VALIDATE_INT,
                    ["options" => ["min_range" => 1]]
                );

                if ($id === false) {
                    throw new InvalidArgumentException("Invalid " . $field);
                }

                $filters[$field] = $id;
            }
        }

        // Validate prices
        foreach (["min_price", "max_price"] as $field) {
            if (isset($filters[$field]) && $filters[$field] !== "") {
                if (
                    !is_scalar($filters[$field]) ||
                    !is_numeric($filters[$field]) ||
                    (float) $filters[$field] < 0
                ) {
                    throw new InvalidArgumentException("Invalid " . $field);
                }

                $filters[$field] = (float) $filters[$field];
            }
        }

        if (
            isset($filters["min_price"], $filters["max_price"]) &&
            $filters["min_price"] !== "" &&
            $filters["max_price"] !== "" &&
            $filters["min_price"] > $filters["max_price"]
        ) {
            throw new InvalidArgumentException("Minimum price exceeds maximum price");
        }

        // Validate sorting
        $allowedSorts = [
            "price_asc",
            "price_desc",
            "newest",
            "oldest"
        ];

        $sort = $filters["sort"] ?? "newest";

        if (!is_string($sort) || !in_array($sort, $allowedSorts, true)) {
            throw new InvalidArgumentException("Invalid sort");
        }

        $filters["sort"] = $sort;

        // Call Product Model
        return $this->productModel->searchProducts($filters);
    }
}
