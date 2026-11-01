<?php

require_once __DIR__ . '/Order.php';

class AdminOrder extends Order
{
    private array $allowedStatuses = [
        'pending',
        'paid',
        'processing',
        'shipped',
        'delivered',
        'cancelled',
        'refunded'
    ];

    private array $statusTransitions = [
        'pending' => ['processing'],
        'processing' => ['paid', 'shipped'],
        'paid' => ['shipped'],
        'shipped' => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
        'refunded' => []
    ];

    public function getPaginatedOrders(
        int $page = 1,
        int $perPage = 10,
        array $filters = []
    ): array {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "o.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(o.id = :order_id OR u.name LIKE :search)";
            $params[':order_id'] = $filters['search'];
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['from_date'])) {
            $where[] = "DATE(o.created_at) >= :from_date";
            $params[':from_date'] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $where[] = "DATE(o.created_at) <= :to_date";
            $params[':to_date'] = $filters['to_date'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT o.*, u.name AS customer_name, u.email
                FROM orders o
                JOIN users u ON o.user_id = u.id
                $whereSql
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countSql = "SELECT COUNT(*)
                     FROM orders o
                     JOIN users u ON o.user_id = u.id
                     $whereSql";

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);

        return [
            'orders' => $orders,
            'total' => (int) $countStmt->fetchColumn(),
            'page' => $page,
            'per_page' => $perPage
        ];
    }

    public function getById(int $orderId): ?array
    {
        $sql = "SELECT o.*, u.name AS customer_name, u.email, u.phone
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $orderId]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        $order['items'] = $this->orderItem->getItemsByOrderId($orderId);

        return $order;
    }

    public function updateStatus(
        int $orderId,
        string $newStatus,
        ?int $adminId = null
    ): bool {
        if (!in_array($newStatus, $this->allowedStatuses, true)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "SELECT status FROM orders WHERE id = :id"
        );

        $stmt->execute([':id' => $orderId]);

        $currentStatus = $stmt->fetchColumn();

        if (!$currentStatus) {
            return false;
        }

        if (
            !in_array(
                $newStatus,
                $this->statusTransitions[$currentStatus] ?? [],
                true
            )
        ) {
            return false;
        }

        $sql = "UPDATE orders
                SET status = :status,
                    processed_by_admin_id = :admin_id";

        if ($newStatus === 'shipped') {
            $sql .= ", shipping_time = NOW()";
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $newStatus,
            ':admin_id' => $adminId,
            ':id' => $orderId
        ]);
    }

    public function cancelOrder(
        int $orderId,
        ?int $adminId = null
    ): bool {
        return $this->cancelOrderProcess($orderId, null, $adminId);
    }
}