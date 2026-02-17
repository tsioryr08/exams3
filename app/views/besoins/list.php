<style>
    .list-container {
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
    .table {
        border-radius: 10px;
        overflow: hidden;
    }
    .table thead {
        background: linear-gradient(135deg, #B8A8D9 0%, #A8C5E6 100%);
        color: #2C2C2C;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
    }
    .table tbody {
        font-family: 'Inter', sans-serif;
    }
    .badge {
        padding: 5px 10px;
        border-radius: 6px;
    }
    .badge-nature {
        background-color: #A8D5BA;
        color: #2C5F3F;
    }
    .badge-materiel {
        background-color: burlywood;
        color: #8B5A00;
    }
    .badge-argent {
        background-color: #FFE5A3;
        color: #8B7000;
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
        background: linear-gradient(135deg, #E8DFF5 0%, #DDE9F5 100%);
        font-weight: bold;
        color: #4A3A6A;
        font-family: 'Montserrat', sans-serif;
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
    <div class="list-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">Liste des Besoins</h2>
                
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
                           
                            <th> Ville</th>
                            <th>Type</th>
                            <th> Libellé</th>
                            <th> Prix unitaire</th>
                            <th> Quantité</th>
                            <th> Total</th>
                            <th> Date</th>
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
                                
                                <td><strong><?= htmlspecialchars($besoin['ville']) ?></strong></td>
                                <td>
                                    <?php
                                    $badgeClass = 'badge-' . $besoin['type'];
                                    if ($besoin['type'] === 'nature') {
                                        $icon = '🌾';
                                    } elseif ($besoin['type'] === 'materiel') {
                                        $icon = '🔨';
                                    } elseif ($besoin['type'] === 'argent') {
                                        $icon = '💰';
                                    } else {
                                        $icon = '📦';
                                    }
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
                            <td colspan="5" class="text-end">TOTAL GÉNÉRAL :</td>
                            <td colspan="2">
                                <strong><?= number_format($grandTotal, 2, ',', ' ') ?> Ar</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <div class="alert alert-info">
                    <strong>Statistiques :</strong>
                    <ul class="mb-0">
                        <li>Nombre total de besoins : <strong><?= count($besoins) ?></strong></li>
                        <li>Montant total : <strong><?= number_format($grandTotal, 2, ',', ' ') ?> Ar</strong></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
