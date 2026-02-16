<?php
require_once __DIR__ . '/config.php';

Flight::register('db', 'PDO', [
    'mysql:host=127.0.0.1;dbname=bngrc;charset=utf8',
    'root', 
    '', 
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
]);

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir le chemin des vues
Flight::set('flight.views.path', __DIR__ . '/views');

// Charger les routes
require_once __DIR__ . '/routes.php';
?>