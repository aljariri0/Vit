<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Models/UserOrder.php';

$db = (new Database())->connect();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec(
    "CREATE TABLE IF NOT EXISTS app_migrations (
        migration_key VARCHAR(190) PRIMARY KEY,
        applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB"
);

$migrationKey = '2026-11-01_jod_catalog_to_usd';
$check = $db->prepare('SELECT 1 FROM app_migrations WHERE migration_key = ?');
$check->execute([$migrationKey]);
if ($check->fetchColumn()) {
    $db->exec("ALTER TABLE orders ALTER currency SET DEFAULT 'usd'");
    $db->exec("ALTER TABLE payments ALTER currency SET DEFAULT 'usd'");
    echo "Migration already applied; no changes made.\n";
    exit(0);
}

$columns = [];
foreach (['orders', 'payments'] as $table) {
    $columns[$table] = array_column(
        $db->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_ASSOC),
        'Field'
    );
}
if (in_array('currency', $columns['orders'], true) || in_array('currency', $columns['payments'], true)) {
    throw new RuntimeException('Currency columns already exist without the migration marker; inspect the database before proceeding.');
}

// Save this database's own catalog prices before changing them.
$backupPath = __DIR__ . '/catalog_prices_before_usd_' . date('Ymd_His') . '.csv';
$backupFile = fopen($backupPath, 'x');
if ($backupFile === false) {
    throw new RuntimeException('Could not create the catalog price backup.');
}
try {
    fputcsv($backupFile, ['id', 'category_id', 'brand_id', 'name', 'price', 'stock', 'is_active']);
    $products = $db->query(
        'SELECT id, category_id, brand_id, name, price, stock, is_active FROM products ORDER BY id'
    );
    while (($product = $products->fetch(PDO::FETCH_ASSOC)) !== false) {
        fputcsv($backupFile, $product);
    }
} finally {
    fclose($backupFile);
}
echo 'Catalog price backup created: ' . basename($backupPath) . "\n";

$pendingOrders = $db->query(
    "SELECT id, user_id FROM orders WHERE status = 'pending' ORDER BY id"
)->fetchAll(PDO::FETCH_ASSOC);
$userOrders = new UserOrder($db);
foreach ($pendingOrders as $order) {
    $payment = $db->prepare(
        "SELECT COUNT(*) FROM payments WHERE order_id = ? AND status IN ('pending', 'succeeded')"
    );
    $payment->execute([(int) $order['id']]);
    if ((int) $payment->fetchColumn() > 0) {
        continue;
    }
    if (!$userOrders->cancelUserOrder((int) $order['id'], (int) $order['user_id'])) {
        throw new RuntimeException('Could not cancel unpaid pending order #' . (int) $order['id']);
    }
    echo 'Cancelled unpaid pending order #' . (int) $order['id'] . " and restored reserved stock.\n";
}

$db->exec("ALTER TABLE orders ADD COLUMN currency CHAR(3) NOT NULL DEFAULT 'jod' AFTER total");
$db->exec("ALTER TABLE payments ADD COLUMN currency CHAR(3) NOT NULL DEFAULT 'jod' AFTER amount");
$db->exec("UPDATE orders SET currency = 'jod'");
$db->exec("UPDATE payments SET currency = 'jod'");
$db->exec("ALTER TABLE orders ALTER currency SET DEFAULT 'usd'");
$db->exec("ALTER TABLE payments ALTER currency SET DEFAULT 'usd'");

$db->beginTransaction();
try {
    $db->exec('UPDATE products SET price = ROUND(price / 0.709, 2)');
    $record = $db->prepare('INSERT INTO app_migrations (migration_key) VALUES (?)');
    $record->execute([$migrationKey]);
    $db->commit();
} catch (Throwable $error) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    throw $error;
}

$summary = $db->query(
    'SELECT COUNT(*) AS product_count, MIN(price) AS min_price, MAX(price) AS max_price FROM products'
)->fetch(PDO::FETCH_ASSOC);
echo 'Converted product prices at 1 USD = 0.709 JOD: ' . json_encode($summary) . "\n";
