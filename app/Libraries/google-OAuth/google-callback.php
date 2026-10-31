<?php

require_once __DIR__ . '/../../Core/Database.php';
require_once __DIR__ . '/../../Core/google_config.php';
require_once __DIR__ . '/../../Core/Controllers/AuthController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$expectedState = $_SESSION['google_oauth_state'] ?? '';
$started = (int) ($_SESSION['google_oauth_started'] ?? 0);
unset($_SESSION['google_oauth_state'], $_SESSION['google_oauth_started']);

if (
    GOOGLE_CLIENT_ID === '' ||
    GOOGLE_CLIENT_SECRET === '' ||
    GOOGLE_REDIRECT_URI === ''
) {
    googleAuthFailure('Google sign-in is not available yet. Please continue with email.');
}

if (
    !is_string($_GET['state'] ?? null) ||
    $expectedState === '' ||
    time() - $started > 600 ||
    !hash_equals($expectedState, $_GET['state'])
) {
    googleAuthFailure('Your Google sign-in session expired or was invalid. Please try again.');
}

if (isset($_GET['error'])) googleAuthFailure('Google sign-in was cancelled. You can try again or continue with email.');
if (!is_string($_GET['code'] ?? null) || $_GET['code'] === '') googleAuthFailure('Google did not return a sign-in code. Please try again.');

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
    googleAuthFailure('We could not complete Google sign-in. Please try again.');
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
    googleAuthFailure('We could not verify your Google profile. Please continue with email.');
}

$database = new Database();
$auth = new AuthController($database->connect());
$result = $auth->handleGoogleUser(
    $userData['sub'],
    $userData['name'],
    $userData['email']
);

if (empty($result['status'])) googleAuthFailure($result['message'] ?? 'Google sign-in failed. Please continue with email.');
$base = APP_BASE_PATH !== '' ? APP_BASE_PATH : rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
header('Location: ' . $base . $result['redirect']);
exit;
