<?php
/**
 * User Model
 * Lietotāju pārvaldība
 */

class User {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function create($data) {
        // Hash paroli
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);

        return $this->db->insert('users', $data);
    }

    public function findById($id) {
        $sql = "SELECT u.*, r.name as role, r.name as role_name, r.display_name as role_display_name
                FROM users u
                LEFT JOIN user_roles r ON u.role_id = r.id
                WHERE u.id = :id";

        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function findByEmail($email) {
        $sql = "SELECT u.*, r.name as role, r.name as role_name
                FROM users u
                LEFT JOIN user_roles r ON u.role_id = r.id
                WHERE u.email = :email";

        return $this->db->fetch($sql, ['email' => $email]);
    }

    public function verifyPassword($user, $password) {
        return password_verify($password, $user['password_hash']);
    }

    public function update($id, $data) {
        return $this->db->update('users', $data, 'id = :id', ['id' => $id]);
    }

    public function emailExists($email) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        return $this->db->fetchColumn($sql, ['email' => $email]) > 0;
    }

    public function getRoleId($roleName) {
        $sql = "SELECT id FROM user_roles WHERE name = :name";
        return $this->db->fetchColumn($sql, ['name' => $roleName]);
    }

    // Lietotāja meta datu pārvaldība (dinamiskais profils)
    public function getMeta($userId, $key = null) {
        if ($key === null) {
            // Iegūt visus meta datus
            $sql = "SELECT meta_key, meta_value FROM user_meta WHERE user_id = :user_id";
            $results = $this->db->fetchAll($sql, ['user_id' => $userId]);

            $meta = [];
            foreach ($results as $row) {
                $meta[$row['meta_key']] = $row['meta_value'];
            }
            return $meta;
        } else {
            // Iegūt konkrētu meta vērtību
            $sql = "SELECT meta_value FROM user_meta WHERE user_id = :user_id AND meta_key = :key";
            return $this->db->fetchColumn($sql, ['user_id' => $userId, 'key' => $key]);
        }
    }

    public function setMeta($userId, $key, $value) {
        $existing = $this->db->fetch(
            "SELECT id FROM user_meta WHERE user_id = :user_id AND meta_key = :key",
            ['user_id' => $userId, 'key' => $key]
        );

        if ($existing) {
            return $this->db->update(
                'user_meta',
                ['meta_value' => $value],
                'user_id = :user_id AND meta_key = :key',
                ['user_id' => $userId, 'key' => $key]
            );
        } else {
            return $this->db->insert('user_meta', [
                'user_id' => $userId,
                'meta_key' => $key,
                'meta_value' => $value
            ]);
        }
    }

    public function deleteMeta($userId, $key) {
        return $this->db->delete('user_meta', 'user_id = :user_id AND meta_key = :key', [
            'user_id' => $userId,
            'key' => $key
        ]);
    }

    // Lietotāja statistika
    public function getSellerStats($userId) {
        $stats = [];

        // Produktu skaits
        $stats['products_count'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM products WHERE seller_id = :id",
            ['id' => $userId]
        );

        // Pasūtījumu skaits
        $stats['orders_count'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM orders WHERE seller_id = :id",
            ['id' => $userId]
        );

        // Kopējie ieņēmumi
        $stats['total_revenue'] = $this->db->fetchColumn(
            "SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE seller_id = :id AND status = 'completed'",
            ['id' => $userId]
        );

        // Vidējais vērtējums
        $stats['avg_rating'] = $this->db->fetchColumn(
            "SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE reviewed_id = :id AND review_type = 'buyer_to_seller'",
            ['id' => $userId]
        );

        return $stats;
    }

    public function getBuyerStats($userId) {
        $stats = [];

        // Pasūtījumu skaits
        $stats['orders_count'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM orders WHERE buyer_id = :id",
            ['id' => $userId]
        );

        // Kopējā iztērētā summa
        $stats['total_spent'] = $this->db->fetchColumn(
            "SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE buyer_id = :id AND status = 'completed'",
            ['id' => $userId]
        );

        // Vidējais vērtējums
        $stats['avg_rating'] = $this->db->fetchColumn(
            "SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE reviewed_id = :id AND review_type = 'seller_to_buyer'",
            ['id' => $userId]
        );

        return $stats;
    }

    public function getAll($limit = 100, $offset = 0) {
        $sql = "SELECT u.*, r.display_name as role_display_name
                FROM users u
                LEFT JOIN user_roles r ON u.role_id = r.id
                ORDER BY u.created_at DESC
                LIMIT :limit OFFSET :offset";

        return $this->db->fetchAll($sql, ['limit' => $limit, 'offset' => $offset]);
    }
}
