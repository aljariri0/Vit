<?php

require_once __DIR__ . '/../app/Core/Database.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$prompt = static function (string $label): string {
    fwrite(STDOUT, $label);
    return trim((string) fgets(STDIN));
};

$name = $prompt('Admin name: ');
$email = strtolower($prompt('Admin email: '));
$password = $prompt('Admin password (12+ characters): ');

if ($name === '' || strlen($name) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
    fwrite(STDERR, "Enter a name, valid email, and password of at least 12 characters.\n");
    exit(1);
}

$db = (new Database())->connect();
$stmt = $db->prepare('INSERT INTO admins (name, email, password_hash) VALUES (?, ?, ?)');
$stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
fwrite(STDOUT, "Admin account created. Sign in at /login.\n");
