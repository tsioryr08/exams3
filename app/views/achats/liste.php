<style>
    .liste-achats-container {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .page-title {
        color: #4A3A6A;
        margin-bottom: 30px;
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        letter-spacing: -1px;
    }
    .table-achats {
        border-radius: 10px;
        overflow: hidden;
    }
    .table-achats thead {
        background: linear-gradient(135deg, #B8A8D9 0%, #A8C5E6 100%);
        color: #2C2C2C;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
    }
    .table-achats tbody {
        font-family: 'Inter', sans-serif;
    }
    .badge-nature {
        background-color: #A8D5BA;
        color: #2C5F3F;
        padding: 5px 10px;
        border-radius: 6px;
    }
    .badge-materiel {
        background-color: #FFD4A3;
        color: #8B5A00;
        padding: 5px 10px;
        border-radius: 6px;
    }
    .alert-info {
        background-color: #E8F0F8;
        border-left: 4px solid #A8C5E6;
        color: #2C2C2C;
    }
    .btn-primary {
        background: linear-gradient(135deg, #B8A8D9 0%, #A8C5E6 100%);
        border: none;
        color: #2C2C2C;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #A89BC9 0%, #98B5D6 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(184, 168, 217, 0.3);
        color: #2C2C2C;
    }
</style>

<div class="container">
    <div class="liste-achats-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">Liste des Achats Effectués</h2>
                <p class="text-muted">Historique de tous les achats validés</p>
            </div>
            <div>
                <a href="/achats/besoins-restants" class="btn btn-primary">
                     Nouveaux achats
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
                     Effectuer des achats
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-achats">
                    <thead>
                        <tr>
                            
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
                        <tr style="background: linear-gradient(135deg, #E8DFF5 0%, #DDE9F5 100%); font-weight: bold; color: #4A3A6A;">
                            <td colspan="6" class="text-end" style="font-family: 'Montserrat', sans-serif;">TOTAUX :</td>
                            <td><?= number_format($totalHT, 2, ',', ' ') ?> Ar</td>
                            <td><?= number_format($totalFrais, 2, ',', ' ') ?> Ar</td>
                            <td><?= number_format($totalTTC, 2, ',', ' ') ?> Ar</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <div class="alert alert-info">
                    <strong>Statistiques :</strong>
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
