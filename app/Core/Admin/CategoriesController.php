<?php

require_once __DIR__ . '/../../Models/Category.php';

class CategoriesController
{
    private Category $categoryModel;

    public function __construct(PDO $pdo)
    {
        $this->categoryModel = new Category($pdo);
    }

    public function index(): array
    {
        $this->requireAdmin();
        return $this->categoryModel->getAll();
    }

    public function show(int $id): ?array
    {
        $this->requireAdmin();
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid category ID.');
        }
        return $this->categoryModel->getById($id) ?: null;
    }

    public function store(array $data): int
    {
        $adminId = $this->requireAdmin();
        $category = $this->validate($data);

        return (int) $this->categoryModel->create(
            $category['name'],
            $category['description'],
            $category['image'],
            $category['is_active'],
            $adminId
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->requireAdmin();
        if ($id <= 0 || !$this->categoryModel->getById($id)) {
            throw new InvalidArgumentException('Category not found.');
        }

        $category = $this->validate($data);
        return $this->categoryModel->update(
            $id,
            $category['name'],
            $category['description'],
            $category['image'],
            $category['is_active']
        );
    }

    public function delete(int $id): bool
    {
        $this->requireAdmin();
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid category ID.');
        }
        if (!$this->categoryModel->delete($id)) {
            throw new InvalidArgumentException('Category is missing or still has products.');
        }
        return true;
    }

    private function validate(array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '' || strlen($name) > 100) {
            throw new InvalidArgumentException('Category name is required and must be at most 100 characters.');
        }

        $isActive = filter_var($data['is_active'] ?? 1, FILTER_VALIDATE_INT);
        if (!in_array($isActive, [0, 1], true)) {
            throw new InvalidArgumentException('Invalid category status.');
        }

        return [
            'name' => $name,
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
            'image' => trim((string) ($data['image'] ?? '')) ?: null,
            'is_active' => $isActive,
        ];
    }

    private function requireAdmin(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            throw new RuntimeException('Admin access is required.');
        }

        return (int) ($_SESSION['id'] ?? 0);
    }
}
