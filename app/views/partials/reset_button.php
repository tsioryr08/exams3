<!-- Bouton Réinitialiser en haut de page -->
<style>
.reset-button-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}
.btn-reset-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: bold;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    cursor: pointer;
    transition: all 0.3s;
}
.btn-reset-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.5);
}
</style>

<div class="reset-button-container">
    <button class="btn-reset-danger" onclick="resetData()">
        🔄 Réinitialiser
    </button>
</div>

<script>
function resetData() {
    if (!confirm('⚠️ Réinitialiser TOUTES les données ?\n\nCette action est irréversible !')) {
        return;
    }
    
    const btn = event.target;
    btn.innerHTML = '🔄 En cours...';
    btn.disabled = true;
    
    fetch('/api/reset', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'confirmation=REINITIALISER'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            window.location.reload();
        } else {
            alert('❌ ' + data.message);
            btn.innerHTML = '🔄 Réinitialiser';
            btn.disabled = false;
        }
    })
    .catch(error => {
        alert('❌ Erreur: ' + error.message);
        btn.innerHTML = '🔄 Réinitialiser';
        btn.disabled = false;
    });
}
</script>
