<style>
    .dispatch-container {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .page-title {
        font-family: 'Playfair Display', serif;
        color: #4A3A6A;
        margin-bottom: 20px;
        font-weight: 700;
    }
    .page-subtitle {
        font-family: 'Inter', sans-serif;
        color: #6c757d;
        margin-bottom: 40px;
    }
    .dispatch-card {
        border: 2px solid #e0e0e0;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
        background: white;
    }
    .dispatch-card:hover {
        border-color: #B8A8D9;
        box-shadow: 0 8px 25px rgba(184, 168, 217, 0.2);
        transform: translateY(-5px);
    }
    .dispatch-card h4 {
        font-family: 'Montserrat', sans-serif;
        color: #4A3A6A;
        font-weight: 600;
        margin-bottom: 15px;
    }
    .dispatch-card p {
        font-family: 'Inter', sans-serif;
        color: #6c757d;
        margin-bottom: 0;
    }
    .dispatch-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #B8A8D9 0%, #A8C5E6 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        margin-bottom: 20px;
    }
    .btn-reset {
        background: linear-gradient(135deg, #E89B95 0%, #F0B575 100%);
        border: none;
        color: white;
        padding: 12px 30px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .btn-reset:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(232, 155, 149, 0.4);
        color: white;
    }
    .alert-reset {
        display: none;
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
    }
</style>

<div class="container">
    <div class="dispatch-container">
        <h2 class="page-title">Méthodes de Dispatch</h2>
        <p class="page-subtitle">Choisissez une méthode de distribution des dons aux villes</p>

        <div class="row">
            <div class="col-md-4">
                <a href="/dispatch/par-date" style="text-decoration: none; color: inherit;">
                    <div class="dispatch-card">
                        <div class="dispatch-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h4>Dispatch par Date</h4>
                        <p>Les villes qui ont enregistré leurs besoins en premier sont servies en priorité (FIFO - First In First Out)</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="/dispatch/ordre-croissant" style="text-decoration: none; color: inherit;">
                    <div class="dispatch-card">
                        <div class="dispatch-icon">
                            <i class="fas fa-sort-amount-up"></i>
                        </div>
                        <h4>Dispatch par Ordre Croissant</h4>
                        <p>Priorité aux villes ayant les demandes les plus faibles. Les petites demandes sont satisfaites en premier</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="/dispatch/proportionnel" style="text-decoration: none; color: inherit;">
                    <div class="dispatch-card">
                        <div class="dispatch-icon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <h4>Dispatch Proportionnel</h4>
                        <p>Distribution équitable selon la formule : (demande / total) × quantité disponible. Partie entière inférieure uniquement</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

