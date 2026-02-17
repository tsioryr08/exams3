# 🔄 Système de Réinitialisation des Données

## 📋 Description

Système complet et sécurisé pour réinitialiser toutes les données de l'application BNGRC à leur état initial.

## 🎯 Fonctionnalités

### ✅ Ce qui est réinitialisé :
- **Tables vidées** : `dispatch`, `achats_besoins`, `dons`, `besoins`, `caisse_historique`, `historique_totaux`
- **Données réinsérées** :
  - 4 villes (Antananarivo, Toamasina, Fianarantsoa, Mahajanga)
  - 11 besoins initiaux (riz, huile, tôle, clou, aide financière)
  - 5 dons initiaux (120 kg riz, 25 tôles, 60L huile, 1 500 000 Ar, 300 clous)
  - Caisse réinitialisée à 1 500 000 Ar

### 🔒 Sécurité

- Protection contre les injections SQL avec PDO préparé
- Liste blanche de tables autorisées
- Double confirmation requise (texte + popup JavaScript)
- Utilisation de transactions pour garantir l'intégrité
- Gestion des contraintes de clés étrangères

## 🚀 Utilisation

### Accès à la page de réinitialisation

**URL** : `http://localhost:8000/admin/reset`

### Procédure de réinitialisation

1. **Accéder** à `/admin/reset`
2. **Vérifier** le résumé de l'état actuel de la base
3. **Taper** exactement `REINITIALISER` dans le champ de confirmation
4. **Cliquer** sur "Réinitialiser toutes les données"
5. **Confirmer** dans le popup JavaScript
6. ✅ **Message de succès** avec détails de la réinitialisation

## 📂 Structure des fichiers

```
app/
├── services/
│   └── DataResetService.php      # Service principal de réinitialisation
├── controllers/
│   └── ResetController.php       # Contrôleur pour les routes
└── views/
    └── admin/
        └── reset.php              # Interface utilisateur
```

## 🔧 Architecture

### DataResetService.php

Classe principale avec les méthodes :
- `resetAllData()` : Fonction principale de réinitialisation
- `truncateAllTables()` : Vider les tables de manière sécurisée
- `insertInitialData()` : Réinsérer les données initiales
- `resetCaisse()` : Réinitialiser la caisse
- `tableExists()` : Vérifier l'existence d'une table
- `getDatabaseSummary()` : Obtenir un résumé de la base

### ResetController.php

Contrôleur avec les actions :
- `showResetPage()` : Afficher la page de réinitialisation
- `processReset()` : Traiter la requête POST
- `apiReset()` : API JSON optionnelle pour AJAX

### reset.php

Interface utilisateur avec :
- Résumé de l'état actuel de la base
- Champ de confirmation avec validation JavaScript
- Double confirmation (texte + popup)
- Affichage des détails après réinitialisation

## 📊 Données initiales

### Villes (4)
```php
Antananarivo (Analamanga)
Toamasina (Atsinanana)
Fianarantsoa (Haute Matsiatra)
Mahajanga (Boeny)
```

### Besoins (11)
```php
- Antananarivo: riz (100 kg), huile (50 L), tôle (30)
- Toamasina: riz (80 kg), clou (500), aide_financiere (1 000 000 Ar)
- Fianarantsoa: riz (60 kg), tôle (20)
- Mahajanga: huile (40 L), aide_financiere (500 000 Ar)
```

### Dons (5)
```php
- riz: 120 kg
- tôle: 25
- huile: 60 L
- aide_financiere: 1 500 000 Ar
- clou: 300
```

### Caisse initiale
```php
1 500 000 Ar (somme des besoins de type "argent")
```

## 🛡️ Points de sécurité

1. **Protection SQL** : Utilisation exclusive de requêtes préparées PDO
2. **Liste blanche** : Seules les tables explicitement listées peuvent être vidées
3. **Transactions** : Rollback automatique en cas d'erreur
4. **Validation** : Vérification de la confirmation côté serveur
5. **Logging** : Toutes les erreurs sont loggées

## 🔗 Routes disponibles

```php
GET  /admin/reset  →  Afficher la page de réinitialisation
POST /admin/reset  →  Exécuter la réinitialisation
POST /api/reset    →  API JSON (optionnel)
```

## 📝 Exemple d'utilisation API

```javascript
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
        console.log('Réinitialisation réussie!');
        console.log('Détails:', data.details);
    } else {
        console.error('Erreur:', data.message);
    }
});
```

## ⚠️ Important

- Cette opération est **IRRÉVERSIBLE**
- Toutes les données existantes seront **SUPPRIMÉES**
- Les villes existantes sont **CONSERVÉES** pour maintenir les relations
- Recommandé de faire une **sauvegarde** avant utilisation en production

## 🎨 Personnalisation

Pour ajouter d'autres données initiales, modifiez les constantes dans `DataResetService.php` :

```php
private const INITIAL_BESOINS = [
    // Vos données ici
];
```

## 🐛 Dépannage

### Erreur "Table doesn't exist"
→ Vérifiez que toutes les tables listées existent dans votre base de données

### Erreur "Foreign key constraint"
→ Les contraintes FK sont automatiquement désactivées/réactivées

### Confirmation ne fonctionne pas
→ Vérifiez que JavaScript est activé dans votre navigateur

## 📞 Support

Pour toute question ou problème, consultez les logs d'erreur PHP.

---

✅ **Système prêt à l'emploi!**

Accédez à `/admin/reset` pour commencer.
