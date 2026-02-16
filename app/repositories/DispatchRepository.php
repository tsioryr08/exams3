<?php

class DispatchRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Créer un dispatch (attribution de don à une ville)
     * @param int $donId - ID du don
     * @param int $villeId - ID de la ville
     * @param string $libelle - Libellé du don
     * @param int $quantiteAttribuee - Quantité attribuée
     * @return bool
     */
    public function create($donId, $villeId, $libelle, $quantiteAttribuee)
    {
        try {
            $sql = "INSERT INTO dispatch (don_id, ville_id, libelle, quantite_attribuee) 
                    VALUES (:don_id, :ville_id, :libelle, :quantite_attribuee)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':don_id' => $donId,
                ':ville_id' => $villeId,
                ':libelle' => $libelle,
                ':quantite_attribuee' => $quantiteAttribuee
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur création dispatch: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer tous les dispatches pour une ville
     * @param int $villeId - ID de la ville
     * @return array
     */
    public function getByVille($villeId)
    {
        try {
            $sql = "SELECT d.*, don.type, v.nom as ville_nom
                    FROM dispatch d
                    JOIN dons don ON d.don_id = don.id
                    JOIN villes v ON d.ville_id = v.id
                    WHERE d.ville_id = :ville_id
                    ORDER BY d.date_dispatch DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':ville_id' => $villeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération dispatch: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer tous les dispatches
     * @return array
     */
    public function getAll()
    {
        try {
            $sql = "SELECT d.*, don.type, v.nom as ville_nom
                    FROM dispatch d
                    JOIN dons don ON d.don_id = don.id
                    JOIN villes v ON d.ville_id = v.id
                    ORDER BY d.date_dispatch DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération dispatches: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Vider tous les dispatches (pour réinitialiser)
     * @return bool
     */
    public function deleteAll()
    {
        try {
            $this->pdo->exec("DELETE FROM dispatch");
            return true;
        } catch (PDOException $e) {
            error_log("Erreur suppression dispatches: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer le total dispatché pour un besoin spécifique
     * @param int $villeId
    /**
     * Récupérer le total dispatché pour un besoin spécifique
     * @param int $villeId
     * @param string $type
     * @param string $libelle
     * @return int
     */
    public function getTotalDispatchedForBesoin($villeId, $type, $libelle)
    {
        try {
            $sql = "SELECT COALESCE(SUM(d.quantite_attribuee), 0) as total
                    FROM dispatch d
                    JOIN dons don ON d.don_id = don.id
                    WHERE d.ville_id = :ville_id 
                    AND don.type = :type 
                    AND d.libelle = :libelle";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':ville_id' => $villeId,
                ':type' => $type,
                ':libelle' => $libelle
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Erreur calcul total dispatché: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Créer un dispatch à partir d'un achat
     * @param int $achatId
     * @param int $villeId
     * @param string $libelle
     * @param int $quantiteAttribuee
     * @return bool
     */
    public function createFromAchat($achatId, $villeId, $libelle, $quantiteAttribuee)
    {
        try {
            $sql = "INSERT INTO dispatch (don_id, ville_id, libelle, quantite_attribuee, source, achat_id) 
                    VALUES (0, :ville_id, :libelle, :quantite_attribuee, 'achat', :achat_id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':ville_id' => $villeId,
                ':libelle' => $libelle,
                ':quantite_attribuee' => $quantiteAttribuee,
                ':achat_id' => $achatId
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur création dispatch depuis achat: " . $e->getMessage());
            return false;
        }
    }
}

