<?php

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../Controllers/Controller.php';

class OverviewController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $database = new Database();
        $this->db = $database->connect();
    }
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Access denied.');
        }

        $stats = [
            'sales' => (float) $this->db->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status IN ('paid','processing','shipped','delivered')")->fetchColumn(),
            'orders' => (int) $this->db->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
            'products' => (int) $this->db->query('SELECT COUNT(*) FROM products WHERE is_active = 1')->fetchColumn(),
            'low_stock' => (int) $this->db->query('SELECT COUNT(*) FROM products WHERE is_active = 1 AND stock < 10')->fetchColumn(),
            'customers' => (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'pending' => (int) $this->db->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending','paid')")->fetchColumn(),
        ];
        $recentOrders = $this->db->query('SELECT o.*, u.name AS customer_name FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC LIMIT 6')->fetchAll();
        $lowStock = $this->db->query('SELECT name, stock FROM products WHERE is_active = 1 AND stock < 10 ORDER BY stock LIMIT 6')->fetchAll();
        $this->view('layout', [
            'page_view' => 'admin/dashboard', 'title' => 'Studio overview', 'stats' => $stats,
            'recentOrders' => $recentOrders, 'lowStock' => $lowStock,
            'base' => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'),
            'base_path' => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'),
            'csrf_token' => $_SESSION['admin_csrf'] ?? '', 'cart_count' => 0,
            'user' => ['name' => $_SESSION['name'] ?? 'Admin'], 'is_admin' => true,
        ]);
    }
}

