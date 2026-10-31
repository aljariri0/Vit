 <?php

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllUsers(): array
    {
        $sql = "SELECT id, name, email, phone, address, city,
                       is_blocked_from_commenting,
                       created_at
                FROM users
                ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function searchUsers (string $search)
    {
        $sql = "select id , name , email , phone , address , city , is_blocked_from_commenting 
        from users where name LIKE :name OR email Like :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
        "name" => "%" . $search . "%",
        "email" => "%" . $search . "%"
    ]);
        return $stmt->fetchAll();

     }

     public function getUserById (int $id):array|false
     {
        $sql= "Select id , name , phone , address , city , is_blocked_from_commenting, created_at 
        from users where id =:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["id"=>$id]);
        return $stmt->fetch();
     }
     public function getUserOrders(int $userId): array
{
    $sql = "SELECT id, status, total, created_at
            FROM orders
            WHERE user_id = :user_id
            ORDER BY created_at DESC, id DESC";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        "user_id" => $userId
    ]);

    return $stmt->fetchAll();
}
public function setCommentBlock(int $userId, bool $isBlocked): bool
{
    $sql = "UPDATE users
            SET is_blocked_from_commenting = :is_blocked
            WHERE id = :user_id";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        "is_blocked" => $isBlocked ? 1 : 0,
        "user_id" => $userId
    ]);
}
} 
   