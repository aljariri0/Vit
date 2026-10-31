<?php

require_once __DIR__ . '/../../Core/Database.php';
require_once __DIR__ . '/../../Core/google_config.php';
require_once __DIR__ . '/../../Core/Controllers/AuthController.php';

if (!isset($_GET['code'])) {
    header("Location: /Vit/public/login.php");
    exit();
}


$tokenUrl = 'https://oauth2.googleapis.com/token';
$postData = [
    'code'          => $_GET['code'],
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$tokenResponse = curl_exec($ch);

$tokenData = json_decode($tokenResponse, true);

if (empty($tokenData['access_token'])) {
die("Google verification failed: " . ($tokenData['error_description'] ?? 'Invalid authorization code'));}


$userInfoUrl = 'https://www.googleapis.com/oauth2/v3/userinfo';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $tokenData['access_token']
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$userResponse = curl_exec($ch);

$userData = json_decode($userResponse, true);

$googleId = $userData['sub'] ?? null;
$email    = $userData['email'] ?? null;
$name     = $userData['name'] ?? null;

if ($googleId && $email) {
    $database = new Database();
    $pdo = $database->connect();
    $auth = new AuthController($pdo);

    $result = $auth->handleGoogleUser($googleId, $name, $email);

    header("Location: /Vit/public/" . $result['redirect']);
    exit();
} else {
    header("Location: /Vit/public/login.php");
    exit();
}