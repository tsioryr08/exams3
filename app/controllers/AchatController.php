<?php

require_once __DIR__ . '/../services/AchatService.php';

/**
 * Contrôleur gérant les achats
 */
class AchatController
{
    private $achatService;

    public function __construct()
    {
        $this->achatService = new AchatService(Flight::db());
    }

    /**
     * Afficher la page des besoins restants (pour sélectionner les achats)
     * GET /achats/besoins-restants
     */
    public function showBesoinsRestants()
    {
        $data = $this->achatService->getBesoinsRestantsAvecArgent();

        Flight::render('achats/besoins_restants', [
            'title' => 'Achats - Besoins Restants',
            'besoins' => $data['besoins'],
            'argent_disponible' => $data['argent_disponible'],
            'frais_pourcentage' => $data['frais_pourcentage']
        ], 'content');

        Flight::render('layouts/main', [
            'title' => 'Achats - Besoins Restants'
        ]);
    }

    /**
     * Simuler des achats
     * POST /achats/simuler
     */
    public function simuler()
    {
        $besoinKeys = $_POST['besoin_keys'] ?? [];
        
        if (empty($besoinKeys)) {
            $_SESSION['error'] = 'Veuillez sélectionner au moins un besoin';
            Flight::redirect('/achats/besoins-restants');
            return;
        }

        $simulation = $this->achatService->simulerAchats($besoinKeys);

        // Stocker la simulation en session
        $_SESSION['simulation'] = $simulation;

        Flight::render('achats/simulation', [
            'title' => 'Simulation d\'Achats',
            'simulation' => $simulation
        ], 'content');

        Flight::render('layouts/main', [
            'title' => 'Simulation d\'Achats'
        ]);
    }

    /**
     * Valider les achats
     * POST /achats/valider
     */
    public function valider()
    {
        $besoinKeys = $_POST['besoin_keys'] ?? [];
        
        if (empty($besoinKeys)) {
            $_SESSION['error'] = 'Aucun besoin à valider';
            Flight::redirect('/achats/besoins-restants');
            return;
        }

        $result = $this->achatService->validerAchats($besoinKeys);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
            $_SESSION['errors'] = $result['errors'] ?? [];
        }

        Flight::redirect('/dons');
    }

    /**
     * Afficher la liste des achats effectués
     * GET /achats/liste
     * 
     * NOTE: Cette route est obsolète dans la nouvelle logique.
     * Les achats sont maintenant directement ajoutés aux dons.
     * Redirigeons vers la page des dons.
     */
    public function liste()
    {
        $_SESSION['info'] = 'Les achats validés sont maintenant visibles dans la liste des dons.';
        Flight::redirect('/dons');
    }
}
