<?php
/**
 * OrderController - Pasūtījumu pārvaldība
 * Pircēji var skatīt savus pasūtījumus
 */

require_once __DIR__ . '/../helpers/RequestHelper.php';

class OrderController {
    private $db;
    private $orderModel;
    private $userModel;

    public function __construct() {
        $this->db = db();
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
            Session::flash('error', lang('messages.user_not_found'));
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

        view('orders/index', [
            'user' => $user,
            'orders' => $orders,
            'config' => config()
        ]);
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
            Session::flash('error', lang('order.order_not_found'));
            header('Location: /orders');
            exit;
        }

        // Pārbaudīt vai lietotājs ir šī pasūtījuma dalībnieks
        if ($order['buyer_id'] != $userId && $order['seller_id'] != $userId) {
            Session::flash('error', lang('messages.access_denied'));
            header('Location: /orders');
            exit;
        }

        view('orders/show', [
            'order' => $order,
            'config' => config()
        ]);
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

        // CSRF verifikācija
        RequestHelper::verifyCsrf();

        $userId = AuthHelper::getUserId();
        $order = $this->orderModel->findById($id);

        if (!$order) {
            Session::flash('error', lang('order.order_not_found'));
            header('Location: /orders');
            exit;
        }

        // Pārbaudīt vai lietotājs ir pasūtījuma pircējs
        if ($order['buyer_id'] != $userId) {
            Session::flash('error', lang('messages.access_denied'));
            header('Location: /orders');
            exit;
        }

        // Pārbaudīt vai pasūtījumu var atcelt
        if (!in_array($order['status'], ['pending', 'confirmed'])) {
            Session::flash('error', lang('order.cannot_cancel_order'));
            header('Location: /order/' . $id);
            exit;
        }

        // Atcelt pasūtījumu
        $result = $this->orderModel->updateStatus($id, 'cancelled');

        if ($result) {
            Session::flash('success', lang('order.order_cancelled'));
        } else {
            Session::flash('error', lang('order.order_cancel_failed'));
        }

        header('Location: /order/' . $id);
        exit;
    }

    /**
     * Iegūt pircēja pasūtījumus
     */
    private function getBuyerOrders($userId) {
        try {
            $sql = "SELECT o.*, u.full_name as seller_name,
                           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count,
                           (SELECT product_title FROM order_items WHERE order_id = o.id LIMIT 1) as product_title
                    FROM orders o
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
            $sql = "SELECT o.*, u.full_name as buyer_name, u.email as buyer_email,
                           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count,
                           (SELECT product_title FROM order_items WHERE order_id = o.id LIMIT 1) as product_title
                    FROM orders o
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
