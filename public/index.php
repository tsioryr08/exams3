<?php
require '../vendor/autoload.php';
require '../app/bootstrap.php';  // ← Utilise votre bootstrap existant


// Routes Admin
Flight::route('GET /admin/login', ['AdminController', 'showLogin']);
Flight::route('POST /admin/login', ['AdminController', 'processLogin']);
Flight::route('GET /admin', ['AdminController', 'dashboard']);
Flight::route('GET /admin/logout', ['AdminController', 'logout']);

// Configurer le chemin des vues
Flight::set('flight.views.path', '../app/views');

Flight::start();