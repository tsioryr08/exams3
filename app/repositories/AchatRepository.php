<?php

/**
 * Repository pour gérer les achats
 */
class AchatRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Créer un nouvel achat
     */
    public function create($data)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO achats (
                    ville_id, besoin_id, type, libelle, quantite, 
                    prix_unitaire, montant_total, frais_achat, 
                    montant_final, pourcentage_frais, statut
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['ville_id'],
                $data['besoin_id'],
                $data['type'],
                $data['libelle'],
                $data['quantite'],
                $data['prix_unitaire'],
                $data['montant_total'],
                $data['frais_achat'],
                $data['montant_final'],
                $data['pourcentage_frais'],
                $data['statut'] ?? 'valide'
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("Erreur création achat: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupérer tous les achats
     */
    public function getAll()
    {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    a.*,
                    v.nom AS ville_nom,
                    v.region
                FROM achats a
                INNER JOIN villes v ON a.ville_id = v.id
                WHERE a.statut = 'valide'
                ORDER BY a.date_achat DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération achats: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les achats par ville
     */
    public function getByVille($villeId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    a.*,
                    v.nom AS ville_nom,
                    v.region
                FROM achats a
                INNER JOIN villes v ON a.ville_id = v.id
                WHERE a.ville_id = ? AND a.statut = 'valide'
                ORDER BY a.date_achat DESC
            ");
            $stmt->execute([$villeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération achats par ville: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculer le montant total des achats validés
     */
    public function getTotalMontantUtilise()
    {
        try {
            $stmt = $this->pdo->query("
                SELECT COALESCE(SUM(montant_final), 0) AS total
                FROM achats
                WHERE statut = 'valide'
            ");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float)$result['total'];
        } catch (Exception $e) {
            error_log("Erreur calcul montant utilisé: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Vérifier si un besoin a déjà été acheté
     */
    public function besoinDejaAchete($besoinId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) AS nb
                FROM achats
                WHERE besoin_id = ? AND statut = 'valide'
            ");
            $stmt->execute([$besoinId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['nb'] > 0;
        } catch (Exception $e) {
            error_log("Erreur vérification achat: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer tous les achats (pour réinitialiser)
     */
    public function deleteAll()
    {
        try {
            $this->pdo->exec("DELETE FROM achats");
            return true;
        } catch (Exception $e) {
            error_log("Erreur suppression achats: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les achats avec dispatch associés
     */
    public function getAchatsWithDispatch()
    {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    a.*,
                    v.nom AS ville_nom,
                    v.region,
                    d.id AS dispatch_id,
                    d.quantite_attribuee
                FROM achats a
                INNER JOIN villes v ON a.ville_id = v.id
                LEFT JOIN dispatch d ON d.achat_id = a.id
                WHERE a.statut = 'valide'
                ORDER BY a.date_achat DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération achats avec dispatch: " . $e->getMessage());
            return [];
        }
    }
}
