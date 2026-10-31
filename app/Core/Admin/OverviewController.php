<?php

require_once __DIR__ . '/../Database.php';

class OverviewController
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }
    public function index(): void
    {
        $stmtProducts = $this->db->query("SELECT COUNT(*) FROM products");
        $totalProducts = (int) $stmtProducts->fetchColumn();
        $stmtUsers = $this->db->query("SELECT COUNT(*) FROM users");
        $totalCustomers = (int) $stmtUsers->fetchColumn();
        $stmtRating = $this->db->query("SELECT AVG(star_rating) FROM reviews");
        $avgRating = round((float) $stmtRating->fetchColumn(), 1); 
        $stmtBrands = $this->db->query("SELECT COUNT(*) FROM brands");
        $totalBrands = (int) $stmtBrands->fetchColumn();
        $stmtLowStock = $this->db->prepare("SELECT name, stock FROM products WHERE stock < ?");
        $stmtLowStock->execute([10]);
        $lowStockProducts = $stmtLowStock->fetchAll(PDO::FETCH_ASSOC);
        $stmtRecent = $this->db->query("SELECT * FROM products ORDER BY id DESC LIMIT 5");
        $recentProducts = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);
        $stmtCategories = $this->db->query("
            SELECT c.name, COUNT(p.id) as product_count 
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id 
            GROUP BY c.id, c.name
        ");
        $categoriesStats = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../../Views/admin/overview.php';
    }
}

