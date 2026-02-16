<?php
require_once __DIR__ . '/config.php';

// Configuration de la connexion PDO pour BNGRC
// Utilisation du port explicite pour compatibilité LAMPP + serveur
Flight::register('db', 'PDO', [
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
    DB_USER, 
    DB_PASS, 
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
]);

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurer le chemin des vues
Flight::set('flight.views.path', __DIR__ . '/views');

// Charger les routes
require_once __DIR__ . '/routes.php';
