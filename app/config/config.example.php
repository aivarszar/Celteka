<?php
/**
 * Marketplace Platform - Konfigurācijas Fails (Paraugs)
 * Nokopējiet uz config.php un pielāgojiet iestatījumus
 */

return [
    // Datubāzes iestatījumi
    'database' => [
        'host' => '127.0.0.1',  // Izmanto IP, nevis localhost, lai izvairītos no socket problēmām
        'dbname' => 'celteka_db',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],

    // Aplikācijas iestatījumi
    'app' => [
        'name' => 'Vietējais Tirgus',
        'url' => 'http://localhost',
        'timezone' => 'Europe/Riga',
        'locale' => 'lv',
        'debug' => false,
    ],

    // Sesijas iestatījumi
    'session' => [
        'name' => 'marketplace_session',
        'lifetime' => 86400,
        'secure' => false,
        'httponly' => true,
    ],

    // Augšupielāžu iestatījumi
    'upload' => [
        'max_size' => 5242880,
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'path' => '/assets/images/uploads/',
    ],
];
