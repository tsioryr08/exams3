<?php
require_once __DIR__ . '/config.php';

Flight::register('db', 'PDO', [
    'mysql:host=127.0.0.1;dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
    DB_USER, 
    DB_PASS, 
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
]);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Flight::set('flight.views.path', __DIR__ . '/views');

require_once __DIR__ . '/routes.php';