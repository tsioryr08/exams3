<style>
    .list-container {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .page-title {
        color: var(--beige-primary);
        margin-bottom: 30px;
        font-weight: 600;
    }
    .table {
        border-radius: 10px;
        overflow: hidden;
    }
    .table thead {
        background: var(--beige-primary);
        color: var(--text-dark);
    }
    .badge {
        padding: 5px 10px;
        border-radius: 5px;
    }
    .badge-nature {
        background-color: #28a745;
        color: white;
    }
    .badge-materiel {
        background-color: #fd7e14;
        color: white;
    }
    .badge-argent {
        background-color: #ffc107;
        color: #333;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    .empty-state i {
        font-size: 80px;
        margin-bottom: 20px;
    }
    .total-row {
        background-color: #f8f9fa;
        font-weight: bold;
    }
</style>

<div class="container">
    <div class="list-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">📋 Liste des Besoins</h2>
                <p class="text-muted">Tous les besoins enregistrés dans le système</p>
            </div>
            <div>
                <a href="/besoins" class="btn btn-primary">
                    ➕ Nouveau besoin
                </a>
            </div>
        </div>

        <?php if (empty($besoins)): ?>
            <div class="empty-state">
                <div class="mb-3">📦</div>
                <h4>Aucun besoin enregistré</h4>
                <p>Commencez par ajouter un nouveau besoin</p>
                <a href="/besoins" class="btn btn-primary mt-3">
                    ➕ Ajouter un besoin
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>🏙️ Ville</th>
                            <th>📦 Type</th>
                            <th>📝 Libellé</th>
                            <th>💵 Prix unitaire</th>
                            <th>🔢 Quantité</th>
                            <th>💰 Total</th>
                            <th>📅 Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grandTotal = 0;
                        foreach ($besoins as $index => $besoin): 
                            $total = $besoin['prix_unitaire'] * $besoin['quantite'];
                            $grandTotal += $total;
                        ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= htmlspecialchars($besoin['ville']) ?></strong></td>
                                <td>
                                    <?php
                                    $badgeClass = 'badge-' . $besoin['type'];
                                    $icon = match($besoin['type']) {
                                        'nature' => '🌾',
                                        'materiel' => '🔨',
                                        'argent' => '💰',
                                        default => '📦'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= $icon ?> <?= ucfirst($besoin['type']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($besoin['libelle']) ?></td>
                                <td><?= number_format($besoin['prix_unitaire'], 2, ',', ' ') ?> Ar</td>
                                <td><?= number_format($besoin['quantite'], 0, ',', ' ') ?></td>
                                <td><strong><?= number_format($total, 2, ',', ' ') ?> Ar</strong></td>
                                <td><?= date('d/m/Y', strtotime($besoin['date_saisie'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="6" class="text-end">TOTAL GÉNÉRAL :</td>
                            <td colspan="2">
                                <strong><?= number_format($grandTotal, 2, ',', ' ') ?> Ar</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <div class="alert alert-info">
                    <strong>ℹ️ Statistiques :</strong>
                    <ul class="mb-0">
                        <li>Nombre total de besoins : <strong><?= count($besoins) ?></strong></li>
                        <li>Montant total : <strong><?= number_format($grandTotal, 2, ',', ' ') ?> Ar</strong></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
