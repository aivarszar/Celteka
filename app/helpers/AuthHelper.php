<?php
/**
 * AuthHelper
 * Autentifikācijas un autorizācijas palīgfunkcijas
 */

class AuthHelper {
    public static function requireLogin() {
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Lūdzu ielogojieties, lai piekļūtu šai lapai');
            redirect('/login');
        }
    }

    public static function requireRole($roles) {
        self::requireLogin();

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $userRole = Session::getUserRole();

        // Iegūt lomas nosaukumu
        $roleResult = db()->fetch("SELECT name FROM user_roles WHERE id = :id", ['id' => $userRole]);

        if (!$roleResult || !in_array($roleResult['name'], $roles)) {
            Session::flash('error', 'Jums nav pietiekamu tiesību šīs lapas skatīšanai');
            redirect('/');
        }
    }

    /**
     * Pārbauda vai lietotājam ir konkrēta loma
     */
    public static function hasRole($role) {
        if (!Session::isLoggedIn()) {
            return false;
        }

        $userId = Session::getUserId();

        try {
            $count = db()->fetchColumn(
                "SELECT COUNT(*) FROM user_role_assignments WHERE user_id = ? AND role = ?",
                [$userId, $role]
            );
            return $count > 0;
        } catch (Exception $e) {
            error_log("AuthHelper::hasRole() - Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Iegūst visas lietotāja lomas
     */
    public static function getUserRoles() {
        if (!Session::isLoggedIn()) {
            return [];
        }

        $userId = Session::getUserId();

        try {
            $roles = db()->fetchAll(
                "SELECT role FROM user_role_assignments WHERE user_id = ? ORDER BY role",
                [$userId]
            );
            return array_column($roles, 'role');
        } catch (Exception $e) {
            error_log("AuthHelper::getUserRoles() - Error: " . $e->getMessage());
            return [];
        }
    }

    public static function isAdmin() {
        return self::hasRole('admin');
    }

    public static function isSeller() {
        return self::hasRole('seller') || self::hasRole('admin');
    }

    public static function check() {
        return Session::isLoggedIn();
    }

    public static function isLoggedIn() {
        return Session::isLoggedIn();
    }

    public static function getUserId() {
        return Session::getUserId();
    }

    public static function user() {
        return Session::getUser();
    }
}
