# 🔄 Système de Réinitialisation - Travail d'Onja

## 📁 Fichiers Créés

### Services
- **`app/services/DataResetService.php`** (329 lignes)
  - Classe principale pour réinitialiser les données
  - Méthodes: `resetAllData()`, `truncateAllTables()`, `insertInitialData()`, `resetCaisse()`
  - Données initiales en constantes: INITIAL_VILLES, INITIAL_BESOINS, INITIAL_DONS
  - Gestion sécurisée avec PDO et transactions

### Contrôleurs
- **`app/controllers/ResetController.php`** (101 lignes)
  - `showResetPage()`: Affiche la page admin
  - `processReset()`: Traite le formulaire POST
  - `apiReset()`: API JSON pour AJAX (avec fix du buffer)

### Vues
- **`app/views/admin/reset.php`** (193 lignes)
  - Interface complète avec confirmation
  - Résumé de la base de données
  - Validation JavaScript
  
- **`app/views/partials/reset_button.php`** (66 lignes)
  - Bouton fixe en haut à droite
  - Appel AJAX vers l'API
  - Simple popup de confirmation

### Documentation
- **`RESET_SYSTEM_README.md`** (185 lignes)
  - Guide complet d'utilisation
  - Architecture du système
  - Points de sécurité

### Scripts de test
- **`public/test_reset_system.php`** (93 lignes)
  - Test CLI avec confirmation
  - Affichage avant/après
  
- **`test_reset_fixed.php`** (66 lignes)
  - Test simplifié pour debug

## 🔧 Fichiers Modifiés

### Routes
- **`app/routes.php`**
  - Ajout require ResetController
  - 3 nouvelles routes:
    - `GET /admin/reset`
    - `POST /admin/reset`
    - `POST /api/reset`

### Layout
- **`app/views/layouts/main.php`**
  - Inclusion du bouton reset: `<?php include ... '/partials/reset_button.php'; ?>`

## ✨ Fonctionnalités Ajoutées

### 1. Données Initiales Automatiques
```php
- 4 villes (Antananarivo, Toamasina, Fianarantsoa, Mahajanga)
- 10 besoins (riz, huile, tôle, clou, aide_financière)
- 5 dons (120 kg riz, 25 tôles, 60L huile, 1,500,000 Ar, 300 clous)
- Caisse: 1,500,000 Ar
```

### 2. Bouton Réinitialisation
- Position fixe en haut à droite sur toutes les pages
- Style: dégradé rouge avec ombre
- Confirmation popup avant action
- Rechargement automatique après succès

### 3. Sécurité
- PDO avec requêtes préparées
- Liste blanche de tables (TABLES_TO_RESET)
- Gestion des clés étrangères
- Fix du buffer pour JSON propre: `ob_end_clean()` + `exit`

### 4. Page Admin Complète
- URL: `/admin/reset`
- Affiche le nombre d'enregistrements par table
- Champ de confirmation (taper "REINITIALISER")
- Double validation (serveur + client)

## 🐛 Bugs Corrigés

1. **Villes non réinsérées**
   - Problème: table `villes` pas dans TABLES_TO_RESET
   - Fix: Ajout de 'villes' + méthode `insertVilles()`

2. **Erreur de transaction**
   - Problème: `TRUNCATE` fait un commit implicite en MySQL
   - Fix: Suppression de `beginTransaction()` et `commit()`

3. **JSON parse error**
   - Problème: Output buffer pollué par Flight
   - Fix: `ob_end_clean()` avant `echo json_encode()`

4. **Bouton caché sur localhost**
   - Demande initiale de cacher sur localhost:8000
   - Fix final: Réactivation du bouton (suppression du check HTTP_HOST)

## 🚀 Utilisation

### Via le bouton
1. Cliquer sur "🔄 Réinitialiser" en haut à droite
2. Confirmer dans le popup
3. ✅ Page rechargée avec données initiales

### Via la page admin
1. Aller sur `/admin/reset`
2. Taper "REINITIALISER" dans le champ
3. Cliquer sur le bouton
4. Double confirmation
5. ✅ Redirection avec message de succès

### Via CLI
```bash
php test_reset_fixed.php
```

## 📊 Résultat Final

Après réinitialisation:
- ✅ 4 villes
- ✅ 10 besoins
- ✅ 5 dons
- ✅ 1 entrée caisse (1,500,000 Ar)
- ✅ 0 achats, 0 dispatch

---

**Date**: 17 février 2026  
**Développeur**: Onja  
**Branche**: dev-onja  
**Commit**: 3036e05
