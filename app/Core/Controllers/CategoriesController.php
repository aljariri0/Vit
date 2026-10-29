<?php

require_once __DIR__ . '../Database.php';

class Categories
{
    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->connect();
    }

    public function create(
        $name,
        $description = null,
        $image = null,
        $isActive = 1,
        $createdByAdminId = null
    ) {
        $name = trim($name);

        if ($name === '') {
            return false;
        }

        $sql = "INSERT INTO categories (name, description, image, is_active, created_by_admin_id) VALUES
                (:name, :description, :image, :is_active, :created_by_admin_id)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':image' => $image,
            ':is_active' => (int) $isActive,
            ':created_by_admin_id' => $createdByAdminId,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function getAll($activeOnly = false)
    {
        $sql = "SELECT categories.*, admins.name AS created_by_admin_name FROM categories
                LEFT JOIN admins ON admins.id = categories.created_by_admin_id";

        if ($activeOnly) {
            $sql .= " WHERE categories.is_active = 1";
        }

        $sql .= " ORDER BY categories.created_at DESC";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    // Read by id
    public function getById($id)
    {
        $sql = "SELECT categories.*, admins.name AS created_by_admin_name FROM categories
                LEFT JOIN admins ON admins.id = categories.created_by_admin_id
                WHERE categories.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([':id' => (int) $id,]);

        return $stmt->fetch();
    }

    // Read by name
    public function getByName($name)
    {
        $sql = "SELECT * FROM categories WHERE name = :name LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([':name' => trim($name),]);

        return $stmt->fetch();
    }

    // update
    public function update(
        $id,
        $name,
        $description = null,
        $image = null,
        $isActive = 1
    ) {
        $id = (int) $id;
        $name = trim($name);

        if ($id <= 0 || $name === '') {
            return false;
        }

        $sql = "UPDATE categories
                SET name = :name, description = :description, image = :image, is_active = :is_active
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':image' => $image,
            ':is_active' => (int) $isActive,
            ':id' => $id,
        ]);

        return $stmt->rowCount() > 0;
    }


    // delete
    public function delete($id)
    {
        $id = (int) $id;

        $sql = "DELETE FROM categories WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id,
        ]);

        return $stmt->rowCount() > 0;
    }

    // active or non-active
    public function setActive($id, $isActive)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return false;
        }

        $sql = "UPDATE categories SET is_active = :is_active WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':is_active' => (int) $isActive,
            ':id' => $id,
        ]);

        return $stmt->rowCount() > 0;
    }
}