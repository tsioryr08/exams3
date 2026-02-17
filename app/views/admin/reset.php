<style>
    .reset-container {
        max-width: 800px;
        margin: 50px auto;
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .warning-box {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        text-align: center;
    }
    .warning-box h2 {
        margin: 0 0 15px 0;
        font-size: 2rem;
    }
    .summary-box {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
    }
    .summary-item:last-child {
        border-bottom: none;
    }
    .confirmation-input {
        background: #fff3cd;
        border: 2px solid #ffc107;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .btn-reset {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        font-size: 1.2rem;
        padding: 15px 40px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .btn-reset:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 20px rgba(238, 90, 111, 0.4);
    }
    .btn-reset:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    .details-box {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
    }
</style>

<div class="container">
    <div class="reset-container">
        <div class="warning-box">
            <h2>⚠️ ZONE DANGEREUSE</h2>
            <p style="font-size: 1.1rem; margin: 0;">
                Cette action va SUPPRIMER TOUTES les données et les remplacer par les données initiales
            </p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <h4><?= htmlspecialchars($_SESSION['success']) ?></h4>
                <?php if (isset($_SESSION['reset_details'])): ?>
                    <div class="details-box mt-3">
                        <strong>📊 Détails de la réinitialisation :</strong>
                        <ul class="mb-0 mt-2">
                            <li>Villes : <?= $_SESSION['reset_details']['villes'] ?></li>
                            <li>Besoins : <?= $_SESSION['reset_details']['besoins'] ?></li>
                            <li>Dons : <?= $_SESSION['reset_details']['dons'] ?></li>
                            <li>Caisse initiale : <?= number_format($_SESSION['reset_details']['caisse_initiale'], 0, ',', ' ') ?> Ar</li>
                        </ul>
                    </div>
                    <?php unset($_SESSION['reset_details']); ?>
                <?php endif; ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="summary-box">
            <h4 class="mb-3">📊 État actuel de la base de données</h4>
            <?php if (!empty($summary)): ?>
                <?php foreach ($summary as $table => $count): ?>
                    <div class="summary-item">
                        <span><strong><?= ucfirst($table) ?></strong></span>
                        <span class="badge bg-primary"><?= $count ?> enregistrement(s)</span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>

        <form method="POST" action="/admin/reset" id="resetForm">
            <div class="confirmation-input">
                <label class="form-label">
                    <strong>⚠️ Pour confirmer, tapez : <code>REINITIALISER</code></strong>
                </label>
                <input 
                    type="text" 
                    name="confirmation" 
                    id="confirmationInput"
                    class="form-control form-control-lg" 
                    placeholder="Tapez REINITIALISER"
                    autocomplete="off"
                    required
                >
            </div>

            <div class="d-flex gap-3 justify-content-between">
                <a href="/" class="btn btn-secondary btn-lg">
                    ← Annuler
                </a>
                <button 
                    type="submit" 
                    class="btn-reset"
                    id="resetButton"
                    disabled
                >
                    🔄 Réinitialiser toutes les données
                </button>
            </div>
        </form>

        <div class="mt-4 text-muted" style="font-size: 0.9rem;">
            <strong>ℹ️ Cette opération va :</strong>
            <ul>
                <li>Vider les tables : dispatch, achats_besoins, dons, besoins, caisse_historique</li>
                <li>Réinsérer les 4 villes de test</li>
                <li>Réinsérer 11 besoins initiaux</li>
                <li>Réinsérer 5 dons initiaux</li>
                <li>Réinitialiser la caisse à <?= number_format(1500000, 0, ',', ' ') ?> Ar</li>
            </ul>
        </div>
    </div>
</div>

<script>
// Activer le bouton uniquement si la confirmation est correcte
document.getElementById('confirmationInput').addEventListener('input', function(e) {
    const button = document.getElementById('resetButton');
    const value = e.target.value.trim();
    
    if (value === 'REINITIALISER') {
        button.disabled = false;
        button.style.background = 'linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%)';
    } else {
        button.disabled = true;
        button.style.background = '#ccc';
    }
});

// Confirmation supplémentaire avant soumission
document.getElementById('resetForm').addEventListener('submit', function(e) {
    const confirmed = confirm(
        '⚠️ DERNIÈRE CONFIRMATION ⚠️\n\n' +
        'Êtes-vous ABSOLUMENT SÛR de vouloir réinitialiser TOUTES les données ?\n\n' +
        'Cette action est IRRÉVERSIBLE !'
    );
    
    if (!confirmed) {
        e.preventDefault();
    }
});
</script>
