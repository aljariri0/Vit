<?php

class Router
{
    private $routes = [];


    // Add GET route
    public function get($url, $action)
    {
        $this->routes['GET'][$url] = $action;
    }


    // Add POST route
    public function post($url, $action)
    {
        $this->routes['POST'][$url] = $action;
    }


    // Run the matched route
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // Get URL without query string (?id=1 etc.)
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove subfolder path (example: /Vit/public)
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($base !== '/' && strpos($url, $base) === 0) {
            $url = substr($url, strlen($base));
        }

        // Remove trailing slash
        $url = rtrim($url, '/');
        if ($url === '') {
            $url = '/';
        }

        // Route not found
        if (!isset($this->routes[$method][$url])) {
            http_response_code(404);
            echo '404 - Page Not Found';
            return;
        }

        // Action format: 'Folder/ControllerName@method'
        list($controllerPath, $controllerMethod) = explode('@', $this->routes[$method][$url]);

        $file = __DIR__ . '/Controllers/' . $controllerPath . '.php';

        if (!file_exists($file)) {
            http_response_code(500);
            echo 'Controller file not found: ' . $file;
            return;
        }

        require_once $file;

        $className = basename($controllerPath);

        $controller = new $className();
        $controller->$controllerMethod();
    }
}