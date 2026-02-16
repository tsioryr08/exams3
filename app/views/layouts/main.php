<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'BNGRC Donating'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --beige-primary: #D4C2A1;
            --beige-light: #E8DCC8;
            --beige-pale: #F5F0E8;
            --beige-accent: #D5CD90;    
            --brown-dark: #3B341F;        
            --white: #FFFFFF;
            --text-dark: #2C2C2C;
            --text-medium: #666666;
            --shadow: rgba(179, 165, 138, 0.15);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--white);
            color: var(--text-dark);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }
        
        .navbar {
            background-color: var(--white) !important;
            box-shadow: 0 1px 3px var(--shadow);
            border-bottom: 1px solid var(--beige-light);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            color: var(--text-dark) !important;
            font-weight: 600;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            transition: color 0.3s ease;
        }
        
        .navbar-brand:hover {
            color: var(--beige-primary) !important;
        }
        
        .nav-link {
            color: var(--text-medium) !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
            border-radius: 6px;
            margin: 0 0.25rem;
        }
        
        .nav-link:hover {
            color: var(--text-dark) !important;
            background-color: var(--beige-pale);
        }
        
        .main-content {
            flex: 1;
            padding: 3rem 0;
            background-color: var(--beige-pale);
        }
        
        footer {
            background-color: var(--beige-primary);
            color: var(--text-dark);
            padding: 2rem 0;
            margin-top: auto;
            border-top: 1px solid var(--beige-light);
        }
        
        footer p {
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        footer small {
            color: var(--text-medium);
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background-color: var(--beige-primary);
            border: none;
            color: var(--text-dark);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }
        
        .btn-primary:hover {
            background-color: #C5B393;
            color: var(--text-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--shadow);
        }
        
        .btn-outline-secondary {
            border: 2px solid var(--beige-light);
            color: var(--text-dark);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--beige-pale);
            border-color: var(--beige-primary);
            color: var(--text-dark);
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px var(--shadow);
            background-color: var(--white);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 4px 16px var(--shadow);
        }
        
        .badge-custom {
            background-color: var(--beige-light);
            color: var(--text-dark);
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            font-weight: 400;
        }
        
        .alert-success {
            background-color: #F0FFF4;
            color: #22543D;
            border-left: 3px solid #38A169;
        }
        
        .alert-danger {
            background-color: #FFF5F5;
            color: #C53030;
            border-left: 3px solid #C53030;
        }
        
        .alert-info {
            background-color: var(--beige-pale);
            color: var(--text-dark);
            border-left: 3px solid var(--beige-primary);
        }
        
        h1, h2, h3, h4, h5 {
            font-weight: 600;
            letter-spacing: -0.5px;
            color: var(--text-dark);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-hands-helping"></i> BNGRC Donating
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">
                            <i class="fas fa-home"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dons">
                            <i class="fas fa-gift"></i> Dons
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dispatch">
                            <i class="fas fa-exchange-alt"></i> Dispatch
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/besoins">
                            <i class="fas fa-clipboard-list"></i> Besoins
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/besoins/list">
                            <i class="fas fa-list"></i> Liste des besoins
                        </a>
                    </li>
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/logout">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/login">
                                <i class="fas fa-user-shield"></i> Administration
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main class="main-content">
        <?php echo $content; ?>
    </main>

   <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <p style="font-weight: 600; margin-bottom: 0.5rem;">BNGRC Donating</p>
                    <small>Système de gestion des dons pour les sinistrés</small>
                </div>
                <div class="col-md-12">
                    <div style="background-color: rgba(255,255,255,0.2); padding: 1rem; border-radius: 8px; display: inline-block;">
                        
                        <small>ETU004184:Tsiory - ETU003966:Njary - ETU003941:Onja</small>
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    <small>&copy; <?php echo date('Y'); ?> Tous droits réservés</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>