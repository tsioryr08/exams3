<?php

require_once __DIR__ . '/../repositories/RecapRepository.php';

/**
 * Service pour les statistiques et récapitulatifs
 */
class RecapService
{
    private $recapRepo;

    public function __construct(PDO $pdo)
    {
        $this->recapRepo = new RecapRepository($pdo);
    }

    /**
     * Récupérer toutes les statistiques
     * @return array
     */
    public function getStatistiques()
    {
        $globales = $this->recapRepo->getStatistiquesGlobales();
        $parVille = $this->recapRepo->getStatistiquesParVille();

        return [
            'globales' => $globales,
            'par_ville' => $parVille
        ];
    }

    /**
     * Formater les statistiques pour l'affichage
     * @return array
     */
    public function getStatistiquesFormatees()
    {
        $stats = $this->getStatistiques();
        
        return [
            'globales' => [
                'besoins_totaux' => [
                    'montant' => $stats['globales']['besoins_totaux_montant'],
                    'montant_format' => number_format($stats['globales']['besoins_totaux_montant'], 2, ',', ' ') . ' Ar'
                ],
                'besoins_satisfaits' => [
                    'montant' => $stats['globales']['besoins_satisfaits_montant'],
                    'montant_format' => number_format($stats['globales']['besoins_satisfaits_montant'], 2, ',', ' ') . ' Ar'
                ],
                'besoins_restants' => [
                    'montant' => $stats['globales']['besoins_restants_montant'],
                    'montant_format' => number_format($stats['globales']['besoins_restants_montant'], 2, ',', ' ') . ' Ar'
                ],
                'pourcentage_satisfaction' => $stats['globales']['pourcentage_satisfaction'],
                'dons_argent' => [
                    'total' => $stats['globales']['dons_argent_total'],
                    'total_format' => number_format($stats['globales']['dons_argent_total'], 2, ',', ' ') . ' Ar',
                    'utilise' => $stats['globales']['achats_montant_utilise'],
                    'utilise_format' => number_format($stats['globales']['achats_montant_utilise'], 2, ',', ' ') . ' Ar',
                    'disponible' => $stats['globales']['argent_disponible'],
                    'disponible_format' => number_format($stats['globales']['argent_disponible'], 2, ',', ' ') . ' Ar'
                ],
                'achats' => [
                    'nombre' => $stats['globales']['achats_nombre'],
                    'montant' => $stats['globales']['achats_montant_utilise']
                ]
            ],
            'par_ville' => $stats['par_ville']
        ];
    }
}
