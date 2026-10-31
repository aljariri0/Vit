<?php

require_once __DIR__ . '/../Database.php';

class AuthController
{
    private PDO $db;
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    public function register(string $name, string $email, string $password): array
    {
        $name = trim($name);
        $email = trim($email);
        $password = trim($password);

        if (empty($name) || empty($email) || empty($password)) {
            return [
                'status' => false,
                'message' => 'All fields are required !'
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => false,
                'message' => 'Enter a valid email address !'
            ];
        }
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            return [
                'status' => false,
                'message' => 'Email is already registered !'
            ];
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = $this->db->prepare("INSERT INTO users (name, email, password_hash, created_at) VALUES (?, ?, ?, NOW())");
        $isInserted = $insertStmt->execute([$name, $email, $hashedPassword]);

        if ($isInserted) {
            return [
                'status' => true,
                'message' => 'Account created successfully !'
            ];
        }

        return [
            'status' => false,
            'message' => 'Something went wrong, please try again.'
        ];
    }
    public function login(string $email, string $password): array
    {
        $email = trim($email);
        $password = trim($password);

        if (empty($email) || empty($password)) {
            return [
                'status' => false,
                'message' => 'All fields are required !'
            ];
        }
        $adminStmt = $this->db->prepare("SELECT id, name, email, password_hash FROM admins WHERE email = ? LIMIT 1");
        $adminStmt->execute([$email]);
        $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $this->startUserSession($admin['id'], $admin['name'], $admin['email'], 'admin');
            return [
                'status' => true,
                'redirect' => 'admin/dashboard.php'
            ];
        }
        $userStmt = $this->db->prepare("SELECT id, name, email, password_hash FROM users WHERE email = ? LIMIT 1");
        $userStmt->execute([$email]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $this->startUserSession($user['id'], $user['name'], $user['email'], 'user');
            return [
                'status' => true,
                'redirect' => 'index.php'
            ];
        }

        return [
            'status' => false,
            'message' => 'Invalid email or password'
        ];
    }
    public function handleGoogleUser(string $googleId, string $name, string $email): array
    {
        $stmt = $this->db->prepare("SELECT id, name, email FROM users WHERE google_id = ? OR email = ? LIMIT 1");
        $stmt->execute([$googleId, $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $updateStmt = $this->db->prepare("UPDATE users SET google_id = ? WHERE id = ?");
            $updateStmt->execute([$googleId, $user['id']]);

            $this->startUserSession($user['id'], $user['name'], $user['email'], 'user');
            return [
                'status' => true,
                'redirect' => 'index.php'
            ];
        }
        $insertStmt = $this->db->prepare("INSERT INTO users (name, email, google_id, created_at) VALUES (?, ?, ?, NOW())");
        $insertStmt->execute([$name, $email, $googleId]);
        $newUserId = $this->db->lastInsertId();

        $this->startUserSession($newUserId, $name, $email, 'user');
        return [
            'status' => true,
            'redirect' => 'index.php'
        ];
    }
    private function startUserSession($id, string $name, string $email, string $role): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['id'] = $id;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role; // Set as 'admin' or 'user' for routing/permissions
    }
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_unset();
        session_destroy();
    }
}