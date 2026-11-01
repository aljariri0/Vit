<?php

require_once __DIR__ . '/../../Models/Review.php';
require_once __DIR__ . '/../BadWordFilter.php';
require_once __DIR__ . '/../Controllers/Controller.php';

class ReviewsController extends Controller
{
    private PDO $db;
    private Review $reviewModel;

    public function __construct(?PDO $pdo = null)
    {
        parent::__construct();
        $pdo ??= (new Database())->connect();
        $this->db = $pdo;
        $this->reviewModel = new Review($pdo);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if (!hash_equals($_SESSION['store_csrf'] ?? '', (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            exit('Invalid request.');
        }
        if (($_SESSION['role'] ?? '') !== 'user' || (int) ($_SESSION['id'] ?? 0) < 1) {
            $_SESSION['flash'] = 'Sign in with a customer account to write a review.';
            $this->redirect($base . '/login');
        }

        $productId = filter_var(
            $_POST['product_id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );
        if ($productId === false) {
            http_response_code(400);
            exit('Invalid product.');
        }
        $returnToProduct = static function () use ($base, $productId): never {
            header('Location: ' . $base . '/product?id=' . (int) $productId . '#reviews');
            exit;
        };
        $productStmt = $this->db->prepare('SELECT id FROM products WHERE id = ? AND is_active = 1');
        $productStmt->execute([$productId]);
        if (!$productStmt->fetchColumn()) {
            http_response_code(404);
            exit('Product not found.');
        }

        $userId = (int) $_SESSION['id'];
        $starRating = filter_var(
            $_POST['star_rating'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 5]]
        );
        $commentInput = $_POST['comment'] ?? '';
        $comment = is_string($commentInput) ? trim($commentInput) : '';
        $commentLength = function_exists('mb_strlen') ? mb_strlen($comment) : strlen($comment);
        if ($starRating === false || $comment === '' || $commentLength > 2000) {
            $_SESSION['flash'] = 'Write a comment of up to 2,000 characters and choose a rating.';
            $returnToProduct();
        }
        if ($this->reviewModel->isUserBlockedFromRating($userId)) {
            $_SESSION['flash'] = 'Your account cannot submit product reviews.';
            $returnToProduct();
        }
        if ($this->reviewModel->isUserBlockedFromCommenting($userId)) {
            $_SESSION['flash'] = 'Your account cannot submit comments.';
            $returnToProduct();
        }

        try {
            $this->reviewModel->create((int) $productId, $userId, (int) $starRating, $comment, null, 'pending');
            $_SESSION['flash'] = 'Thanks for sharing. Your review is waiting for admin approval.';
        } catch (PDOException $e) {
            if (($e->errorInfo[0] ?? $e->getCode()) === '23000') {
                $_SESSION['flash'] = 'You have already reviewed this product.';
            } else {
                error_log('Product review could not be saved: ' . $e->getMessage());
                $_SESSION['flash'] = 'We could not save your review. Please try again.';
            }
        }
        $returnToProduct();
    }


    public function index()
    {
        $this->requireAdmin();
        $_SESSION['review_csrf'] ??= bin2hex(random_bytes(32));
        $reviews = $this->reviewModel->getAll();
        $this->view('layout', [
            'page_view' => 'admin/reviews/index',
            'reviews' => $reviews,
            'csrf_token' => $_SESSION['review_csrf'],
            'base_path' => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'),
            'cart_count' => 0,
            'user' => ['name' => $_SESSION['name'] ?? 'Admin'],
            'is_admin' => true,
            'title' => 'Reviews',
        ]);
    }

    public function approve()
    {
        $adminId = $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }
        $this->validateCsrf();

        $id = filter_var($_POST['review_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($id === false) {
            http_response_code(400);
            exit('Invalid review ID');
        }

        $updated = $this->reviewModel->updateStatus(
            $id,
            'approved',
            $adminId
        );

        if (!$updated) {
            http_response_code(404);
            exit('Pending review not found');
        }

        $this->redirect($this->adminReviewsUrl());
    }

    public function reject()
    {
        $adminId = $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }
        $this->validateCsrf();

        $id = filter_var($_POST['review_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($id === false) {
            http_response_code(400);
            exit('Invalid review ID');
        }

        $updated = $this->reviewModel->updateStatus(
            $id,
            'rejected',
            $adminId
        );

        if (!$updated) {
            http_response_code(404);
            exit('Pending review not found');
        }

        $this->redirect($this->adminReviewsUrl());
    }

    private function validateCsrf(): void
    {
        if (!hash_equals($_SESSION['review_csrf'] ?? '', (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            exit('Invalid request');
        }
    }

    private function adminReviewsUrl(): string
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        return $base . '/admin/reviews';
    }

    private function requireAdmin(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Access denied');
        }

        return (int) $_SESSION['id'];
    }


}
