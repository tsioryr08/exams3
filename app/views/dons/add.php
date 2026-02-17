<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dons">Dons</a></li>
                    <li class="breadcrumb-item active">Ajouter un don</li>
                </ol>
            </nav>

            <!-- En-tête -->
            <h1 class="mb-4"><i class="fas fa-gift"></i> Ajouter un don</h1>

            <!-- Message d'erreur -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="/dons/add">
                        <!-- Type de don -->
                        <div class="mb-3">
                            <label for="type" class="form-label">
                                <i class="fas fa-tag"></i> Type de don <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">-- Sélectionnez un type --</option>
                                <option value="nature" <?php echo (isset($type) && $type === 'nature') ? 'selected' : ''; ?>>
                                    <i class="fas fa-leaf"></i> Nature (riz, huile, etc.)
                                </option>
                                <option value="materiel" <?php echo (isset($type) && $type === 'materiel') ? 'selected' : ''; ?>>
                                    <i class="fas fa-tools"></i> Matériel (tôle, clou, etc.)
                                </option>
                                <option value="argent" <?php echo (isset($type) && $type === 'argent') ? 'selected' : ''; ?>>
                                    <i class="fas fa-coins"></i> Argent (aide financière)
                                </option>
                            </select>
                        </div>

                        <!-- Libellé -->
                        <div class="mb-3">
                            <label for="libelle" class="form-label">
                                <i class="fas fa-file-alt"></i> Libellé <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="libelle" 
                                name="libelle" 
                                placeholder="Ex: riz, tôle, aide_financiere"
                                maxlength="100"
                                value="<?php echo isset($libelle) ? htmlspecialchars($libelle) : ''; ?>"
                                required
                            >
                            <small class="form-text text-muted">
                                Maximum 100 caractères
                            </small>
                        </div>

                        <!-- Quantité -->
                        <div class="mb-4">
                            <label for="quantite" class="form-label">
                                <i class="fas fa-calculator"></i> Quantité <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="quantite" 
                                name="quantite" 
                                placeholder="Ex: 100"
                                min="1"
                                value="<?php echo isset($quantite) ? htmlspecialchars($quantite) : ''; ?>"
                                required
                            >
                            <small class="form-text text-muted">
                                Doit être un nombre positif
                            </small>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="/dons" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer le don
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info box -->
            <div class="alert alert-info mt-4">
                <h6><i class="fas fa-info-circle"></i> Information</h6>
                <ul class="mb-0">
                    <li><strong>Nature :</strong> Produits alimentaires et biens de consommation</li>
                    <li><strong>Matériel :</strong> Équipements et matériaux de construction</li>
                    <li><strong>Argent :</strong> Aides financières en Ariary</li>
                </ul>
            </div>
        </div>
    </div>
</div>
