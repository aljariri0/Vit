<?php

class Controller
{
    public function __construct()
    {
        // Start session if it hasn't been started yet
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Load a view file and pass data to it
     * 
     * @param string $viewName The name of the view (e.g., 'checkout' or 'admin/users/index')
     * @param array $data Data to pass to the view (keys become variables)
     */
    protected function view($viewName, $data = [])
    {
        // Extract the array so $data['order'] becomes $order in the view
        extract($data);

        // Try to find the view file in app/Core/Views/
        $viewPath = __DIR__ . '/../Views/' . $viewName . '.php';

        // Fallback: If your Views folder is outside Core (e.g., app/Views/)
        if (!file_exists($viewPath)) {
            $viewPath = __DIR__ . '/../../Views/' . $viewName . '.php';
        }

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("<h1>Error:</h1> View file not found: <strong>" . htmlspecialchars($viewName) . ".php</strong>");
        }
    }

    /**
     * Redirect the user to another URL
     */
    protected function redirect($url)
    {
        header("Location: " . $url);
        exit;
    }

    /**
     * Return a JSON response and stop execution
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
