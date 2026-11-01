<?php

require_once __DIR__ . '/../../Core/Env.php';
Env::load();
require_once __DIR__ . '/../../Core/google_config.php';

$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$loginUrl = $basePath . '/login';

if (GOOGLE_CLIENT_ID === '' || GOOGLE_CLIENT_SECRET === '' || GOOGLE_REDIRECT_URI === '') {
    header('Location: ' . $loginUrl . '?error=google_not_configured');
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$state = bin2hex(random_bytes(32));
$states = $_SESSION['google_oauth_states'] ?? [];
if (!is_array($states)) $states = [];
foreach ($states as $knownState => $expiresAt) {
    if (!is_int($expiresAt) || $expiresAt < time()) unset($states[$knownState]);
}
$states[$state] = time() + 600;
$_SESSION['google_oauth_states'] = $states;

$params = [
    'response_type' => 'code',
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'scope' => 'openid email profile',
    'access_type' => 'online',
    'state' => $state,
    'prompt' => 'select_account',
];

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
exit;
