<?php

/**
 * Service de réinitialisation complète des données
 * Sécurisé contre les injections SQL avec PDO
 */
class DataResetService {
    private $pdo;
    
    // Données initiales des villes
    private const INITIAL_VILLES = [
        ['nom' => 'Toamasina', 'region' => 'Atsinanana', 'date_creation' => '2026-02-15 08:00:00'],
        ['nom' => 'Mananjary', 'region' => 'Vatovavy', 'date_creation' => '2026-02-15 08:00:00'],
        ['nom' => 'Farafangana', 'region' => 'Atsimo-Atsinanana', 'date_creation' => '2026-02-15 08:00:00'],
        ['nom' => 'Nosy Be', 'region' => 'Diana', 'date_creation' => '2026-02-15 08:00:00'],
        ['nom' => 'Morondava', 'region' => 'Menabe', 'date_creation' => '2026-02-15 08:00:00']
    ];
    
    // Données initiales des besoins (ordre chronologique strict 1-26)
    private const INITIAL_BESOINS = [
        // Ordre 1: Toamasina - Bâche
        ['ville_id' => 1, 'type' => 'materiel', 'libelle' => 'Bâche', 'prix_unitaire' => 15000, 'quantite' => 200, 'date_saisie' => '2026-02-15 00:00:01'],
        // Ordre 2: Nosy Be - Tôle
        ['ville_id' => 4, 'type' => 'materiel', 'libelle' => 'Tôle', 'prix_unitaire' => 25000, 'quantite' => 40, 'date_saisie' => '2026-02-15 01:10:00'],
        // Ordre 3: Mananjary - Argent
        ['ville_id' => 2, 'type' => 'argent', 'libelle' => 'Argent', 'prix_unitaire' => 1, 'quantite' => 6000000, 'date_saisie' => '2026-02-15 02:20:00'],
        // Ordre 4: Toamasina - Eau
        ['ville_id' => 1, 'type' => 'nature', 'libelle' => 'Eau (L)', 'prix_unitaire' => 1000, 'quantite' => 1500, 'date_saisie' => '2026-02-15 03:30:00'],
        // Ordre 5: Nosy Be - Riz
        ['ville_id' => 4, 'type' => 'nature', 'libelle' => 'Riz (kg)', 'prix_unitaire' => 3000, 'quantite' => 300, 'date_saisie' => '2026-02-15 04:40:00'],
        // Ordre 6: Mananjary - Tôle
        ['ville_id' => 2, 'type' => 'materiel', 'libelle' => 'Tôle', 'prix_unitaire' => 25000, 'quantite' => 80, 'date_saisie' => '2026-02-15 05:50:00'],
        // Ordre 7: Nosy Be - Argent
        ['ville_id' => 4, 'type' => 'argent', 'libelle' => 'Argent', 'prix_unitaire' => 1, 'quantite' => 4000000, 'date_saisie' => '2026-02-15 07:00:00'],
        // Ordre 8: Farafangana - Bâche
        ['ville_id' => 3, 'type' => 'materiel', 'libelle' => 'Bâche', 'prix_unitaire' => 15000, 'quantite' => 150, 'date_saisie' => '2026-02-15 08:10:00'],
        // Ordre 9: Mananjary - Riz
        ['ville_id' => 2, 'type' => 'nature', 'libelle' => 'Riz (kg)', 'prix_unitaire' => 3000, 'quantite' => 500, 'date_saisie' => '2026-02-15 09:20:00'],
        // Ordre 10: Farafangana - Argent
        ['ville_id' => 3, 'type' => 'argent', 'libelle' => 'Argent', 'prix_unitaire' => 1, 'quantite' => 8000000, 'date_saisie' => '2026-02-15 10:30:00'],
        // Ordre 11: Morondava - Riz
        ['ville_id' => 5, 'type' => 'nature', 'libelle' => 'Riz (kg)', 'prix_unitaire' => 3000, 'quantite' => 700, 'date_saisie' => '2026-02-15 11:40:00'],
        // Ordre 12: Toamasina - Argent
        ['ville_id' => 1, 'type' => 'argent', 'libelle' => 'Argent', 'prix_unitaire' => 1, 'quantite' => 12000000, 'date_saisie' => '2026-02-15 12:50:00'],
        // Ordre 13: Morondava - Argent
        ['ville_id' => 5, 'type' => 'argent', 'libelle' => 'Argent', 'prix_unitaire' => 1, 'quantite' => 10000000, 'date_saisie' => '2026-02-15 14:00:00'],
        // Ordre 14: Farafangana - Eau
        ['ville_id' => 3, 'type' => 'nature', 'libelle' => 'Eau (L)', 'prix_unitaire' => 1000, 'quantite' => 1000, 'date_saisie' => '2026-02-15 15:10:00'],
        // Ordre 15: Morondava - Bâche
        ['ville_id' => 5, 'type' => 'materiel', 'libelle' => 'Bâche', 'prix_unitaire' => 15000, 'quantite' => 180, 'date_saisie' => '2026-02-15 16:20:00'],
        // Ordre 16: Toamasina - Groupe électrogène
        ['ville_id' => 1, 'type' => 'materiel', 'libelle' => 'Groupe électrogène', 'prix_unitaire' => 2250000, 'quantite' => 3, 'date_saisie' => '2026-02-15 17:30:00'],
        // Ordre 17: Toamasina - Riz
        ['ville_id' => 1, 'type' => 'nature', 'libelle' => 'Riz (kg)', 'prix_unitaire' => 3000, 'quantite' => 800, 'date_saisie' => '2026-02-15 18:40:00'],
        // Ordre 18: Nosy Be - Haricots
        ['ville_id' => 4, 'type' => 'nature', 'libelle' => 'Haricots', 'prix_unitaire' => 4000, 'quantite' => 200, 'date_saisie' => '2026-02-15 19:50:00'],
        // Ordre 19: Mananjary - Clous
        ['ville_id' => 2, 'type' => 'materiel', 'libelle' => 'Clous (kg)', 'prix_unitaire' => 8000, 'quantite' => 60, 'date_saisie' => '2026-02-15 21:00:00'],
        // Ordre 20: Morondava - Eau
        ['ville_id' => 5, 'type' => 'nature', 'libelle' => 'Eau (L)', 'prix_unitaire' => 1000, 'quantite' => 1200, 'date_saisie' => '2026-02-15 22:10:00'],
        // Ordre 21: Farafangana - Riz
        ['ville_id' => 3, 'type' => 'nature', 'libelle' => 'Riz (kg)', 'prix_unitaire' => 3000, 'quantite' => 600, 'date_saisie' => '2026-02-15 23:20:00'],
        // Ordre 22: Morondava - Bois
        ['ville_id' => 5, 'type' => 'materiel', 'libelle' => 'Bois', 'prix_unitaire' => 10000, 'quantite' => 150, 'date_saisie' => '2026-02-16 00:30:00'],
        // Ordre 23: Toamasina - Tôle
        ['ville_id' => 1, 'type' => 'materiel', 'libelle' => 'Tôle', 'prix_unitaire' => 25000, 'quantite' => 120, 'date_saisie' => '2026-02-16 01:40:00'],
        // Ordre 24: Nosy Be - Clous
        ['ville_id' => 4, 'type' => 'materiel', 'libelle' => 'Clous (kg)', 'prix_unitaire' => 8000, 'quantite' => 30, 'date_saisie' => '2026-02-16 02:50:00'],
        // Ordre 25: Mananjary - Huile
        ['ville_id' => 2, 'type' => 'nature', 'libelle' => 'Huile (L)', 'prix_unitaire' => 6000, 'quantite' => 120, 'date_saisie' => '2026-02-16 04:00:00'],
        // Ordre 26: Farafangana - Bois
        ['ville_id' => 3, 'type' => 'materiel', 'libelle' => 'Bois', 'prix_unitaire' => 10000, 'quantite' => 100, 'date_saisie' => '2026-02-16 05:10:00']
    ];
    
    // Données initiales des dons
    private const INITIAL_DONS = [
        // ['type' => 'nature', 'libelle' => 'Riz (kg)', 'quantite' => 2000, 'date_saisie' => '2026-02-14 10:00:00'],
        // ['type' => 'nature', 'libelle' => 'Eau (L)', 'quantite' => 3000, 'date_saisie' => '2026-02-14 11:00:00'],
        // ['type' => 'nature', 'libelle' => 'Huile (L)', 'quantite' => 150, 'date_saisie' => '2026-02-14 12:00:00'],
        // ['type' => 'nature', 'libelle' => 'Haricots', 'quantite' => 250, 'date_saisie' => '2026-02-14 13:00:00'],
        // ['type' => 'materiel', 'libelle' => 'Tôle', 'quantite' => 200, 'date_saisie' => '2026-02-14 14:00:00'],
        // ['type' => 'materiel', 'libelle' => 'Bâche', 'quantite' => 400, 'date_saisie' => '2026-02-14 15:00:00'],
        // ['type' => 'materiel', 'libelle' => 'Clous (kg)', 'quantite' => 100, 'date_saisie' => '2026-02-14 16:00:00'],
        // ['type' => 'materiel', 'libelle' => 'Bois', 'quantite' => 300, 'date_saisie' => '2026-02-14 17:00:00'],
        // ['type' => 'materiel', 'libelle' => 'Groupe électrogène', 'quantite' => 5, 'date_saisie' => '2026-02-14 18:00:00'],
        // ['type' => 'argent', 'libelle' => 'Argent', 'quantite' => 50000000, 'date_saisie' => '2026-02-14 19:00:00']
    ];
    
    // Configuration par défaut
    private const DEFAULT_CONFIG = [
        'frais_achat_pourcentage' => 10
    ];
    
    // Tables à réinitialiser (dans l'ordre pour respecter les contraintes FK)
    private const TABLES_TO_RESET = [
        'dispatch',
        'achats_besoins',
        'dons',
        'besoins',
        'villes',
        'caisse_historique',
        'historique_totaux'
    ];
    
    // Vues à rafraîchir (si elles existent)
    private const VIEWS_TO_REFRESH = [
        'v_argent_disponible',
        'v_besoins_restants',
        'v_recapitulatif'
    ];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Fonction principale de réinitialisation
     * @return array Résultat avec succès et message
     */
    public function resetAllData() {
        try {
            // Note: TRUNCATE fait un commit implicite en MySQL, donc pas besoin de beginTransaction()
            
            // 1. Désactiver les vérifications de clés étrangères temporairement
            $this->disableForeignKeyChecks();
            
            // 2. Vider toutes les tables
            $this->truncateAllTables();
            
            // 3. Réinsérer les données initiales
            $this->insertInitialData();
            
            // 4. Réactiver les vérifications de clés étrangères
            $this->enableForeignKeyChecks();
            
            // 5. Rafraîchir les vues (si elles existent)
            $this->refreshViews();
            
            return [
                'success' => true,
                'message' => '✅ RÉINITIALISÉ AVEC DONNÉES INITIALES ✅',
                'details' => [
                    'villes' => count(self::INITIAL_VILLES),
                    'besoins' => count(self::INITIAL_BESOINS),
                    'dons' => count(self::INITIAL_DONS)
                ]
            ];
            
        } catch (Exception $e) {
            error_log("ERREUR RESET DATA: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => '❌ Erreur lors de la réinitialisation: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Désactiver les contraintes de clés étrangères
     */
    private function disableForeignKeyChecks() {
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    }

    /**
     * Réactiver les contraintes de clés étrangères
     */
    private function enableForeignKeyChecks() {
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    /**
     * Vider toutes les tables de manière sécurisée
     */
    private function truncateAllTables() {
        // Liste blanche de tables autorisées pour éviter les injections
        $allowedTables = self::TABLES_TO_RESET;
        
        foreach ($allowedTables as $table) {
            // Vérifier que la table existe
            if ($this->tableExists($table)) {
                // Utiliser un nom de table sécurisé (pas de paramètres dans TRUNCATE)
                $sanitizedTable = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
                $this->pdo->exec("TRUNCATE TABLE `$sanitizedTable`");
            }
        }
    }

    /**
     * Vérifier si une table existe
     */
    private function tableExists($tableName) {
        $stmt = $this->pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$tableName]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Insérer toutes les données initiales
     */
    private function insertInitialData() {
        // Insérer les villes en premier (nécessaire pour les FK des besoins)
        $this->insertVilles();
        
        // Insérer les besoins
        $this->insertBesoins();
        
        // Insérer les dons
        $this->insertDons();
    }

    /**
     * Insérer les villes initiales
     */
    private function insertVilles() {
        $stmt = $this->pdo->prepare("
            INSERT INTO villes (nom, region, date_creation)
            VALUES (?, ?, ?)
        ");
        
        foreach (self::INITIAL_VILLES as $ville) {
            $stmt->execute([
                $ville['nom'],
                $ville['region'],
                $ville['date_creation']
            ]);
        }
    }

    /**
     * Insérer les besoins initiaux
     */
    private function insertBesoins() {
        $stmt = $this->pdo->prepare("
            INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach (self::INITIAL_BESOINS as $besoin) {
            $stmt->execute([
                $besoin['ville_id'],
                $besoin['type'],
                $besoin['libelle'],
                $besoin['prix_unitaire'],
                $besoin['quantite'],
                $besoin['date_saisie']
            ]);
        }
    }

    /**
     * Insérer les dons initiaux
     */
    private function insertDons() {
        $stmt = $this->pdo->prepare("
            INSERT INTO dons (type, libelle, quantite, date_saisie)
            VALUES (?, ?, ?, ?)
        ");
        
        foreach (self::INITIAL_DONS as $don) {
            $stmt->execute([
                $don['type'],
                $don['libelle'],
                $don['quantite'],
                $don['date_saisie']
            ]);
        }
    }

    /**
     * Réinitialiser la caisse avec le montant initial
     */
    private function resetCaisse() {
        // Vérifier si la table caisse_historique existe
        if (!$this->tableExists('caisse_historique')) {
            return; // Table n'existe pas, on passe
        }
        
        $montantInitial = $this->calculateInitialCaisse();
        
        $stmt = $this->pdo->prepare("
            INSERT INTO caisse_historique (type, source, montant, solde_apres, description, date_operation)
            VALUES ('entree', 'initialisation', ?, ?, 'Initialisation de la caisse - Reset système', NOW())
        ");
        
        $stmt->execute([$montantInitial, $montantInitial]);
    }

    /**
     * Calculer le montant initial de la caisse (total des besoins argent)
     */
    private function calculateInitialCaisse() {
        $total = 0;
        foreach (self::INITIAL_BESOINS as $besoin) {
            if ($besoin['type'] === 'argent') {
                $total += $besoin['prix_unitaire'] * $besoin['quantite'];
            }
        }
        return $total;
    }

    /**
     * Rafraîchir les vues matérialisées (si elles existent)
     */
    private function refreshViews() {
        // MySQL ne supporte pas les vues matérialisées nativement
        // Cette méthode est préparée pour une future implémentation
        foreach (self::VIEWS_TO_REFRESH as $viewName) {
            if ($this->viewExists($viewName)) {
                // Possibilité de recréer la vue si nécessaire
                // Pour l'instant, on log simplement
                error_log("Vue $viewName existe et sera automatiquement mise à jour");
            }
        }
    }

    /**
     * Vérifier si une vue existe
     */
    private function viewExists($viewName) {
        $stmt = $this->pdo->prepare("
            SELECT TABLE_NAME 
            FROM information_schema.VIEWS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ?
        ");
        $stmt->execute([$viewName]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Réinitialiser la configuration par défaut
     */
    public function resetConfigDefaults() {
        // Si vous avez une table config, réinitialisez-la ici
        if ($this->tableExists('config')) {
            $this->pdo->exec("TRUNCATE TABLE config");
            
            $stmt = $this->pdo->prepare("
                INSERT INTO config (cle, valeur) VALUES (?, ?)
            ");
            
            foreach (self::DEFAULT_CONFIG as $key => $value) {
                $stmt->execute([$key, $value]);
            }
        }
    }

    /**
     * Obtenir un résumé de l'état actuel de la base
     */
    public function getDatabaseSummary() {
        $summary = [];
        
        $tables = ['villes', 'besoins', 'dons', 'dispatch', 'achats_besoins', 'caisse_historique'];
        
        foreach ($tables as $table) {
            if ($this->tableExists($table)) {
                $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM `$table`");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $summary[$table] = $result['count'];
            }
        }
        
        return $summary;
    }
}
