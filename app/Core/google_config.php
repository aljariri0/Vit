<?php

require_once __DIR__ . '/Env.php';
Env::load();

define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
define('GOOGLE_REDIRECT_URI', getenv('GOOGLE_REDIRECT_URI') ?: '');
define('APP_BASE_PATH', rtrim(getenv('APP_BASE_PATH') ?: '', '/'));

function googleAuthFailure(string $message): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['oauth_error'] = $message;
    $mode = ($_SESSION['google_return_mode'] ?? '') === 'register' ? 'register' : 'login';
    $base = APP_BASE_PATH !== '' ? APP_BASE_PATH : rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    header('Location: ' . $base . '/' . $mode);
    exit;
}
