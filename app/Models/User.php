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
} 
   