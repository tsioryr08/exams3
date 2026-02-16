<style>
    .recap-container {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .page-title {
        font-family: 'Playfair Display', serif;
        color: #4A3A6A;
        margin-bottom: 30px;
        font-weight: 600;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }
    .stat-card h4 {
        font-size: 1rem;
        margin-bottom: 10px;
        opacity: 0.9;
    }
    .stat-card .montant {
        font-size: 2rem;
        font-weight: bold;
    }
    .stat-card.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .stat-card.warning {
        background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
    }
    .stat-card.info {
        background: linear-gradient(135deg, #7B5438 0%, #C9B18F 100%);
    }
    .progress-custom {
        height: 30px;
        border-radius: 15px;
        background-color: #e9ecef;
        overflow: hidden;
    }
    .progress-bar-custom {
        height: 100%;
        background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        transition: width 0.6s ease;
    }
    .table-villes {
        border-radius: 10px;
        overflow: hidden;
    }
    .table-villes thead {
        background: var(--beige-primary);
    }
    .btn-actualiser {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 10px 25px;
        font-weight: bold;
        border-radius: 8px;
    }
    .btn-actualiser:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    .btn-actualiser:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<div class="container">
    <div class="recap-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">Récapitulatif Global</h2>
                <p class="text-muted">Vue d'ensemble des besoins, dons et achats</p>
            </div>
            <div>
                <button id="btnActualiser" class="btn btn-actualiser">
                    🔄 Actualiser
                </button>
            </div>
        </div>

        <!-- Statistiques principales -->
        <div class="row" id="statsContainer">
            <div class="col-md-4">
                <div class="stat-card">
                    <h4>💰 Besoins Totaux</h4>
                    <div class="montant" id="besoins-totaux">
                        <?= $stats['globales']['besoins_totaux']['montant_format'] ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card success">
                    <h4>✅ Besoins Satisfaits</h4>
                    <div class="montant" id="besoins-satisfaits">
                        <?= $stats['globales']['besoins_satisfaits']['montant_format'] ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card warning">
                    <h4>⏳ Besoins Restants</h4>
                    <div class="montant" id="besoins-restants">
                        <?= $stats['globales']['besoins_restants']['montant_format'] ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barre de progression -->
        <div class="mb-4">
            <h5 class="mb-3">📈 Taux de Satisfaction</h5>
            <div class="progress-custom">
                <div 
                    class="progress-bar-custom" 
                    id="progress-bar"
                    style="width: <?= $stats['globales']['pourcentage_satisfaction'] ?>%"
                >
                    <span id="progress-text"><?= $stats['globales']['pourcentage_satisfaction'] ?>%</span>
                </div>
            </div>
        </div>

        <!-- Informations sur l'argent -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card info">
                    <h4>💵 Dons en Argent</h4>
                    <div class="montant" id="dons-argent">
                        <?= $stats['globales']['dons_argent']['total_format'] ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #C76B66 0%, #D18A4F 100%);">
                    <h4>🛒 Argent Utilisé (Achats)</h4>
                    <div class="montant" id="argent-utilise">
                        <?= $stats['globales']['dons_argent']['utilise_format'] ?>
                    </div>
                    <small>Nombre d'achats : <span id="nb-achats"><?= $stats['globales']['achats']['nombre'] ?></span></small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card success">
                    <h4>💳 Argent Disponible</h4>
                    <div class="montant" id="argent-disponible">
                        <?= $stats['globales']['dons_argent']['disponible_format'] ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques par ville -->
        <h5 class="mt-5 mb-3">🏙️ Statistiques par Ville</h5>
        <div class="table-responsive">
            <table class="table table-hover table-villes">
                <thead>
                    <tr>
                        <th>Ville</th>
                        <th>Région</th>
                        <th>Besoins Totaux</th>
                        <th>Besoins Satisfaits</th>
                        <th>Besoins Restants</th>
                        <th>% Satisfaction</th>
                    </tr>
                </thead>
                <tbody id="table-villes-body">
                    <?php foreach ($stats['par_ville'] as $ville): 
                        $pourcentage = $ville['besoins_total'] > 0 
                            ? round(($ville['besoins_satisfaits'] / $ville['besoins_total']) * 100, 2) 
                            : 0;
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($ville['ville_nom']) ?></strong></td>
                            <td><?= htmlspecialchars($ville['region']) ?></td>
                            <td><?= number_format($ville['besoins_total'], 2, ',', ' ') ?> Ar</td>
                            <td><?= number_format($ville['besoins_satisfaits'], 2, ',', ' ') ?> Ar</td>
                            <td><?= number_format($ville['besoins_restants'], 2, ',', ' ') ?> Ar</td>
                            <td>
                                <span class="badge" style="background-color: <?= $pourcentage >= 75 ? '#28a745' : ($pourcentage >= 50 ? '#ffc107' : '#dc3545') ?>">
                                    <?= $pourcentage ?>%
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-4">
            <strong>ℹ️ Information :</strong>
            Cliquez sur le bouton "Actualiser" pour mettre à jour les statistiques en temps réel
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnActualiser = document.getElementById('btnActualiser');
    
    btnActualiser.addEventListener('click', function() {
        actualiserStats();
    });

    function actualiserStats() {
        btnActualiser.disabled = true;
        btnActualiser.innerHTML = '⏳ Actualisation...';

        fetch('/recap/ajax')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const stats = data.data;
                    
                    // Mettre à jour les montants
                    document.getElementById('besoins-totaux').textContent = stats.globales.besoins_totaux.montant_format;
                    document.getElementById('besoins-satisfaits').textContent = stats.globales.besoins_satisfaits.montant_format;
                    document.getElementById('besoins-restants').textContent = stats.globales.besoins_restants.montant_format;
                    
                    // Mettre à jour la barre de progression
                    const progressBar = document.getElementById('progress-bar');
                    const progressText = document.getElementById('progress-text');
                    const pourcentage = stats.globales.pourcentage_satisfaction;
                    progressBar.style.width = pourcentage + '%';
                    progressText.textContent = pourcentage + '%';
                    
                    // Mettre à jour les infos argent
                    document.getElementById('dons-argent').textContent = stats.globales.dons_argent.total_format;
                    document.getElementById('argent-utilise').textContent = stats.globales.dons_argent.utilise_format;
                    document.getElementById('argent-disponible').textContent = stats.globales.dons_argent.disponible_format;
                    document.getElementById('nb-achats').textContent = stats.globales.achats.nombre;
                    
                    // Mettre à jour le tableau des villes
                    const tbody = document.getElementById('table-villes-body');
                    tbody.innerHTML = '';
                    stats.par_ville.forEach(ville => {
                        const pourcentageVille = ville.besoins_total > 0 
                            ? Math.round((ville.besoins_satisfaits / ville.besoins_total) * 100 * 100) / 100
                            : 0;
                        const badgeColor = pourcentageVille >= 75 ? '#28a745' : (pourcentageVille >= 50 ? '#ffc107' : '#dc3545');
                        
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${ville.ville_nom}</strong></td>
                            <td>${ville.region}</td>
                            <td>${formatNumber(ville.besoins_total)} Ar</td>
                            <td>${formatNumber(ville.besoins_satisfaits)} Ar</td>
                            <td>${formatNumber(ville.besoins_restants)} Ar</td>
                            <td>
                                <span class="badge" style="background-color: ${badgeColor}">
                                    ${pourcentageVille}%
                                </span>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                    
                    // Afficher un message de succès
                    showToast(' Statistiques actualisées avec succès !');
                } else {
                    showToast('❌ Erreur lors de l\'actualisation', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showToast('❌ Erreur de connexion', 'error');
            })
            .finally(() => {
                btnActualiser.disabled = false;
                btnActualiser.innerHTML = '🔄 Actualiser';
            });
    }

    function formatNumber(number) {
        return new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(number);
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : 'success'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
});
</script>
