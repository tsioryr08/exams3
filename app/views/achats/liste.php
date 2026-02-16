<style>
    .liste-achats-container {
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
    .table-achats {
        border-radius: 10px;
        overflow: hidden;
    }
    .table-achats thead {
        background: var(--beige-primary);
        color: var(--text-dark);
    }
    .badge-nature {
        background-color: #28a745;
        color: white;
    }
    .badge-materiel {
        background-color: #fd7e14;
        color: white;
    }
</style>

<div class="container">
    <div class="liste-achats-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">📋 Liste des Achats Effectués</h2>
                <p class="text-muted">Historique de tous les achats validés</p>
            </div>
            <div>
                <a href="/achats/besoins-restants" class="btn btn-primary">
                    🛒 Nouveaux achats
                </a>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($achats)): ?>
            <div class="text-center py-5">
                <div style="font-size: 80px; margin-bottom: 20px;">🛒</div>
                <h4>Aucun achat effectué</h4>
                <p class="text-muted">Commencez par acheter des besoins avec les dons en argent</p>
                <a href="/achats/besoins-restants" class="btn btn-primary mt-3">
                    🛒 Effectuer des achats
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-achats">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Ville</th>
                            <th>Type</th>
                            <th>Libellé</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Montant HT</th>
                            <th>Frais (<?= !empty($achats) ? $achats[0]['pourcentage_frais'] : 0 ?>%)</th>
                            <th>Montant TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalHT = 0;
                        $totalFrais = 0;
                        $totalTTC = 0;
                        foreach ($achats as $index => $achat): 
                            $totalHT += $achat['montant_total'];
                            $totalFrais += $achat['frais_achat'];
                            $totalTTC += $achat['montant_final'];
                        ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($achat['date_achat'])) ?></td>
                                <td><strong><?= htmlspecialchars($achat['ville_nom']) ?></strong></td>
                                <td>
                                    <span class="badge badge-<?= $achat['type'] ?>">
                                        <?= $achat['type'] === 'nature' ? '🌾' : '🔨' ?>
                                        <?= ucfirst($achat['type']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($achat['libelle']) ?></td>
                                <td><?= number_format($achat['quantite'], 0, ',', ' ') ?></td>
                                <td><?= number_format($achat['prix_unitaire'], 2, ',', ' ') ?> Ar</td>
                                <td><?= number_format($achat['montant_total'], 2, ',', ' ') ?> Ar</td>
                                <td><?= number_format($achat['frais_achat'], 2, ',', ' ') ?> Ar</td>
                                <td><strong><?= number_format($achat['montant_final'], 2, ',', ' ') ?> Ar</strong></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background-color: #f8f9fa; font-weight: bold;">
                            <td colspan="7" class="text-end">TOTAUX :</td>
                            <td><?= number_format($totalHT, 2, ',', ' ') ?> Ar</td>
                            <td><?= number_format($totalFrais, 2, ',', ' ') ?> Ar</td>
                            <td><?= number_format($totalTTC, 2, ',', ' ') ?> Ar</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <div class="alert alert-info">
                    <strong>ℹ️ Statistiques :</strong>
                    <ul class="mb-0">
                        <li>Nombre total d'achats : <strong><?= count($achats) ?></strong></li>
                        <li>Montant total dépensé : <strong><?= number_format($totalTTC, 2, ',', ' ') ?> Ar</strong></li>
                        <li>Total des frais : <strong><?= number_format($totalFrais, 2, ',', ' ') ?> Ar</strong></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
