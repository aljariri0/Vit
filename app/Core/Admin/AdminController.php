<?php

require_once __DIR__ . "/../../Models/User.php";

class AdminController
{
    private User $userModel;

    public function __construct(PDO $pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function getUsers(string $search = ""): array
    {
        $search = trim($search);

        if ($search === "") {
            return $this->userModel->getAllUsers();
        }

        return $this->userModel->searchUsers($search);
    }
    public function getUserDetails(int $id): array|false
{
    return $this->userModel->getUserById($id);
}
}