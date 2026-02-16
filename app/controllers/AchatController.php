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
        $villeId = $_GET['ville_id'] ?? null;
        $data = $this->achatService->getBesoinsRestantsAvecArgent($villeId);

        // Récupérer toutes les villes pour le filtre
        $pdo = Flight::db();
        $stmt = $pdo->query("SELECT id, nom FROM villes ORDER BY nom");
        $villes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Flight::render('achats/besoins_restants', [
            'title' => 'Achats - Besoins Restants',
            'besoins' => $data['besoins'],
            'argent_disponible' => $data['argent_disponible'],
            'frais_pourcentage' => $data['frais_pourcentage'],
            'villes' => $villes,
            'ville_id_selected' => $villeId
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
        $besoinIds = $_POST['besoin_ids'] ?? [];
        
        if (empty($besoinIds)) {
            $_SESSION['error'] = 'Veuillez sélectionner au moins un besoin';
            Flight::redirect('/achats/besoins-restants');
            return;
        }

        $simulation = $this->achatService->simulerAchats($besoinIds);

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
        $besoinIds = $_POST['besoin_ids'] ?? [];
        
        if (empty($besoinIds)) {
            $_SESSION['error'] = 'Aucun besoin à valider';
            Flight::redirect('/achats/besoins-restants');
            return;
        }

        $result = $this->achatService->validerAchats($besoinIds);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
            $_SESSION['errors'] = $result['errors'] ?? [];
        }

        Flight::redirect('/achats/liste');
    }

    /**
     * Afficher la liste des achats effectués
     * GET /achats/liste
     */
    public function liste()
    {
        $pdo = Flight::db();
        $achatRepo = new AchatRepository($pdo);
        $achats = $achatRepo->getAll();

        $success = $_SESSION['success'] ?? '';
        $error = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);

        Flight::render('achats/liste', [
            'title' => 'Liste des Achats',
            'achats' => $achats,
            'success' => $success,
            'error' => $error
        ], 'content');

        Flight::render('layouts/main', [
            'title' => 'Liste des Achats'
        ]);
    }
}
