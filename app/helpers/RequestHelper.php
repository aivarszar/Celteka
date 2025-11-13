<?php
/**
 * RequestHelper
 * HTTP pieprasījumu apstrādes palīgfunkcijas
 */

class RequestHelper {
    /**
     * Verificē CSRF token POST pieprasījumiem
     * Izmet error, ja token nav derīgs
     */
    public static function verifyCsrf() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';

            if (!Session::verifyCsrfToken($token)) {
                SecurityHelper::logSecurityIncident('csrf_token_invalid', [
                    'url' => $_SERVER['REQUEST_URI'] ?? '',
                    'method' => $_SERVER['REQUEST_METHOD'] ?? '',
                ]);

                Session::flash('error', 'Nederīgs pieprasījums. Lūdzu mēģiniet vēlreiz.');
                redirect($_SERVER['HTTP_REFERER'] ?? '/');
                exit;
            }
        }
    }

    /**
     * Pārbauda, vai ir POST pieprasījums
     */
    public static function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Pārbauda, vai ir GET pieprasījums
     */
    public static function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Pārbauda, vai ir PUT pieprasījums
     */
    public static function isPut() {
        return $_SERVER['REQUEST_METHOD'] === 'PUT' ||
               ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_method'] ?? '') === 'PUT');
    }

    /**
     * Pārbauda, vai ir DELETE pieprasījums
     */
    public static function isDelete() {
        return $_SERVER['REQUEST_METHOD'] === 'DELETE' ||
               ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_method'] ?? '') === 'DELETE');
    }

    /**
     * Pārbauda, vai ir AJAX pieprasījums
     */
    public static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Iegūst pieprasījuma metodi
     */
    public static function method() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
            return strtoupper($_POST['_method']);
        }
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Iegūst input vērtību
     */
    public static function input($key, $default = null, $sanitize = true) {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;

        if ($sanitize && is_string($value)) {
            return ValidationHelper::sanitizeString($value);
        }

        return $value;
    }

    /**
     * Iegūst visus input datus
     */
    public static function all($sanitize = true) {
        $data = array_merge($_GET, $_POST);

        if ($sanitize) {
            array_walk_recursive($data, function(&$value) {
                if (is_string($value)) {
                    $value = ValidationHelper::sanitizeString($value);
                }
            });
        }

        return $data;
    }

    /**
     * Iegūst POST datus
     */
    public static function post($key = null, $default = null, $sanitize = true) {
        if ($key === null) {
            $data = $_POST;
            if ($sanitize) {
                array_walk_recursive($data, function(&$value) {
                    if (is_string($value)) {
                        $value = ValidationHelper::sanitizeString($value);
                    }
                });
            }
            return $data;
        }

        $value = $_POST[$key] ?? $default;

        if ($sanitize && is_string($value)) {
            return ValidationHelper::sanitizeString($value);
        }

        return $value;
    }

    /**
     * Iegūst GET datus
     */
    public static function get($key = null, $default = null, $sanitize = true) {
        if ($key === null) {
            $data = $_GET;
            if ($sanitize) {
                array_walk_recursive($data, function(&$value) {
                    if (is_string($value)) {
                        $value = ValidationHelper::sanitizeString($value);
                    }
                });
            }
            return $data;
        }

        $value = $_GET[$key] ?? $default;

        if ($sanitize && is_string($value)) {
            return ValidationHelper::sanitizeString($value);
        }

        return $value;
    }

    /**
     * Pārbauda, vai input eksistē
     */
    public static function has($key) {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    /**
     * Pārbauda, vai visi atslēgas eksistē
     */
    public static function hasAll(array $keys) {
        foreach ($keys as $key) {
            if (!self::has($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Iegūst tikai norādītos laukus
     */
    public static function only(array $keys, $sanitize = true) {
        $result = [];
        $data = array_merge($_GET, $_POST);

        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $value = $data[$key];
                if ($sanitize && is_string($value)) {
                    $value = ValidationHelper::sanitizeString($value);
                }
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Iegūst visus datus, izņemot norādītos
     */
    public static function except(array $keys, $sanitize = true) {
        $data = array_merge($_GET, $_POST);
        $result = [];

        foreach ($data as $key => $value) {
            if (!in_array($key, $keys)) {
                if ($sanitize && is_string($value)) {
                    $value = ValidationHelper::sanitizeString($value);
                }
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Iegūst uploaded file
     */
    public static function file($key) {
        return $_FILES[$key] ?? null;
    }

    /**
     * Pārbauda, vai ir uploaded file
     */
    public static function hasFile($key) {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Iegūst header vērtību
     */
    public static function header($key, $default = null) {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$key] ?? $default;
    }

    /**
     * Iegūst user agent
     */
    public static function userAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Iegūst IP adresi
     */
    public static function ip() {
        return SecurityHelper::getClientIp();
    }

    /**
     * Iegūst URL
     */
    public static function url() {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    /**
     * Iegūst pilnu URL
     */
    public static function fullUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return $protocol . '://' . $host . $uri;
    }

    /**
     * Validē pieprasījumu ar noteikumiem
     */
    public static function validate(array $rules) {
        $errors = [];
        $data = array_merge($_GET, $_POST);

        foreach ($rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($rules as $rule) {
                $params = [];
                if (strpos($rule, ':') !== false) {
                    list($rule, $paramString) = explode(':', $rule, 2);
                    $params = explode(',', $paramString);
                }

                switch ($rule) {
                    case 'required':
                        if (!ValidationHelper::required($value)) {
                            $errors[$field] = "Lauks {$field} ir obligāts";
                        }
                        break;

                    case 'email':
                        if (!empty($value) && !ValidationHelper::validateEmail($value)) {
                            $errors[$field] = "Lauks {$field} nav derīga e-pasta adrese";
                        }
                        break;

                    case 'min':
                        $min = $params[0] ?? 0;
                        if (!empty($value) && !ValidationHelper::validateLength($value, $min)) {
                            $errors[$field] = "Lauks {$field} jābūt vismaz {$min} simbolu garam";
                        }
                        break;

                    case 'max':
                        $max = $params[0] ?? null;
                        if (!empty($value) && !ValidationHelper::validateLength($value, null, $max)) {
                            $errors[$field] = "Lauks {$field} nedrīkst būt garāks par {$max} simboliem";
                        }
                        break;

                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field] = "Lauks {$field} jābūt skaitlim";
                        }
                        break;

                    case 'in':
                        if (!empty($value) && !ValidationHelper::validateIn($value, $params)) {
                            $errors[$field] = "Lauks {$field} nav derīgs";
                        }
                        break;
                }
            }
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::set('old_input', self::except(['password', 'password_confirm', 'csrf_token']));
            return false;
        }

        return true;
    }
}
