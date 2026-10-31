<?php

require_once __DIR__ . '/../Core/Database.php';

class Category
{
    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->connect();
    }
    public function create($name, $description = null, $image = null, $isActive = 1, $adminId = null)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (name, description, image, is_active, created_by_admin_id)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->execute([$name, $description, $image, $isActive, $adminId]);

        return $this->pdo->lastInsertId();
    }
    public function getAll()
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM categories ORDER BY created_at DESC"
        );

        return $stmt->fetchAll();
    }
    public function getById($id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categories WHERE id = ? LIMIT 1"
        );

        $stmt->execute([$id]);

        return $stmt->fetch();
    }
    public function update($id, $name, $description = null, $image = null, $isActive = 1)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE categories
             SET name = ?, description = ?, image = ?, is_active = ?
             WHERE id = ?"
        );

        return $stmt->execute([$name, $description, $image, $isActive, $id]);
    }
    public function delete($id)
    {
        $check = $this->pdo->prepare(
            "SELECT COUNT(*) FROM products WHERE category_id = ?"
        );
        $check->execute([$id]);

        if ($check->fetchColumn() > 0) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "DELETE FROM categories WHERE id = ?"
        );
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}
