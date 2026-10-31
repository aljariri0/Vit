<?php

require_once __DIR__ . "/../../Models/User.php";

class AdminController
{
    private User $userModel;

    public function __construct(PDO $pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function getUsers(string $search = ""): array
    {
        $this->requireAdmin();
        $search = trim($search);

        if ($search === "") {
            return $this->userModel->getAllUsers();
        }

        return $this->userModel->searchUsers($search);
    }
public function getUserDetails(int $id): array|false
{
    $this->requireAdmin();
    $user = $this->userModel->getUserById($id);

    if ($user === false) {
        return false;
    }

    $orders = $this->userModel->getUserOrders($id);

    return [
        "user" => $user,
        "orders_count" => count($orders),
        "orders" => $orders
    ];
}
public function setUserCommentBlock(
    int $userId,
    bool $isBlocked
): bool
{
    $this->requireAdmin();
    return $this->userModel->setCommentBlock(
        $userId,
        $isBlocked
    );
}

private function requireAdmin(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        throw new RuntimeException('Admin access is required.');
    }
}
}
 
