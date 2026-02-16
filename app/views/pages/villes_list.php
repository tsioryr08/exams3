<div class="container">
    <div class="text-center mb-4">
        <p style="font-family: 'Dancing Script', cursive; font-size: 1.3rem; background: linear-gradient(135deg, #8B73D9 0%, #4A8FD9 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-style: italic; margin-bottom: 0; font-weight: 600;">
            "Un petit geste peut réparer un grand malheur"
        </p>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 style="font-family: 'Playfair Display', serif; font-weight: 700; letter-spacing: -1px;">
            </i> Gestion des villes
        </h1>
        <a href="/villes/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter une ville
        </a>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Liste des villes (<?= count($villes) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($villes)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-city fa-3x mb-3"></i>
                    <p>Aucune ville enregistrée</p>
                    <a href="/villes/add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter la première ville
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: linear-gradient(135deg, #B8A8D9 0%, #A8C5E6 100%); color: white; font-family: 'Montserrat', sans-serif; font-weight: 600;">
                            <tr>
                                <th>Nom de la ville</th>
                                <th>Région</th>
                                <th>Date de création</th>
                                <th width="150" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($villes as $ville): ?>
                                <tr>
                                    
                                    <td>
                                        <strong><?= htmlspecialchars($ville['nom']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge-custom">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?= htmlspecialchars($ville['region']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($ville['date_creation'])) ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <a href="/villes/detail/<?= $ville['id'] ?>" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-secondary" 
                                                onclick="confirmDelete(<?= $ville['id'] ?>, '<?= htmlspecialchars($ville['nom']) ?>')"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
</div>

<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="_method" value="DELETE">
</form>

<script>
function confirmDelete(id, nom) {
    if (confirm('Êtes-vous sûr de vouloir supprimer la ville "' + nom + '" ?\n\nAttention : Tous les besoins associés seront également supprimés.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/villes/delete/' + id;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
