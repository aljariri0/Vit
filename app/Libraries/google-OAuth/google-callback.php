<?php

require_once __DIR__ . '/../../Core/Database.php';
require_once __DIR__ . '/../../Core/google_config.php';
require_once __DIR__ . '/../../Core/Controllers/AuthController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$expectedState = $_SESSION['google_oauth_state'] ?? '';
unset($_SESSION['google_oauth_state']);

if (
    GOOGLE_CLIENT_ID === '' ||
    GOOGLE_CLIENT_SECRET === '' ||
    GOOGLE_REDIRECT_URI === ''
) {
    http_response_code(503);
    exit('Google sign-in is not configured.');
}

if (
    empty($_GET['code']) ||
    !is_string($_GET['state'] ?? null) ||
    $expectedState === '' ||
    !hash_equals($expectedState, $_GET['state'])
) {
    http_response_code(400);
    exit('Invalid Google sign-in response.');
}

$tokenRequest = curl_init('https://oauth2.googleapis.com/token');
curl_setopt_array($tokenRequest, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'code' => $_GET['code'],
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code',
    ]),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
]);

$tokenResponse = curl_exec($tokenRequest);
$tokenStatus = (int) curl_getinfo($tokenRequest, CURLINFO_HTTP_CODE);
curl_close($tokenRequest);
$tokenData = is_string($tokenResponse) ? json_decode($tokenResponse, true) : null;

if ($tokenStatus !== 200 || empty($tokenData['access_token'])) {
    http_response_code(502);
    exit('Google token verification failed.');
}

$userRequest = curl_init('https://www.googleapis.com/oauth2/v3/userinfo');
curl_setopt_array($userRequest, [
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tokenData['access_token']],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
]);

$userResponse = curl_exec($userRequest);
$userStatus = (int) curl_getinfo($userRequest, CURLINFO_HTTP_CODE);
curl_close($userRequest);
$userData = is_string($userResponse) ? json_decode($userResponse, true) : null;

if (
    $userStatus !== 200 ||
    empty($userData['sub']) ||
    empty($userData['email']) ||
    empty($userData['email_verified']) ||
    empty($userData['name'])
) {
    http_response_code(502);
    exit('Google profile verification failed.');
}

$database = new Database();
$auth = new AuthController($database->connect());
$result = $auth->handleGoogleUser(
    $userData['sub'],
    $userData['name'],
    $userData['email']
);

header('Location: ' . APP_BASE_PATH . $result['redirect']);
exit;
