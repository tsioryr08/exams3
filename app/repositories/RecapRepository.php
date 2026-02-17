<?php

/**
 * Repository pour les statistiques et récapitulatifs
 */
class RecapRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupérer les statistiques globales
     * @return array
     */
    public function getStatistiquesGlobales()
    {
        try {
            // Besoins totaux
            $stmtBesoins = $this->pdo->query("
                SELECT 
                    COALESCE(SUM(prix_unitaire * quantite), 0) AS montant_total
                FROM besoins
            ");
            $besoinsTotal = $stmtBesoins->fetch(PDO::FETCH_ASSOC);

            // Besoins satisfaits (via dispatch)
            $stmtSatisfaits = $this->pdo->query("
                SELECT 
                    COALESCE(SUM(d.quantite_attribuee * b.prix_unitaire), 0) AS montant_satisfait
                FROM dispatch d
                INNER JOIN besoins b ON d.ville_id = b.ville_id AND d.libelle = b.libelle
            ");
            $besoinsSatisfaits = $stmtSatisfaits->fetch(PDO::FETCH_ASSOC);

            // Dons en argent
            $stmtDonsArgent = $this->pdo->query("
                SELECT 
                    COALESCE(SUM(quantite), 0) AS total_argent
                FROM dons
                WHERE type = 'argent'
            ");
            $donsArgent = $stmtDonsArgent->fetch(PDO::FETCH_ASSOC);

            // Achats effectués
            $stmtAchats = $this->pdo->query("
                SELECT 
                    COALESCE(SUM(montant_final), 0) AS total_achats,
                    COUNT(*) AS nb_achats
                FROM achats
                WHERE statut = 'valide'
            ");
            $achats = $stmtAchats->fetch(PDO::FETCH_ASSOC);

            $montantTotal = (float)$besoinsTotal['montant_total'];
            $montantSatisfait = (float)$besoinsSatisfaits['montant_satisfait'];
            $montantRestant = $montantTotal - $montantSatisfait;
            $pourcentageSatisfaction = $montantTotal > 0 
                ? round(($montantSatisfait / $montantTotal) * 100, 2) 
                : 0;

            return [
                'besoins_totaux_montant' => $montantTotal,
                'besoins_satisfaits_montant' => $montantSatisfait,
                'besoins_restants_montant' => $montantRestant,
                'pourcentage_satisfaction' => $pourcentageSatisfaction,
                'dons_argent_total' => (float)$donsArgent['total_argent'],
                'achats_montant_utilise' => (float)$achats['total_achats'],
                'achats_nombre' => (int)$achats['nb_achats'],
                'argent_disponible' => (float)$donsArgent['total_argent'] - (float)$achats['total_achats']
            ];
        } catch (Exception $e) {
            error_log("Erreur récupération statistiques: " . $e->getMessage());
            return [
                'besoins_totaux_montant' => 0,
                'besoins_satisfaits_montant' => 0,
                'besoins_restants_montant' => 0,
                'pourcentage_satisfaction' => 0,
                'dons_argent_total' => 0,
                'achats_montant_utilise' => 0,
                'achats_nombre' => 0,
                'argent_disponible' => 0
            ];
        }
    }

    /**
     * Récupérer les statistiques par ville
     * @return array
     */
    public function getStatistiquesParVille()
    {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    v.id AS ville_id,
                    v.nom AS ville_nom,
                    v.region,
                    COALESCE(SUM(b.prix_unitaire * b.quantite), 0) AS besoins_total,
                    COALESCE(SUM(d.quantite_attribuee * b.prix_unitaire), 0) AS besoins_satisfaits,
                    (COALESCE(SUM(b.prix_unitaire * b.quantite), 0) - COALESCE(SUM(d.quantite_attribuee * b.prix_unitaire), 0)) AS besoins_restants
                FROM villes v
                LEFT JOIN besoins b ON b.ville_id = v.id
                LEFT JOIN dispatch d ON d.ville_id = v.id AND d.libelle = b.libelle
                GROUP BY v.id, v.nom, v.region
                ORDER BY v.nom
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération statistiques par ville: " . $e->getMessage());
            return [];
        }
    }
}
