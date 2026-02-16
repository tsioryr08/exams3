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
}
