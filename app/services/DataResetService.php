<?php

/**
 * Service de réinitialisation complète des données
 * Sécurisé contre les injections SQL avec PDO
 */
class DataResetService {
    private $pdo;
    
    // Données initiales des villes
    private const INITIAL_VILLES = [
        ['nom' => 'Antananarivo', 'region' => 'Analamanga'],
        ['nom' => 'Toamasina', 'region' => 'Atsinanana'],
        ['nom' => 'Fianarantsoa', 'region' => 'Haute Matsiatra'],
        ['nom' => 'Mahajanga', 'region' => 'Boeny']
    ];
    
    // Données initiales des besoins
    private const INITIAL_BESOINS = [
        // Antananarivo (ville_id = 1)
        ['ville_id' => 1, 'type' => 'nature', 'libelle' => 'riz', 'prix_unitaire' => 2500, 'quantite' => 100],
        ['ville_id' => 1, 'type' => 'nature', 'libelle' => 'huile', 'prix_unitaire' => 8000, 'quantite' => 50],
        ['ville_id' => 1, 'type' => 'materiel', 'libelle' => 'tôle', 'prix_unitaire' => 35000, 'quantite' => 30],
        
        // Toamasina (ville_id = 2)
        ['ville_id' => 2, 'type' => 'nature', 'libelle' => 'riz', 'prix_unitaire' => 2500, 'quantite' => 80],
        ['ville_id' => 2, 'type' => 'materiel', 'libelle' => 'clou', 'prix_unitaire' => 200, 'quantite' => 500],
        ['ville_id' => 2, 'type' => 'argent', 'libelle' => 'aide_financiere', 'prix_unitaire' => 1, 'quantite' => 1000000],
        
        // Fianarantsoa (ville_id = 3)
        ['ville_id' => 3, 'type' => 'nature', 'libelle' => 'riz', 'prix_unitaire' => 2500, 'quantite' => 60],
        ['ville_id' => 3, 'type' => 'materiel', 'libelle' => 'tôle', 'prix_unitaire' => 35000, 'quantite' => 20],
        
        // Mahajanga (ville_id = 4)
        ['ville_id' => 4, 'type' => 'nature', 'libelle' => 'huile', 'prix_unitaire' => 8000, 'quantite' => 40],
        ['ville_id' => 4, 'type' => 'argent', 'libelle' => 'aide_financiere', 'prix_unitaire' => 1, 'quantite' => 500000]
    ];
    
    // Données initiales des dons
    private const INITIAL_DONS = [
        ['type' => 'nature', 'libelle' => 'riz', 'quantite' => 120, 'date_saisie' => '2026-02-16 13:00:00'],
        ['type' => 'materiel', 'libelle' => 'tôle', 'quantite' => 25, 'date_saisie' => '2026-02-16 14:00:00'],
        ['type' => 'nature', 'libelle' => 'huile', 'quantite' => 60, 'date_saisie' => '2026-02-16 15:00:00'],
        ['type' => 'argent', 'libelle' => 'aide_financiere', 'quantite' => 1500000, 'date_saisie' => '2026-02-16 16:00:00'],
        ['type' => 'materiel', 'libelle' => 'clou', 'quantite' => 300, 'date_saisie' => '2026-02-16 17:00:00']
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
            
            // 4. Réinitialiser la caisse
            $this->resetCaisse();
            
            // 5. Réactiver les vérifications de clés étrangères
            $this->enableForeignKeyChecks();
            
            // 6. Rafraîchir les vues (si elles existent)
            $this->refreshViews();
            
            return [
                'success' => true,
                'message' => '✅ RÉINITIALISÉ AVEC DONNÉES INITIALES ✅',
                'details' => [
                    'villes' => count(self::INITIAL_VILLES),
                    'besoins' => count(self::INITIAL_BESOINS),
                    'dons' => count(self::INITIAL_DONS),
                    'caisse_initiale' => $this->calculateInitialCaisse()
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
            INSERT INTO villes (nom, region)
            VALUES (?, ?)
        ");
        
        foreach (self::INITIAL_VILLES as $ville) {
            $stmt->execute([
                $ville['nom'],
                $ville['region']
            ]);
        }
    }

    /**
     * Insérer les besoins initiaux
     */
    private function insertBesoins() {
        $stmt = $this->pdo->prepare("
            INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach (self::INITIAL_BESOINS as $besoin) {
            $stmt->execute([
                $besoin['ville_id'],
                $besoin['type'],
                $besoin['libelle'],
                $besoin['prix_unitaire'],
                $besoin['quantite']
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
