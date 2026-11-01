<?php

require_once __DIR__ . '/../Controllers/Controller.php';
require_once __DIR__ . '/../../Models/Payment.php';

class PaymentsController extends Controller
{
    public function index(): void
    {
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Access denied.');
        }

        $payments = (new Payment())->getAll();
        $_SESSION['store_csrf'] ??= bin2hex(random_bytes(32));
        $this->view('layout', [
            'page_view' => 'admin/payments/index', 'payments' => $payments,
            'title' => 'Payments', 'base_path' => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'),
            'cart_count' => 0, 'user' => ['name' => $_SESSION['name'] ?? 'Admin'], 'is_admin' => true,
        ]);
    }
}
