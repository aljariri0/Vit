<?php

class Controller
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    protected function view($viewName, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../../Views/' . $viewName . '.php';

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("<h1>Error:</h1> View file not found: <strong>" . htmlspecialchars($viewName) . ".php</strong>");
        }
    }
    protected function redirect($url)
    {
        header("Location: " . $url);
        exit;
    }
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
