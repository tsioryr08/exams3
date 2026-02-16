<div class="container">
    <!-- Message de succès -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-gift"></i> Liste des Dons</h1>
        <div>
            <a href="/dispatch" class="btn btn-outline-secondary me-2">
                <i class="fas fa-exchange-alt"></i> Dispatch
            </a>
            <a href="/dons/add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un don
            </a>
        </div>
    </div>

    <!-- Dons par catégorie -->
    <?php if (empty($dons)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Aucun don enregistré pour le moment.
        </div>
    <?php else: ?>
        <?php
        // Séparer les dons par type
        $donsNature = array_filter($dons, function($don) { return $don['type'] === 'nature'; });
        $donsMateriel = array_filter($dons, function($don) { return $don['type'] === 'materiel'; });
        $donsArgent = array_filter($dons, function($don) { return $don['type'] === 'argent'; });
        ?>

        <div class="row">
            <!-- Colonne NATURE -->
            <div class="col-md-4 mb-4">
                <div class="card h-100" style="border-top: 4px solid #28a745;">
                    <div class="card-header" style="background-color: #d4edda; border-bottom: 2px solid #28a745;">
                        <h5 class="mb-0 text-success">
                            <i class="fas fa-leaf"></i> Nature
                        </h5>
                        <small class="text-muted"><?php echo count($donsNature); ?> don(s)</small>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <?php if (empty($donsNature)): ?>
                            <p class="text-muted text-center"><i>Aucun don</i></p>
                        <?php else: ?>
                            <?php foreach ($donsNature as $don): ?>
                                <div class="mb-3 p-3" style="background-color: #f8f9fa; border-radius: 8px; border-left: 3px solid #28a745;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <strong class="text-success"><?php echo htmlspecialchars($don['libelle']); ?></strong>
                                        <span class="badge bg-success">#<?php echo $don['id']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark">
                                            <i class="fas fa-box"></i> 
                                            <strong><?php echo number_format($don['quantite'], 0, ',', ' '); ?></strong>
                                        </span>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i>
                                            <?php 
                                            $date = new DateTime($don['date_saisie']);
                                            echo $date->format('d/m H:i'); 
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-center" style="background-color: #d4edda;">
                        <strong class="text-success">
                            Total: <?php echo number_format(array_sum(array_column($donsNature, 'quantite')), 0, ',', ' '); ?>
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Colonne MATERIEL -->
            <div class="col-md-4 mb-4">
                <div class="card h-100" style="border-top: 4px solid #007bff;">
                    <div class="card-header" style="background-color: #d1ecf1; border-bottom: 2px solid #007bff;">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-tools"></i> Matériel
                        </h5>
                        <small class="text-muted"><?php echo count($donsMateriel); ?> don(s)</small>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <?php if (empty($donsMateriel)): ?>
                            <p class="text-muted text-center"><i>Aucun don</i></p>
                        <?php else: ?>
                            <?php foreach ($donsMateriel as $don): ?>
                                <div class="mb-3 p-3" style="background-color: #f8f9fa; border-radius: 8px; border-left: 3px solid #007bff;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <strong class="text-primary"><?php echo htmlspecialchars($don['libelle']); ?></strong>
                                        <span class="badge bg-primary">#<?php echo $don['id']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark">
                                            <i class="fas fa-box"></i> 
                                            <strong><?php echo number_format($don['quantite'], 0, ',', ' '); ?></strong>
                                        </span>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i>
                                            <?php 
                                            $date = new DateTime($don['date_saisie']);
                                            echo $date->format('d/m H:i'); 
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-center" style="background-color: #d1ecf1;">
                        <strong class="text-primary">
                            Total: <?php echo number_format(array_sum(array_column($donsMateriel, 'quantite')), 0, ',', ' '); ?>
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Colonne ARGENT -->
            <div class="col-md-4 mb-4">
                <div class="card h-100" style="border-top: 4px solid #ffc107;">
                    <div class="card-header" style="background-color: #fff3cd; border-bottom: 2px solid #ffc107;">
                        <h5 class="mb-0 text-warning">
                            <i class="fas fa-coins"></i> Argent
                        </h5>
                        <small class="text-muted"><?php echo count($donsArgent); ?> don(s)</small>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <?php if (empty($donsArgent)): ?>
                            <p class="text-muted text-center"><i>Aucun don</i></p>
                        <?php else: ?>
                            <?php foreach ($donsArgent as $don): ?>
                                <div class="mb-3 p-3" style="background-color: #f8f9fa; border-radius: 8px; border-left: 3px solid #ffc107;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <strong class="text-warning"><?php echo htmlspecialchars($don['libelle']); ?></strong>
                                        <span class="badge bg-warning text-dark">#<?php echo $don['id']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark">
                                            <i class="fas fa-money-bill-wave"></i> 
                                            <strong><?php echo number_format($don['quantite'], 0, ',', ' '); ?> Ar</strong>
                                        </span>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i>
                                            <?php 
                                            $date = new DateTime($don['date_saisie']);
                                            echo $date->format('d/m H:i'); 
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-center" style="background-color: #fff3cd;">
                        <strong class="text-warning">
                            Total: <?php echo number_format(array_sum(array_column($donsArgent, 'quantite')), 0, ',', ' '); ?> Ar
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques globales -->
        <div class="card mt-3">
            <div class="card-body" style="background-color: var(--beige-pale);">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4 class="mb-0 text-success"><?php echo count($donsNature); ?></h4>
                        <small class="text-muted">Dons Nature</small>
                    </div>
                    <div class="col-md-4">
                        <h4 class="mb-0 text-primary"><?php echo count($donsMateriel); ?></h4>
                        <small class="text-muted">Dons Matériel</small>
                    </div>
                    <div class="col-md-4">
                        <h4 class="mb-0 text-warning"><?php echo count($donsArgent); ?></h4>
                        <small class="text-muted">Dons Argent</small>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
