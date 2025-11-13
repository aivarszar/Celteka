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
        $csrfToken = self::get('csrf_token');
        error_log("Session::getCsrfToken() - Token from session: " . ($csrfToken ?? 'NULL'));
        return $csrfToken;
    }

    public static function verifyCsrfToken($token) {
        error_log("Session::verifyCsrfToken() - START");
        error_log("Session::verifyCsrfToken() - Before getCsrfToken() call");

        $sessionToken = self::getCsrfToken();

        error_log("Session::verifyCsrfToken() - After getCsrfToken() call");
        error_log("Session::verifyCsrfToken() - Session token: " . ($sessionToken ?? 'NULL'));
        error_log("Session::verifyCsrfToken() - Provided token: " . ($token ?? 'NULL'));
        error_log("Session::verifyCsrfToken() - Session token length: " . (isset($sessionToken) ? strlen($sessionToken) : 'NULL'));
        error_log("Session::verifyCsrfToken() - Provided token length: " . (isset($token) ? strlen($token) : 'NULL'));

        if ($sessionToken === null || $token === null) {
            error_log("Session::verifyCsrfToken() - One of tokens is NULL, returning false");
            error_log("Session::verifyCsrfToken() - END (NULL token)");
            return false;
        }

        error_log("Session::verifyCsrfToken() - Both tokens present, comparing");
        error_log("Session::verifyCsrfToken() - About to call hash_equals()");

        try {
            $result = hash_equals($sessionToken, $token);

            error_log("Session::verifyCsrfToken() - hash_equals() returned");
            error_log("Session::verifyCsrfToken() - hash_equals result: " . ($result ? 'TRUE' : 'FALSE'));
            error_log("Session::verifyCsrfToken() - END (success)");
            return $result;
        } catch (Exception $e) {
            error_log("Session::verifyCsrfToken() - hash_equals EXCEPTION: " . $e->getMessage());
            error_log("Session::verifyCsrfToken() - Exception trace: " . $e->getTraceAsString());
            error_log("Session::verifyCsrfToken() - END (exception)");
            return false;
        } catch (Throwable $e) {
            error_log("Session::verifyCsrfToken() - hash_equals THROWABLE: " . $e->getMessage());
            error_log("Session::verifyCsrfToken() - Throwable trace: " . $e->getTraceAsString());
            error_log("Session::verifyCsrfToken() - END (throwable)");
            return false;
        }
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
