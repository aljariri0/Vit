<?php

require_once __DIR__ . '/../Core/Database.php';

class Payment
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? (new Database())->connect();
    }

    public function createPending(int $orderId, float $amount, string $transactionId): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO payments (order_id, method, amount, provider_transaction_id, status)
             VALUES (?, 'visa', ?, ?, 'pending')"
        );
        $stmt->execute([$orderId, $amount, $transactionId]);

        return (int) $this->pdo->lastInsertId();
    }

    public function markSucceeded(int $paymentId, ?string $cardLast4, array $metadata): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE payments
             SET status = 'succeeded', paid_at = NOW(), card_last4 = ?, gateway_metadata = ?
             WHERE id = ? AND status = 'pending'"
        );
        return $stmt->execute([
            $cardLast4,
            json_encode($metadata, JSON_THROW_ON_ERROR),
            $paymentId,
        ]);
    }

    public function markFailed(int $paymentId): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE payments SET status = 'failed' WHERE id = ? AND status = 'pending'"
        );
        return $stmt->execute([$paymentId]);
    }

    public function getByTransactionId(string $transactionId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM payments WHERE provider_transaction_id = ? LIMIT 1'
        );
        $stmt->execute([$transactionId]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        return $payment === false ? null : $payment;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT p.*, o.user_id FROM payments p JOIN orders o ON o.id = p.order_id ORDER BY p.created_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
