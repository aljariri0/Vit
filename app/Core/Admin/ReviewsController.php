<?php

require_once __DIR__ . '/../../Models/Review.php';
require_once __DIR__ . '/../BadWordFilter.php';

class ReviewsController
{
    private Review $reviewModel;
    private BadWordFilter $badWordFilter;

    public function __construct(PDO $pdo)
    {
        $this->reviewModel = new Review($pdo);
        $this->badWordFilter = new BadWordFilter();
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $userId = 0;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (($_SESSION['role'] ?? '') === 'user') {
            $userId = (int) ($_SESSION['id'] ?? 0);
        }
        $starRating = (int) ($_POST['star_rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        $image = null;

        if ($productId <= 0 || $userId <= 0 || $starRating < 1 || $starRating > 5) {
            http_response_code(400);
            exit('Invalid review data');
        }

        $hasBadWords = $this->badWordFilter->containsBadWords($comment);

        $status = $hasBadWords ? 'pending' : 'approved';

        $this->reviewModel->create(
            $productId,
            $userId,
            $starRating,
            $comment,
            $image,
            $status
        );
        header('Location: /Vit/public/');
        exit;
    }


    public function index()
    {
        $this->requireAdmin();

        return $this->reviewModel->getPendingReviews();
    }

    public function approve()
    {
        $adminId = $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $id = (int) ($_POST['review_id'] ?? 0);

        if ($id <= 0) {
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

        header('Location: /Vit/public/');
        exit;
    }

    public function reject()
    {
        $adminId = $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $id = (int) ($_POST['review_id'] ?? 0);

        if ($id <= 0) {
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

        header('Location: /Vit/public/');
        exit;
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
