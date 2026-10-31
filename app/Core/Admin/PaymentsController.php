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
        $this->view('admin/payments/index', ['payments' => $payments]);
    }
}
