<?php
/**
 * Order Model
 * Pasūtījumu pārvaldība
 */

class Order {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function create($data) {
        // Ģenerēt pasūtījuma numuru
        $data['order_number'] = $this->generateOrderNumber();

        return $this->db->insert('orders', $data);
    }

    public function findById($id) {
        $sql = "SELECT o.*,
                       b.full_name as buyer_name, b.email as buyer_email, b.phone as buyer_phone,
                       s.full_name as seller_name, s.email as seller_email, s.phone as seller_phone,
                       dm.name as delivery_method_name
                FROM orders o
                LEFT JOIN users b ON o.buyer_id = b.id
                LEFT JOIN users s ON o.seller_id = s.id
                LEFT JOIN delivery_methods dm ON o.delivery_method_id = dm.id
                WHERE o.id = :id";

        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function getOrderItems($orderId) {
        $sql = "SELECT oi.*, p.slug as product_slug
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id";

        return $this->db->fetchAll($sql, ['order_id' => $orderId]);
    }

    public function addItem($orderId, $data) {
        $data['order_id'] = $orderId;
        $data['subtotal'] = $data['price'] * $data['quantity'];

        return $this->db->insert('order_items', $data);
    }

    public function getUserOrders($userId, $type = 'buyer', $limit = 50) {
        $column = ($type === 'buyer') ? 'buyer_id' : 'seller_id';

        $sql = "SELECT o.*,
                       b.full_name as buyer_name,
                       s.full_name as seller_name
                FROM orders o
                LEFT JOIN users b ON o.buyer_id = b.id
                LEFT JOIN users s ON o.seller_id = s.id
                WHERE o.{$column} = :user_id
                ORDER BY o.created_at DESC
                LIMIT :limit";

        return $this->db->fetchAll($sql, ['user_id' => $userId, 'limit' => $limit]);
    }

    public function updateStatus($id, $status) {
        $data = ['status' => $status];

        if ($status === 'completed') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        return $this->db->update('orders', $data, 'id = :id', ['id' => $id]);
    }

    public function update($id, $data) {
        return $this->db->update('orders', $data, 'id = :id', ['id' => $id]);
    }

    private function generateOrderNumber() {
        $prefix = 'ORD';
        $timestamp = time();
        $random = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        return $prefix . '-' . $timestamp . '-' . $random;
    }

    public function getAll($limit = 100, $offset = 0) {
        $sql = "SELECT o.*,
                       b.full_name as buyer_name,
                       s.full_name as seller_name
                FROM orders o
                LEFT JOIN users b ON o.buyer_id = b.id
                LEFT JOIN users s ON o.seller_id = s.id
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset";

        return $this->db->fetchAll($sql, ['limit' => $limit, 'offset' => $offset]);
    }
}
