<style>
    .achats-container {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .page-header {
        margin-bottom: 30px;
    }
    .page-title {
        color: var(--beige-primary);
        font-weight: 600;
    }
    .info-card {
        background: linear-gradient(135deg, #B8A8D9 0%, #A8C5E6 100%);
        color: #2C2C2C;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        border: 1px solid rgba(184, 168, 217, 0.3);
    }
    .info-card h4 {
        margin-bottom: 10px;
        font-size: 1.1rem;
        color: #5A4A7A;
    }
    .info-card .montant {
        font-size: 2rem;
        font-weight: bold;
        color: #4A3A6A;
    }
    .filter-section {
        background: #F5F0E8;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 1px solid #E8DCC8;
    }
    .table-besoins {
        border-radius: 10px;
        overflow: hidden;
    }
    .table-besoins thead {
        background: var(--beige-primary);
        color: var(--text-dark);
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
    .achetable {
        background-color: #E8F5E9;
    }
    .non-achetable {
        background-color: #FFE5E8;
        opacity: 0.8;
    }
    .btn-simuler {
        background: linear-gradient(135deg, #B8A8D9 0%, #A8C5E6 100%);
        border: none;
        color: #2C2C2C;
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .btn-simuler:hover {
        background: linear-gradient(135deg, #A89BC9 0%, #98B5D6 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(184, 168, 217, 0.3);
        color: #2C2C2C;
    }
</style>

<div class="container">
    <div class="achats-container">
        <div class="page-header">
            <h2 class="page-title mb-2">🛒 Achats - Besoins Restants</h2>
            <p class="text-muted">Sélectionnez les besoins à acheter avec les dons en argent. Les achats seront ajoutés aux dons disponibles.</p>
        </div>

        <div class="info-card">
            <h4>Argent Disponible</h4>
            <div class="montant"><?= number_format($argent_disponible, 2, ',', ' ') ?> Ar</div>
            <small>Frais d'achat appliqués : <?= $frais_pourcentage ?>%</small>
        </div>

        <?php if (empty($besoins)): ?>
            <div class="alert alert-info">
                <strong>ℹ️ Information :</strong> Aucun besoin restant à acheter.
                Tous les besoins sont déjà satisfaits ou couverts par les dons disponibles !
            </div>
        <?php else: ?>
            <form method="POST" action="/achats/simuler" id="formAchats">
                <div class="table-responsive">
                    <table class="table table-hover table-besoins">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="selectAll" title="Tout sélectionner">
                                </th>
                                <th>Type</th>
                                <th>Libellé</th>
                                <th>Total Besoins</th>
                                <th>Dons Dispo.</th>
                                <th>Qté à Acheter</th>
                                <th>Prix unitaire</th>
                                <th>Montant HT</th>
                                <th>Frais (<?= $frais_pourcentage ?>%)</th>
                                <th>Montant TTC</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($besoins as $besoin): 
                                $besoinKey = $besoin['type'] . '_' . $besoin['libelle'];
                                $needsAchat = $besoin['besoin_achat'] ?? true;
                            ?>
                                <tr class="<?= $besoin['peut_acheter'] ? 'achetable' : 'non-achetable' ?>">
                                    <td>
                                        <input 
                                            type="checkbox" 
                                            name="besoin_keys[]" 
                                            value="<?= htmlspecialchars($besoinKey) ?>"
                                            class="besoin-checkbox"
                                            <?= !$besoin['peut_acheter'] || !$needsAchat ? 'disabled' : '' ?>
                                        >
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $besoin['type'] ?>">
                                            <?= $besoin['type'] === 'nature' ? '🌾' : '🔨' ?>
                                            <?= ucfirst($besoin['type']) ?>
                                        </span>
                                    </td>
                                    <td><strong><?= htmlspecialchars($besoin['libelle']) ?></strong></td>
                                    <td><?= number_format($besoin['quantite_restante'], 0, ',', ' ') ?></td>
                                    <td>
                                        <span class="badge" style="background-color: #17a2b8; color: white;">
                                            <?= number_format($besoin['don_disponible'] ?? 0, 0, ',', ' ') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?= number_format($besoin['quantite_a_acheter'] ?? 0, 0, ',', ' ') ?></strong>
                                    </td>
                                    <td><?= number_format($besoin['prix_unitaire'], 2, ',', ' ') ?> Ar</td>
                                    <td><?= number_format(($besoin['quantite_a_acheter'] ?? 0) * $besoin['prix_unitaire'], 2, ',', ' ') ?> Ar</td>
                                    <td><?= number_format($besoin['frais_achat'], 2, ',', ' ') ?> Ar</td>
                                    <td><strong><?= number_format($besoin['montant_avec_frais'], 2, ',', ' ') ?> Ar</strong></td>
                                    <td>
                                        <?php if (!$needsAchat): ?>
                                            <span class="badge" style="background-color: #6c757d;">✓ Couvert par dons</span>
                                        <?php elseif ($besoin['peut_acheter']): ?>
                                            <span class="badge" style="background-color: #28a745;">✓ Achetable</span>
                                        <?php else: ?>
                                            <span class="badge" style="background-color: #dc3545;">✗ Argent insuffisant</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span id="selectedCount" class="text-muted">0 besoin(s) sélectionné(s)</span>
                    </div>
                    <div>
                        <a href="/dons" class="btn btn-secondary me-2">↩ Retour aux dons</a>
                        <button type="submit" class="btn btn-simuler" id="btnSimuler" disabled>
                            Simuler les achats
                        </button>
                    </div>
                </div>
            </form>

            <div class="alert alert-warning mt-3">
                <strong>💡 Bon à savoir :</strong> 
                <ul class="mb-0 mt-2">
                    <li>Les achats avec frais de <?= $frais_pourcentage ?>% seront déduits de l'argent disponible</li>
                    <li>Les achats validés seront <strong>ajoutés aux dons disponibles</strong></li>
                    <li>Ces dons seront ensuite <strong>dispatchés aux villes</strong> via le système de dispatch</li>
                    <li>Les besoins en gris sont déjà couverts par les dons disponibles</li>
                    <li>Les besoins en rouge ne peuvent pas être achetés (argent insuffisant)</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Gestion de la sélection multiple
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.besoin-checkbox');
    const btnSimuler = document.getElementById('btnSimuler');
    const selectedCount = document.getElementById('selectedCount');

    function updateUI() {
        const checkedCount = document.querySelectorAll('.besoin-checkbox:checked').length;
        selectedCount.textContent = checkedCount + ' besoin(s) sélectionné(s)';
        btnSimuler.disabled = checkedCount === 0;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                if (!cb.disabled) {
                    cb.checked = this.checked;
                }
            });
            updateUI();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateUI);
    });

    updateUI();
});
</script>
