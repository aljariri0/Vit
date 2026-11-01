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
        $recentOrders = $this->db->query('SELECT o.*, u.name AS customer_name FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC LIMIT 8')->fetchAll();
        $lowStock = $this->db->query('SELECT name, stock FROM products WHERE is_active = 1 AND stock < 10 ORDER BY stock LIMIT 8')->fetchAll();
        $salesSeries = $this->db->query("SELECT DATE(created_at) AS day, SUM(total) AS revenue FROM orders WHERE status IN ('paid','processing','shipped','delivered') AND created_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY) GROUP BY DATE(created_at) ORDER BY day")->fetchAll();
        $statusSeries = $this->db->query('SELECT status, COUNT(*) AS count FROM orders GROUP BY status')->fetchAll();
        $topProducts = $this->db->query("SELECT oi.product_name AS name, SUM(oi.quantity) AS units FROM order_items oi JOIN orders o ON o.id=oi.order_id WHERE o.status IN ('paid','processing','shipped','delivered') GROUP BY oi.product_id, oi.product_name ORDER BY units DESC LIMIT 6")->fetchAll();
        $categorySales = $this->db->query("SELECT COALESCE(c.name, 'Uncategorized') AS name, SUM(oi.order_total) AS revenue FROM order_items oi JOIN orders o ON o.id=oi.order_id LEFT JOIN products p ON p.id=oi.product_id LEFT JOIN categories c ON c.id=p.category_id WHERE o.status IN ('paid','processing','shipped','delivered') GROUP BY c.id, c.name ORDER BY revenue DESC")->fetchAll();
        $customerGrowth = $this->db->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS customers FROM users WHERE created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), '%Y-%m-01') GROUP BY month ORDER BY month")->fetchAll();
        $this->view('layout', [
            'topProducts' => $topProducts, 'categorySales' => $categorySales, 'customerGrowth' => $customerGrowth,
            'salesSeries' => $salesSeries, 'statusSeries' => $statusSeries,
            'page_view' => 'admin/dashboard', 'title' => 'Studio overview', 'stats' => $stats,
            'recentOrders' => $recentOrders, 'lowStock' => $lowStock,
            'base' => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'),
            'base_path' => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'),
            'csrf_token' => $_SESSION['admin_csrf'] ?? '', 'cart_count' => 0,
            'user' => ['name' => $_SESSION['name'] ?? 'Admin'], 'is_admin' => true,
        ]);
    }
}

