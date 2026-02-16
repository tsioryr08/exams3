<?php
require_once __DIR__ . '/controllers/VilleController.php';

Flight::route('GET /', function() {
    Flight::redirect('/villes');
});

Flight::route('GET /villes', function() {
    VilleController::index();
});

Flight::route('GET /villes/add', function() {
    VilleController::add();
});

Flight::route('POST /villes/add', function() {
    VilleController::store();
});

Flight::route('POST /villes/delete/@id', function($id) {
    VilleController::delete($id);
});

Flight::route('GET /villes/detail/@id', function($id) {
    VilleController::detail($id);
});

Flight::map('notFound', function() {
    $title = 'Page non trouvée';
    ob_start();
    ?>
    <div class="container">
        <div class="text-center py-5">
            <h1 class="display-1">404</h1>
            <h2>Page non trouvée</h2>
            <a href="/villes" class="btn btn-primary mt-3">
                <i class="fas fa-city"></i> Gestion des villes
            </a>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layouts/main.php';
});
// ============================================
Flight::map('notFound', function() {
    echo '<h1>404 - Page non trouvée</h1>';
});

Flight::start();
?>