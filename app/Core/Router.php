<?php

class Router
{
    private $routes = [];
    public function get($url, $action)
    {
        $this->routes['GET'][$url] = $action;
    }
    public function post($url, $action)
    {
        $this->routes['POST'][$url] = $action;
    }
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($base !== '/' && strpos($url, $base) === 0) {
            $url = substr($url, strlen($base));
        }
        $url = rtrim($url, '/');
        if ($url === '') {
            $url = '/';
        }
        if (!isset($this->routes[$method][$url])) {
            http_response_code(404);
            echo '404 - Page Not Found';
            return;
        }
        list($controllerPath, $controllerMethod) = explode('@', $this->routes[$method][$url]);

        $file = __DIR__ . '/Controllers/' . $controllerPath . '.php';
        if (strpos($controllerPath, 'Admin/') === 0) {
            $file = __DIR__ . '/' . $controllerPath . '.php';
        }

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
