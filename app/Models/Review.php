<?php


require_once __DIR__ . '/../Core/Database.php';

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
                    reviews.status,
                    users.name AS user_name,
                    products.name AS product_name
                FROM reviews 
                JOIN users ON reviews.user_id = users.id 
                JOIN products ON reviews.product_id = products.id
                ORDER BY reviews.id DESC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getByProductId(int $productId): array
    {
        $sql = "SELECT 
            reviews.id, 
            reviews.product_id, 
            reviews.user_id, 
            reviews.star_rating, 
            reviews.comment, 
            reviews.image, 
            reviews.status,
            reviews.created_at, 
            reviews.updated_at, 
            users.name AS user_name 
        FROM reviews 
        JOIN users ON reviews.user_id = users.id 
        WHERE reviews.product_id = ? 
        AND reviews.status = 'approved'
        ORDER BY reviews.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getAllApprovedReviews(): array
    {
        $sql = "SELECT
                reviews.id,
                reviews.product_id,
                reviews.user_id,
                reviews.star_rating,
                reviews.comment,
                reviews.created_at,
                users.name AS user_name
            FROM reviews
            JOIN users ON reviews.user_id = users.id
            WHERE reviews.status = 'approved'
            ORDER BY reviews.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
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
                    reviews.status,
                    users.name AS user_name 
                FROM reviews 
                JOIN users ON reviews.user_id = users.id 
                WHERE reviews.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        return $review ?: null;
    }
    public function create(
        int $productId,
        int $userId,
        int $starRating,
        ?string $comment = null,
        ?string $image = null,
        string $status = 'pending'
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
            $image,
            $status
        ]);

        return (int) $this->db->lastInsertId();
    }
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
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM reviews WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }


    public function updateStatus(int $id, string $status): bool
    {
        $allowedStatuses = ['approved', 'rejected'];

        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        $sql = "UPDATE reviews
            SET status = ?
            WHERE id = ? AND status = 'pending'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status, $id]);

        return $stmt->rowCount() > 0;
    }

    public function getPendingReviews(): array
    {
        $sql = "SELECT
                reviews.id,
                reviews.product_id,
                reviews.user_id,
                reviews.star_rating,
                reviews.comment,
                reviews.image,
                reviews.status,
                reviews.created_at,
                users.name AS user_name,
                products.name AS product_name
            FROM reviews
            JOIN users ON reviews.user_id = users.id
            JOIN products ON reviews.product_id = products.id
            WHERE reviews.status = 'pending'
            ORDER BY reviews.created_at DESC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}