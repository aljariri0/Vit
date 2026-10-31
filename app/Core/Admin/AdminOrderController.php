<?php

require_once __DIR__ . '/../../Models/AdminOrder.php';
class AdminOrderController
{
    private $adminOrder;

    public function __construct($pdo)
    {
        $this->adminOrder = new AdminOrder($pdo);
    }

    private function getAdminId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(401);
            return false;
        }

        return (int) $_SESSION['id'];
    }

    public function index($page = 1, $perPage = 10, $filters = [])
    {
        try {
            if ($this->getAdminId() === false) {
                return false;
            }

            return $this->adminOrder->getPaginatedOrders(
                $page,
                $perPage,
                $filters
            );

        } catch (Throwable $e) {
            http_response_code(500);
            error_log($e->getMessage());
            return false;
        }
    }

    public function show($orderId)
    {
        try {
            if ($this->getAdminId() === false) {
                return false;
            }

            $order = $this->adminOrder->getById($orderId);

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

    public function updateStatus($orderId, $status)
    {
        try {
            $adminId = $this->getAdminId();

            if ($adminId === false) {
                return false;
            }

            $result = $this->adminOrder->updateStatus(
                $orderId,
                $status,
                $adminId
            );

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

    public function cancel($orderId)
    {
        try {
            $adminId = $this->getAdminId();

            if ($adminId === false) {
                return false;
            }

            $result = $this->adminOrder->cancelOrder($orderId, $adminId);

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
