<?php

require_once __DIR__ . '/../../Models/UserOrder.php';
class UserOrderController
{
    private $userOrder;

    public function __construct($pdo)
    {
        $this->userOrder = new UserOrder($pdo);
    }

    private function getUserId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'user') {
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