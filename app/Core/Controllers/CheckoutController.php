<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../../Models/Payment.php';
require_once __DIR__ . '/../../Models/Order.php';
require_once __DIR__ . '/../../Services/StripeService.php';

class CheckoutController extends Controller
{
    private PDO $db;
    private Payment $paymentModel;
    private Order $orderModel;
    private ?StripeService $stripe = null;
    private array $config;

    public function __construct()
    {
        parent::__construct();
        $this->config = require __DIR__ . '/../config.php';

        if ($this->userId() <= 0) {
            $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
            $this->redirect($base . '/login');
        }

        $pdo = (new Database())->connect();
        $this->db = $pdo;
        $this->paymentModel = new Payment($pdo);
        $this->orderModel = new Order($pdo);
    }

    public function index(): void
    {
        $orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT) ?: 0;
        $order = $this->orderModel->getById($orderId);

        if (!$order || (int) $order['user_id'] !== $this->userId()) {
            http_response_code(404);
            exit('Order not found.');
        }

        if ($order['status'] !== 'pending') {
            http_response_code(409);
            exit('This order is no longer awaiting payment.');
        }

        $method = (string) ($_GET['method'] ?? '');
        $intent = null;
        if ($method === 'card') {
            if (strtolower((string) ($order['currency'] ?? '')) !== strtolower((string) $this->config['stripe_currency'])) {
                http_response_code(409);
                exit('This order uses a different currency. Please create a new order to continue.');
            }

            try {
                $this->stripe = new StripeService($this->config);
            } catch (RuntimeException $e) {
                http_response_code(503);
                exit(htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
            }

            $intent = $this->stripe->createPaymentIntent($orderId, (float) $order['total']);
            if (!$intent['ok']) {
                http_response_code(502);
                exit(htmlspecialchars($intent['message'], ENT_QUOTES, 'UTF-8'));
            }

            $this->paymentModel->createPending($orderId, (float) $order['total'], $intent['intent_id'], (string) $order['currency']);
        } elseif (!in_array($method, ['', 'cash_on_delivery'], true)) {
            http_response_code(400);
            exit('Choose a valid payment method.');
        }

        $_SESSION['checkout_csrf'] ??= bin2hex(random_bytes(32));

        $this->view('checkout', [
            'order' => $order,
            'method' => $method,
            'client_secret' => $intent['client_secret'] ?? null,
            'stripe_pub_key' => $this->config['stripe_publishable_key'],
            'currency' => strtoupper((string) ($order['currency'] ?? 'usd')),
            'csrf_token' => $_SESSION['checkout_csrf'],
            'base_path' => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'),
        ]);
    }

    public function cashOnDelivery(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['checkout_csrf'] ?? '', (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            exit('Invalid request.');
        }

        $orderId = filter_var($_POST['order_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$orderId) {
            http_response_code(400);
            exit('Invalid order.');
        }

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? FOR UPDATE');
            $stmt->execute([$orderId, $this->userId()]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$order) {
                $this->db->rollBack();
                http_response_code(404);
                exit('Order not found.');
            }

            if ($order['status'] === 'pending') {
                $expireOtherMethods = $this->db->prepare("UPDATE payments SET status = 'failed'
                    WHERE order_id = ? AND method <> 'cash_on_delivery' AND status = 'pending'");
                $expireOtherMethods->execute([$orderId]);
                $this->paymentModel->createCashOnDelivery(
                    (int) $order['id'],
                    (float) $order['total'],
                    (string) $order['currency']
                );
                $update = $this->db->prepare("UPDATE orders SET status = 'processing' WHERE id = ? AND status = 'pending'");
                $update->execute([$orderId]);
                if ($update->rowCount() !== 1) {
                    throw new RuntimeException('Order state changed before cash on delivery could be confirmed.');
                }
            } elseif ($order['status'] === 'processing') {
                $existing = $this->db->prepare("SELECT id FROM payments WHERE order_id = ? AND method = 'cash_on_delivery' LIMIT 1");
                $existing->execute([$orderId]);
                if ($existing->fetchColumn() === false) {
                    throw new RuntimeException('This order is already being processed with another payment method.');
                }
            } else {
                throw new RuntimeException('This order can no longer be checked out.');
            }

            $this->db->commit();
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Cash on delivery confirmation failed: ' . $error->getMessage());
            $_SESSION['flash'] = 'Could not confirm cash on delivery. Please refresh and try again.';
            $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
            $this->redirect($base . '/checkout?order_id=' . $orderId . '&method=cash_on_delivery');
        }

        $_SESSION['flash'] = 'Order confirmed. Pay the courier when it arrives.';
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        $this->redirect($base . '/account/order?id=' . $orderId);
    }

    public function returnFromStripe(): void
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        $orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT) ?: 0;
        $intentId = trim((string) ($_GET['payment_intent'] ?? ''));
        $payment = $intentId !== '' ? $this->paymentModel->getByTransactionId($intentId) : null;
        $order = $payment ? $this->orderModel->getById((int) $payment['order_id']) : null;

        if (!$payment || !$order || (int) $order['id'] !== $orderId || (int) $order['user_id'] !== $this->userId()) {
            $_SESSION['flash'] = 'We could not match that payment to your order.';
            $this->redirect($base . '/account/orders');
        }

        if (($payment['status'] ?? '') === 'succeeded' && in_array(($order['status'] ?? ''), ['processing', 'shipped', 'delivered'], true)) {
            $this->redirect($base . '/account/order?id=' . $orderId);
        }

        try {
            $this->stripe ??= new StripeService($this->config);
            $result = $this->stripe->verifyPayment($intentId);
        } catch (RuntimeException $e) {
            $_SESSION['flash'] = 'Payment verification is temporarily unavailable.';
            $this->redirect($base . '/checkout?order_id=' . $orderId);
        }

        $expectedAmount = (int) round((float) $order['total'] * 100);
        $valid = ($result['ok'] ?? false)
            && ($result['amount'] ?? null) === $expectedAmount
            && (int) round((float) $payment['amount'] * 100) === $expectedAmount
            && ($result['currency'] ?? '') === strtolower((string) $order['currency'])
            && ($result['order_id'] ?? '') === (string) $orderId;

        if ($valid) {
            try {
                $this->db->beginTransaction();
                $orderUpdated = $this->orderModel->updateStatus($orderId, 'paid');
                $paymentUpdated = $orderUpdated && $this->paymentModel->markSucceeded(
                    (int) $payment['id'],
                    $result['last4'] ?? null,
                    $result['raw_data'] ?? []
                );
                if ($orderUpdated && $paymentUpdated) {
                    $this->db->commit();
                    $_SESSION['flash'] = 'Payment successful. Your order is confirmed.';
                    $this->redirect($base . '/account/order?id=' . $orderId);
                }
                if ($this->db->inTransaction()) $this->db->rollBack();
            } catch (Throwable $e) {
                if ($this->db->inTransaction()) $this->db->rollBack();
                error_log('Stripe return update failed: ' . $e->getMessage());
            }
            $_SESSION['flash'] = 'Payment was received, but the order status could not be updated. Contact support.';
            $this->redirect($base . '/account/order?id=' . $orderId);
        }

        $_SESSION['flash'] = 'Payment was not completed. You can retry checkout.';
        $this->redirect($base . '/checkout?order_id=' . $orderId);
    }

    public function verify(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input) || !hash_equals($_SESSION['checkout_csrf'] ?? '', (string) ($input['csrf_token'] ?? ''))) {
            $this->json(['ok' => false, 'message' => 'Invalid request.'], 403);
        }

        $intentId = trim((string) ($input['payment_intent_id'] ?? ''));
        $payment = $intentId !== '' ? $this->paymentModel->getByTransactionId($intentId) : null;
        if (!$payment) {
            $this->json(['ok' => false, 'message' => 'Payment not found.'], 404);
        }

        $order = $this->orderModel->getById((int) $payment['order_id']);
        if (!$order || (int) $order['user_id'] !== $this->userId()) {
            $this->json(['ok' => false, 'message' => 'Payment not found.'], 404);
        }
        if ($payment['status'] === 'succeeded') {
            $this->json(['ok' => true, 'message' => 'Payment successful.']);
        }
        if ($order['status'] !== 'pending') {
            $this->json(['ok' => false, 'message' => 'This order is no longer pending.'], 409);
        }
        if ($payment['status'] !== 'pending') {
            $this->json(['ok' => false, 'message' => 'This payment is no longer pending.'], 409);
        }

        try {
            $this->stripe ??= new StripeService($this->config);
        } catch (RuntimeException $e) {
            $this->json(['ok' => false, 'message' => 'Payment verification is unavailable.'], 503);
        }

        $result = $this->stripe->verifyPayment($intentId);
        $expectedAmount = (int) round((float) $order['total'] * 100);
        $validIntent = ($result['ok'] ?? false)
            && ($result['amount'] ?? null) === $expectedAmount
            && (int) round((float) $payment['amount'] * 100) === $expectedAmount
            && ($result['currency'] ?? '') === strtolower((string) $payment['currency'])
            && ($result['order_id'] ?? '') === (string) $payment['order_id'];

        if ($validIntent) {
            try {
                $this->db->beginTransaction();
                $orderUpdated = $this->orderModel->updateStatus(
                    (int) $payment['order_id'],
                    'paid'
                );
                $paymentUpdated = $orderUpdated && $this->paymentModel->markSucceeded(
                    (int) $payment['id'],
                    $result['last4'],
                    $result['raw_data']
                );

                if ($orderUpdated && $paymentUpdated) {
                    $this->db->commit();
                    $this->json(['ok' => true, 'message' => 'Payment successful.']);
                }

                $this->db->rollBack();
            } catch (Throwable $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                error_log('Payment state update failed: ' . $e->getMessage());
                $this->json(['ok' => false, 'message' => 'Could not save payment status.'], 500);
            }

            $latestPayment = $this->paymentModel->getByTransactionId($intentId);
            $latestOrder = $this->orderModel->getById((int) $payment['order_id']);
            if (
                ($latestPayment['status'] ?? '') === 'succeeded' &&
                in_array(($latestOrder['status'] ?? ''), ['processing', 'shipped', 'delivered'], true)
            ) {
                $this->json(['ok' => true, 'message' => 'Payment successful.']);
            }

            $this->json(['ok' => false, 'message' => 'Payment status changed. Please refresh and try again.'], 409);
        }

        if (in_array($result['status'] ?? '', ['canceled', 'requires_payment_method'], true)) {
            $this->paymentModel->markFailed((int) $payment['id']);
        }
        $this->json(['ok' => false, 'message' => 'Payment has not succeeded.'], 402);
    }

    private function userId(): int
    {
        return ($_SESSION['role'] ?? '') === 'user'
            ? (int) ($_SESSION['id'] ?? $_SESSION['user_id'] ?? 0)
            : 0;
    }
}
