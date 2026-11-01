<?php

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../Models/AdminOrder.php';
require_once __DIR__ . '/../../Services/AIReviewAnalyzer.php';
require_once __DIR__ . '/../../Services/ProductImageResolver.php';

class AdminConsoleController extends Controller
{
    private PDO $db;
    private string $basePath;

    public function __construct()
    {
        parent::__construct();
        $this->basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        $this->requireAdmin();
        $this->db = (new Database())->connect();
        $_SESSION['admin_csrf'] ??= bin2hex(random_bytes(32));
    }

    public function products(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name,
                s.summary_text AS review_summary, s.review_count AS review_summary_review_count,
                s.generated_at AS review_summary_generated_at,
                (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id AND r.status = 'approved' AND r.comment IS NOT NULL AND TRIM(r.comment) <> '') AS approved_review_count
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN brands b ON b.id = p.brand_id
            LEFT JOIN product_review_summaries s ON s.product_id = p.id
            ";
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE p.name LIKE ? OR c.name LIKE ? OR b.name LIKE ?';
            $params = array_fill(0, 3, '%' . $search . '%');
        }
        $sql .= ' ORDER BY p.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        $this->render('admin/products', ['products' => $rows, 'search' => $search, 'title' => 'Products']);
    }

    public function generateReviewNotes(): void
    {
        $this->validateCsrf();
        $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$productId) {
            $_SESSION['flash'] = 'Choose a valid product.';
            $this->redirect($this->basePath . '/admin/products');
        }

        try {
            $productStmt = $this->db->prepare('SELECT id FROM products WHERE id = ?');
            $productStmt->execute([$productId]);
            if (!$productStmt->fetchColumn()) {
                $_SESSION['flash'] = 'That product could not be found.';
                $this->redirect($this->basePath . '/admin/products');
            }

            $reviewStmt = $this->db->prepare("SELECT star_rating, comment FROM reviews
                WHERE product_id = ? AND status = 'approved' AND comment IS NOT NULL AND TRIM(comment) <> ''
                ORDER BY created_at DESC");
            $reviewStmt->execute([$productId]);
            $reviews = $reviewStmt->fetchAll();
            if (!$reviews) {
                $_SESSION['flash'] = 'This product has no approved written reviews to summarize.';
                $this->redirect($this->basePath . '/admin/products');
            }

            $summary = (new AIReviewAnalyzer())->summarizeReviews($reviews);
            $saveStmt = $this->db->prepare("INSERT INTO product_review_summaries (product_id, summary_text, review_count)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE summary_text = VALUES(summary_text), review_count = VALUES(review_count), generated_at = CURRENT_TIMESTAMP");
            $saveStmt->execute([$productId, $summary, count($reviews)]);
            $_SESSION['flash'] = 'Review Notes generated from ' . count($reviews) . ' approved reviews.';
        } catch (Throwable $error) {
            error_log('Review note generation failed: ' . $error->getMessage());
            $_SESSION['flash'] = 'Could not generate Review Notes. Check the OpenAI API settings and try again.';
        }

        $this->redirect($this->basePath . '/admin/products');
    }

    public function productForm(): void
    {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $product = $stmt->fetch() ?: null;
        $categories = $this->db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
        $brands = $this->db->query('SELECT id, name FROM brands ORDER BY name')->fetchAll();
        $this->render('admin/product-form', ['product' => $product, 'categories' => $categories, 'brands' => $brands, 'title' => $product ? 'Edit product' : 'New product']);
    }

    public function saveProduct(): void
    {
        $this->validateCsrf();
        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
        $categoryId = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT);
        $brandId = filter_var($_POST['brand_id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        $name = trim((string) ($_POST['name'] ?? ''));
        $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
        $discount = filter_var($_POST['discount_percent'] ?? 0, FILTER_VALIDATE_FLOAT);
        $stock = filter_var($_POST['stock'] ?? null, FILTER_VALIDATE_INT);
        $active = isset($_POST['is_active']) ? 1 : 0;
        if (!$categoryId || $name === '' || strlen($name) > 150 || $price === false || $price < 0 || $discount === false || $discount < 0 || $discount > 100 || $stock === false || $stock < 0) {
            $_SESSION['flash'] = 'Check the product details and try again.';
            $this->redirect($this->basePath . '/admin/product' . ($id ? '?id=' . $id : ''));
        }
        $images = array_values(array_filter(array_map('trim', explode("\n", (string) ($_POST['image'] ?? '')))));
        $sizes = array_values(array_filter(array_map('trim', explode(',', (string) ($_POST['sizes'] ?? '')))));
        $data = [
            'category_id' => $categoryId, 'brand_id' => $brandId, 'name' => $name,
            'description' => trim((string) ($_POST['description'] ?? '')) ?: null,
            'ingredients' => trim((string) ($_POST['ingredients'] ?? '')) ?: null,
            'warnings' => trim((string) ($_POST['warnings'] ?? '')) ?: null,
            'price' => $price, 'discount_percent' => $discount, 'stock' => $stock,
            'sizes' => json_encode($sizes, JSON_THROW_ON_ERROR),
            'weight' => is_numeric($_POST['weight'] ?? null) ? (float) $_POST['weight'] : null,
            'image' => json_encode($images, JSON_THROW_ON_ERROR), 'is_active' => $active,
            'admin_id' => (int) $_SESSION['id'],
        ];
        if ($id) {
            $data['id'] = $id;
            $data['updated_by_admin_id'] = $data['admin_id'];
            unset($data['admin_id']);
            $stmt = $this->db->prepare('UPDATE products SET category_id=:category_id,brand_id=:brand_id,name=:name,description=:description,ingredients=:ingredients,warnings=:warnings,price=:price,discount_percent=:discount_percent,stock=:stock,sizes=:sizes,weight=:weight,image=:image,is_active=:is_active,updated_by_admin_id=:updated_by_admin_id WHERE id=:id');
        } else {
            $data['created_by_admin_id'] = $data['admin_id'];
            unset($data['admin_id']);
            $stmt = $this->db->prepare('INSERT INTO products (category_id,brand_id,name,description,ingredients,warnings,price,discount_percent,stock,sizes,weight,image,is_active,created_by_admin_id) VALUES (:category_id,:brand_id,:name,:description,:ingredients,:warnings,:price,:discount_percent,:stock,:sizes,:weight,:image,:is_active,:created_by_admin_id)');
        }
        $stmt->execute($data);
        $this->redirect($this->basePath . '/admin/products');
    }

    public function deleteProduct(): void
    {
        $this->validateCsrf();
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id) {
            $stmt = $this->db->prepare('UPDATE products SET is_active = 0, updated_by_admin_id = ? WHERE id = ?');
            $stmt->execute([(int) $_SESSION['id'], $id]);
        }
        $this->redirect($this->basePath . '/admin/products');
    }

    public function orders(): void
    {
        $model = new AdminOrder($this->db);
        $result = $model->getPaginatedOrders(max(1, (int) ($_GET['page'] ?? 1)), 8, ['status' => trim((string) ($_GET['status'] ?? '')), 'search' => trim((string) ($_GET['q'] ?? ''))]);
        $this->render('admin/orders', ['orders' => $result['orders'], 'page' => $result['page'], 'total' => $result['total'], 'title' => 'Orders']);
    }

    public function orderDetails(): void
    {
        $orderId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $order = $orderId ? (new AdminOrder($this->db))->getById($orderId) : null;
        if (!$order) {
            http_response_code(404);
            $this->render('store/not-found', ['title' => 'Order not found']);
            return;
        }

        $imageResolver = new ProductImageResolver();
        foreach ($order['items'] as &$item) {
            $resolved = $imageResolver->resolveProduct([
                'name' => $item['live_product_name'] ?? $item['product_name'],
                'image' => $item['product_images'] ?? '[]',
            ], $this->basePath);
            $item['image_url'] = (string) ($resolved['image_url'] ?? '');
            $item['product_url'] = !empty($item['product_id'])
                ? $this->basePath . '/product?id=' . (int) $item['product_id']
                : '';
        }
        unset($item);

        $this->render('admin/order-detail', ['order' => $order, 'title' => 'Order #' . $orderId]);
    }

    public function users(): void
    {
        $users = $this->db->query('SELECT u.id, u.name, u.email, u.phone, u.city, u.created_at, (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS order_count FROM users u ORDER BY u.created_at DESC LIMIT 250')->fetchAll();
        $this->render('admin/users', ['users' => $users, 'title' => 'Customers']);
    }

    public function catalog(): void
    {
        $categories = $this->db->query('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count FROM categories c ORDER BY c.name')->fetchAll();
        $brands = $this->db->query('SELECT b.*, (SELECT COUNT(*) FROM products p WHERE p.brand_id = b.id) AS product_count FROM brands b ORDER BY b.name')->fetchAll();
        $this->render('admin/catalog', ['categories' => $categories, 'brands' => $brands, 'title' => 'Catalog']);
    }

    public function saveCategory(): void
    {
        $this->validateCsrf();
        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? '')) ?: null;
        if ($name === '' || strlen($name) > 100) {
            $_SESSION['flash'] = 'Category names must be under 100 characters.';
            $this->redirect($this->basePath . '/admin/catalog');
        }
        if ($id) {
            $stmt = $this->db->prepare('UPDATE categories SET name=?, description=?, is_active=? WHERE id=?');
            $stmt->execute([$name, $description, isset($_POST['is_active']) ? 1 : 0, $id]);
        } else {
            $stmt = $this->db->prepare('INSERT INTO categories (name,description,is_active,created_by_admin_id) VALUES (?,?,1,?)');
            $stmt->execute([$name, $description, (int) $_SESSION['id']]);
        }
        $this->redirect($this->basePath . '/admin/catalog');
    }

    public function saveBrand(): void
    {
        $this->validateCsrf();
        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
        $name = trim((string) ($_POST['name'] ?? ''));
        $logo = trim((string) ($_POST['logo'] ?? '')) ?: null;
        $discount = filter_var($_POST['discount_percent'] ?? 0, FILTER_VALIDATE_FLOAT);
        if ($name === '' || strlen($name) > 100 || $discount === false || $discount < 0 || $discount > 100) {
            $_SESSION['flash'] = 'Check the brand name and discount value.';
            $this->redirect($this->basePath . '/admin/catalog');
        }
        if ($id) {
            $stmt = $this->db->prepare('UPDATE brands SET name=?, logo=?, discount_percent=? WHERE id=?');
            $stmt->execute([$name, $logo, $discount, $id]);
        } else {
            $stmt = $this->db->prepare('INSERT INTO brands (name,logo,discount_percent,created_by_admin_id) VALUES (?,?,?,?)');
            $stmt->execute([$name, $logo, $discount, (int) $_SESSION['id']]);
        }
        $this->redirect($this->basePath . '/admin/catalog');
    }

    public function updateOrderStatus(): void
    {
        $this->validateCsrf();
        $orderId = filter_var($_POST['order_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $status = (string) ($_POST['status'] ?? '');
        $updated = $orderId
            && in_array($status, ['processing', 'paid', 'shipped', 'delivered'], true)
            && (new AdminOrder($this->db))->updateStatus($orderId, $status, (int) $_SESSION['id']);
        if (!$updated) {
            $_SESSION['flash'] = 'That order status transition is not available.';
        } elseif ($status === 'paid') {
            $stmt = $this->db->prepare("UPDATE payments SET status = 'succeeded', paid_at = NOW()
                WHERE order_id = ? AND method = 'cash_on_delivery' AND status = 'pending'");
            $stmt->execute([$orderId]);
        }
        $this->redirect($this->basePath . '/admin/orders');
    }

    private function render(string $page, array $data): void
    {
        $this->view('layout', $data + [
            'page_view' => $page, 'base_path' => $this->basePath,
            'csrf_token' => $_SESSION['admin_csrf'], 'cart_count' => 0,
            'user' => ['name' => $_SESSION['name'] ?? 'Admin'], 'is_admin' => true,
        ]);
    }

    private function requireAdmin(): void
    {
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Admin access required.');
        }
    }

    private function validateCsrf(): void
    {
        if (!hash_equals($_SESSION['admin_csrf'] ?? '', (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            exit('Invalid request.');
        }
    }
}
