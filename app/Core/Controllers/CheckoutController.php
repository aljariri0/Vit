<?php
// app/Core/Controllers/CheckoutController.php

require_once __DIR__ . '/Controller.php'; // Your base controller
require_once __DIR__ . '/../../Models/Payment.php';
require_once __DIR__ . '/../../Models/Order.php';
require_once __DIR__ . '/../../Services/StripeService.php';

class CheckoutController extends Controller
{
    private $paymentModel;
    private $orderModel;
    private $stripe;
    private $config;

    public function __construct()
    {
        parent::__construct();
        $this->paymentModel = new Payment();
        $this->orderModel   = new Order();
        $this->stripe       = new StripeService();
        $this->config       = require __DIR__ . '/../config.php';

        // Ensure user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    // GET /checkout?order_id=123
    public function index()
    {
        $orderId = (int)($_GET['order_id'] ?? 0);
        $order = $this->orderModel->getById($orderId);

        // Security check: Ensure order belongs to logged-in user
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            die("Invalid order.");
        }

        // 1. Create Intent with Stripe
        $intentData = $this->stripe->createPaymentIntent($orderId, $order['total']);

        if (!$intentData['ok']) {
            die("Stripe Error: " . $intentData['message']);
        }

        // 2. Save Pending Payment in DB
        $paymentId = $this->paymentModel->createPending(
            $orderId,
            $order['total'],
            $intentData['intent_id'] // Saved in provider_transaction_id
        );

        // 3. Pass data to the View
        $this->view('checkout', [
            'order'             => $order,
            'client_secret'     => $intentData['client_secret'],
            'intent_id'         => $intentData['intent_id'],
            'stripe_pub_key'    => $this->config['stripe_publishable_key']
        ]);
    }

    // POST /checkout/verify (Called via AJAX from frontend)
    public function verify()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $intentId = $input['payment_intent_id'] ?? '';

        if (!$intentId) {
            echo json_encode(['ok' => false, 'message' => 'Missing intent ID']);
            return;
        }

        // 1. Ask Stripe if it actually succeeded
        $verification = $this->stripe->verifyPayment($intentId);

        // 2. Find our DB record
        $payment = $this->paymentModel->getByTransactionId($intentId);

        if (!$payment) {
            echo json_encode(['ok' => false, 'message' => 'Payment record not found']);
            return;
        }

        if ($verification['ok']) {
            // SUCCESS: Update DB with card last4 and FULL JSON metadata
            $this->paymentModel->markSucceeded(
                $payment['id'],
                $verification['last4'],
                $verification['raw_data'] // Saved in gateway_metadata
            );

            // Update Order status to 'paid'
            $this->orderModel->updateStatus($payment['order_id'], 'paid');

            echo json_encode(['ok' => true, 'message' => 'Payment successful!']);
        } else {
            // FAILED
            $this->paymentModel->markFailed($payment['id']);
            echo json_encode(['ok' => false, 'message' => 'Payment failed: ' . $verification['status']]);
        }
    }
}
