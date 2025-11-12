<?php
/**
 * LanguageController
 * Valodas maiņas kontrolieris
 */

class LanguageController {
    public function change($locale) {
        // Atļautās valodas
        $allowedLanguages = ['lv', 'en', 'ru', 'lt', 'ee'];

        if (in_array($locale, $allowedLanguages)) {
            $_SESSION['locale'] = $locale;
        }

        // Atgriezties uz iepriekšējo lapu
        back();
    }
}
