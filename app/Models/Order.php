<?php

require_once __DIR__ . '/../Core/Database.php';

class Order
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? (new Database())->connect();
    }

    public function getById(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        return $order === false ? null : $order;
    }

    public function updateStatus(int $orderId, string $status): bool
    {
        $allowedStatuses = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
        if (!in_array($status, $allowedStatuses, true)) {
            throw new InvalidArgumentException('Invalid order status');
        }

        $stmt = $this->pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $orderId]);
    }
}
