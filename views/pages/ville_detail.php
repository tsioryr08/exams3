<?php
$title = 'Détails ville - BNGRC Donating';
ob_start();
?>
<div class="container">
    <div class="mb-3">
        <a href="/villes" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(135deg, #B8A8D9 0%, #A8C5E6 100%); color: white;">
            <h4 class="mb-0" style="font-family: 'Playfair Display', serif;">
                <i class="fas fa-city"></i> <?= htmlspecialchars($ville['nom']) ?>
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Région :</strong> 
                        <span class="badge-custom">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($ville['region']) ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Date de création :</strong> 
                        <small class="text-muted">
                            <?= date('d/m/Y à H:i', strtotime($ville['date_creation'])) ?>
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-hand-holding-heart"></i> Besoins de la ville</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center py-3">
                        <i class="fas fa-info-circle"></i> 
                        Fonctionnalité à venir (DEV B - Besoins)
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-gift"></i> Dons reçus</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center py-3">
                        <i class="fas fa-info-circle"></i> 
                        Fonctionnalité à venir (DEV C - Dispatch)
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../app/views/layouts/main.php';
