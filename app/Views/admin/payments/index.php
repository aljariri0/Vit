<?php
$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<h1>Payments</h1>
<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead><tr><th>ID</th><th>Order</th><th>Customer</th><th>Method</th><th>Amount</th><th>Status</th><th>Created</th></tr></thead>
        <tbody>
        <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?= $escape($payment['id']) ?></td>
                <td><?= $escape($payment['order_id']) ?></td>
                <td><?= $escape($payment['user_id']) ?></td>
                <td><?= $escape($payment['method']) ?></td>
                <td><?= $escape(number_format((float) $payment['amount'], 2)) ?> JOD</td>
                <td><?= $escape(ucfirst($payment['status'])) ?></td>
                <td><?= $escape($payment['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$payments): ?><tr><td colspan="7">No payments found.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
