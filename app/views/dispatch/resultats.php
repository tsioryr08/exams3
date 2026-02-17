<style>
    .resultat-container {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .page-title {
        font-family: 'Playfair Display', serif;
        color: #4A3A6A;
        margin-bottom: 10px;
        font-weight: 700;
    }
    .page-subtitle {
        font-family: 'Montserrat', sans-serif;
        color: #6c757d;
        margin-bottom: 30px;
        font-weight: 500;
    }
    .stats-card {
        background: linear-gradient(135deg, #B8A8D9 0%, #A8C5E6 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
    }
    .stats-card h5 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        margin-bottom: 15px;
    }
    .stat-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    .stat-item:last-child {
        border-bottom: none;
    }
    .stat-value {
        font-weight: 700;
        font-size: 1.1rem;
    }
    .table-resultat thead {
        background: linear-gradient(135deg, #B8A8D9 0%, #A8C5E6 100%);
        color: #2C2C2C;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
    }
    .badge-attribue {
        background-color: #A8D5BA;
        color: #2C5F3F;
        padding: 5px 10px;
        border-radius: 6px;
    }
    .badge-non-attribue {
        background-color: #FFD4A3;
        color: #8B5A00;
        padding: 5px 10px;
        border-radius: 6px;
    }
    .section-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        color: #4A3A6A;
        margin-top: 40px;
        margin-bottom: 20px;
    }
    .alert-reste {
        background-color: #FFF3CD;
        border-left: 4px solid #FFE5A3;
        color: #856404;
        padding: 15px;
        border-radius: 8px;
    }
</style>

<div class="container">
    <div class="resultat-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title">Résultats du Dispatch</h2>
                <p class="page-subtitle"><?= htmlspecialchars($methode_nom) ?></p>
            </div>
            <a href="/dispatch" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <!-- Statistiques globales -->
        <div class="stats-card">
            <h5><i class="fas fa-chart-bar"></i> Statistiques</h5>
            <div class="stat-item">
                <span>Total dons traités :</span>
                <span class="stat-value"><?= $stats['total_dons'] ?></span>
            </div>
            <div class="stat-item">
                <span>Dons attribués :</span>
                <span class="stat-value"><?= $stats['dons_attribues'] ?></span>
            </div>
            <div class="stat-item">
                <span>Quantité totale distribuée :</span>
                <span class="stat-value"><?= number_format($stats['quantite_distribuee'], 0, ',', ' ') ?></span>
            </div>
            <?php if ($stats['quantite_reste'] > 0): ?>
            <div class="stat-item">
                <span>Quantité restante :</span>
                <span class="stat-value text-warning"><?= number_format($stats['quantite_reste'], 0, ',', ' ') ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Alertes pour les restes -->
        <?php if (!empty($restes)): ?>
        <div class="alert-reste mb-4">
            <h6 class="fw-bold"><i class="fas fa-exclamation-triangle"></i> Quantités non distribuées</h6>
            <ul class="mb-0 mt-2">
                <?php foreach ($restes as $reste): ?>
                    <li>
                        <strong><?= htmlspecialchars($reste['libelle']) ?></strong> (<?= htmlspecialchars($reste['type']) ?>) : 
                        <?= number_format($reste['quantite'], 0, ',', ' ') ?> unités non attribuées
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Tableau des attributions -->
        <h5 class="section-title">Attributions par Ville</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Don #</th>
                        <th>Type</th>
                        <th>Libellé</th>
                        <th>Ville</th>
                        <?php if (isset($resultats[0]['date_saisie'])): ?>
                        <th>Date de saisie</th>
                        <?php endif; ?>
                        <?php if (isset($resultats[0]['quantite_demandee'])): ?>
                        <th>Qté demandée</th>
                        <?php endif; ?>
                        <th>Quantité attribuée</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($resultats)): ?>
                        <tr>
                            <td colspan="<?= isset($resultats[0]['date_saisie']) || isset($resultats[0]['quantite_demandee']) ? '8' : '6' ?>" class="text-center text-muted">
                                Aucune attribution effectuée
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($resultats as $resultat): ?>
                            <tr>
                                <td><span class="badge bg-secondary">#<?= $resultat['don_id'] ?></span></td>
                                <td>
                                    <span class="badge" style="background-color: 
                                        <?= $resultat['type'] === 'nature' ? '#A8D5BA' : 
                                            ($resultat['type'] === 'materiel' ? 'burlywood' : '#FFE5A3') ?>; 
                                        color: #333;">
                                        <?= ucfirst($resultat['type']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($resultat['libelle']) ?></td>
                                <td><strong><?= htmlspecialchars($resultat['ville_nom']) ?></strong></td>
                                <?php if (isset($resultat['date_saisie'])): ?>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-calendar"></i> 
                                        <?= date('d/m/Y H:i', strtotime($resultat['date_saisie'])) ?>
                                    </small>
                                </td>
                                <?php endif; ?>
                                <?php if (isset($resultat['quantite_demandee'])): ?>
                                <td><?= number_format($resultat['quantite_demandee'], 0, ',', ' ') ?></td>
                                <?php endif; ?>
                                <td><?= number_format($resultat['quantite_attribuee'], 0, ',', ' ') ?></td>
                                <td>
                                    <?php if ($resultat['quantite_attribuee'] > 0): ?>
                                        <span class="badge-attribue">Attribué</span>
                                    <?php else: ?>
                                        <span class="badge-non-attribue">Non attribué</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Détails des besoins par ville -->
        <?php if (!empty($details_par_ville)): ?>
        <h5 class="section-title">Détails des Besoins par Ville</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Ville</th>
                        <th>Type</th>
                        <th>Libellé</th>
                        <th>Qté demandée</th>
                        <th>Qté reçue</th>
                        <th>Taux</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $currentVille = '';
                    foreach ($details_par_ville as $detail): 
                        $showVille = ($currentVille !== $detail['ville_nom']);
                        $currentVille = $detail['ville_nom'];
                        $taux = $detail['quantite_demandee'] > 0 ? ($detail['quantite_recue'] / $detail['quantite_demandee']) * 100 : 0;
                        $badgeColor = $taux >= 75 ? '#A8D5BA' : ($taux >= 50 ? '#FFE5A3' : '#FFD4A3');
                    ?>
                        <tr>
                            <td><?= $showVille ? '<strong>'.htmlspecialchars($detail['ville_nom']).'</strong>' : '' ?></td>
                            <td>
                                <span class="badge" style="background-color: 
                                    <?= $detail['type'] === 'nature' ? '#A8D5BA' : 
                                        ($detail['type'] === 'materiel' ? 'burlywood' : '#FFE5A3') ?>; 
                                    color: #333;">
                                    <?= ucfirst($detail['type']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($detail['libelle']) ?></td>
                            <td><?= number_format($detail['quantite_demandee'], 0, ',', ' ') ?></td>
                            <td><?= number_format($detail['quantite_recue'], 0, ',', ' ') ?></td>
                            <td>
                                <span class="badge" style="background-color: <?= $badgeColor ?>; color: #333;">
                                    <?= number_format($taux, 1) ?>%
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Détail par ville -->
        <?php if (!empty($par_ville)): ?>
        <h5 class="section-title">Récapitulatif par Ville</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ville</th>
                        <th>Quantité demandée</th>
                        <th>Quantité reçue</th>
                        <th>Taux de satisfaction</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($par_ville as $ville): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($ville['nom']) ?></strong></td>
                            <td><?= number_format($ville['demandee'], 0, ',', ' ') ?></td>
                            <td><?= number_format($ville['recue'], 0, ',', ' ') ?></td>
                            <td>
                                <?php 
                                $taux = $ville['demandee'] > 0 ? ($ville['recue'] / $ville['demandee']) * 100 : 0;
                                $badgeColor = $taux >= 75 ? '#A8D5BA' : ($taux >= 50 ? '#FFE5A3' : '#FFD4A3');
                                ?>
                                <span class="badge" style="background-color: <?= $badgeColor ?>; color: #333;">
                                    <?= number_format($taux, 1) ?>%
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Bouton de réinitialisation -->
        <div class="mt-5 text-center">
            <a href="/dispatch" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Retour aux méthodes
            </a>
            <button id="btnReset" class="btn btn-danger">
                <i class="fas fa-redo"></i> Réinitialiser ces résultats
            </button>
        </div>

    </div>
</div>

<div id="alertReset" class="alert" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnReset = document.getElementById('btnReset');
    const alertReset = document.getElementById('alertReset');

    btnReset.addEventListener('click', function() {
        if (!confirm('Êtes-vous sûr de vouloir réinitialiser tous les dispatches ? Cette action supprimera toutes les attributions.')) {
            return;
        }

        btnReset.disabled = true;
        btnReset.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Réinitialisation...';

        fetch('/dispatch/reinitialiser', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Dispatches réinitialisés avec succès !', 'success');
                // Rediriger vers la page d'index (ne pas recharger car ça re-dispatch !)
                setTimeout(() => {
                    window.location.href = '/dispatch';
                }, 1500);
            } else {
                showAlert(data.message || 'Erreur lors de la réinitialisation', 'danger');
                btnReset.disabled = false;
                btnReset.innerHTML = '<i class="fas fa-redo"></i> Réinitialiser ces résultats';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Erreur de connexion', 'danger');
            btnReset.disabled = false;
            btnReset.innerHTML = '<i class="fas fa-redo"></i> Réinitialiser ces résultats';
        });
    });

    function showAlert(message, type) {
        alertReset.className = `alert alert-${type}`;
        alertReset.textContent = message;
        alertReset.style.display = 'block';

        setTimeout(() => {
            alertReset.style.display = 'none';
        }, 3000);
    }
});
</script>
