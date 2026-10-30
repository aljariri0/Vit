<?php

require_once __DIR__ . '/../../Database.php'; 

class OverviewController
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

  
    //   Fetch all metrics, low stock items, and recent products for the admin overview dashboard.
    public function index(): void
    {
        // 1. Total Products Count
        $stmtProducts = $this->db->query("SELECT COUNT(*) FROM products");
        $totalProducts = (int) $stmtProducts->fetchColumn();

        // 2. Total Customers / Users Count
        $stmtUsers = $this->db->query("SELECT COUNT(*) FROM users");
        $totalCustomers = (int) $stmtUsers->fetchColumn();

        // 3. Average Customer Rating from reviews table
        $stmtRating = $this->db->query("SELECT AVG(star_rating) FROM reviews");
        $avgRating = round((float) $stmtRating->fetchColumn(), 1); 

        // 4. Total Partner Brands Count
        $stmtBrands = $this->db->query("SELECT COUNT(*) FROM brands");
        $totalBrands = (int) $stmtBrands->fetchColumn();

        // 5. Low-stock products (fewer than 10 units)
        $stmtLowStock = $this->db->prepare("SELECT name, stock FROM products WHERE stock < ?");
        $stmtLowStock->execute([10]);
        $lowStockProducts = $stmtLowStock->fetchAll(PDO::FETCH_ASSOC);

        // 6. Recent product collection for the table
        $stmtRecent = $this->db->query("SELECT * FROM products ORDER BY id DESC LIMIT 5");
        $recentProducts = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

        // 7. Collection by category (Products count per category)
        $stmtCategories = $this->db->query("
            SELECT c.name, COUNT(p.id) as product_count 
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id 
            GROUP BY c.id, c.name
        ");
        $categoriesStats = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

        // pass all the upper func to overview.php
        require_once __DIR__ . '/../../Views/admin/overview.php';
    }
}