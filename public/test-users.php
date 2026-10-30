<?php

require_once __DIR__ . "/../app/Core/Database.php";
require_once __DIR__ . "/../app/Core/Controllers/AdminController.php";

header("Content-Type: application/json; charset=utf-8");

$database = new Database();
$pdo = $database->connect();

$controller = new AdminController($pdo);

$search = $_GET["search"] ?? "";

$users = $controller->getUsers($search);

echo json_encode($users, JSON_UNESCAPED_UNICODE);
?>