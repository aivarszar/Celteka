<?php
/**
 * Marketplace Platform - Entry Point
 * Galvenais ievades punkts
 */

// Definēt root direktoriju
define('ROOT_DIR', dirname(__DIR__));

// Pārbaudīt vai instalēts
$configFile = ROOT_DIR . '/app/config/config.php';

if (!file_exists($configFile)) {
    // Pāradresēt uz instalāciju
    header('Location: /install/');
    exit;
}

// Ielādēt konfigurāciju
$config = require $configFile;

// Ielādēt core klases
require_once ROOT_DIR . '/app/core/Database.php';
require_once ROOT_DIR . '/app/core/Session.php';
require_once ROOT_DIR . '/app/core/Router.php';
require_once ROOT_DIR . '/app/core/Lang.php';
require_once ROOT_DIR . '/app/core/App.php';

// Inicializēt aplikāciju
$app = App::getInstance($config);

// Palaist aplikāciju
$app->run();
