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
                    <?php if (empty($besoinsVille)): ?>
                        <p class="text-muted text-center py-3">
                            <i class="fas fa-info-circle"></i> 
                            Aucun besoin enregistré pour cette ville
                        </p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Libellé</th>
                                        <th>Quantité</th>
                                        <th>Prix unitaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($besoinsVille as $besoin): ?>
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
                                                $class = $typeClass[$besoin['type']] ?? 'secondary';
                                                $icon = $typeIcon[$besoin['type']] ?? 'fa-question';
                                                ?>
                                                <span class="badge bg-<?php echo $class; ?>">
                                                    <i class="fas <?php echo $icon; ?>"></i> 
                                                    <?php echo ucfirst($besoin['type']); ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($besoin['libelle']) ?></td>
                                            <td><strong><?= number_format($besoin['quantite'], 0, ',', ' ') ?></strong></td>
                                            <td><?= number_format($besoin['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-gift"></i> Dons reçus</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($dispatches)): ?>
                        <p class="text-muted text-center py-3">
                            <i class="fas fa-info-circle"></i> 
                            Aucun don reçu pour cette ville
                        </p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Libellé</th>
                                        <th>Quantité</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dispatches as $dispatch): ?>
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
                                            <td><?= htmlspecialchars($dispatch['libelle']) ?></td>
                                            <td><strong><?= number_format($dispatch['quantite_attribuee'], 0, ',', ' ') ?></strong></td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php 
                                                    $date = new DateTime($dispatch['date_dispatch']);
                                                    echo $date->format('d/m/Y'); 
                                                    ?>
                                                </small>
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
</div>
