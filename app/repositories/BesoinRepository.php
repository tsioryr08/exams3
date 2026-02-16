<?php

class BesoinRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupérer les besoins par type et libellé, triés par date
     * Utilisé pour le dispatch
     * @param string $type
     * @param string $libelle
     * @return array
     */
    public function getByTypeAndLibelle($type, $libelle)
    {
        try {
            $sql = "SELECT b.*, v.nom as ville_nom, v.region
                    FROM besoins b
                    JOIN villes v ON b.ville_id = v.id
                    WHERE b.type = :type AND b.libelle = :libelle
                    ORDER BY b.date_saisie ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':type' => $type,
                ':libelle' => $libelle
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération besoins: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer tous les besoins
     * @return array
     */
    public function getAll()
    {
        try {
            $sql = "SELECT b.*, v.nom as ville_nom, v.region
                    FROM besoins b
                    JOIN villes v ON b.ville_id = v.id
                    ORDER BY b.date_saisie DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération besoins: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer un besoin par ID
     * @param int $id
     * @return array|null
     */
    public function getById($id)
    {
        try {
            $sql = "SELECT b.*, v.nom as ville_nom, v.region
                    FROM besoins b
                    JOIN villes v ON b.ville_id = v.id
                    WHERE b.id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erreur récupération besoin: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupérer les besoins restants (non satisfaits par les dispatches)
     * Uniquement nature et matériel (achetables)
     * @param int|null $villeId - Filtrer par ville (optionnel)
     * @return array
     */
    public function getBesoinsRestants($villeId = null)
    {
        try {
            $sql = "SELECT 
                        b.id AS besoin_id,
                        b.ville_id,
                        v.nom AS ville_nom,
                        v.region,
                        b.type,
                        b.libelle,
                        b.prix_unitaire,
                        b.quantite AS quantite_besoin,
                        COALESCE(SUM(d.quantite_attribuee), 0) AS quantite_satisfaite,
                        (b.quantite - COALESCE(SUM(d.quantite_attribuee), 0)) AS quantite_restante,
                        (b.prix_unitaire * (b.quantite - COALESCE(SUM(d.quantite_attribuee), 0))) AS montant_restant
                    FROM besoins b
                    INNER JOIN villes v ON b.ville_id = v.id
                    LEFT JOIN dispatch d ON d.ville_id = b.ville_id AND d.libelle = b.libelle
                    WHERE b.type IN ('nature', 'materiel')";
            
            if ($villeId !== null) {
                $sql .= " AND b.ville_id = :ville_id";
            }
            
            $sql .= " GROUP BY b.id, b.ville_id, v.nom, v.region, b.type, b.libelle, b.prix_unitaire, b.quantite
                     HAVING quantite_restante > 0
                     ORDER BY v.nom, b.type, b.libelle";
            
            $stmt = $this->pdo->prepare($sql);
            
            if ($villeId !== null) {
                $stmt->execute([':ville_id' => $villeId]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération besoins restants: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer le montant total des besoins
     * @return float
     */
    public function getMontantTotalBesoins()
    {
        try {
            $stmt = $this->pdo->query("
                SELECT COALESCE(SUM(prix_unitaire * quantite), 0) AS total
                FROM besoins
            ");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float)$result['total'];
        } catch (PDOException $e) {
            error_log("Erreur calcul montant total besoins: " . $e->getMessage());
            return 0;
        }
    }
}
