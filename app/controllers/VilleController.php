<?php
require_once __DIR__ . '/../models/Ville.php';

class VilleController {
    
    /**
     * Afficher la liste des villes
     */
    public static function index() {
        $villeModel = new Ville();
        $villes = $villeModel->getAll();
        
        require __DIR__ . '/../../views/pages/villes_list.php';
    }
    
    /**
     * Afficher le formulaire d'ajout
     */
    public static function add() {
        require __DIR__ . '/../../views/pages/villes_add.php';
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
        
        require __DIR__ . '/../../views/pages/ville_detail.php';
    }
}
