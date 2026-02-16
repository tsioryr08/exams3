<?php

require_once __DIR__ . '/../services/DonService.php';

class DonController
{
    private $donService;

    public function __construct()
    {
        $this->donService = new DonService(Flight::db());
    }

    /**
     * Afficher la liste des dons
     * GET /dons
     */
    public function index()
    {
        $dons = $this->donService->list();
        
        Flight::render('dons/index', [
            'title' => 'Liste des Dons',
            'dons' => $dons
        ], 'content');
        
        Flight::render('layouts/main', [
            'title' => 'Liste des Dons'
        ]);
    }

    /**
     * Afficher le formulaire d'ajout de don
     * GET /dons/add
     */
    public function showAddForm()
    {
        Flight::render('dons/add', [
            'title' => 'Ajouter un Don'
        ], 'content');
        
        Flight::render('layouts/main', [
            'title' => 'Ajouter un Don'
        ]);
    }

    /**
     * Traiter l'ajout d'un don
     * POST /dons/add
     */
    public function store()
    {
        $request = Flight::request();
        
        $type = $request->data->type ?? '';
        $libelle = $request->data->libelle ?? '';
        $quantite = $request->data->quantite ?? '';

        $result = $this->donService->add($type, $libelle, $quantite);

        if ($result['success']) {
            // Redirection vers la liste avec message de succès
            $_SESSION['success_message'] = $result['message'];
            Flight::redirect('/dons');
        } else {
            // Ré-afficher le formulaire avec les erreurs
            Flight::render('dons/add', [
                'title' => 'Ajouter un Don',
                'error' => $result['message'],
                'type' => $type,
                'libelle' => $libelle,
                'quantite' => $quantite
            ], 'content');
            
            Flight::render('layouts/main', [
                'title' => 'Ajouter un Don'
            ]);
        }
    }
}
