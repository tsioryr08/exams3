<?php
require_once __DIR__ . '/config.php';

// Configuration de la connexion PDO pour BNGRC
// Utilisation du port explicite pour compatibilité LAMPP + serveur
Flight::register('db', 'PDO', [
    'mysql:host=' . DB_HOST . ';port=3306;dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
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

// Tester la connexion à la base de données
try {
    $pdo = Flight::db();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());

    
} catch (Throwable $e) {
    // Ignorer les erreurs de seeding en développement
    // En production, vous devriez logger ces erreurs
}

require_once __DIR__ . '/routes.php';