<?php
/**
 * Lang - Lokalizācijas klase
 * Atbalsta vairākas valodas ar ārējiem tulkošanas failiem
 */

class Lang {
    private $locale;
    private $translations = [];

    public function __construct($locale = 'lv') {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    private function loadTranslations() {
        $langFile = __DIR__ . '/../../lang/' . $this->locale . '/messages.php';

        if (file_exists($langFile)) {
            $this->translations = require $langFile;
        } else {
            error_log("Valodas fails nav atrasts: {$langFile}");
            $this->translations = [];
        }
    }

    public function get($key, $params = []) {
        $keys = explode('.', $key);
        $value = $this->translations;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $key; // Atgriezt atslēgu, ja tulkojums nav atrasts
            }
            $value = $value[$k];
        }

        // Aizstāt parametrus
        foreach ($params as $param => $val) {
            $value = str_replace(':' . $param, $val, $value);
        }

        return $value;
    }

    public function setLocale($locale) {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    public function getLocale() {
        return $this->locale;
    }
}
