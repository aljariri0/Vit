<?php

require_once __DIR__ . '/../Core/Database.php'; // Adjust path if Database.php is elsewhere

class Payment
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new Database())->connect();
    }

    // Create a pending payment record
    public function createPending($orderId, $amount, $transactionId)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO payments (order_id, method, amount, provider_transaction_id, status) 
             VALUES (?, 'visa', ?, ?, 'pending')"
        );
        $stmt->execute([$orderId, $amount, $transactionId]);
        return (int) $this->pdo->lastInsertId();
    }

    // Update with success data and full JSON metadata
    public function markSucceeded($paymentId, $cardLast4, $metadataArray)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE payments 
             SET status = 'succeeded', 
                 paid_at = NOW(), 
                 card_last4 = ?, 
                 gateway_metadata = ? 
             WHERE id = ?"
        );
        $stmt->execute([
            $cardLast4,
            json_encode($metadataArray),
            $paymentId
        ]);
    }

    public function markFailed($paymentId)
    {
        $stmt = $this->pdo->prepare("UPDATE payments SET status = 'failed' WHERE id = ?");
        $stmt->execute([$paymentId]);
    }

    public function getByTransactionId($transactionId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM payments WHERE provider_transaction_id = ? LIMIT 1");
        $stmt->execute([$transactionId]);
        return $stmt->fetch();
    }
}
