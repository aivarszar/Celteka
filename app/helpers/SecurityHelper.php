<?php
/**
 * SecurityHelper
 * Drošības funkcijas - XSS, CSRF, SQL injection aizsardzība
 */

class SecurityHelper {
    /**
     * Escape HTML (aizsardzība pret XSS)
     * Alias priekš htmlspecialchars
     */
    public static function escape($string) {
        if ($string === null) {
            return '';
        }
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Escape HTML atribūtiem
     */
    public static function escapeAttr($string) {
        if ($string === null) {
            return '';
        }
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Escape JavaScript stringiem
     */
    public static function escapeJs($string) {
        if ($string === null) {
            return '';
        }
        return json_encode($string, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    /**
     * Escape URL parametriem
     */
    public static function escapeUrl($string) {
        return urlencode($string);
    }

    /**
     * Ģenerē CSRF token
     */
    public static function generateCsrfToken() {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    /**
     * Verificē CSRF token
     */
    public static function verifyCsrfToken($token) {
        $sessionToken = Session::get('csrf_token');
        return $sessionToken && hash_equals($sessionToken, $token);
    }

    /**
     * Pārbauda, vai lietotājs ir autentificēts
     */
    public static function requireAuth() {
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Lūdzu ielogojieties');
            redirect('/login');
        }
    }

    /**
     * Pārbauda lietotāja lomu
     */
    public static function requireRole($roles) {
        self::requireAuth();

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $userRole = Session::getUserRole();
        $roleResult = db()->fetch("SELECT name FROM user_roles WHERE id = :id", ['id' => $userRole]);

        if (!$roleResult || !in_array($roleResult['name'], $roles)) {
            Session::flash('error', 'Jums nav pietiekamu tiesību');
            redirect('/');
        }
    }

    /**
     * Hash paroli
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verificē paroli
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Ģenerē drošu random string
     */
    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Sanitizē SQL LIKE meklēšanas pattern
     * (Aizsardzība pret LIKE injection)
     */
    public static function sanitizeLikePattern($pattern) {
        return addcslashes($pattern, '%_');
    }

    /**
     * Pārbauda, vai IP adrese ir whitelist'ā
     */
    public static function isIpWhitelisted($ip, array $whitelist) {
        return in_array($ip, $whitelist);
    }

    /**
     * Iegūst klienta IP adresi
     */
    public static function getClientIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '';
        }
    }

    /**
     * Rate limiting - pārbauda, vai lietotājs nav pārsniedz pieprasījumu skaitu
     */
    public static function checkRateLimit($key, $maxAttempts = 5, $timeWindow = 60) {
        $attempts = Session::get("rate_limit_{$key}", 0);
        $timestamp = Session::get("rate_limit_{$key}_time", 0);

        $now = time();

        // Reset, ja laika logs ir beidzies
        if ($now - $timestamp > $timeWindow) {
            $attempts = 0;
            $timestamp = $now;
        }

        $attempts++;

        Session::set("rate_limit_{$key}", $attempts);
        Session::set("rate_limit_{$key}_time", $timestamp);

        return $attempts <= $maxAttempts;
    }

    /**
     * Pārbauda, vai string satur potenciāli bīstamu saturu
     */
    public static function containsSqlInjection($string) {
        $patterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(--|\#|\/\*|\*\/)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $string)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Pārbauda, vai string satur XSS mēģinājumu
     */
    public static function containsXss($string) {
        $patterns = [
            '/<script\b[^>]*>(.*?)<\/script>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $string)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Logē drošības incidentu
     */
    public static function logSecurityIncident($type, $details = []) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'ip' => self::getClientIp(),
            'user_id' => Session::getUserId() ?? 'guest',
            'details' => $details,
        ];

        error_log('SECURITY INCIDENT: ' . json_encode($logData));
    }

    /**
     * Validē un sanitizē redirect URL
     */
    public static function safeRedirect($url, $default = '/') {
        // Atļaut tikai relatīvus URL vai URL uz pašu domēnu
        if (!$url || $url[0] !== '/') {
            return $default;
        }

        // Pārbaudīt, vai nav mēģinājums redirectot uz citu domēnu
        if (strpos($url, '//') !== false) {
            return $default;
        }

        return $url;
    }

    /**
     * Pārbauda, vai lietotājs ir bloķēts
     */
    public static function isUserBlocked($userId) {
        try {
            $user = db()->fetch("SELECT is_active FROM users WHERE id = :id", ['id' => $userId]);
            return $user && !$user['is_active'];
        } catch (Exception $e) {
            error_log("Error checking user blocked status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Iestatīt drošus HTTP headerus
     */
    public static function setSecurityHeaders() {
        // XSS aizsardzība
        header('X-XSS-Protection: 1; mode=block');

        // Nepieļaut MIME type sniffing
        header('X-Content-Type-Options: nosniff');

        // Frame aizsardzība (clickjacking)
        header('X-Frame-Options: SAMEORIGIN');

        // Strict Transport Security (tikai HTTPS)
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }

        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
    }
}
