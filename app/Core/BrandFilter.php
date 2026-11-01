<?php

class BrandFilter
{
    private PDO $db;
    private int $selectedBrand;

    public function __construct(PDO $db, $requestedBrand)
    {
        $this->db = $db;
        $this->selectedBrand = filter_var($requestedBrand, FILTER_VALIDATE_INT) ?: 0;
    }

    public function apply(string &$sql, array &$params): void
    {
        if ($this->selectedBrand > 0) {
            $sql .= ' AND p.brand_id = ?';
            $params[] = $this->selectedBrand;
        }
    }

    public function options(): array
    {
        return $this->db->query(
            'SELECT DISTINCT b.id, b.name FROM brands b '
            . 'INNER JOIN products p ON p.brand_id = b.id '
            . 'WHERE p.is_active = 1 ORDER BY b.name'
        )->fetchAll();
    }

    public function selected(): int
    {
        return $this->selectedBrand;
    }
}