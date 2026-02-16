<?php
// Route d'accueil
Flight::route('GET /', function () {
    Flight::render('layouts/main', ['content' => '<div class="container"><h1>Bienvenue sur BNGRC Donating</h1><p>Utilisez le menu pour accéder aux besoins.</p></div>']);
});

// Routes pour les besoins
require_once __DIR__ . '/controllers/BesoinsController.php';
require_once __DIR__ . '/repositories/BesoinsRepository.php';
require_once __DIR__ . '/services/BesoinsService.php';

Flight::route('GET /besoins', ['BesoinsController', 'showForm']);
Flight::route('POST /besoins', ['BesoinsController', 'createBesoin']);
Flight::route('GET /besoins/list', ['BesoinsController', 'listBesoins']);