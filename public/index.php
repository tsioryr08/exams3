<?php
// Charger l'autoloader de Composer
require '../vendor/autoload.php';

// Charger la configuration et initialiser Flight
require '../app/bootstrap.php';

// Charger les routes
require '../app/routes.php';

// Démarrer Flight
Flight::start();
