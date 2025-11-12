<?php
/**
 * Marketplace Platform - Main Entry Point
 * Vienkārša struktūra BEZ public/ direktorijas
 */

// Definēt root direktoriju
define('ROOT_DIR', __DIR__);

// Pārbaudīt vai instalēts
$configFile = ROOT_DIR . '/app/config/config.php';

if (!file_exists($configFile)) {
    // Pāradresēt uz instalāciju
    header('Location: setup.php');
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

// Ielādēt helpers
require_once ROOT_DIR . '/app/helpers/AuthHelper.php';

// Inicializēt aplikāciju
$app = App::getInstance($config);

// Palaist aplikāciju
$app->run();
