<?php

require_once __DIR__ . '/../../Core/Database.php';
require_once __DIR__ . '/../../Core/google_config.php';
require_once __DIR__ . '/../../Core/Controllers/AuthController.php';

$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$fail = static function (string $reason) use ($basePath): never {
    header('Location: ' . $basePath . '/login?error=' . rawurlencode($reason));
    exit;
};

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$states = $_SESSION['google_oauth_states'] ?? [];
$returnedState = is_string($_GET['state'] ?? null) ? $_GET['state'] : '';
$stateExpiry = is_array($states) ? ($states[$returnedState] ?? 0) : 0;
if (is_array($states)) unset($states[$returnedState]);
$_SESSION['google_oauth_states'] = is_array($states) ? $states : [];

if (isset($_GET['error'])) {
    $fail('google_cancelled');
}

if (
    GOOGLE_CLIENT_ID === '' ||
    GOOGLE_CLIENT_SECRET === '' ||
    GOOGLE_REDIRECT_URI === ''
) {
    http_response_code(503);
    exit('Google sign-in is not configured.');
}

if (
    !is_string($_GET['code'] ?? null) ||
    $_GET['code'] === '' ||
    $returnedState === '' ||
    !is_int($stateExpiry) ||
    $stateExpiry < time()
) {
    $fail('google_failed');
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
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 15,
]);

$tokenResponse = curl_exec($tokenRequest);
$tokenStatus = (int) curl_getinfo($tokenRequest, CURLINFO_HTTP_CODE);
$tokenError = curl_error($tokenRequest);
curl_close($tokenRequest);
$tokenData = is_string($tokenResponse) ? json_decode($tokenResponse, true) : null;

if ($tokenStatus !== 200 || empty($tokenData['access_token'])) {
    error_log('Google OAuth token exchange failed: ' . $tokenError);
    $fail('google_failed');
}

$userRequest = curl_init('https://openidconnect.googleapis.com/v1/userinfo');
curl_setopt_array($userRequest, [
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tokenData['access_token']],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 5,
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
    !in_array($userData['email_verified'] ?? null, [true, 'true', 1, '1'], true) ||
    empty($userData['name']) ||
    strlen((string) $userData['sub']) > 100 ||
    strlen((string) $userData['name']) > 100 ||
    strlen((string) $userData['email']) > 150
) {
    $fail('google_failed');
}

$database = new Database();
$auth = new AuthController($database->connect());
$result = $auth->handleGoogleUser(
    $userData['sub'],
    $userData['name'],
    $userData['email']
);

if (empty($result['status'])) {
    $fail('google_failed');
}

header('Location: ' . $basePath . $result['redirect']);
exit;
