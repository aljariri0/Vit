<?php

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../../Models/Profile.php';

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

        $isAdmin = isset($_SESSION['role']);
        $loggedInUserId = $_SESSION['id'] ?? null;
        if ($isAdmin && isset($_GET['user_id'])) {
            $targetUserId = (int) $_GET['user_id'];
        } else {
            $targetUserId = (int) $loggedInUserId;
        }
        if (!$targetUserId) {
            header('Location: /Vit/public/login');
            exit;
        }

        $user = $this->profileModel->getUserById($targetUserId);

        if (!$user) {
            http_response_code(404);
            die("User noot found.");
        }
        $isOwner = ($loggedInUserId !== null && (int)$loggedInUserId === (int)$targetUserId);

        require_once __DIR__ . '/../../Views/profile.php';
    }
    public function changePassword(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Vit/public/profile.php');
            exit;
        }

        $loggedInUserId = $_SESSION['id'] ?? null;
        if (!$loggedInUserId) {
            header('Location: /Vit/public/login.php');
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            header('Location: /Vit/public/profile.php?error=empty_fields');
            exit;
        }
        if ($newPassword !== $confirmPassword) {
            header('Location: /Vit/public/profile.php?error=password_mismatch');
            exit;
        }
        $user = $this->profileModel->getUserById((int)$loggedInUserId);
        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            header('Location: /Vit/public/profile.php?error=wrong_current_password');
            exit;
        }
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->profileModel->updatePassword((int)$loggedInUserId, $newHashedPassword);

        header('Location: /Vit/public/profile.php?success=password_updated');
        exit;
    }
    public function update(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Vit/public/profile');
            exit;
        }

        $loggedInUserId = $_SESSION['user_id'] ?? null;
        if (!$loggedInUserId) {
            header('Location: /Vit/public/login');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? null);
        $address = trim($_POST['address'] ?? null);
        $city = trim($_POST['city'] ?? null);

        if (!empty($name)) {
            $this->profileModel->updateProfile((int)$loggedInUserId, $name, $phone, $address, $city);
        }

        header('Location: /Vit/public/profile?success=1');
        exit;
    }
}