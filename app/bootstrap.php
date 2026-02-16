<?php
require_once __DIR__ . '/config.php';

Flight::register('db', 'PDO', [
    'mysql:host=127.0.0.1;dbname=takalo;charset=utf8',
    'root', 
    '', 
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

try {
    $pdo = Flight::db();
    
    // Vérifier si un admin existe, sinon en créer un
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE statut = 'admin'");
    $stmt->execute();
    
    if ((int)$stmt->fetchColumn() === 0) {
        // Créer un admin par défaut (mot de passe en clair : "admin")
        $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, password_hash, telephone, statut) VALUES (?,?,?,?,?,?)")
            ->execute(['Admin', 'Système', 'admin@example.com', 'admin', '0123456789', 'admin']);
    }
    
    // Vérifier si des catégories existent, sinon les créer
    $cnt = $pdo->query("SELECT COUNT(*) FROM categorie")->fetchColumn();
    if ((int)$cnt === 0) {
        $categories = [
            ['Vêtements', 'Habits, chaussures, accessoires'],
            ['Livres', 'Romans, BD, magazines'],
            ['DVD/Blu-ray', 'Films et séries'],
            ['Jeux vidéo', 'Consoles et jeux'],
            ['Électronique', 'Appareils électroniques'],
            ['Sport', 'Équipements sportifs'],
            ['Décoration', 'Objets de décoration']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO categorie (nom, description) VALUES (?, ?)");
        foreach ($categories as $cat) {
            $stmt->execute($cat);
        }
    }
    
} catch (Throwable $e) {
    // Ignorer les erreurs de seeding en développement
    // En production, vous devriez logger ces erreurs
}

require_once __DIR__ . '/routes.php';