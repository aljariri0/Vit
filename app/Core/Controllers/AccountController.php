<?php

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/CartController.php';
require_once __DIR__ . '/../../Models/UserOrder.php';

class AccountController extends Controller
{
    private UserOrder $orders;
    private string $basePath;

    public function __construct()
    {
        parent::__construct();
        $this->basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if (($_SESSION['role'] ?? '') !== 'user') $this->redirect($this->basePath . '/login');
        $this->orders = new UserOrder((new Database())->connect());
    }

    public function orders(): void
    {
        $rows = $this->orders->getUserOrders((int) $_SESSION['id']);
        $this->render('account/orders', ['orders' => $rows, 'title' => 'Your orders']);
    }

    public function order(): void
    {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $order = $id ? $this->orders->getUserOrderById($id, (int) $_SESSION['id']) : null;
        if (!$order) {
            http_response_code(404);
            $this->render('store/not-found', ['title' => 'Order not found']);
            return;
        }
        $this->render('account/order', ['order' => $order, 'title' => 'Order #' . $id]);
    }

    private function render(string $page, array $data): void
    {
        $_SESSION['store_csrf'] ??= bin2hex(random_bytes(32));
        $cart = new CartController();
        $this->view('layout', $data + [
            'page_view' => $page,
            'base_path' => $this->basePath,
            'csrf_token' => $_SESSION['store_csrf'],
            'cart_count' => $cart->getCartCount(),
            'user' => ['name' => $_SESSION['name'] ?? 'Account'],
            'is_admin' => false,
        ]);
    }
}
