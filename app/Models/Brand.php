<?php

class Brand
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    private function format(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['discount_percent'] = (float) $row['discount_percent'];
        $row['created_by_admin_id'] = $row['created_by_admin_id'] !== null ? (int) $row['created_by_admin_id'] : null;
        return $row;
    }

    public function getAll(): array
    {
        $sql = "SELECT id, name, logo, discount_percent, created_by_admin_id, created_at
                FROM brands
                ORDER BY id DESC";

        $stmt = $this->db->query($sql);
        return array_map([$this, 'format'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function get(int $id): ?array
    {
        $sql = "SELECT id, name, logo, discount_percent, created_by_admin_id, created_at
                FROM brands WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        $brand = $stmt->fetch(PDO::FETCH_ASSOC);
        return $brand ? $this->format($brand) : null;
    }

    public function create(
        string $name,
        ?string $logo,
        float $discountPercent,
        ?int $createdByAdminId
    ): int {
        $sql = "INSERT INTO brands
                (name, logo, discount_percent, created_by_admin_id)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name, $logo, $discountPercent, $createdByAdminId]);
        return (int) $this->db->lastInsertId();
    }

    public function update(
        int $id,
        string $name,
        ?string $logo,
        float $discountPercent
    ): bool {
        $sql = "UPDATE brands
                SET name = ?, logo = ?, discount_percent = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name, $logo, $discountPercent, $id]);
        return true;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM brands WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
