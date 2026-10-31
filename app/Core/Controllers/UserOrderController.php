<?php

require_once __DIR__ . '/../../Models/UserOrder.php';
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/CartController.php';

class UserOrderController
{
    private $userOrder;

    public function __construct(?PDO $pdo = null)
    {
        $this->userOrder = new UserOrder($pdo ?? (new Database())->connect());
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['ok' => false, 'message' => 'Method not allowed.'], 405);
        }

        if ($this->getUserId() === false) {
            $this->json(['ok' => false, 'message' => 'Sign in first.'], 401);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (
            !is_array($input) ||
            !is_array($input['items'] ?? null) ||
            !is_array($input['shipping_data'] ?? null) ||
            !hash_equals($_SESSION['order_csrf'] ?? '', (string) ($input['csrf_token'] ?? ''))
        ) {
            $this->json(['ok' => false, 'message' => 'Invalid order data.'], 400);
        }

        $orderId = $this->create($input['items'], $input['shipping_data']);
        if ($orderId === false) {
            $this->json(['ok' => false, 'message' => 'Could not create the order.'], http_response_code() ?: 400);
        }

        (new CartController())->clearCart();

        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        $this->json([
            'ok' => true,
            'order_id' => $orderId,
            'checkout_url' => $basePath . '/checkout?order_id=' . $orderId,
        ], 201);
    }

    public function csrfToken(): void
    {
        if ($this->getUserId() === false) {
            $this->json(['ok' => false, 'message' => 'Sign in first.'], 401);
        }

        $_SESSION['order_csrf'] ??= bin2hex(random_bytes(32));
        $this->json(['ok' => true, 'csrf_token' => $_SESSION['order_csrf']], 200);
    }

    private function json(array $data, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function getUserId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id']) || ($_SESSION['role'] ?? '') !== 'user') {
            http_response_code(401);
            return false;
        }

        return (int) $_SESSION['id'];
    }

    public function create($items, $shippingData)
    {
        try {
            $userId = $this->getUserId();

            if ($userId === false) {
                return false;
            }

            return $this->userOrder->createOrder($userId, $items, $shippingData);

        } catch (Throwable $e) {
            http_response_code(400);
            error_log($e->getMessage());
            return false;
        }
    }

    public function index()
    {
        try {
            $userId = $this->getUserId();

            if ($userId === false) {
                return false;
            }

            return $this->userOrder->getUserOrders($userId);

        } catch (Throwable $e) {
            http_response_code(500);
            error_log($e->getMessage());
            return false;
        }
    }

    public function show($orderId)
    {
        try {
            $userId = $this->getUserId();

            if ($userId === false) {
                return false;
            }

            $order = $this->userOrder->getUserOrderById($orderId, $userId);

            if ($order === null) {
                http_response_code(404);
                return false;
            }

            return $order;

        } catch (Throwable $e) {
            http_response_code(500);
            error_log($e->getMessage());
            return false;
        }
    }

    public function track($orderId)
    {
        try {
            $userId = $this->getUserId();

            if ($userId === false) {
                return false;
            }

            $order = $this->userOrder->trackOrder($orderId, $userId);

            if ($order === null) {
                http_response_code(404);
                return false;
            }

            return $order;

        } catch (Throwable $e) {
            http_response_code(500);
            error_log($e->getMessage());
            return false;
        }
    }

    public function update($orderId, $items, $shippingData)
    {
        try {
            $userId = $this->getUserId();

            if ($userId === false) {
                return false;
            }

            $result = $this->userOrder->updateOrder(
                $orderId,
                $userId,
                $items,
                $shippingData
            );

            if (!$result) {
                http_response_code(400);
                return false;
            }

            return true;

        } catch (Throwable $e) {
            http_response_code(400);
            error_log($e->getMessage());
            return false;
        }
    }

    public function cancel($orderId)
    {
        try {
            $userId = $this->getUserId();

            if ($userId === false) {
                return false;
            }

            $result = $this->userOrder->cancelUserOrder($orderId, $userId);

            if (!$result) {
                http_response_code(400);
                return false;
            }

            return true;

        } catch (Throwable $e) {
            http_response_code(500);
            error_log($e->getMessage());
            return false;
        }
    }
}
