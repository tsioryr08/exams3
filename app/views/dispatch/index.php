<div class="container">
    <!-- En-tête -->
    <div class="mb-4">
        <h1><i class="fas fa-exchange-alt"></i> Dispatch des Dons</h1>
        <p class="text-muted">Distribution automatique des dons aux villes selon leurs besoins</p>
    </div>

    <!-- Détails du dernier dispatch -->
    <?php if (isset($_SESSION['dispatch_details']) && !empty($_SESSION['dispatch_details'])): ?>
        <?php
        // Séparer les dons attribués et non attribués
        $donsAttribues = [];
        $donsNonAttribues = [];
        
        foreach ($_SESSION['dispatch_details'] as $detail) {
            if (isset($detail['ville']) && $detail['statut'] === 'Attribué') {
                $donsAttribues[] = $detail;
            } else {
                $donsNonAttribues[] = $detail;
            }
        }
        ?>

        <div class="row">
            <!-- Colonne Dons Attribués -->
            <div class="col-md-6 mb-4">
                <div class="card h-100" style="border-top: 4px solid #28a745;">
                    <div class="card-header" style="background-color: #d4edda;">
                        <h5 class="mb-0 text-success">
                            <i class="fas fa-check-circle"></i> Dons Attribués
                        </h5>
                        <small class="text-muted"><?php echo count($donsAttribues); ?> attribution(s)</small>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <?php if (empty($donsAttribues)): ?>
                            <p class="text-muted text-center"><i>Aucun don attribué</i></p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Don #</th>
                                            <th>Libellé</th>
                                            <th>Ville</th>
                                            <th>Quantité</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($donsAttribues as $detail): ?>
                                            <tr>
                                                <td><span class="badge bg-success">#<?php echo $detail['don_id']; ?></span></td>
                                                <td><?php echo htmlspecialchars($detail['libelle']); ?></td>
                                                <td><?php echo htmlspecialchars($detail['ville']); ?></td>
                                                <td><strong><?php echo number_format($detail['quantite_attribuee'], 0, ',', ' '); ?></strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Colonne Dons Non Attribués -->
            <div class="col-md-6 mb-4">
                <div class="card h-100" style="border-top: 4px solid #dc3545;">
                    <div class="card-header" style="background-color: #f8d7da;">
                        <h5 class="mb-0 text-danger">
                            <i class="fas fa-times-circle"></i> Dons Non Attribués
                        </h5>
                        <small class="text-muted"><?php echo count($donsNonAttribues); ?> don(s)</small>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <?php if (empty($donsNonAttribues)): ?>
                            <p class="text-muted text-center"><i>Tous les dons ont été attribués ✓</i></p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Don #</th>
                                            <th>Libellé</th>
                                            <th>Quantité</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($donsNonAttribues as $detail): ?>
                                            <tr>
                                                <td><span class="badge bg-danger">#<?php echo $detail['don_id']; ?></span></td>
                                                <td><?php echo htmlspecialchars($detail['libelle']); ?></td>
                                                <td><strong><?php echo number_format($detail['quantite'], 0, ',', ' '); ?></strong></td>
                                                <td>
                                                    <small class="text-muted"><?php echo htmlspecialchars($detail['statut']); ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['dispatch_details']); ?>
    <?php endif; ?>

    <!-- Attributions par ville -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-city"></i> Attributions par ville</h5>
        </div>
        <div class="card-body">
            <?php if (empty($dispatches)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucune attribution pour le moment.
                </div>
            <?php else: ?>
                <?php
                // Grouper par ville
                $dispatchesParVille = [];
                foreach ($dispatches as $dispatch) {
                    $villeNom = $dispatch['ville_nom'];
                    if (!isset($dispatchesParVille[$villeNom])) {
                        $dispatchesParVille[$villeNom] = [];
                    }
                    $dispatchesParVille[$villeNom][] = $dispatch;
                }
                ?>

                <div class="accordion" id="accordionDispatches">
                    <?php foreach ($dispatchesParVille as $villeName => $villeDispatches): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo md5($villeName); ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?php echo md5($villeName); ?>">
                                    <strong><?php echo htmlspecialchars($villeName); ?></strong>
                                    <span class="badge bg-primary ms-2"><?php echo count($villeDispatches); ?> attribution(s)</span>
                                </button>
                            </h2>
                            <div id="collapse<?php echo md5($villeName); ?>" class="accordion-collapse collapse" 
                                 data-bs-parent="#accordionDispatches">
                                <div class="accordion-body">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Libellé</th>
                                                <th>Quantité reçue</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($villeDispatches as $dispatch): ?>
                                                <tr>
                                                    <td>
                                                        <?php
                                                        $typeClass = [
                                                            'nature' => 'success',
                                                            'materiel' => 'primary',
                                                            'argent' => 'warning'
                                                        ];
                                                        $typeIcon = [
                                                            'nature' => 'fa-leaf',
                                                            'materiel' => 'fa-tools',
                                                            'argent' => 'fa-coins'
                                                        ];
                                                        $class = $typeClass[$dispatch['type']] ?? 'secondary';
                                                        $icon = $typeIcon[$dispatch['type']] ?? 'fa-question';
                                                        ?>
                                                        <span class="badge bg-<?php echo $class; ?>">
                                                            <i class="fas <?php echo $icon; ?>"></i> 
                                                            <?php echo ucfirst($dispatch['type']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($dispatch['libelle']); ?></td>
                                                    <td><strong><?php echo number_format($dispatch['quantite_attribuee'], 0, ',', ' '); ?></strong></td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?php 
                                                            $date = new DateTime($dispatch['date_dispatch']);
                                                            echo $date->format('d/m/Y H:i'); 
                                                            ?>
                                                        </small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
