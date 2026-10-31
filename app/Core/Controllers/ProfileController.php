<?php

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../../Models/Profile.php';
require_once __DIR__ . '/CartController.php';

class ProfileController
{
    private PDO $db;
    private Profile $profileModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
        $this->profileModel = new Profile($this->db);
    }
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $isAdmin = ($_SESSION['role'] ?? '') === 'admin';
        $loggedInUserId = $_SESSION['id'] ?? null;
        if (!$isAdmin && ($_SESSION['role'] ?? '') !== 'user') {
            http_response_code(401);
            exit('Sign in to view your profile.');
        }
        if ($isAdmin && isset($_GET['user_id'])) {
            $targetUserId = (int) $_GET['user_id'];
        } else {
            $targetUserId = (int) $loggedInUserId;
        }
        if (!$targetUserId) {
            http_response_code(404);
            exit('Profile not found.');
        }

        $user = $this->profileModel->getUserById($targetUserId);

        if (!$user) {
            http_response_code(404);
            die("User noot found.");
        }
        $isOwner = ($loggedInUserId !== null && (int)$loggedInUserId === (int)$targetUserId);
        $_SESSION['profile_csrf'] ??= bin2hex(random_bytes(32));

        $cart = new CartController();
        $this->view('layout', [
            'page_view' => 'profile',
            'title' => 'Your profile',
            'profile' => $user,
            'is_admin' => $isAdmin,
            'cart_count' => $cart->getCartCount(),
            'base_path' => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'),
        ]);
    }
    public function changePassword(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $loggedInUserId = $_SESSION['id'] ?? null;
        if (!$loggedInUserId || ($_SESSION['role'] ?? '') !== 'user') {
            http_response_code(401);
            exit('Sign in to update your password.');
        }
        if (!hash_equals($_SESSION['profile_csrf'] ?? '', (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            exit('Invalid request.');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        if (!is_string($currentPassword) || !is_string($newPassword) || !is_string($confirmPassword)) {
            $this->redirect($this->profileUrl('error=empty_fields'));
        }
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $this->redirect($this->profileUrl('error=empty_fields'));
            exit;
        }
        if ($newPassword !== $confirmPassword) {
            $this->redirect($this->profileUrl('error=password_mismatch'));
            exit;
        }
        if (strlen($newPassword) < 8) {
            $this->redirect($this->profileUrl('error=password_too_short'));
            exit;
        }
        $user = $this->profileModel->getUserById((int)$loggedInUserId);
        if (!$user || !is_string($user['password_hash']) || !password_verify($currentPassword, $user['password_hash'])) {
            $this->redirect($this->profileUrl('error=wrong_current_password'));
            exit;
        }
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->profileModel->updatePassword((int)$loggedInUserId, $newHashedPassword);

        $this->redirect($this->profileUrl('success=password_updated'));
        exit;
    }
    public function update(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $loggedInUserId = $_SESSION['id'] ?? null;
        if (!$loggedInUserId || ($_SESSION['role'] ?? '') !== 'user') {
            http_response_code(401);
            exit('Sign in to update your profile.');
        }
        if (!hash_equals($_SESSION['profile_csrf'] ?? '', (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            exit('Invalid request.');
        }

        $fields = [
            $_POST['name'] ?? '',
            $_POST['phone'] ?? '',
            $_POST['address'] ?? '',
            $_POST['city'] ?? '',
        ];
        if (array_filter($fields, static fn ($field) => !is_string($field))) {
            $this->redirect($this->profileUrl('error=invalid_profile'));
        }
        [$name, $phone, $address, $city] = array_map('trim', $fields);

        if (
            $name === '' || strlen($name) > 100 ||
            strlen($phone) > 30 || strlen($address) > 255 || strlen($city) > 80
        ) {
            $this->redirect($this->profileUrl('error=invalid_profile'));
        }

        $this->profileModel->updateProfile(
            (int) $loggedInUserId,
            $name,
            $phone !== '' ? $phone : null,
            $address !== '' ? $address : null,
            $city !== '' ? $city : null
        );

        $this->redirect($this->profileUrl('success=1'));
        exit;
    }

    private function profileUrl(string $query = ''): string
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        return $base . '/profile' . ($query !== '' ? '?' . $query : '');
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
