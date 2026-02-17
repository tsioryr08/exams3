<?php

// ================== REPOSITORIES ==================
require_once __DIR__ . '/repositories/DonRepository.php';
require_once __DIR__ . '/repositories/DispatchRepository.php';
require_once __DIR__ . '/repositories/BesoinRepository.php';
require_once __DIR__ . '/repositories/BesoinsRepository.php';
require_once __DIR__ . '/repositories/ConfigRepository.php';
require_once __DIR__ . '/repositories/AchatRepository.php';
require_once __DIR__ . '/repositories/RecapRepository.php';

// ================== SERVICES ==================
require_once __DIR__ . '/services/DonService.php';
require_once __DIR__ . '/services/DispatchService.php';
require_once __DIR__ . '/services/BesoinsService.php';
require_once __DIR__ . '/services/AchatService.php';
require_once __DIR__ . '/services/RecapService.php';

// ================== CONTROLLERS ==================
require_once __DIR__ . '/controllers/DonController.php';
require_once __DIR__ . '/controllers/DispatchController.php';
require_once __DIR__ . '/controllers/BesoinsController.php';
require_once __DIR__ . '/controllers/VilleController.php';
require_once __DIR__ . '/controllers/ResetController.php';
require_once __DIR__ . '/controllers/AchatController.php';
require_once __DIR__ . '/controllers/RecapController.php';

// ================== ROUTES HOME ==================
Flight::route('GET /', ['VilleController', 'index']);

// ================== ROUTES VILLES (DEV-TSIORY) ==================
Flight::route('GET /villes', ['VilleController', 'index']);
Flight::route('GET /villes/add', ['VilleController', 'add']);
Flight::route('POST /villes/add', ['VilleController', 'store']);
Flight::route('GET /villes/delete/@id', ['VilleController', 'delete']);
Flight::route('GET /villes/detail/@id', ['VilleController', 'detail']);

// ================== ROUTES DONS (DEV-NJARY) ==================
Flight::route('GET /dons', [new DonController(), 'index']);
Flight::route('GET /dons/add', [new DonController(), 'showAddForm']);
Flight::route('POST /dons/add', [new DonController(), 'store']);

// ================== ROUTES DISPATCH (DEV-NJARY) ==================
$dispatchController = new DispatchController();
Flight::route('GET /dispatch', [$dispatchController, 'index']);
Flight::route('GET /dispatch/par-date', [$dispatchController, 'dispatchParDate']);
Flight::route('GET /dispatch/ordre-croissant', [$dispatchController, 'dispatchOrdreCroissant']);
Flight::route('GET /dispatch/proportionnel', [$dispatchController, 'dispatchProportionnel']);
Flight::route('POST /dispatch/reinitialiser', [$dispatchController, 'reinitialiser']);

// ================== ROUTES BESOINS (DEV-ONJA) ==================
Flight::route('GET /besoins', ['BesoinsController', 'showForm']);
Flight::route('POST /besoins', ['BesoinsController', 'createBesoin']);
Flight::route('GET /besoins/list', ['BesoinsController', 'listBesoins']);

// ================== ROUTES ACHATS (V2 - NOUVELLE FONCTIONNALITÉ) ==================
$achatController = new AchatController();

Flight::route('GET /achats/besoins-restants', [$achatController, 'showBesoinsRestants']);
Flight::route('POST /achats/simuler', [$achatController, 'simuler']);
Flight::route('POST /achats/valider', [$achatController, 'valider']);
Flight::route('GET /achats/liste', [$achatController, 'liste']);

// ================== ROUTES RÉCAPITULATIF (V2 - NOUVELLE FONCTIONNALITÉ) ==================
$recapController = new RecapController();

Flight::route('GET /recap', [$recapController, 'index']);
Flight::route('GET /recap/ajax', [$recapController, 'ajax']);

// ================== ROUTES ADMINISTRATION - RESET DONNÉES ==================
Flight::route('GET /admin/reset', ['ResetController', 'showResetPage']);
Flight::route('POST /admin/reset', ['ResetController', 'processReset']);
Flight::route('POST /api/reset', ['ResetController', 'apiReset']); // API optionnelle
