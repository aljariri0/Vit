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

        $this->paymentModel->createPending($orderId, (float) $order['total'], $intent['intent_id']);
        $_SESSION['checkout_csrf'] ??= bin2hex(random_bytes(32));

        $this->view('checkout', [
            'order' => $order,
            'client_secret' => $intent['client_secret'],
            'stripe_pub_key' => $this->config['stripe_publishable_key'],
            'currency' => strtoupper($this->config['stripe_currency']),
            'csrf_token' => $_SESSION['checkout_csrf'],
            'base_path' => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'),
        ]);
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
            && ($result['currency'] ?? '') === strtolower($this->config['stripe_currency'])
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
                ($latestOrder['status'] ?? '') === 'paid'
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
