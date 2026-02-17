<?php

require_once __DIR__ . '/../services/RecapService.php';

/**
 * Contrôleur pour la page de récapitulation
 */
class RecapController
{
    private $recapService;

    public function __construct()
    {
        $this->recapService = new RecapService(Flight::db());
    }

    /**
     * Afficher la page de récapitulation
     * GET /recap
     */
    public function index()
    {
        $stats = $this->recapService->getStatistiquesFormatees();

        Flight::render('recap/index', [
            'title' => 'Récapitulatif',
            'stats' => $stats
        ], 'content');

        Flight::render('layouts/main', [
            'title' => 'Récapitulatif'
        ]);
    }

    /**
     * Endpoint Ajax pour actualiser les statistiques
     * GET /recap/ajax
     */
    public function ajax()
    {
        header('Content-Type: application/json');
        
        try {
            $stats = $this->recapService->getStatistiquesFormatees();
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
}
