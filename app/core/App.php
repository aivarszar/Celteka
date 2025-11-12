<?php
/**
 * App - Galvenā aplikācijas klase
 * Inicializē un koordinē visus sistēmas komponentus
 */

class App {
    private static $instance = null;
    private $config;
    private $db;
    private $router;
    private $lang;

    private function __construct($config) {
        $this->config = $config;

        // Iestatīt laika zonu
        date_default_timezone_set($config['app']['timezone']);

        // Iestatīt kļūdu ziņošanu
        if ($config['app']['debug']) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }

        // Inicializēt datubāzi
        $this->db = Database::getInstance($config['database']);

        // Inicializēt sesiju
        Session::start($config['session']);

        // Inicializēt router
        $this->router = new Router();

        // Ielādēt lokalizāciju (no sesijas, ja pieejama)
        $locale = $_SESSION['locale'] ?? $config['app']['locale'];
        $this->lang = new Lang($locale);
    }

    public static function getInstance($config = null) {
        if (self::$instance === null) {
            if ($config === null) {
                die("Aplikācijas konfigurācija nav norādīta");
            }
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function getConfig($key = null) {
        if ($key === null) {
            return $this->config;
        }

        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public function getDb() {
        return $this->db;
    }

    public function getRouter() {
        return $this->router;
    }

    public function getLang() {
        return $this->lang;
    }

    public function run() {
        // Ielādēt maršrutus
        require_once ROOT_DIR . '/app/config/routes.php';

        // Dispatch
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        // Noņemt /index.php no URI
        $uri = str_replace('/index.php', '', $uri);

        // Noņemt query string
        $uri = strtok($uri, '?');

        try {
            $this->router->dispatch($uri, $method);
        } catch (Exception $e) {
            if ($this->config['app']['debug']) {
                die("Kļūda: " . $e->getMessage() . "\n\n" . $e->getTraceAsString());
            } else {
                http_response_code(500);
                echo "Sistēmas kļūda. Lūdzu mēģiniet vēlāk.";
            }
        }
    }
}

// Palīgfunkcijas
function app() {
    return App::getInstance();
}

function db() {
    return app()->getDb();
}

function config($key = null) {
    return app()->getConfig($key);
}

function lang($key, $params = []) {
    return app()->getLang()->get($key, $params);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function back() {
    redirect($_SERVER['HTTP_REFERER'] ?? '/');
}

function view($name, $data = []) {
    extract($data);
    $viewFile = __DIR__ . '/../views/' . $name . '.php';

    if (!file_exists($viewFile)) {
        die("View nav atrasts: $name");
    }

    require_once $viewFile;
}

function asset($path) {
    // Vienkārši path uz assets direktoriju
    return '/assets/' . ltrim($path, '/');
}

function url($path = '') {
    // Vienkārši relatīvs URL
    return '/' . ltrim($path, '/');
}

function csrf_field() {
    $token = Session::getCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

function csrf_token() {
    return Session::getCsrfToken();
}

function old($key, $default = '') {
    return Session::get('_old_' . $key, $default);
}

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
