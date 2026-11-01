<?php

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../BrandFilter.php';
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/CartController.php';
require_once __DIR__ . '/../../Models/Review.php';
require_once __DIR__ . '/../../Services/ProductImageResolver.php';

class StorefrontController extends Controller
{
    private PDO $db;
    private CartController $cartModel;
    private ProductImageResolver $imageResolver;

    public function __construct()
    {
        parent::__construct();
        $this->db = (new Database())->connect();
        $this->cartModel = new CartController();
        $this->imageResolver = new ProductImageResolver();
        $_SESSION['store_csrf'] ??= bin2hex(random_bytes(32));
    }

    public function home(): void
    {
        $bestSellers = $this->db->query("SELECT p.*, (SELECT c.name FROM categories c WHERE c.id = p.category_id) AS category_name, SUM(oi.quantity) AS units_sold
            FROM order_items oi
            INNER JOIN orders o ON o.id = oi.order_id
            INNER JOIN products p ON p.id = oi.product_id
            WHERE p.is_active = 1 AND o.status IN ('paid', 'processing', 'shipped', 'delivered')
            GROUP BY p.id
            ORDER BY units_sold DESC, p.id DESC
            LIMIT 30")->fetchAll();
        $hasSalesData = (bool) $bestSellers;
        if (!$hasSalesData) {
            $bestSellers = $this->db->query('SELECT p.*, c.name AS category_name, 0 AS units_sold FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 ORDER BY p.id DESC LIMIT 30')->fetchAll();
        }
        $bestSellers = array_values(array_filter(array_map([$this, 'normalizeProduct'], $bestSellers), static fn (array $product): bool => $product['image_url'] !== ''));
        $bestSellers = array_slice($bestSellers, 0, 3);
        $categories = $this->db->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY name LIMIT 6')->fetchAll();
        $this->render('store/home', ['best_sellers' => $bestSellers, 'has_sales_data' => $hasSalesData, 'categories' => $categories, 'title' => 'Wellness, made personal']);
    }

    public function shop(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $category = filter_var($_GET['category'] ?? null, FILTER_VALIDATE_INT) ?: 0;
        $brandFilter = new BrandFilter($this->db, $_GET['brand'] ?? null);
        $sql = 'SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1';
        $params = [];
        if ($search !== '') {
            $sql .= ' AND (p.name LIKE ? OR p.description LIKE ? OR p.ingredients LIKE ?)';
            $params = array_fill(0, 3, '%' . $search . '%');
        }
        if ($category > 0) {
            $sql .= ' AND p.category_id = ?';
            $params[] = $category;
        }
        $brandFilter->apply($sql, $params);
        $sql .= ' ORDER BY p.id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = array_values(array_filter(array_map([$this, 'normalizeProduct'], $stmt->fetchAll()), static fn (array $product): bool => $product['image_url'] !== ''));
        $perPage = 12;
        $totalPages = max(1, (int) ceil(count($products) / $perPage));
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
        $page = min($page, $totalPages);
        $this->render('store/shop', [
            'products' => array_slice($products, ($page - 1) * $perPage, $perPage),
            'page' => $page,
            'total_pages' => $totalPages,
            'product_total' => count($products),
            'categories' => $this->db->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY name')->fetchAll(),
            'brands' => $brandFilter->options(),
            'search' => $search,
            'selectedCategory' => $category,
            'selectedBrand' => $brandFilter->selected(),
            'title' => $search !== '' ? 'Results for “' . $search . '”' : 'The wellness edit',
        ]);
    }

    public function product(): void
    {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $stmt = $this->db->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ? AND (p.is_active = 1 OR ? = 'admin')");
        $stmt->execute([$id ?: 0, ($_SESSION['role'] ?? '') === 'admin' ? 'admin' : 'user']);
        $product = $stmt->fetch();
        if (!$product) {
            http_response_code(404);
            $this->render('store/not-found', ['title' => 'Product not found']);
            return;
        }
        $product = $this->normalizeProduct($product);
        if ($product['image_url'] === '') {
            http_response_code(404);
            $this->render('store/not-found', ['title' => 'Product not found']);
            return;
        }
        $reviews = (new Review($this->db))->getByProductId((int) $product['id']);
        $this->render('store/product', [
            'product' => $product,
            'reviews' => $reviews,
            'title' => $product['name'],
        ]);
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
                $normalized = $this->normalizeProduct($product);
                if ($normalized['image_url'] !== '') {
                    $products[(int) $product['id']] = $normalized;
                }
            }
        }
        $items = [];
        $total = 0.0;
        foreach ($this->cartModel->getCart() as $entry) {
            $id = (int) $entry['product_id'];
            if (!isset($products[$id])) {
                $this->cartModel->removeFromCart($id, (string) ($entry['size'] ?? ''));
                continue;
            }
            $product = $products[$id];
            $price = round((float) $product['price'] * (1 - (float) $product['discount_percent'] / 100), 2);
            $line = $price * (int) $entry['quantity'];
            $total += $line;
            $items[] = ['product' => $product, 'size' => (string) $entry['size'], 'quantity' => (int) $entry['quantity'], 'price' => $price, 'subtotal' => $line];
        }
        if (($_GET['format'] ?? '') === 'json') {
            $this->json([
                'items' => array_map(static fn ($item) => [
                    'id' => (int) $item['product']['id'],
                    'name' => (string) $item['product']['name'],
                    'image_url' => (string) $item['product']['image_url'],
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ], $items),
                'total' => $total,
                'count' => $this->cartModel->getCartCount(),
            ]);
        }
        $this->render('store/cart', ['items' => $items, 'total' => $total, 'title' => 'Your bag']);
    }

    public function addToCart(): void
    {
        $this->verifyStoreCsrf();
        try {
            $productId = $this->positiveInteger($_POST['product_id'] ?? null);
            $quantity = $this->positiveInteger($_POST['quantity'] ?? '1');
            $sizeInput = $_POST['size'] ?? '';
            if ($productId === null || $quantity === null || !is_string($sizeInput)) {
                throw new InvalidArgumentException('Product and quantity must be valid whole numbers.');
            }
            $this->assertProductHasImage($productId);
            $this->cartModel->addToCart($this->db, $productId, $quantity, trim($sizeInput));
            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                $this->json(['ok' => true, 'count' => $this->cartModel->getCartCount(), 'message' => 'Added to your bag.']);
            }
            $_SESSION['flash'] = 'Added to your bag.';
        } catch (Throwable $e) {
            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                $this->json(['ok' => false, 'message' => $e->getMessage()], 422);
            }
            $_SESSION['flash'] = $e->getMessage();
        }
        $this->redirect($this->basePath() . '/cart');
    }

    public function updateCart(): void
    {
        $this->verifyStoreCsrf();
        $isAjax = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
        try {
            $productId = $this->positiveInteger($_POST['product_id'] ?? null);
            $quantity = $this->positiveInteger($_POST['quantity'] ?? null);
            $sizeInput = $_POST['size'] ?? '';
            if ($productId === null || $quantity === null || !is_string($sizeInput)) {
                throw new InvalidArgumentException('Quantity must be a positive whole number.');
            }
            $this->assertProductHasImage($productId);
            $this->cartModel->updateCartQuantity($this->db, $productId, $quantity, trim($sizeInput));
            if ($isAjax) $this->json(['ok' => true, 'count' => $this->cartModel->getCartCount()]);
        } catch (Throwable $e) {
            if ($isAjax) $this->json(['ok' => false, 'message' => $e->getMessage()], 422);
            $_SESSION['flash'] = $e->getMessage();
        }
        $this->redirect($this->basePath() . '/cart');
    }

    public function removeFromCart(): void
    {
        $this->verifyStoreCsrf();
        $isAjax = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
        try {
            $this->cartModel->removeFromCart((int) ($_POST['product_id'] ?? 0), trim((string) ($_POST['size'] ?? '')));
            if ($isAjax) $this->json(['ok' => true, 'count' => $this->cartModel->getCartCount()]);
        } catch (Throwable $e) {
            if ($isAjax) $this->json(['ok' => false, 'message' => $e->getMessage()], 422);
            $_SESSION['flash'] = $e->getMessage();
        }
        $this->redirect($this->basePath() . '/cart');
    }

    private function productRows(string $suffix): array
    {
        return array_values(array_filter(array_map([$this, 'normalizeProduct'], $this->db->query('SELECT p.* FROM products p WHERE p.is_active = 1 ' . $suffix)->fetchAll()), static fn (array $product): bool => $product['image_url'] !== ''));
    }

    private function assertProductHasImage(int $productId): void
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = ? AND is_active = 1');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        if (!$product || $this->normalizeProduct($product)['image_url'] === '') {
            throw new InvalidArgumentException('This product is no longer available.');
        }
    }

    public function normalizeProduct(array $product): array
    {
        $product = $this->imageResolver->resolveProduct($product, $this->basePath());
        $sizes = json_decode((string) ($product['sizes'] ?? ''), true);
        $product['sizes'] = is_array($sizes) ? $sizes : [];
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

    private function positiveInteger($value): ?int
    {
        if (!is_string($value) && !is_int($value)) {
            return null;
        }
        $integer = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return $integer === false ? null : $integer;
    }

    private function basePath(): string
    {
        return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    }
}
