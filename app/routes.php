<?php

// ================== REPOSITORIES ==================
require_once __DIR__ . '/repositories/DonRepository.php';
require_once __DIR__ . '/repositories/DispatchRepository.php';
require_once __DIR__ . '/repositories/BesoinRepository.php';
require_once __DIR__ . '/repositories/BesoinsRepository.php';

// ================== SERVICES ==================
require_once __DIR__ . '/services/DonService.php';
require_once __DIR__ . '/services/DispatchService.php';
require_once __DIR__ . '/services/BesoinsService.php';

// ================== CONTROLLERS ==================
require_once __DIR__ . '/controllers/DonController.php';
require_once __DIR__ . '/controllers/DispatchController.php';
require_once __DIR__ . '/controllers/BesoinsController.php';
require_once __DIR__ . '/controllers/VilleController.php';

// ================== ROUTES HOME ==================
Flight::route('GET /', ['VilleController', 'index']);

// ================== ROUTES VILLES (DEV-TSIORY) ==================
Flight::route('GET /villes', ['VilleController', 'index']);
Flight::route('GET /villes/add', ['VilleController', 'add']);
Flight::route('POST /villes/add', ['VilleController', 'store']);
Flight::route('GET /villes/delete/@id', ['VilleController', 'delete']);
Flight::route('GET /villes/@id', ['VilleController', 'detail']);

// ================== ROUTES DONS (DEV-NJARY) ==================
Flight::route('GET /dons', [new DonController(), 'index']);
Flight::route('GET /dons/add', [new DonController(), 'showAddForm']);
Flight::route('POST /dons/add', [new DonController(), 'store']);

// ================== ROUTES DISPATCH (DEV-NJARY) ==================
Flight::route('GET /dispatch', [new DispatchController(), 'index']);

// ================== ROUTES BESOINS (DEV-ONJA) ==================
Flight::route('GET /besoins', ['BesoinsController', 'showForm']);
Flight::route('POST /besoins', ['BesoinsController', 'createBesoin']);
Flight::route('GET /besoins/list', ['BesoinsController', 'listBesoins']);
