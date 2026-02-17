<?php

require_once __DIR__ . '/../services/DataResetService.php';

/**
 * Contrôleur pour la réinitialisation des données
 */
class ResetController {
    
    /**
     * Afficher la page de réinitialisation
     */
    public static function showResetPage() {
        $pdo = Flight::db();
        $service = new DataResetService($pdo);
        
        // Obtenir le résumé actuel
        $summary = $service->getDatabaseSummary();
        
        // Capturer le contenu de la vue
        ob_start();
        include Flight::get('flight.views.path') . '/admin/reset.php';
        $content = ob_get_clean();
        
        // Rendre avec le layout
        Flight::render('layouts/main', [
            'content' => $content,
            'title' => 'Réinitialisation des données - BNGRC'
        ]);
    }
    
    /**
     * Traiter la réinitialisation (POST)
     */
    public static function processReset() {
        // Vérification de sécurité : mot de passe ou confirmation
        $confirmation = $_POST['confirmation'] ?? '';
        
        if ($confirmation !== 'REINITIALISER') {
            $_SESSION['error'] = '❌ Veuillez taper "REINITIALISER" pour confirmer';
            Flight::redirect('/admin/reset');
            return;
        }
        
        $pdo = Flight::db();
        $service = new DataResetService($pdo);
        
        // Exécuter la réinitialisation
        $result = $service->resetAllData();
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            if (isset($result['details'])) {
                $_SESSION['reset_details'] = $result['details'];
            }
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        Flight::redirect('/admin/reset');
    }
    
    /**
     * API JSON pour AJAX (optionnel)
     */
    public static function apiReset() {
        // Nettoyer tout output buffer précédent
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Définir le header JSON en premier
        header('Content-Type: application/json');
        
        try {
            $confirmation = $_POST['confirmation'] ?? '';
            
            if ($confirmation !== 'REINITIALISER') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Confirmation invalide'
                ]);
                exit;
            }
            
            $pdo = Flight::db();
            $service = new DataResetService($pdo);
            $result = $service->resetAllData();
            
            echo json_encode($result);
            exit;
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
            exit;
        }
    }
}
