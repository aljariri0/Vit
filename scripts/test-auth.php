<?php
// Run with PHP CLI against the local database. All account writes are rolled back.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../app/Core/Controllers/AuthController.php';
session_save_path(sys_get_temp_dir());
session_start();
$db = (new Database())->connect();
$db->beginTransaction();
$check = static function ($condition, $message) { if (!$condition) throw new RuntimeException($message); };
try {
    $auth = new AuthController($db);
    $suffix = bin2hex(random_bytes(8));
    $email = 'auth-test-' . $suffix . '@example.test';
    $password = ' test-password-' . $suffix . ' ';
    $check(!$auth->register('Test', $email, 'short')['status'], 'Short passwords must fail.');
    $check($auth->register('Auth test', $email, $password)['status'], 'Registration failed.');
    $check(!$auth->register('Duplicate', $email, $password)['status'], 'Duplicate account was accepted.');
    $check(!$auth->login($email, 'wrong-password')['status'], 'Wrong password was accepted.');
    $check($auth->login($email, $password)['status'], 'Password sign-in failed.');
    $check(($_SESSION['role'] ?? '') === 'user', 'User session missing.');
    $check(!$auth->handleGoogleUser('test-google-' . $suffix, 'Test', $email)['status'], 'Password account must not be linked by email alone.');
    $googleEmail = 'google-test-' . $suffix . '@example.test';
    $check($auth->handleGoogleUser('test-google-' . $suffix, 'Google test', $googleEmail)['status'], 'Google account creation failed.');
    $googleUserId = $_SESSION['id'];
    $check($auth->handleGoogleUser('test-google-' . $suffix, 'Google test', $googleEmail)['status'], 'Google repeat sign-in failed.');
    $check($_SESSION['id'] == $googleUserId, 'Google identity did not return the same user.');
    $check(!$auth->login($googleEmail, $password)['status'], 'Google-only account accepted a password.');
    $passed = true;
} finally {
    $db->rollBack();
    $_SESSION = [];
    session_destroy();
}
if ($passed ?? false) echo "PASS: registration, password login, duplicates, Google identity, and account-link protection. All test accounts rolled back.\n";
