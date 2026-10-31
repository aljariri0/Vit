<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/CartController.php';

class AuthPageController extends Controller
{
    private AuthController $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new AuthController((new Database())->connect());
        $_SESSION['auth_csrf'] ??= bin2hex(random_bytes(32));
    }

    public function index(): void
    {
        $this->renderAuth('');
    }

    private function renderAuth(string $error): void
    {
        $this->view('layout', [
            'page_view' => 'auth-panel',
            'title' => 'Your Shifa account',
            'csrf_token' => $_SESSION['auth_csrf'],
            'error' => $error,
            'success' => '',
            'base_path' => $this->basePath(),
            'cart_count' => (new CartController())->getCartCount(),
            'user' => ($_SESSION['role'] ?? '') === 'user' ? ['name' => $_SESSION['name'] ?? 'Account'] : null,
            'is_admin' => ($_SESSION['role'] ?? '') === 'admin',
        ]);
        exit;
    }

    public function login(): void
    {
        $this->requirePost();
        $this->validateCsrf();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if (!is_string($email) || !is_string($password)) {
            $this->render('Invalid sign-in data.');
        }

        $result = $this->auth->login($email, $password);
        if (empty($result['status'])) {
            $this->render($result['message'] ?? 'Sign-in failed.');
        }

        $this->redirect($this->basePath() . $result['redirect']);
    }

    public function register(): void
    {
        $this->requirePost();
        $this->validateCsrf();

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if (!is_string($name) || !is_string($email) || !is_string($password)) {
            $this->render('Invalid registration data.');
        }

        $result = $this->auth->register($name, $email, $password);
        if (empty($result['status'])) {
            $this->render($result['message'] ?? 'Registration failed.');
        }

        $login = $this->auth->login($email, $password);
        if (empty($login['status'])) {
            $this->render('Account created. Please sign in.');
        }

        $this->redirect($this->basePath() . $login['redirect']);
    }

    public function logout(): void
    {
        $this->requirePost();
        $this->validateCsrf();
        $this->auth->logout();
        $this->redirect($this->basePath() . '/login');
    }

    private function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }
    }

    private function validateCsrf(): void
    {
        if (!hash_equals($_SESSION['auth_csrf'] ?? '', (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            exit('Invalid request.');
        }
    }

    private function render(string $error): void
    {
        http_response_code(400);
        $this->renderAuth($error);
    }

    private function basePath(): string
    {
        return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    }
}
