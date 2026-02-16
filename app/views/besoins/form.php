<style>
    .form-container {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        max-width: 600px;
        margin: 0 auto;
    }
    .form-title {
        color: var(--beige-primary);
        margin-bottom: 30px;
        font-weight: 600;
    }
    .btn-primary {
        background: var(--beige-primary);
        border: none;
        padding: 12px 40px;
    }
    .btn-primary:hover {
        background: var(--brown-dark);
    }
    .form-label {
        font-weight: 500;
        color: #333;
    }
</style>

<div class="container">
    <div class="form-container">
        <div class="text-center mb-4">
            <h2 class="form-title"> Nouveau Besoin</h2>
            <p class="text-muted">Enregistrer un nouveau besoin pour une ville sinistrée</p>
        </div>

        <?php if (isset($success) && !empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Succès !</strong> <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>❌ Erreurs :</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="/besoins" method="POST">
            <!-- Ville -->
            <div class="mb-3">
                <label for="ville_id" class="form-label">🏙️ Ville </label>
                <select class="form-select" id="ville_id" name="ville_id" required>
                    <option value="">-- Sélectionner une ville --</option>
                    <?php foreach ($villes as $ville): ?>
                        <option value="<?= $ville['id'] ?>" 
                            <?= (isset($old['ville_id']) && $old['ville_id'] == $ville['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ville['nom']) ?> (<?= htmlspecialchars($ville['region']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Type -->
            <div class="mb-3">
                <label for="type" class="form-label">📦 Type de besoin </label>
                <select class="form-select" id="type" name="type" required>
                    <option value="">-- Sélectionner un type --</option>
                    <option value="nature" <?= (isset($old['type']) && $old['type'] == 'nature') ? 'selected' : '' ?>>
                        🌾 Nature (riz, huile, etc.)
                    </option>
                    <option value="materiel" <?= (isset($old['type']) && $old['type'] == 'materiel') ? 'selected' : '' ?>>
                        🔨 Matériel (tôle, clou, etc.)
                    </option>
                    <option value="argent" <?= (isset($old['type']) && $old['type'] == 'argent') ? 'selected' : '' ?>>
                        💰 Argent (aide financière)
                    </option>
                </select>
            </div>

            <!-- Libellé -->
            <div class="mb-3">
                <label for="libelle" class="form-label">📝 Libellé </label>
                <input type="text" 
                       class="form-control" 
                       id="libelle" 
                       name="libelle" 
                       placeholder="Ex: riz, huile, tôle, aide financière..." 
                       value="<?= htmlspecialchars($old['libelle'] ?? '') ?>"
                       required>
                <small class="text-muted">Description du besoin (minimum 3 caractères)</small>
            </div>

            <!-- Prix unitaire -->
            <div class="mb-3">
                <label for="prix_unitaire" class="form-label">💵 Prix unitaire (Ar) </label>
                <input type="number" 
                       class="form-control" 
                       id="prix_unitaire" 
                       name="prix_unitaire" 
                       min="0" 
                       step="0.01" 
                       placeholder="Ex: 2500"
                       value="<?= htmlspecialchars($old['prix_unitaire'] ?? '') ?>"
                       required>
            </div>

            <!-- Quantité -->
            <div class="mb-3">
                <label for="quantite" class="form-label">🔢 Quantité </label>
                <input type="number" 
                       class="form-control" 
                       id="quantite" 
                       name="quantite" 
                       min="1" 
                       placeholder="Ex: 100"
                       value="<?= htmlspecialchars($old['quantite'] ?? '') ?>"
                       required>
            </div>

            <!-- Date -->
            <div class="mb-4">
                <label for="date_saisie" class="form-label">📅 Date de saisie </label>
                <input type="date" 
                       class="form-control" 
                       id="date_saisie" 
                       name="date_saisie" 
                       value="<?= htmlspecialchars($old['date_saisie'] ?? date('Y-m-d')) ?>"
                       required>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                     Enregistrer le besoin
                </button>
                <a href="/besoins/list" class="btn btn-outline-secondary">
                    📋 Voir la liste des besoins
                </a>
            </div>
        </form>
    </div>
</div>
