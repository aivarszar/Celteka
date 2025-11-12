<?php
/**
 * Lang - Daudzvalodu lokalizācijas klase
 * Atbalsta fallback uz EN ja nav tulkojuma
 */

class Lang {
    private static $instance = null;
    private $locale;
    private $translations = [];
    private $fallbackTranslations = [];

    public function __construct($locale = 'lv') {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    // Static wrapper lai atbalstītu Lang::get() izsaukumus view failos
    public static function __callStatic($method, $args) {
        if (function_exists('app')) {
            $instance = app()->getLang();
            if ($instance && method_exists($instance, $method)) {
                return call_user_func_array([$instance, $method], $args);
            }
        }

        // Fallback - ja nav app() instance
        if (self::$instance === null) {
            self::$instance = new self();
        }

        if (method_exists(self::$instance, $method)) {
            return call_user_func_array([self::$instance, $method], $args);
        }

        throw new BadMethodCallException("Method {$method} does not exist");
    }

    private function loadTranslations() {
        // Ielādēt izvēlēto valodu
        $langFile = ROOT_DIR . '/lang/' . $this->locale . '.php';
        if (file_exists($langFile)) {
            $this->translations = require $langFile;
        }

        // Ielādēt fallback valodu (EN) ja nav izvēlētā valoda
        if ($this->locale !== 'en') {
            $fallbackFile = ROOT_DIR . '/lang/en.php';
            if (file_exists($fallbackFile)) {
                $this->fallbackTranslations = require $fallbackFile;
            }
        }
    }

    public function get($key, $params = []) {
        // Mēģināt iegūt no izvēlētās valodas
        $value = $this->getFromArray($this->translations, $key);

        // Ja nav, mēģināt no fallback (EN)
        if ($value === null && !empty($this->fallbackTranslations)) {
            $value = $this->getFromArray($this->fallbackTranslations, $key);
        }

        // Ja joprojām nav, atgriezt pašu atslēgu
        if ($value === null) {
            return $key;
        }

        // Aizstāt parametrus
        foreach ($params as $param => $val) {
            $value = str_replace(':' . $param, $val, $value);
        }

        return $value;
    }

    private function getFromArray($array, $key) {
        $keys = explode('.', $key);
        $value = $array;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public function setLocale($locale) {
        $this->locale = $locale;
        $this->loadTranslations();

        // Saglabāt sesijā
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['locale'] = $locale;
        }
    }

    public function getLocale() {
        return $this->locale;
    }

    public function getAvailableLanguages() {
        $languages = [];
        $langDir = ROOT_DIR . '/lang';

        if (is_dir($langDir)) {
            $files = scandir($langDir);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $code = pathinfo($file, PATHINFO_FILENAME);
                    $languages[$code] = $this->getLanguageName($code);
                }
            }
        }

        return $languages;
    }

    public function getLanguageName($code) {
        $names = [
            'lv' => 'Latviešu',
            'en' => 'English',
            'ru' => 'Русский',
            'lt' => 'Lietuvių',
            'ee' => 'Eesti',
        ];

        return $names[$code] ?? strtoupper($code);
    }
}
