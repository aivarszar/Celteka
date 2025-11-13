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

        $userId = Session::getUserId();

        // Iegūt lietotāja lomas no user_role_assignments
        $userRoles = self::getUserRoles();

        // Pārbaudīt, vai lietotājam ir kāda no nepieciešamajām lomām
        $hasRequiredRole = false;
        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                $hasRequiredRole = true;
                break;
            }
        }

        if (!$hasRequiredRole) {
            // Izveidot draudzīgu ziņojumu atkarībā no vajadzīgajām lomām
            if (in_array('seller', $roles)) {
                Session::flash('error', 'Lai piekļūtu pārdevēja funkcijām, lūdzu <a href="/profile/edit">pievienojiet pārdevēja lomu savam profilam</a>.');
            } elseif (in_array('admin', $roles)) {
                Session::flash('error', 'Piekļuve liegta. Nepieciešamas administratora tiesības.');
            } else {
                $roleNames = array_map(function($r) {
                    $names = [
                        'buyer' => 'pircēja',
                        'seller' => 'pārdevēja',
                        'admin' => 'administratora'
                    ];
                    return $names[$r] ?? $r;
                }, $roles);
                Session::flash('error', 'Lai piekļūtu šai lapai, nepieciešama ' . implode(' vai ', $roleNames) . ' loma. <a href="/profile/edit">Pievienojiet lomu savam profilam</a>.');
            }
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
