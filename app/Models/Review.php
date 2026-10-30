<?php

require_once __DIR__ . '/../Database.php';

class Review
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function isUserBlockedFromCommenting(int $userId): bool
    {
        $sql = "SELECT is_blocked_from_commenting FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        $status = $stmt->fetchColumn();

        return (int) $status === 1;
    }

    // 1. Get all reviews (with user names and product names for admin overview)
    public function getAll(): array
    {
        $sql = "SELECT 
                    reviews.id, 
                    reviews.product_id, 
                    reviews.user_id, 
                    reviews.star_rating, 
                    reviews.comment, 
                    reviews.image, 
                    reviews.created_at, 
                    reviews.updated_at, 
                    users.name AS user_name,
                    products.name AS product_name
                FROM reviews 
                JOIN users ON reviews.user_id = users.id 
                JOIN products ON reviews.product_id = products.id
                ORDER BY reviews.id DESC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Get reviews by product ID (to show them on the product page)
    public function getByProductId(int $productId): array
    {
        $sql = "SELECT 
                    reviews.id, 
                    reviews.product_id, 
                    reviews.user_id, 
                    reviews.star_rating, 
                    reviews.comment, 
                    reviews.image, 
                    reviews.created_at, 
                    reviews.updated_at, 
                    users.name AS user_name 
                FROM reviews 
                JOIN users ON reviews.user_id = users.id 
                WHERE reviews.product_id = ? 
                ORDER BY reviews.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Get single review by ID
    public function getById(int $id): ?array
    {
        $sql = "SELECT 
                    reviews.id, 
                    reviews.product_id, 
                    reviews.user_id, 
                    reviews.star_rating, 
                    reviews.comment, 
                    reviews.image, 
                    reviews.created_at, 
                    reviews.updated_at, 
                    users.name AS user_name 
                FROM reviews 
                JOIN users ON reviews.user_id = users.id 
                WHERE reviews.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        return $review ?: null;
    }

    // 4. Create review
    public function create(
        int $productId,
        int $userId,
        int $starRating,
        ?string $comment = null,
        ?string $image = null
    ): int {
        if ($this->isUserBlockedFromCommenting($userId)) {
            $comment = null;
        }

        $sql = "INSERT INTO reviews 
                (product_id, user_id, star_rating, comment, image)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $productId,
            $userId,
            $starRating,
            $comment,
            $image
        ]);

        return (int) $this->db->lastInsertId();
    }

    // 5. Update review
    public function update(
        int $id,
        int $starRating,
        ?string $comment = null,
        ?string $image = null
    ): bool {
        $sql = "UPDATE reviews
                SET star_rating = ?,
                    comment = ?,
                    image = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $starRating,
            $comment,
            $image,
            $id
        ]);

        return $stmt->rowCount() > 0;
    }

    // 6. Delete review
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM reviews WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}