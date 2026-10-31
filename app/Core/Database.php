<?php

require_once __DIR__ . '/Env.php';

class Database
{
    private $servername;
    private $username;
    private $password;
    private $dbname;

    public function __construct()
    {
        Env::load();
        $this->servername = getenv('DB_HOST') ?: 'localhost';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->dbname = getenv('DB_NAME') ?: 'vitemines';
    }

    public function connect()
    {
        try {
            $pdo = new PDO(
                "mysql:host={$this->servername};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            return $pdo;

        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            http_response_code(503);
            exit('The Shifa store is temporarily unavailable. Check the database configuration.');
        }
    }
}
