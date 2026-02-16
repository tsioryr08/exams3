<style>
    .simulation-container {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .simulation-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    .simulation-result {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .result-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
    }
    .result-item:last-child {
        border-bottom: none;
        font-weight: bold;
        font-size: 1.2rem;
    }
    .table-simulation {
        border-radius: 10px;
        overflow: hidden;
    }
    .table-simulation thead {
        background: var(--beige-primary);
    }
    .alert-simulation {
        border-left: 4px solid;
    }
    .alert-simulation.success {
        border-left-color: #28a745;
        background-color: #d4edda;
    }
    .alert-simulation.error {
        border-left-color: #dc3545;
        background-color: #f8d7da;
    }
</style>

<div class="container">
    <div class="simulation-container">
        <div class="simulation-header text-center">
            <h2>🔍 Simulation d'Achats</h2>
            <p class="mb-0">Voici le résultat de votre simulation</p>
        </div>

        <?php if (!$simulation['success'] || !empty($simulation['errors'])): ?>
            <div class="alert alert-simulation error">
                <h5>❌ Erreurs détectées</h5>
                <ul class="mb-0">
                    <?php foreach ($simulation['errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($simulation['achats'])): ?>
            <div class="alert alert-simulation success mb-4">
                <h5>✅ Simulation réussie !</h5>
                <p class="mb-0"><?= count($simulation['achats']) ?> achat(s) peuvent être effectués</p>
            </div>

            <!-- Résumé financier -->
            <div class="simulation-result">
                <h5 class="mb-3">💰 Résumé Financier</h5>
                <div class="result-item">
                    <span>Montant total HT :</span>
                    <span><?= number_format($simulation['total_sans_frais'], 2, ',', ' ') ?> Ar</span>
                </div>
                <div class="result-item">
                    <span>Frais d'achat :</span>
                    <span><?= number_format($simulation['total_frais'], 2, ',', ' ') ?> Ar</span>
                </div>
                <div class="result-item">
                    <span>Montant total TTC :</span>
                    <span><?= number_format($simulation['total_avec_frais'], 2, ',', ' ') ?> Ar</span>
                </div>
                <div class="result-item">
                    <span>Argent disponible :</span>
                    <span><?= number_format($simulation['argent_disponible'], 2, ',', ' ') ?> Ar</span>
                </div>
                <div class="result-item">
                    <span>Argent après achat :</span>
                    <span class="<?= $simulation['argent_restant'] < 0 ? 'text-danger' : 'text-success' ?>">
                        <?= number_format($simulation['argent_restant'], 2, ',', ' ') ?> Ar
                    </span>
                </div>
            </div>

            <!-- Détails des achats -->
            <h5 class="mt-4 mb-3">📋 Détails des Achats</h5>
            <div class="table-responsive">
                <table class="table table-hover table-simulation">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ville</th>
                            <th>Type</th>
                            <th>Libellé</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Montant HT</th>
                            <th>Frais</th>
                            <th>Montant TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($simulation['achats'] as $index => $achat): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
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
                                <td><?= number_format($achat['montant_sans_frais'], 2, ',', ' ') ?> Ar</td>
                                <td><?= number_format($achat['frais'], 2, ',', ' ') ?> Ar</td>
                                <td><strong><?= number_format($achat['montant_avec_frais'], 2, ',', ' ') ?> Ar</strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Actions -->
            <div class="mt-4 d-flex justify-content-between">
                <a href="/achats/besoins-restants" class="btn btn-secondary">
                    ← Retour
                </a>
                <?php if ($simulation['success']): ?>
                    <form method="POST" action="/achats/valider" style="display:inline;">
                        <?php foreach ($simulation['achats'] as $achat): ?>
                            <input type="hidden" name="besoin_ids[]" value="<?= $achat['besoin_id'] ?>">
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-success" onclick="return confirm('Êtes-vous sûr de valider ces achats ? Cette action est irréversible.')">
                            ✅ Valider les achats
                        </button>
                    </form>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">
                <h5>⚠️ Aucun achat possible</h5>
                <p class="mb-0">Aucun achat ne peut être effectué avec les sélections actuelles.</p>
            </div>
            <div class="text-center mt-4">
                <a href="/achats/besoins-restants" class="btn btn-primary">← Retour aux besoins</a>
            </div>
        <?php endif; ?>
    </div>
</div>
