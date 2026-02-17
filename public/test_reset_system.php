<?php
/**
 * Script de test pour le système de réinitialisation
 * Exécuter : php public/test_reset_system.php
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/services/DataResetService.php';

echo "========================================\n";
echo "TEST DU SYSTÈME DE RÉINITIALISATION\n";
echo "========================================\n\n";

try {
    // Connexion à la base de données
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✅ Connexion à la base de données : OK\n";
    echo "   Base : " . DB_NAME . "\n\n";
    
    // Créer le service
    $service = new DataResetService($pdo);
    
    // Afficher l'état AVANT
    echo "📊 État de la base AVANT réinitialisation :\n";
    echo "----------------------------------------\n";
    $summaryBefore = $service->getDatabaseSummary();
    foreach ($summaryBefore as $table => $count) {
        echo sprintf("   %-20s : %d enregistrements\n", $table, $count);
    }
    echo "\n";
    
    // Demander confirmation
    echo "⚠️  ATTENTION : Cette opération va réinitialiser TOUTES les données !\n";
    echo "   Tapez 'oui' pour continuer (ou autre chose pour annuler) : ";
    
    $handle = fopen("php://stdin", "r");
    $confirmation = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($confirmation) !== 'oui') {
        echo "\n❌ Opération annulée par l'utilisateur.\n";
        exit(0);
    }
    
    echo "\n🔄 Réinitialisation en cours...\n\n";
    
    // Exécuter la réinitialisation
    $result = $service->resetAllData();
    
    if ($result['success']) {
        echo "✅ " . $result['message'] . "\n\n";
        
        if (isset($result['details'])) {
            echo "📋 Détails :\n";
            echo "   - Villes : " . $result['details']['villes'] . "\n";
            echo "   - Besoins : " . $result['details']['besoins'] . "\n";
            echo "   - Dons : " . $result['details']['dons'] . "\n";
            echo "   - Caisse initiale : " . number_format($result['details']['caisse_initiale'], 0, ',', ' ') . " Ar\n\n";
        }
        
        // Afficher l'état APRÈS
        echo "📊 État de la base APRÈS réinitialisation :\n";
        echo "----------------------------------------\n";
        $summaryAfter = $service->getDatabaseSummary();
        foreach ($summaryAfter as $table => $count) {
            echo sprintf("   %-20s : %d enregistrements\n", $table, $count);
        }
        echo "\n";
        
        echo "✅ Test terminé avec succès !\n";
        
    } else {
        echo "❌ Erreur : " . $result['message'] . "\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
    echo "   Fichier : " . $e->getFile() . "\n";
    echo "   Ligne : " . $e->getLine() . "\n";
    exit(1);
}

echo "\n========================================\n";
echo "✅ Tous les tests sont passés !\n";
echo "========================================\n";
