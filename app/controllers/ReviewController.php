<?php
/**
 * ReviewController
 * Atsauksmju sistēma (divpusējas - buyer to seller un seller to buyer)
 */

require_once __DIR__ . '/../helpers/RequestHelper.php';

class ReviewController {
    private $db;
    private $reviewModel;
    private $orderModel;

    public function __construct() {
        $this->db = db();
        require_once ROOT_DIR . '/app/models/Order.php';
        $this->orderModel = new Order();
    }

    /**
     * Saglabāt jaunu atsauksmi
     */
    public function store($orderId) {
        // Pārbaudīt autentifikāciju
        if (!AuthHelper::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /orders');
            exit;
        }

        // CSRF verifikācija
        RequestHelper::verifyCsrf();

        $userId = AuthHelper::getUserId();
        $order = $this->orderModel->findById($orderId);

        if (!$order) {
            Session::flash('error', lang('order.order_not_found'));
            header('Location: /orders');
            exit;
        }

        // Pārbaudīt vai lietotājs ir pasūtījuma dalībnieks
        if ($order['buyer_id'] != $userId && $order['seller_id'] != $userId) {
            Session::flash('error', lang('messages.access_denied'));
            header('Location: /orders');
            exit;
        }

        // Noteikt review type un reviewed_id
        if ($order['buyer_id'] == $userId) {
            // Pircējs vērtē pārdevēju
            $reviewType = 'buyer_to_seller';
            $reviewedId = $order['seller_id'];
        } else {
            // Pārdevējs vērtē pircēju
            $reviewType = 'seller_to_buyer';
            $reviewedId = $order['buyer_id'];
        }

        // Validācija
        $errors = [];
        $rating = intval($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            $errors[] = 'Vērtējumam jābūt no 1 līdz 5';
        }

        if (empty($comment)) {
            $errors[] = 'Komentārs ir obligāts';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            header('Location: /order/' . $orderId);
            exit;
        }

        // Pārbaudīt vai atsauksme jau eksistē
        $existing = $this->db->fetch(
            "SELECT id FROM reviews WHERE order_id = :order_id AND reviewer_id = :reviewer_id AND review_type = :review_type",
            [
                'order_id' => $orderId,
                'reviewer_id' => $userId,
                'review_type' => $reviewType
            ]
        );

        if ($existing) {
            Session::flash('error', 'Jūs jau esat atstājis atsauksmi par šo pasūtījumu');
            header('Location: /order/' . $orderId);
            exit;
        }

        // Saglabāt atsauksmi
        try {
            $this->db->insert('reviews', [
                'order_id' => $orderId,
                'reviewer_id' => $userId,
                'reviewed_id' => $reviewedId,
                'rating' => $rating,
                'comment' => $comment,
                'review_type' => $reviewType,
                'is_visible' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            Session::flash('success', lang('review.review_submitted'));
        } catch (Exception $e) {
            error_log("Error storing review: " . $e->getMessage());
            Session::flash('error', 'Kļūda saglabājot atsauksmi');
        }

        header('Location: /order/' . $orderId);
        exit;
    }

    /**
     * Iegūt lietotāja saņemtās atsauksmes
     */
    public function getUserReviews($userId, $reviewType = null) {
        $sql = "SELECT r.*,
                       u.full_name as reviewer_name,
                       o.order_number
                FROM reviews r
                JOIN users u ON r.reviewer_id = u.id
                JOIN orders o ON r.order_id = o.id
                WHERE r.reviewed_id = :user_id AND r.is_visible = 1";

        $params = ['user_id' => $userId];

        if ($reviewType) {
            $sql .= " AND r.review_type = :review_type";
            $params['review_type'] = $reviewType;
        }

        $sql .= " ORDER BY r.created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }
}
