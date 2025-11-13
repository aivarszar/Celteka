<?php
/**
 * Session - Sesiju pārvaldības klase
 * Droša un vienkārša sesiju apstrāde
 */

class Session {
    private static $started = false;

    public static function start($config = []) {
        if (self::$started) {
            return;
        }

        if (!empty($config['name'])) {
            session_name($config['name']);
        }

        if (!empty($config['lifetime'])) {
            ini_set('session.gc_maxlifetime', $config['lifetime']);
        }

        if (!empty($config['secure'])) {
            ini_set('session.cookie_secure', '1');
        }

        if (!empty($config['httponly'])) {
            ini_set('session.cookie_httponly', '1');
        }

        session_start();
        self::$started = true;

        // CSRF token ģenerēšana
        if (!self::has('csrf_token')) {
            self::set('csrf_token', bin2hex(random_bytes(32)));
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    public static function delete($key) {
        unset($_SESSION[$key]);
    }

    public static function flash($key, $value = null) {
        if ($value === null) {
            // Iegūt flash ziņojumu
            $flashKey = '_flash_' . $key;
            $value = self::get($flashKey);
            self::delete($flashKey);
            return $value;
        } else {
            // Uzstādīt flash ziņojumu
            self::set('_flash_' . $key, $value);
        }
    }

    public static function getCsrfToken() {
        return self::get('csrf_token');
    }

    public static function verifyCsrfToken($token) {
        return hash_equals(self::getCsrfToken(), $token);
    }

    public static function regenerate() {
        session_regenerate_id(true);
    }

    public static function destroy() {
        session_destroy();
        self::$started = false;
    }

    // Lietotāja autentifikācijas palīgfunkcijas
    public static function setUser($user) {
        self::set('user_id', $user['id']);
        self::set('user_email', $user['email']);
        self::set('user_role', $user['role_id']);
        self::set('user_name', $user['full_name']);
        self::regenerate();
    }

    public static function getUser() {
        if (!self::isLoggedIn()) {
            return null;
        }

        return [
            'id' => self::get('user_id'),
            'email' => self::get('user_email'),
            'role_id' => self::get('user_role'),
            'full_name' => self::get('user_name'),
        ];
    }

    public static function isLoggedIn() {
        return self::has('user_id');
    }

    public static function logout() {
        self::delete('user_id');
        self::delete('user_email');
        self::delete('user_role');
        self::delete('user_name');
        self::regenerate();
    }

    public static function getUserId() {
        return self::get('user_id');
    }

    public static function getUserRole() {
        return self::get('user_role');
    }

    /**
     * Old input pārvaldība (formu atkārtotai aizpildei)
     */
    public static function getOldInput($key = null, $default = '') {
        $oldInput = self::get('old_input', []);

        if ($key === null) {
            // Atgriezanim visus old input datus
            self::delete('old_input');
            return $oldInput;
        }

        $value = $oldInput[$key] ?? $default;
        return $value;
    }

    public static function hasOldInput($key) {
        $oldInput = self::get('old_input', []);
        return isset($oldInput[$key]);
    }

    public static function clearOldInput() {
        self::delete('old_input');
    }
}
