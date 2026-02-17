<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="mb-3">
                <a href="/villes" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"></i> Ajouter une nouvelle ville</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="/villes/add">
                        <div class="mb-3">
                            <label for="nom_ville" class="form-label">
                                Nom de la ville <span class="text-danger"></span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nom_ville" 
                                   name="nom_ville" 
                                   placeholder="Ex: Antananarivo"
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="region" class="form-label">
                                Région <span class="text-danger"></span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="region" 
                                   name="region" 
                                   placeholder="Ex: Analamanga"
                                   required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                            <a href="/villes" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
        
        </div>
    </div>
</div>
