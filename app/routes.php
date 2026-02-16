<?php

// ================== REPOSITORIES ==================
require_once __DIR__ . '/repositories/DonRepository.php';
require_once __DIR__ . '/repositories/DispatchRepository.php';
require_once __DIR__ . '/repositories/BesoinRepository.php';

// ================== SERVICES ==================
require_once __DIR__ . '/services/DonService.php';
require_once __DIR__ . '/services/DispatchService.php';

// ================== CONTROLLERS ==================
require_once __DIR__ . '/controllers/DonController.php';
require_once __DIR__ . '/controllers/DispatchController.php';

// ================== ROUTES HOME ==================
Flight::route('GET /', function () {
    Flight::redirect('/dons');
});

// ================== ROUTES DONS (DEV C) ==================
Flight::route('GET /dons', [new DonController(), 'index']);
Flight::route('GET /dons/add', [new DonController(), 'showAddForm']);
Flight::route('POST /dons/add', [new DonController(), 'store']);

// ================== ROUTES DISPATCH (DEV C) ==================
Flight::route('GET /dispatch', [new DispatchController(), 'index']);

// ================== START ==================
// Flight::start();
