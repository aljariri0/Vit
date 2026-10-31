<?php

require_once __DIR__ . '/../../Models/Review.php';
require_once __DIR__ . '/../BadWordFilter.php';
require_once __DIR__ . '/../Controllers/Controller.php';

class ReviewsController extends Controller
{
    private Review $reviewModel;
    private BadWordFilter $badWordFilter;

    public function __construct(?PDO $pdo = null)
    {
        parent::__construct();
        $pdo ??= (new Database())->connect();
        $this->reviewModel = new Review($pdo);
        $this->badWordFilter = new BadWordFilter();
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $productId = filter_var(
            $_POST['product_id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );
        $userId = 0;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (($_SESSION['role'] ?? '') === 'user') {
            $userId = (int) ($_SESSION['id'] ?? 0);
        }
        $starRating = filter_var(
            $_POST['star_rating'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 5]]
        );
        $commentInput = $_POST['comment'] ?? '';
        if (!is_string($commentInput)) {
            http_response_code(400);
            exit('Invalid review data');
        }
        $comment = trim($commentInput);
        $image = null;

        if ($productId === false || $userId <= 0 || $starRating === false) {
            http_response_code(400);
            exit('Invalid review data');
        }
        if ($this->reviewModel->isUserBlockedFromRating($userId)) {
            http_response_code(403);
            exit('You cannot submit ratings.');
        }

        $hasBadWords = $this->badWordFilter->containsBadWords($comment);

        $status = $hasBadWords ? 'pending' : null;

        $this->reviewModel->create(
            $productId,
            $userId,
            $starRating,
            $comment,
            $image,
            $status
        );
        $this->json(['ok' => true, 'message' => 'Review received.'], 201);
    }


    public function index()
    {
        $this->requireAdmin();
        $_SESSION['review_csrf'] ??= bin2hex(random_bytes(32));
        $reviews = $this->reviewModel->getPendingReviews();
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
