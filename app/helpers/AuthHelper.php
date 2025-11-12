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

    public static function isAdmin() {
        if (!Session::isLoggedIn()) {
            return false;
        }

        $userRole = Session::getUserRole();
        $roleResult = db()->fetch("SELECT name FROM user_roles WHERE id = :id", ['id' => $userRole]);

        return $roleResult && $roleResult['name'] === 'admin';
    }

    public static function isSeller() {
        if (!Session::isLoggedIn()) {
            return false;
        }

        $userRole = Session::getUserRole();
        $roleResult = db()->fetch("SELECT name FROM user_roles WHERE id = :id", ['id' => $userRole]);

        return $roleResult && in_array($roleResult['name'], ['seller', 'admin']);
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
