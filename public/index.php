<?php
require '../vendor/autoload.php';
require '../app/bootstrap.php';

// Configurer le chemin des vues
Flight::set('flight.views.path', '../app/views');

Flight::start();