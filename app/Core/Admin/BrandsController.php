<?php

require_once __DIR__ . '/../../Models/Brand.php';

class BrandsController
{
    private Brand $brandModel;

    public function __construct(PDO $pdo)
    {
        $this->brandModel = new Brand($pdo);
    }

    // List all brands.
    public function index(): array
    {
        $this->requireAdmin();
        return $this->brandModel->getAll();
    }

    // Get one brand by ID.
    public function show(int $id): ?array
    {
        $this->requireAdmin();

        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid brand ID.');
        }

        return $this->brandModel->get($id);
    }

    // Add a brand.
    public function store(array $data): int
    {
        $adminId = $this->requireAdmin();
        $brand = $this->validate($data);

        return $this->brandModel->create(
            $brand['name'],
            $brand['logo'],
            $brand['discount_percent'],
            $adminId
        );
    }

    // Edit a brand.
    public function update(int $id, array $data): bool
    {
        $this->requireAdmin();

        if ($id <= 0 || !$this->brandModel->get($id)) {
            throw new InvalidArgumentException('Brand not found.');
        }

        $brand = $this->validate($data);

        return $this->brandModel->update(
            $id,
            $brand['name'],
            $brand['logo'],
            $brand['discount_percent']
        );
    }

    // Delete a brand.
    public function delete(int $id): bool
    {
        $this->requireAdmin();

        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid brand ID.');
        }

        return $this->brandModel->delete($id);
    }

    private function validate(array $data): array
    {
        $name = trim($data['name'] ?? '');
        $logo = trim($data['logo'] ?? '');
        $discount = $data['discount_percent'] ?? 0;

        if ($name === '' || strlen($name) > 100) {
            throw new InvalidArgumentException(
                'Brand name is required and must be under 100 characters.'
            );
        }

        if (!is_numeric($discount) || $discount < 0 || $discount > 100) {
            throw new InvalidArgumentException(
                'Discount must be between 0 and 100.'
            );
        }

        return [
            'name' => $name,
            'logo' => $logo !== '' ? $logo : null,
            'discount_percent' => (float) $discount
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

        return (int) $_SESSION['id'];
    }
}
