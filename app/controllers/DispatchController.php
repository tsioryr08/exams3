<?php

require_once __DIR__ . '/../services/DispatchService.php';

class DispatchController
{
    private $dispatchService;

    public function __construct()
    {
        $this->dispatchService = new DispatchService(Flight::db());
    }

    /**
     * Afficher la page de dispatch
     * Le dispatch se fait automatiquement
     * GET /dispatch
     */
    public function index()
    {
        // Exécuter automatiquement le dispatch
        $rapport = $this->dispatchService->runDispatch();
        
        // Stocker seulement les détails, pas le message
        if ($rapport['success']) {
            $_SESSION['dispatch_details'] = $rapport['details'];
        }
        
        $dispatches = $this->dispatchService->getDispatchSummary();
        
        Flight::render('dispatch/index', [
            'title' => 'Dispatch des Dons',
            'dispatches' => $dispatches
        ], 'content');
        
        Flight::render('layouts/main', [
            'title' => 'Dispatch des Dons'
        ]);
    }
}
