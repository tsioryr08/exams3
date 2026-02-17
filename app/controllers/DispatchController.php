<?php

require_once __DIR__ . '/../services/DispatchService.php';

class DispatchController
{
    private $dispatchService;

    public function __construct()
    {
        $this->dispatchService = new DispatchService(Flight::db());
    }

    public function index()
    {
        Flight::render('dispatch/index', [
            'title' => 'Méthodes de Dispatch'
        ], 'content');
        
        Flight::render('layouts/main', [
            'title' => 'Méthodes de Dispatch'
        ]);
    }

  
    public function dispatchParDate()
    {
        $resultats = $this->dispatchService->dispatchParDate();
        
        $this->afficherResultats($resultats, 'Dispatch par Date (FIFO)');
    }

    public function dispatchOrdreCroissant()
    {
        $resultats = $this->dispatchService->dispatchOrdreCroissant();
        
        $this->afficherResultats($resultats, 'Dispatch par Ordre Croissant');
    }

    public function dispatchProportionnel()
    {
        $resultats = $this->dispatchService->dispatchProportionnel();
        
        $this->afficherResultats($resultats, 'Dispatch Proportionnel');
    }

    /**
     * Réinitialiser tous les dispatches (Ajax)
     * POST /dispatch/reinitialiser
     */
    public function reinitialiser()
    {
        try {
            $this->dispatchService->reinitialiserDispatches();
            
            Flight::json([
                'success' => true,
                'message' => 'Dispatches réinitialisés avec succès'
            ]);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ]);
        }
    }


    private function afficherResultats($resultats, $methode_nom)
    {
        Flight::render('dispatch/resultats', [
            'title' => 'Résultats du Dispatch',
            'methode_nom' => $methode_nom,
            'resultats' => $resultats['attributions'] ?? [],
            'restes' => $resultats['restes'] ?? [],
            'stats' => $resultats['stats'] ?? [],
            'par_ville' => $resultats['par_ville'] ?? []
        ], 'content');
        
        Flight::render('layouts/main', [
            'title' => 'Résultats du Dispatch'
        ]);
    }
}
