<?php
require_once __DIR__ . '/../models/Ville.php';

class VilleController {
    
    /**
     * Afficher la liste des villes
     */
    public static function index() {
        $villeModel = new Ville();
        $villes = $villeModel->getAll();
        
        // Capturer le contenu de la vue partielle
        ob_start();
        include Flight::get('flight.views.path') . '/pages/villes_list.php';
        $content = ob_get_clean();
        
        // Rendre avec le layout
        Flight::render('layouts/main', [
            'content' => $content,
            'title' => 'Liste des villes - BNGRC'
        ]);
    }
    
    /**
     * Afficher le formulaire d'ajout
     */
    public static function add() {
        // Capturer le contenu de la vue partielle
        ob_start();
        include Flight::get('flight.views.path') . '/pages/villes_add.php';
        $content = ob_get_clean();
        
        // Rendre avec le layout
        Flight::render('layouts/main', [
            'content' => $content,
            'title' => 'Ajouter une ville - BNGRC'
        ]);
    }
    
    /**
     * Enregistrer une nouvelle ville
     */
    public static function store() {
        $nom = $_POST['nom_ville'] ?? '';
        $region = $_POST['region'] ?? '';
        
        // Validation simple
        if (empty($nom) || empty($region)) {
            $_SESSION['error'] = 'Tous les champs sont obligatoires';
            Flight::redirect('/villes/add');
            return;
        }
        
        $villeModel = new Ville();
        
        if ($villeModel->create($nom, $region)) {
            $_SESSION['success'] = 'Ville ajoutée avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'ajout de la ville';
        }
        
        Flight::redirect('/villes');
    }
    
    /**
     * Supprimer une ville
     */
    public static function delete($id) {
        $villeModel = new Ville();
        
        if ($villeModel->delete($id)) {
            $_SESSION['success'] = 'Ville supprimée avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression';
        }
        
        Flight::redirect('/villes');
    }
    
    /**
     * Afficher les détails d'une ville
     */
    public static function detail($id) {
        $villeModel = new Ville();
        $ville = $villeModel->getById($id);
        
        if (!$ville) {
            $_SESSION['error'] = 'Ville introuvable';
            Flight::redirect('/villes');
            return;
        }
        
        // Capturer le contenu de la vue partielle
        ob_start();
        include Flight::get('flight.views.path') . '/pages/ville_detail.php';
        $content = ob_get_clean();
        
        // Rendre avec le layout
        Flight::render('layouts/main', [
            'content' => $content,
            'title' => 'Détails ville - BNGRC'
        ]);
    }
}
