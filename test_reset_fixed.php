<?php
// Test du système de reset corrigé

require_once __DIR__ . '/app/services/DataResetService.php';

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bngrc_suivi;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TEST DU SYSTÈME DE RESET ===\n\n";
    
    $service = new DataResetService($pdo);
    $result = $service->resetAllData();
    
    echo "Résultat: " . ($result['success'] ? '✅ SUCCÈS' : '❌ ÉCHEC') . "\n";
    echo "Message: " . $result['message'] . "\n";
    
    if (!$result['success']) {
        echo "\n⚠️  Le reset a échoué. Vérification de l'état actuel...\n";
    }
    
    if (isset($result['details'])) {
        echo "\nDétails:\n";
        foreach ($result['details'] as $key => $value) {
            echo "  - $key: $value\n";
        }
    }
    
    echo "\n=== VÉRIFICATION DES DONNÉES ===\n\n";
    
    $tables = ['villes', 'besoins', 'dons', 'caisse_historique'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "$table: $count lignes\n";
    }
    
    echo "\n=== VILLES ===\n";
    $stmt = $pdo->query("SELECT id, nom, region FROM villes ORDER BY id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['id']}: {$row['nom']} ({$row['region']})\n";
    }
    
    echo "\n=== BESOINS (premiers 5) ===\n";
    $stmt = $pdo->query("SELECT id, ville_id, type, libelle, quantite FROM besoins ORDER BY id LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['id']}: Ville {$row['ville_id']} - {$row['type']} - {$row['libelle']} (Qté: {$row['quantite']})\n";
    }
    
    echo "\n=== DONS ===\n";
    $stmt = $pdo->query("SELECT id, type, libelle, quantite FROM dons ORDER BY id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['id']}: {$row['type']} - {$row['libelle']} (Qté: {$row['quantite']})\n";
    }
    
    echo "\n=== CAISSE ===\n";
    $stmt = $pdo->query("SELECT * FROM caisse_historique ORDER BY id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['id']}: {$row['type']} - {$row['montant']} Ar - Solde: {$row['solde_apres']} Ar - {$row['description']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
