<?php

require_once __DIR__ . '/../Core/Database.php';

class Payment
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? (new Database())->connect();
    }

    public function createPending(int $orderId, float $amount, string $transactionId, string $currency = 'usd'): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO payments (order_id, method, amount, currency, provider_transaction_id, status)
             VALUES (?, 'card', ?, ?, ?, 'pending')"
        );
        $stmt->execute([$orderId, $amount, strtolower($currency), $transactionId]);

        return (int) $this->pdo->lastInsertId();
    }

    public function createCashOnDelivery(int $orderId, float $amount, string $currency = 'usd'): int
    {
        $existing = $this->pdo->prepare(
            "SELECT id FROM payments WHERE order_id = ? AND method = 'cash_on_delivery' AND status = 'pending' LIMIT 1"
        );
        $existing->execute([$orderId]);
        $paymentId = $existing->fetchColumn();
        if ($paymentId !== false) {
            return (int) $paymentId;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO payments (order_id, method, amount, currency, status)
             VALUES (?, 'cash_on_delivery', ?, ?, 'pending')"
        );
        $stmt->execute([$orderId, $amount, strtolower($currency)]);

        return (int) $this->pdo->lastInsertId();
    }

    public function markSucceeded(int $paymentId, ?string $cardLast4, array $metadata): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE payments
             SET status = 'succeeded', paid_at = NOW(), card_last4 = ?, gateway_metadata = ?
             WHERE id = ? AND status = 'pending'"
        );
        $stmt->execute([
            $cardLast4,
            json_encode($metadata, JSON_THROW_ON_ERROR),
            $paymentId,
        ]);

        return $stmt->rowCount() === 1;
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
