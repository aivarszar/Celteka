<?php
/**
 * ValidationHelper
 * Universālas input validācijas funkcijas
 */

class ValidationHelper {
    /**
     * Validē e-pasta adresi
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validē tālruņa numuru
     */
    public static function validatePhone($phone) {
        // Atļaut tikai ciparus, +, -, (), atstarpes
        return preg_match('/^[\d\s\+\-\(\)]+$/', $phone);
    }

    /**
     * Validē paroli
     */
    public static function validatePassword($password, $minLength = 6) {
        return strlen($password) >= $minLength;
    }

    /**
     * Validē, vai vērtība nav tukša
     */
    public static function required($value) {
        if (is_string($value)) {
            return trim($value) !== '';
        }
        return !empty($value);
    }

    /**
     * Validē garumu
     */
    public static function validateLength($value, $min = null, $max = null) {
        $length = strlen($value);

        if ($min !== null && $length < $min) {
            return false;
        }

        if ($max !== null && $length > $max) {
            return false;
        }

        return true;
    }

    /**
     * Validē skaitli
     */
    public static function validateNumber($value, $min = null, $max = null) {
        if (!is_numeric($value)) {
            return false;
        }

        $number = floatval($value);

        if ($min !== null && $number < $min) {
            return false;
        }

        if ($max !== null && $number > $max) {
            return false;
        }

        return true;
    }

    /**
     * Validē, vai vērtība ir vienā no atļautajām
     */
    public static function validateIn($value, array $allowed) {
        return in_array($value, $allowed, true);
    }

    /**
     * Validē URL
     */
    public static function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validē datumu
     */
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Validē pasta indeksu
     */
    public static function validatePostalCode($code) {
        // LV-xxxx vai tikai xxxx formāts
        return preg_match('/^(LV-)?[0-9]{4}$/', $code);
    }

    /**
     * Sanitizē string (noņem HTML tagus un trim)
     */
    public static function sanitizeString($string) {
        return trim(strip_tags($string));
    }

    /**
     * Sanitizē e-pastu
     */
    public static function sanitizeEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitizē URL
     */
    public static function sanitizeUrl($url) {
        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }

    /**
     * Validē CSRF token
     */
    public static function validateCsrfToken($token = null) {
        if ($token === null) {
            $token = $_POST['csrf_token'] ?? '';
        }

        return Session::verifyCsrfToken($token);
    }

    /**
     * Validē, ka ir POST pieprasījums
     */
    public static function isPostRequest() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Validē, ka ir GET pieprasījums
     */
    public static function isGetRequest() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Iegūst un sanitizē POST parametru
     */
    public static function getPostParam($key, $default = null, $sanitize = true) {
        if (!isset($_POST[$key])) {
            return $default;
        }

        $value = $_POST[$key];

        if ($sanitize && is_string($value)) {
            return self::sanitizeString($value);
        }

        return $value;
    }

    /**
     * Iegūst un sanitizē GET parametru
     */
    public static function getGetParam($key, $default = null, $sanitize = true) {
        if (!isset($_GET[$key])) {
            return $default;
        }

        $value = $_GET[$key];

        if ($sanitize && is_string($value)) {
            return self::sanitizeString($value);
        }

        return $value;
    }

    /**
     * Validē failu augšupielādi
     */
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = null) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if (!empty($allowedTypes)) {
            $fileType = mime_content_type($file['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                return false;
            }
        }

        if ($maxSize !== null && $file['size'] > $maxSize) {
            return false;
        }

        return true;
    }

    /**
     * Validē attēlu
     */
    public static function validateImage($file, $maxSize = 5242880) { // 5MB
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        return self::validateFileUpload($file, $allowedTypes, $maxSize);
    }
}
