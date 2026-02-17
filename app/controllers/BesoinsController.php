<?php

class BesoinsController
{
    public static function showForm()
    {
        $pdo = Flight::db();
        $repo = new BesoinsRepository($pdo);
        $service = new BesoinsService($repo);
        
        $villes = $service->getAllVilles();
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        $success = $_SESSION['success'] ?? '';
        
        // Nettoyer la session
        unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);
        
        // Capturer le contenu de la vue partielle
        ob_start();
        include Flight::get('flight.views.path') . '/besoins/form.php';
        $content = ob_get_clean();
        
        // Rendre avec le layout
        Flight::render('layouts/main', [
            'content' => $content,
            'title' => 'Nouveau Besoin - BNGRC'
        ]);
    }

    public static function createBesoin()
    {
        $pdo = Flight::db();
        $repo = new BesoinsRepository($pdo);
        $service = new BesoinsService($repo);
        
        $data = [
            'ville_id' => $_POST['ville_id'] ?? '',
            'type' => $_POST['type'] ?? '',
            'libelle' => $_POST['libelle'] ?? '',
            'prix_unitaire' => $_POST['prix_unitaire'] ?? 0,
            'quantite' => $_POST['quantite'] ?? 0,
            'date_saisie' => $_POST['date_saisie'] ?? ''
        ];
        
        $result = $service->validateAndCreate($data);
        
        if ($result['success']) {
            $_SESSION['success'] = 'Besoin créé avec succès !';
            Flight::redirect('/besoins');
        } else {
            $_SESSION['errors'] = $result['errors'];
            $_SESSION['old'] = $data;
            Flight::redirect('/besoins');
        }
    }

    public static function listBesoins()
    {
        $pdo = Flight::db();
        $repo = new BesoinsRepository($pdo);
        $service = new BesoinsService($repo);
        
        $besoins = $service->getAllBesoins();
        
        // Capturer le contenu de la vue partielle
        ob_start();
        include Flight::get('flight.views.path') . '/besoins/list.php';
        $content = ob_get_clean();
        
        // Rendre avec le layout
        Flight::render('layouts/main', [
            'content' => $content,
            'title' => 'Liste des Besoins - BNGRC'
        ]);
    }
}