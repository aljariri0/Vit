<?php

class Profile
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUserById(int $id): array|false
    {
        $sql = "SELECT id, name, email, password_hash, phone, address, city, 
                       is_blocked_from_commenting, is_blocked_from_rating, created_at 
                FROM users 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function updateProfile(int $id, string $name, ?string $phone, ?string $address, ?string $city): bool
    {
        $sql = "UPDATE users 
                SET name = :name, phone = :phone, address = :address, city = :city, updated_at = NOW() 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'name'    => $name,
            'phone'   => $phone,
            'address' => $address,
            'city'    => $city,
            'id'      => $id
        ]);
    }

    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $sql = "UPDATE users 
                SET password_hash = :password_hash, updated_at = NOW() 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'password_hash' => $hashedPassword,
            'id'            => $id
        ]);
    }
}