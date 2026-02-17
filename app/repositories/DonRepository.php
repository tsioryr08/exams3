<?php

class DonRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Créer un nouveau don
     * @param string $type - Type du don (nature, materiel, argent)
     * @param string $libelle - Libellé du don
     * @param int $quantite - Quantité
     * @return bool - True si succès, false sinon
     */
    public function create($type, $libelle, $quantite)
    {
        try {
            $sql = "INSERT INTO dons (type, libelle, quantite) VALUES (:type, :libelle, :quantite)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':type' => $type,
                ':libelle' => $libelle,
                ':quantite' => $quantite
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur création don: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer tous les dons triés par date (du plus récent au plus ancien)
     * @return array - Liste des dons
     */
    public function getAllOrderByDate()
    {
        try {
            $sql = "SELECT * FROM dons ORDER BY date_saisie DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération dons: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer tous les dons (alias de getAllOrderByDate)
     * @return array - Liste des dons
     */
    public function getAll()
    {
        return $this->getAllOrderByDate();
    }

    /**
     * Récupérer tous les dons triés par date ASC (pour le dispatch)
     * @return array - Liste des dons
     */
    public function getAllOrderByDateAsc()
    {
        try {
            $sql = "SELECT * FROM dons ORDER BY date_saisie ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération dons: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer un don par son ID
     * @param int $id - ID du don
     * @return array|null - Don ou null si non trouvé
     */
    public function getById($id)
    {
        try {
            $sql = "SELECT * FROM dons WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erreur récupération don: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupérer les quantités de dons disponibles (non dispatches) par type/libellé
     * @return array - Tableau associatif avec type_libelle => quantité disponible
     */
    public function getDonsDisponiblesParTypeLibelle()
    {
        try {
            $sql = "SELECT 
                        d.type,
                        d.libelle,
                        SUM(d.quantite) AS quantite_totale_dons,
                        COALESCE(SUM(disp.quantite_attribuee), 0) AS quantite_dispatchee,
                        (SUM(d.quantite) - COALESCE(SUM(disp.quantite_attribuee), 0)) AS quantite_disponible
                    FROM dons d
                    LEFT JOIN dispatch disp ON disp.don_id = d.id
                    WHERE d.type IN ('nature', 'materiel')
                    GROUP BY d.type, d.libelle
                    HAVING quantite_disponible > 0
                    ORDER BY d.type, d.libelle";
            
            $stmt = $this->pdo->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convertir en tableau associatif pour accès rapide
            $donsDisponibles = [];
            foreach ($results as $row) {
                $key = $row['type'] . '_' . $row['libelle'];
                $donsDisponibles[$key] = [
                    'type' => $row['type'],
                    'libelle' => $row['libelle'],
                    'quantite_disponible' => (int)$row['quantite_disponible']
                ];
            }
            
            return $donsDisponibles;
        } catch (PDOException $e) {
            error_log("Erreur récupération dons disponibles: " . $e->getMessage());
            return [];
        }
    }
}
