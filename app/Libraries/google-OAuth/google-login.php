<?php

require_once __DIR__ . '/../../Core/google_config.php';

if (GOOGLE_CLIENT_ID === '' || GOOGLE_CLIENT_SECRET === '' || GOOGLE_REDIRECT_URI === '') {
    googleAuthFailure('Google sign-in is not available yet. Please continue with email.');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$state = bin2hex(random_bytes(32));
$_SESSION['google_oauth_state'] = $state;
$_SESSION['google_oauth_started'] = time();

$params = [
    'response_type' => 'code',
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'scope' => 'openid email profile',
    'access_type' => 'online',
    'state' => $state,
];

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
exit;
