<?php
/**
 * OrderController - Pasūtījumu pārvaldība
 * Pircēji var skatīt savus pasūtījumus
 */

class OrderController {
    private $db;
    private $orderModel;
    private $userModel;

    public function __construct() {
        $this->db = Database::getInstance();
        require_once ROOT_DIR . '/app/models/Order.php';
        require_once ROOT_DIR . '/app/models/User.php';
        $this->orderModel = new Order();
        $this->userModel = new User();
    }

    /**
     * Parāda visus lietotāja pasūtījumus
     */
    public function index() {
        // Pārbaudīt vai lietotājs ir autorizēts
        if (!AuthHelper::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $userId = AuthHelper::getUserId();
        $user = $this->userModel->findById($userId);

        if (!$user) {
            $_SESSION['error'] = Lang::get('user_not_found');
            header('Location: /');
            exit;
        }

        // Iegūt pasūtījumus atkarībā no lietotāja lomas
        if ($user['role'] === 'seller') {
            // Pārdevēja pasūtījumi (produkti, ko pārdod)
            $orders = $this->getSellerOrders($userId);
        } else {
            // Pircēja pasūtījumi
            $orders = $this->getBuyerOrders($userId);
        }

        require_once ROOT_DIR . '/app/views/orders/index.php';
    }

    /**
     * Parāda konkrēta pasūtījuma detaļas
     */
    public function show($id) {
        // Pārbaudīt vai lietotājs ir autorizēts
        if (!AuthHelper::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $userId = AuthHelper::getUserId();
        $order = $this->orderModel->findById($id);

        if (!$order) {
            $_SESSION['error'] = Lang::get('order_not_found');
            header('Location: /orders');
            exit;
        }

        // Pārbaudīt vai lietotājs ir šī pasūtījuma dalībnieks
        if ($order['buyer_id'] != $userId && $order['seller_id'] != $userId) {
            $_SESSION['error'] = Lang::get('access_denied');
            header('Location: /orders');
            exit;
        }

        require_once ROOT_DIR . '/app/views/orders/show.php';
    }

    /**
     * Atcelt pasūtījumu
     */
    public function cancel($id) {
        // Pārbaudīt vai lietotājs ir autorizēts
        if (!AuthHelper::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /orders');
            exit;
        }

        $userId = AuthHelper::getUserId();
        $order = $this->orderModel->findById($id);

        if (!$order) {
            $_SESSION['error'] = Lang::get('order_not_found');
            header('Location: /orders');
            exit;
        }

        // Pārbaudīt vai lietotājs ir pasūtījuma pircējs
        if ($order['buyer_id'] != $userId) {
            $_SESSION['error'] = Lang::get('access_denied');
            header('Location: /orders');
            exit;
        }

        // Pārbaudīt vai pasūtījumu var atcelt
        if (!in_array($order['status'], ['pending', 'confirmed'])) {
            $_SESSION['error'] = Lang::get('cannot_cancel_order');
            header('Location: /order/' . $id);
            exit;
        }

        // Atcelt pasūtījumu
        $result = $this->orderModel->updateStatus($id, 'cancelled');

        if ($result) {
            $_SESSION['success'] = Lang::get('order_cancelled');
        } else {
            $_SESSION['error'] = Lang::get('order_cancel_failed');
        }

        header('Location: /order/' . $id);
        exit;
    }

    /**
     * Iegūt pircēja pasūtījumus
     */
    private function getBuyerOrders($userId) {
        try {
            $sql = "SELECT o.*, p.title as product_title, p.slug as product_slug,
                           u.name as seller_name
                    FROM orders o
                    JOIN products p ON o.product_id = p.id
                    JOIN users u ON o.seller_id = u.id
                    WHERE o.buyer_id = ?
                    ORDER BY o.created_at DESC";

            return $this->db->fetchAll($sql, [$userId]);
        } catch (Exception $e) {
            error_log("Error fetching buyer orders: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Iegūt pārdevēja pasūtījumus
     */
    private function getSellerOrders($userId) {
        try {
            $sql = "SELECT o.*, p.title as product_title, p.slug as product_slug,
                           u.name as buyer_name, u.email as buyer_email
                    FROM orders o
                    JOIN products p ON o.product_id = p.id
                    JOIN users u ON o.buyer_id = u.id
                    WHERE o.seller_id = ?
                    ORDER BY o.created_at DESC";

            return $this->db->fetchAll($sql, [$userId]);
        } catch (Exception $e) {
            error_log("Error fetching seller orders: " . $e->getMessage());
            return [];
        }
    }
}
