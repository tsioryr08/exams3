# UPDATE FRAIS D'ACHAT

## Fichiers créés
*Aucun fichier créé*

## Fichiers modifiés

### 1. app/controllers/AchatController.php
- Ajout de la méthode `updateFrais()` pour gérer la mise à jour du taux de frais d'achat
- Modification de la méthode `showBesoinsRestants()` pour passer les messages de succès/erreur à la vue

### 2. app/routes.php
- Ajout de la route `POST /achats/update-frais` pour traiter la mise à jour du taux

### 3. app/views/achats/besoins_restants.php
- Ajout d'un formulaire inline pour modifier le taux de frais d'achat
- Ajout de l'affichage des messages de succès et d'erreur en haut de page

---

# REFONTE SYSTÈME D'ACHAT - VERSION 3 (17/02/2026)

## 🎯 CONCEPT ET DÉROULEMENT

### Problème identifié dans la version 2
Dans l'ancienne version, le système d'achat fonctionnait de manière incorrecte :
- Les besoins étaient affichés **par ville**
- On pouvait filtrer par ville et acheter pour une ville spécifique
- L'achat créait directement un enregistrement avec `ville_id` dans la table `achats`
- Un dispatch était créé automatiquement pour cette ville spécifique

**❌ Problème** : Cette logique n'avait pas de sens car :
1. On achète des fournitures globalement, pas pour une ville en particulier
2. Les achats ne devraient pas être pré-attribués avant le dispatch
3. Le système de dispatch devenait inutile puisque tout était déjà attribué

### Nouveau concept (Version 3)

#### Le flux correct :
```
1. BESOINS TOTAUX
   ↓ (agréger par type/libellé, sans distinction de ville)
   
2. ÉVALUER LES RESSOURCES DISPONIBLES
   ↓ (vérifier s'il existe déjà des dons pour ce type/libellé)
   
3. CALCUL DES ACHATS NÉCESSAIRES
   ↓ Quantité à acheter = Total besoins - Dons disponibles
   
4. ACHETER (si nécessaire)
   ↓ Créer un DON dans la table `dons` (pas d'attribution aux villes)
   
5. CHOISIR TYPE DE DISPATCH
   ↓ (par date, ordre croissant, proportionnel)
   
6. SIMULER ET VALIDER LE DISPATCH
   ↓ Attribuer les dons (achetés ou reçus) aux villes
   
7. DISPATCH VALIDÉ
   (Les villes reçoivent leurs attributions)
```

#### Principe clé
**Les achats ne sont PAS attribués aux villes** - ils sont ajoutés au pool des dons disponibles, qui seront ensuite dispatchés selon la méthode choisie.

---

## 📁 FICHIERS CRÉÉS

### 1. MODIFICATIONS_ACHAT_V3.md
**Description** : Documentation complète des modifications du système d'achat
**Contenu** :
- Comparaison ancien vs nouveau processus
- Liste détaillée des fichiers modifiés
- Nouveau flux de données
- Guide de test
- Avantages du nouveau système

---

## 📝 FICHIERS MODIFIÉS

### 1. **app/repositories/BesoinRepository.php**

**Modifications** :
- ✅ **Nouvelle méthode** : `getTotalBesoinsRestantsAgreges()`
  ```php
  // Récupère les besoins totaux par type/libellé (sans distinction de ville)
  // Retourne: type, libellé, quantité_totale_besoins, quantité_satisfaite, quantité_restante
  ```

**Logique** :
- Agrège tous les besoins de type `nature` et `materiel` par (type, libellé)
- Calcule la somme des quantités demandées par toutes les villes
- Calcule la somme des quantités déjà dispatchées
- Retourne uniquement les besoins ayant une quantité restante > 0

**Exemple de résultat** :
```
type: nature, libellé: riz, quantité_totale: 240 (100+80+60), quantité_satisfaite: 120, quantité_restante: 120
```

---

### 2. **app/repositories/DonRepository.php**

**Modifications** :
- ✅ **Nouvelle méthode** : `getDonsDisponiblesParTypeLibelle()`
  ```php
  // Récupère les quantités de dons disponibles (non dispatchés) par type/libellé
  // Retourne: tableau associatif [type_libelle => quantité_disponible]
  ```

**Logique** :
- Pour chaque don, calcule : quantité_totale - quantité_dispatchée
- Retourne uniquement les dons ayant une quantité disponible > 0
- Format de clé : "nature_riz", "materiel_tôle", etc.

**Exemple de résultat** :
```php
[
  'nature_riz' => ['type' => 'nature', 'libelle' => 'riz', 'quantite_disponible' => 50],
  'materiel_tôle' => ['type' => 'materiel', 'libelle' => 'tôle', 'quantite_disponible' => 15]
]
```

---

### 3. **app/services/AchatService.php**

**Modifications majeures** :

#### A. `getBesoinsRestantsAvecArgent()` - REFONTE COMPLÈTE
**Avant** :
```php
public function getBesoinsRestantsAvecArgent($villeId = null)
// Acceptait un paramètre ville_id pour filtrer
```

**Après** :
```php
public function getBesoinsRestantsAvecArgent()
// Plus de paramètre ville - travaille sur les totaux agrégés
```

**Nouvelle logique** :
1. Récupère les besoins agrégés via `getTotalBesoinsRestantsAgreges()`
2. Récupère les dons disponibles via `getDonsDisponiblesParTypeLibelle()`
3. Pour chaque besoin :
   - Calcule : `quantite_a_acheter = quantite_restante - don_disponible`
   - Calcule le montant avec frais uniquement sur la quantité à acheter
   - Détermine si achetable : quantité_a_acheter > 0 ET argent suffisant
4. Retourne les besoins avec toutes les informations nécessaires

**Données retournées par besoin** :
- `type` et `libelle`
- `quantite_restante` : Total des besoins non satisfaits
- `don_disponible` : Quantité déjà en stock (non dispatchée)
- `quantite_a_acheter` : Ce qu'il faut vraiment acheter
- `montant_avec_frais` : Coût de l'achat
- `peut_acheter` : Boolean si l'achat est possible
- `besoin_achat` : Boolean si on a vraiment besoin d'acheter

#### B. `simulerAchats($besoinKeys)` - CHANGEMENT DE PARAMÈTRES
**Avant** :
```php
public function simulerAchats($besoinIds)
// Recevait des IDs de besoins (par ville)
```

**Après** :
```php
public function simulerAchats($besoinKeys)
// Reçoit des clés type_libelle (ex: "nature_riz")
```

**Nouvelle logique** :
1. Parcourt les `$besoinKeys` (format: "nature_riz")
2. Trouve le besoin agrégé correspondant
3. Vérifie les dons disponibles pour ce type/libellé
4. Calcule : `quantite_a_acheter = quantite_restante - dons_disponibles`
5. Vérifie que quantite_a_acheter > 0 (sinon erreur : "déjà couvert par les dons")
6. Calcule les montants (HT, frais, TTC)
7. Vérifie la disponibilité de l'argent
8. Retourne la simulation avec liste des achats possibles

**Structure de retour** :
```php
[
  'success' => true/false,
  'errors' => [],
  'achats' => [
    [
      'type' => 'nature',
      'libelle' => 'riz',
      'quantite' => 70,  // Quantité à acheter (besoin - dons)
      'montant_avec_frais' => 185500,
      'besoin_key' => 'nature_riz'
    ]
  ],
  'total_avec_frais' => 185500,
  'argent_restant' => 1314500
]
```

#### C. `validerAchats($besoinKeys)` - CHANGEMENT COMPLET DE LOGIQUE
**Avant** :
```php
// 1. Créait des enregistrements dans table `achats` avec ville_id
// 2. Créait immédiatement des dispatches vers les villes
```

**Après** :
```php
// 1. Crée des enregistrements dans table `dons`
// 2. NE crée PAS de dispatch (sera fait plus tard)
```

**Nouvelle logique** :
1. Simule les achats pour validation
2. Pour chaque achat validé :
   ```php
   $this->donRepo->create(
       $achat['type'],      // 'nature' ou 'materiel'
       $achat['libelle'],   // 'riz', 'tôle', etc.
       $achat['quantite']   // Quantité achetée
   );
   ```
3. Commit de la transaction
4. Retourne un message : "X achat(s) validé(s) et ajoutés aux dons disponibles"

**Important** : 
- ❌ Plus de création dans table `achats`
- ❌ Plus de `ville_id`
- ❌ Plus de dispatch automatique
- ✅ Création de dons qui seront dispatchés plus tard

#### D. Suppression de la méthode obsolète
- ❌ Supprimé : `verifierDonDirectExistant()` 
  - Raison : La nouvelle logique intègre cette vérification directement dans `getBesoinsRestantsAvecArgent()`

---

### 4. **app/controllers/AchatController.php**

**Modifications** :

#### A. `showBesoinsRestants()` - SIMPLIFICATION
**Avant** :
```php
public function showBesoinsRestants() {
    $villeId = $_GET['ville_id'] ?? null;
    $data = $this->achatService->getBesoinsRestantsAvecArgent($villeId);
    
    // Récupération des villes pour le filtre
    $stmt = $pdo->query("SELECT id, nom FROM villes");
    $villes = $stmt->fetchAll();
    
    // Passage des villes et ville_id_selected à la vue
}
```

**Après** :
```php
public function showBesoinsRestants() {
    $data = $this->achatService->getBesoinsRestantsAvecArgent();
    
    // Plus de paramètre ville
    // Plus de récupération des villes
    // Plus de variables 'villes' et 'ville_id_selected'
}
```

#### B. `simuler()` et `valider()` - CHANGEMENT DE PARAMÈTRES
**Avant** :
```php
$besoinIds = $_POST['besoin_ids'] ?? [];  // IDs numériques
```

**Après** :
```php
$besoinKeys = $_POST['besoin_keys'] ?? [];  // Clés type_libelle
```

#### C. `valider()` - CHANGEMENT DE REDIRECTION
**Avant** :
```php
Flight::redirect('/achats/liste');  // Vers la liste des achats
```

**Après** :
```php
Flight::redirect('/dons');  // Vers la liste des dons
```
**Raison** : Les achats sont maintenant dans la table dons

#### D. `liste()` - REDIRECTION
**Avant** :
```php
// Affichait la page achats/liste.php avec tous les achats
```

**Après** :
```php
public function liste() {
    $_SESSION['info'] = 'Les achats validés sont maintenant visibles dans la liste des dons.';
    Flight::redirect('/dons');
}
```
**Raison** : Les achats sont maintenant stockés comme des dons

---

### 5. **app/views/achats/besoins_restants.php**

**Modifications majeures** :

#### A. Suppression section filtre
```html
<!-- ❌ SUPPRIMÉ -->
<div class="filter-section">
    <form method="GET">
        <select name="ville_id">
            <option value="">-- Toutes les villes --</option>
        </select>
    </form>
</div>
```

#### B. Modification en-tête du tableau
**Avant** :
```html
<th>Ville</th>
<th>Type</th>
<th>Libellé</th>
<th>Quantité</th>
```

**Après** :
```html
<th>Type</th>
<th>Libellé</th>
<th>Total Besoins</th>      <!-- Agrégé -->
<th>Dons Dispo.</th>         <!-- Nouveau -->
<th>Qté à Acheter</th>       <!-- Nouveau -->
```

#### C. Modification des lignes du tableau
**Avant** :
```html
<input name="besoin_ids[]" value="<?= $besoin['besoin_id'] ?>">
<td><?= $besoin['ville_nom'] ?></td>
```

**Après** :
```html
<?php
    $besoinKey = $besoin['type'] . '_' . $besoin['libelle'];  // Ex: "nature_riz"
    $needsAchat = $besoin['besoin_achat'] ?? true;
?>
<input name="besoin_keys[]" value="<?= $besoinKey ?>">
<!-- Plus de colonne ville -->
```

#### D. Nouvelles colonnes d'information
```html
<!-- Total des besoins restants (toutes villes confondues) -->
<td><?= number_format($besoin['quantite_restante'], 0) ?></td>

<!-- Dons déjà disponibles (non dispatchés) -->
<td>
    <span class="badge" style="background-color: #17a2b8;">
        <?= number_format($besoin['don_disponible'] ?? 0, 0) ?>
    </span>
</td>

<!-- Quantité qu'il faut vraiment acheter -->
<td>
    <strong><?= number_format($besoin['quantite_a_acheter'] ?? 0, 0) ?></strong>
</td>
```

#### E. Nouveaux badges de statut
```html
<?php if (!$needsAchat): ?>
    <span class="badge" style="background-color: #6c757d;">
        ✓ Couvert par dons
    </span>
<?php elseif ($besoin['peut_acheter']): ?>
    <span class="badge" style="background-color: #28a745;">
        ✓ Achetable
    </span>
<?php else: ?>
    <span class="badge" style="background-color: #dc3545;">
        ✗ Argent insuffisant
    </span>
<?php endif; ?>
```

#### F. Nouvelle section d'information
```html
<div class="alert alert-warning">
    <strong>💡 Bon à savoir :</strong>
    <ul>
        <li>Les achats validés seront <strong>ajoutés aux dons disponibles</strong></li>
        <li>Ces dons seront ensuite <strong>dispatchés aux villes</strong> via le système de dispatch</li>
        <li>Les besoins en gris sont déjà couverts par les dons disponibles</li>
    </ul>
</div>
```

#### G. Changement du bouton retour
**Avant** :
```html
<a href="/achats/liste" class="btn btn-secondary">Voir les achats</a>
```

**Après** :
```html
<a href="/dons" class="btn btn-secondary">↩ Retour aux dons</a>
```

---

### 6. **app/views/achats/simulation.php**

**Modifications** :

#### A. Suppression colonne Ville
**Avant** :
```html
<thead>
    <tr>
        <th>Ville</th>
        <th>Type</th>
        <th>Libellé</th>
    </tr>
</thead>
<tbody>
    <td><?= $achat['ville_nom'] ?></td>
</tbody>
```

**Après** :
```html
<thead>
    <tr>
        <th>Type</th>      <!-- Plus de colonne Ville -->
        <th>Libellé</th>
    </tr>
</thead>
<tbody>
    <!-- Plus de ville_nom -->
</tbody>
```

#### B. Changement des inputs cachés
**Avant** :
```html
<form method="POST" action="/achats/valider">
    <?php foreach ($simulation['achats'] as $achat): ?>
        <input type="hidden" name="besoin_ids[]" value="<?= $achat['besoin_id'] ?>">
    <?php endforeach; ?>
</form>
```

**Après** :
```html
<form method="POST" action="/achats/valider">
    <?php foreach ($simulation['achats'] as $achat): ?>
        <input type="hidden" name="besoin_keys[]" value="<?= $achat['besoin_key'] ?>">
    <?php endforeach; ?>
</form>
```

#### C. Message de confirmation modifié
**Avant** :
```javascript
onclick="return confirm('Êtes-vous sûr de valider ces achats ? Cette action est irréversible.')"
```

**Après** :
```javascript
onclick="return confirm('Êtes-vous sûr de valider ces achats ? Ils seront ajoutés aux dons disponibles.')"
```

---

## 🔄 DÉROULEMENT COMPLET DU PROCESSUS

### ÉTAPE 1 : Consultation des besoins restants
**URL** : `/achats/besoins-restants`

**Traitement** :
1. `BesoinRepository->getTotalBesoinsRestantsAgreges()` récupère les besoins agrégés
2. `DonRepository->getDonsDisponiblesParTypeLibelle()` récupère les dons disponibles
3. Pour chaque besoin agrégé :
   ```
   Riz : Besoin total = 240 kg (100+80+60 des 3 villes)
         Dons disponibles = 50 kg
         À acheter = 190 kg
         Coût = 190 × 2500 × 1.05 = 498,750 Ar
   ```

**Affichage** :
- Tableau avec colonnes : Type, Libellé, Total Besoins, Dons Dispo., Qté à Acheter, Prix, Montant
- Badges colorés : Vert (achetable), Gris (couvert), Rouge (insuffisant)
- **AUCUNE mention de ville** car c'est un total global

### ÉTAPE 2 : Sélection et simulation
**Action** : L'utilisateur coche les besoins à acheter et clique "Simuler"

**Traitement** :
1. POST `besoin_keys[]` = ["nature_riz", "materiel_tôle"]
2. `AchatService->simulerAchats($besoinKeys)` :
   - Vérifie pour chaque clé que la quantité à acheter > 0
   - Calcule les montants (HT, frais, TTC)
   - Vérifie la disponibilité de l'argent
   - Accumule les totaux

**Affichage simulation** :
```
Détails des Achats :
- Riz (nature) : 190 kg × 2,500 Ar = 475,000 Ar + 23,750 Ar (frais) = 498,750 Ar
- Tôle (materiel) : 5 unités × 35,000 Ar = 175,000 Ar + 8,750 Ar (frais) = 183,750 Ar

Total HT : 650,000 Ar
Frais : 32,500 Ar
Total TTC : 682,500 Ar
Argent restant : 817,500 Ar
```

### ÉTAPE 3 : Validation de l'achat
**Action** : L'utilisateur clique "Valider les achats"

**Traitement** :
1. POST `besoin_keys[]` vers `/achats/valider`
2. `AchatService->validerAchats($besoinKeys)` :
   - Re-simule pour vérification
   - Pour chaque achat :
     ```sql
     INSERT INTO dons (type, libelle, quantite) 
     VALUES ('nature', 'riz', 190);
     
     INSERT INTO dons (type, libelle, quantite) 
     VALUES ('materiel', 'tôle', 5);
     ```
   - Commit de la transaction
3. Message de succès : "2 achat(s) validé(s) et ajoutés aux dons disponibles"
4. Redirection vers `/dons`

**Résultat** :
- Les achats apparaissent maintenant dans la liste des dons
- Ils ne sont PAS encore attribués aux villes
- Ils sont disponibles pour être dispatchés

### ÉTAPE 4 : Dispatch aux villes
**URL** : `/dispatch`

**Traitement** :
1. L'utilisateur choisit une méthode de dispatch :
   - Par date (FIFO - premier arrivé, premier servi)
   - Ordre croissant (petites quantités d'abord)
   - Proportionnel (distribution équitable)

2. Le système dispatche TOUS les dons disponibles (achetés + reçus) :
   ```
   Riz disponible : 50 (don initial) + 190 (acheté) = 240 kg
   
   Distribution par date (exemple) :
   - Antananarivo (besoin 100 kg, date 01/02) → reçoit 100 kg
   - Toamasina (besoin 80 kg, date 02/02) → reçoit 80 kg
   - Fianarantsoa (besoin 60 kg, date 03/02) → reçoit 60 kg
   Total dispatché : 240 kg ✓
   ```

3. Création des enregistrements `dispatch` avec attribution aux villes

### ÉTAPE 5 : Résultat final
**État de la base de données** :

```sql
-- Table dons
id | type    | libelle | quantite | date_saisie
1  | nature  | riz     | 50       | 2026-02-16 (don initial)
2  | nature  | riz     | 190      | 2026-02-17 (acheté)

-- Table dispatch (après dispatch par date)
id | don_id | ville_id | libelle | quantite_attribuee
1  | 1      | 1        | riz     | 50
2  | 2      | 1        | riz     | 50
3  | 2      | 2        | riz     | 80
4  | 2      | 3        | riz     | 60

-- Résultat : 
-- Antananarivo : 100 kg (50+50)
-- Toamasina : 80 kg
-- Fianarantsoa : 60 kg
```

---

## 💡 AVANTAGES DU NOUVEAU SYSTÈME

### 1. Logique cohérente
- ✅ On achète globalement, pas pour une ville spécifique
- ✅ Les achats sont traités comme des dons
- ✅ Un seul flux de distribution (dispatch) pour tous les dons

### 2. Flexibilité
- ✅ Les dons achetés peuvent être dispatchés selon différentes méthodes
- ✅ On peut changer de stratégie de distribution sans refaire les achats
- ✅ Optimisation possible de la distribution

### 3. Optimisation des ressources
- ✅ Évite d'acheter ce qui est déjà disponible en dons
- ✅ Affiche clairement : besoin total vs dons disponibles vs quantité à acheter
- ✅ Calcul précis des besoins réels

### 4. Transparence
- ✅ L'utilisateur voit combien de dons sont déjà disponibles
- ✅ L'utilisateur voit exactement ce qu'il faut acheter
- ✅ Messages clairs sur le processus (ajout aux dons → dispatch → attribution)

### 5. Intégrité des données
- ✅ Un seul point d'entrée pour les dons (table `dons`)
- ✅ Pas de duplication de logique entre achats et dons
- ✅ Système de dispatch unifié pour tous les types de dons

---

## 🧪 TESTS À EFFECTUER

### Test 1 : Affichage des besoins agrégés
1. Aller sur `/achats/besoins-restants`
2. Vérifier :
   - ✅ Plus de filtre par ville
   - ✅ Plus de colonne "Ville"
   - ✅ Colonnes "Total Besoins", "Dons Dispo.", "Qté à Acheter" présentes
   - ✅ Les quantités sont agrégées (somme de toutes les villes)

### Test 2 : Vérification des dons disponibles
1. Ajouter un don : Riz 50 kg
2. Aller sur `/achats/besoins-restants`
3. Vérifier :
   - ✅ Colonne "Dons Dispo." affiche 50
   - ✅ Colonne "Qté à Acheter" = Total Besoins - 50
   - ✅ Montant calculé sur la quantité à acheter uniquement

### Test 3 : Simulation d'achat
1. Sélectionner des besoins
2. Cliquer "Simuler"
3. Vérifier :
   - ✅ Plus de colonne "Ville" dans le tableau
   - ✅ Quantités correctes (quantité à acheter, pas totale)
   - ✅ Montants corrects avec frais

### Test 4 : Validation d'achat
1. Cliquer "Valider les achats"
2. Vérifier :
   - ✅ Redirection vers `/dons`
   - ✅ Message de succès affiché
   - ✅ Nouveaux dons visibles dans la liste
   - ✅ Quantités correctes dans la table `dons`

### Test 5 : Dispatch après achat
1. Aller sur `/dispatch`
2. Choisir une méthode
3. Simuler et valider
4. Vérifier :
   - ✅ Les dons achetés sont inclus dans la distribution
   - ✅ Les villes reçoivent leurs attributions
   - ✅ Les quantités totales correspondent

---

## 📊 COMPARAISON AVANT/APRÈS

| Aspect | Version 2 (Avant) | Version 3 (Après) |
|--------|-------------------|-------------------|
| **Vue des besoins** | Par ville (filtrable) | Agrégé (global) |
| **Colonne Ville** | Oui | Non |
| **Paramètres** | `besoin_ids[]` (numériques) | `besoin_keys[]` ("type_libelle") |
| **Vérification dons** | Non | Oui (affiche dons disponibles) |
| **Calcul achat** | Sur besoin total | Sur besoin - dons disponibles |
| **Stockage** | Table `achats` avec `ville_id` | Table `dons` sans ville |
| **Attribution** | Immédiate (auto-dispatch) | Différée (via système dispatch) |
| **Redirection** | `/achats/liste` | `/dons` |
| **Flexibilité** | Faible (pré-attribué) | Élevée (dispatch flexible) |

---

## 🎯 CONCLUSION

Le nouveau système d'achat respecte maintenant la logique métier correcte :

1. **On identifie les besoins globaux** (toutes villes confondues)
2. **On vérifie les ressources disponibles** (dons non dispatchés)
3. **On achète ce qui manque vraiment** (besoin - dons)
4. **Les achats deviennent des dons** (stockés dans table dons)
5. **On dispatche tout selon la méthode choisie** (attribution aux villes)

Cette approche est plus logique, plus flexible, et permet une meilleure gestion des ressources.

---

*Document rédigé le 17 février 2026*
*Refonte complète du système d'achat - Version 3*

