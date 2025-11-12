<?php
/**
 * Review Model
 * Divpusējo atsauksmju pārvaldība
 */

class Review {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function create($data) {
        return $this->db->insert('reviews', $data);
    }

    public function findById($id) {
        $sql = "SELECT r.*,
                       reviewer.full_name as reviewer_name,
                       reviewed.full_name as reviewed_name
                FROM reviews r
                LEFT JOIN users reviewer ON r.reviewer_id = reviewer.id
                LEFT JOIN users reviewed ON r.reviewed_id = reviewed.id
                WHERE r.id = :id";

        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function getUserReviews($userId, $type = 'received') {
        if ($type === 'received') {
            $sql = "SELECT r.*,
                           reviewer.full_name as reviewer_name,
                           o.order_number
                    FROM reviews r
                    LEFT JOIN users reviewer ON r.reviewer_id = reviewer.id
                    LEFT JOIN orders o ON r.order_id = o.id
                    WHERE r.reviewed_id = :user_id AND r.is_visible = 1
                    ORDER BY r.created_at DESC";
        } else {
            $sql = "SELECT r.*,
                           reviewed.full_name as reviewed_name,
                           o.order_number
                    FROM reviews r
                    LEFT JOIN users reviewed ON r.reviewed_id = reviewed.id
                    LEFT JOIN orders o ON r.order_id = o.id
                    WHERE r.reviewer_id = :user_id
                    ORDER BY r.created_at DESC";
        }

        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }

    public function getOrderReviews($orderId) {
        $sql = "SELECT r.*,
                       reviewer.full_name as reviewer_name,
                       reviewed.full_name as reviewed_name
                FROM reviews r
                LEFT JOIN users reviewer ON r.reviewer_id = reviewer.id
                LEFT JOIN users reviewed ON r.reviewed_id = reviewed.id
                WHERE r.order_id = :order_id";

        return $this->db->fetchAll($sql, ['order_id' => $orderId]);
    }

    public function canReview($orderId, $userId, $reviewType) {
        // Pārbaudīt vai jau nav atstāta atsauksme
        $sql = "SELECT COUNT(*) FROM reviews
                WHERE order_id = :order_id AND reviewer_id = :user_id AND review_type = :type";

        return $this->db->fetchColumn($sql, [
            'order_id' => $orderId,
            'user_id' => $userId,
            'type' => $reviewType
        ]) == 0;
    }

    public function getAverageRating($userId, $reviewType) {
        $sql = "SELECT AVG(rating) FROM reviews
                WHERE reviewed_id = :user_id AND review_type = :type AND is_visible = 1";

        return round($this->db->fetchColumn($sql, [
            'user_id' => $userId,
            'type' => $reviewType
        ]) ?: 0, 1);
    }

    public function getRatingCount($userId, $reviewType) {
        $sql = "SELECT COUNT(*) FROM reviews
                WHERE reviewed_id = :user_id AND review_type = :type AND is_visible = 1";

        return $this->db->fetchColumn($sql, [
            'user_id' => $userId,
            'type' => $reviewType
        ]);
    }
}
