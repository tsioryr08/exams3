<?php
// Diagnostic rapide de la base de données

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bngrc_suivi;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ÉTAT DE LA BASE DE DONNÉES ===\n\n";
    
    $tables = ['villes', 'besoins', 'dons', 'achats_besoins', 'caisse_historique'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "$table: $count lignes\n";
        
        if ($count > 0 && $count < 10) {
            $stmt = $pdo->query("SELECT * FROM $table LIMIT 5");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                echo "  - " . json_encode($row) . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}
