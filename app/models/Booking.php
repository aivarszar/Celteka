<?php
/**
 * Booking Model
 * Pieteikšanās/rezervāciju pārvaldība
 */

require_once __DIR__ . '/../core/Database.php';

class Booking {
    private $db;
    private $table = 'bookings';

    public function __construct() {
        $this->db = db();
    }

    /**
     * Izveidot jaunu pieteikumu
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table}
                (product_id, buyer_id, seller_id, quantity, booking_date, booking_time, notes, status)
                VALUES
                (:product_id, :buyer_id, :seller_id, :quantity, :booking_date, :booking_time, :notes, :status)";

        $this->db->execute($sql, [
            'product_id' => $data['product_id'],
            'buyer_id' => $data['buyer_id'],
            'seller_id' => $data['seller_id'],
            'quantity' => $data['quantity'] ?? 1,
            'booking_date' => $data['booking_date'] ?? null,
            'booking_time' => $data['booking_time'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? 'pending'
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Atrast pieteikumu pēc ID
     */
    public function findById($id) {
        $sql = "SELECT b.*,
                       p.title as product_title,
                       p.price as product_price,
                       p.type as product_type,
                       buyer.full_name as buyer_name,
                       buyer.email as buyer_email,
                       buyer.phone as buyer_phone,
                       seller.full_name as seller_name
                FROM {$this->table} b
                LEFT JOIN products p ON b.product_id = p.id
                LEFT JOIN users buyer ON b.buyer_id = buyer.id
                LEFT JOIN users seller ON b.seller_id = seller.id
                WHERE b.id = :id";

        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Iegūt visas lietotāja pieteikšanās
     */
    public function getByUser($userId, $role = 'buyer') {
        $userField = $role === 'buyer' ? 'buyer_id' : 'seller_id';
        $otherField = $role === 'buyer' ? 'seller' : 'buyer';

        $sql = "SELECT b.*,
                       p.title as product_title,
                       p.price as product_price,
                       p.type as product_type,
                       {$otherField}.full_name as {$otherField}_name,
                       {$otherField}.email as {$otherField}_email,
                       {$otherField}.phone as {$otherField}_phone
                FROM {$this->table} b
                LEFT JOIN products p ON b.product_id = p.id
                LEFT JOIN users {$otherField} ON b.{$otherField}_id = {$otherField}.id
                WHERE b.{$userField} = :user_id
                ORDER BY b.created_at DESC";

        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }

    /**
     * Iegūt produkta pieteikšanās
     */
    public function getByProduct($productId, $status = null) {
        $sql = "SELECT b.*,
                       buyer.full_name as buyer_name,
                       buyer.email as buyer_email,
                       buyer.phone as buyer_phone
                FROM {$this->table} b
                LEFT JOIN users buyer ON b.buyer_id = buyer.id
                WHERE b.product_id = :product_id";

        $params = ['product_id' => $productId];

        if ($status) {
            $sql .= " AND b.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY b.created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Pārbaudīt vai lietotājs jau ir pieteicies
     */
    public function hasUserBooked($productId, $buyerId) {
        $sql = "SELECT COUNT(*) FROM {$this->table}
                WHERE product_id = :product_id
                AND buyer_id = :buyer_id
                AND status IN ('pending', 'confirmed')";

        $count = $this->db->fetchColumn($sql, [
            'product_id' => $productId,
            'buyer_id' => $buyerId
        ]);

        return $count > 0;
    }

    /**
     * Saskaitīt apstiprinātas pieteikšanās (tikai confirmed)
     * Tikai apstiprinātas pieteikšanās ieskaita kapacitātē
     */
    public function countConfirmedBookings($productId) {
        $sql = "SELECT COALESCE(SUM(quantity), 0) FROM {$this->table}
                WHERE product_id = :product_id
                AND status = 'confirmed'";

        return (int) $this->db->fetchColumn($sql, ['product_id' => $productId]);
    }

    /**
     * Atjaunināt pieteikumu
     */
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];

        $allowedFields = ['quantity', 'status', 'booking_date', 'booking_time', 'notes'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";

        return $this->db->execute($sql, $params);
    }

    /**
     * Dzēst pieteikumu
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }

    /**
     * Atcelt pieteikumu
     */
    public function cancel($id) {
        return $this->update($id, ['status' => 'cancelled']);
    }

    /**
     * Apstiprināt pieteikumu
     */
    public function confirm($id) {
        return $this->update($id, ['status' => 'confirmed']);
    }

    /**
     * Pabeigt pieteikumu
     */
    public function complete($id) {
        return $this->update($id, ['status' => 'completed']);
    }

    /**
     * Iegūt pieteikumu statistiku produktam
     */
    public function getProductStats($productId) {
        $sql = "SELECT
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_count,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                    SUM(CASE WHEN status = 'pending' THEN quantity ELSE 0 END) as pending_quantity,
                    SUM(CASE WHEN status = 'confirmed' THEN quantity ELSE 0 END) as confirmed_quantity,
                    SUM(CASE WHEN status = 'confirmed' THEN quantity ELSE 0 END) as active_quantity
                FROM {$this->table}
                WHERE product_id = :product_id";

        return $this->db->fetch($sql, ['product_id' => $productId]);
    }
}
