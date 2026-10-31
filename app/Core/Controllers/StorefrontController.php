<?php

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/CartController.php';

class StorefrontController extends Controller
{
    private PDO $db;
    private CartController $cartModel;

    public function __construct()
    {
        parent::__construct();
        $this->db = (new Database())->connect();
        $this->cartModel = new CartController();
        $_SESSION['store_csrf'] ??= bin2hex(random_bytes(32));
    }

    public function home(): void
    {
        $products = $this->productRows('ORDER BY p.id DESC LIMIT 8');
        $categories = $this->db->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY name LIMIT 6')->fetchAll();
        $this->render('store/home', ['products' => $products, 'categories' => $categories, 'title' => 'Wellness, made personal']);
    }

    public function shop(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $category = filter_var($_GET['category'] ?? null, FILTER_VALIDATE_INT) ?: 0;
        $sql = 'SELECT p.* FROM products p WHERE p.is_active = 1';
        $params = [];
        if ($search !== '') {
            $sql .= ' AND (p.name LIKE ? OR p.description LIKE ? OR p.ingredients LIKE ?)';
            $params = array_fill(0, 3, '%' . $search . '%');
        }
        if ($category > 0) {
            $sql .= ' AND p.category_id = ?';
            $params[] = $category;
        }
        $sql .= ' ORDER BY p.id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $this->render('store/shop', [
            'products' => array_map([$this, 'normalizeProduct'], $stmt->fetchAll()),
            'categories' => $this->db->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY name')->fetchAll(),
            'search' => $search,
            'selectedCategory' => $category,
            'title' => $search !== '' ? 'Results for “' . $search . '”' : 'The wellness edit',
        ]);
    }

    public function product(): void
    {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = ? AND is_active = 1');
        $stmt->execute([$id ?: 0]);
        $product = $stmt->fetch();
        if (!$product) {
            http_response_code(404);
            $this->render('store/not-found', ['title' => 'Product not found']);
            return;
        }
        $this->render('store/product', ['product' => $this->normalizeProduct($product), 'title' => $product['name']]);
    }

    public function cart(): void
    {
        $ids = array_values(array_unique(array_map(static fn ($item) => (int) $item['product_id'], $this->cartModel->getCart())));
        $products = [];
        if ($ids) {
            $marks = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $this->db->prepare("SELECT * FROM products WHERE is_active = 1 AND id IN ($marks)");
            $stmt->execute($ids);
            foreach ($stmt->fetchAll() as $product) {
                $products[(int) $product['id']] = $this->normalizeProduct($product);
            }
        }
        $items = [];
        $total = 0.0;
        foreach ($this->cartModel->getCart() as $entry) {
            $id = (int) $entry['product_id'];
            if (!isset($products[$id])) continue;
            $product = $products[$id];
            $price = round((float) $product['price'] * (1 - (float) $product['discount_percent'] / 100), 2);
            $line = $price * (int) $entry['quantity'];
            $total += $line;
            $items[] = ['product' => $product, 'size' => (string) $entry['size'], 'quantity' => (int) $entry['quantity'], 'price' => $price, 'subtotal' => $line];
        }
        $this->render('store/cart', ['items' => $items, 'total' => $total, 'title' => 'Your bag']);
    }

    public function addToCart(): void
    {
        $this->verifyStoreCsrf();
        try {
            $this->cartModel->addToCart($this->db, (int) ($_POST['product_id'] ?? 0), max(1, (int) ($_POST['quantity'] ?? 1)), trim((string) ($_POST['size'] ?? '')));
            $_SESSION['flash'] = 'Added to your bag.';
        } catch (Throwable $e) {
            $_SESSION['flash'] = $e->getMessage();
        }
        $this->redirect($this->basePath() . '/cart');
    }

    public function updateCart(): void
    {
        $this->verifyStoreCsrf();
        try {
            $this->cartModel->updateCartQuantity($this->db, (int) ($_POST['product_id'] ?? 0), max(1, (int) ($_POST['quantity'] ?? 1)), trim((string) ($_POST['size'] ?? '')));
        } catch (Throwable $e) {
            $_SESSION['flash'] = $e->getMessage();
        }
        $this->redirect($this->basePath() . '/cart');
    }

    public function removeFromCart(): void
    {
        $this->verifyStoreCsrf();
        try {
            $this->cartModel->removeFromCart((int) ($_POST['product_id'] ?? 0), trim((string) ($_POST['size'] ?? '')));
        } catch (Throwable $e) {
            $_SESSION['flash'] = $e->getMessage();
        }
        $this->redirect($this->basePath() . '/cart');
    }

    private function productRows(string $suffix): array
    {
        return array_map([$this, 'normalizeProduct'], $this->db->query('SELECT p.* FROM products p WHERE p.is_active = 1 ' . $suffix)->fetchAll());
    }

    public function normalizeProduct(array $product): array
    {
        foreach (['image', 'sizes'] as $field) {
            $decoded = json_decode((string) ($product[$field] ?? ''), true);
            $product[$field] = is_array($decoded) ? $decoded : [];
        }
        $product['image_url'] = (string) ($product['image'][0] ?? '');
        return $product;
    }

    private function render(string $page, array $data): void
    {
        $data += [
            'page_view' => $page,
            'base_path' => $this->basePath(),
            'csrf_token' => $_SESSION['store_csrf'],
            'cart_count' => $this->cartModel->getCartCount(),
            'user' => ($_SESSION['role'] ?? '') === 'user' ? ['name' => $_SESSION['name'] ?? 'Account'] : null,
            'is_admin' => ($_SESSION['role'] ?? '') === 'admin',
        ];
        $this->view('layout', $data);
    }

    private function verifyStoreCsrf(): void
    {
        if (!hash_equals($_SESSION['store_csrf'] ?? '', (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            exit('Invalid request.');
        }
    }

    private function basePath(): string
    {
        return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    }
}
