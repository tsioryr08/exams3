# Modifications du Système d'Achat - Version 3

## 📋 Résumé des changements

Le système d'achat a été complètement revu pour respecter le nouveau processus :

### Ancien processus (Version 2) ❌
1. Afficher les besoins restants **par ville**
2. Acheter pour une ville spécifique
3. Créer un enregistrement dans la table `achats` avec `ville_id`
4. Créer immédiatement un dispatch vers cette ville

**Problème**: Les achats étaient liés directement aux villes, ce qui n'avait pas de sens logique.

### Nouveau processus (Version 3) ✅
1. Afficher les besoins restants **agrégés** (total par type/libellé, sans distinction de ville)
2. Vérifier d'abord les dons disponibles (non dispatchés)
3. Si besoin > dons disponibles, permettre l'achat
4. Les achats validés sont **ajoutés à la table `dons`** (pas encore attribués aux villes)
5. Ensuite, utiliser le système de **dispatch** pour attribuer ces dons (achetés ou non) aux villes

**Avantage**: Logique correcte - on achète globalement, puis on dispatche aux villes selon la méthode choisie.

---

## 🔧 Fichiers modifiés

### 1. **app/repositories/BesoinRepository.php**
- ✅ Ajout méthode `getTotalBesoinsRestantsAgreges()` : Récupère les besoins totaux par type/libellé (sans ville)

### 2. **app/repositories/DonRepository.php**
- ✅ Ajout méthode `getDonsDisponiblesParTypeLibelle()` : Calcule les quantités de dons disponibles (non dispatchés)

### 3. **app/services/AchatService.php**
- ✅ Modification `getBesoinsRestantsAvecArgent()` : 
  - Plus de paramètre `$villeId`
  - Utilise les besoins agrégés
  - Vérifie les dons disponibles
  - Calcule la quantité **à acheter** = besoin restant - dons disponibles
  
- ✅ Modification `simulerAchats($besoinKeys)` :
  - Paramètre changé : `$besoinKeys` (ex: "nature_riz") au lieu de `$besoinIds`
  - Vérifie que le besoin n'est pas déjà couvert par les dons
  - Plus de référence aux villes
  
- ✅ Modification `validerAchats($besoinKeys)` :
  - Crée des dons via `DonRepository->create()` au lieu de créer des achats avec ville_id
  - Ne crée plus de dispatch immédiatement
  - Message de succès : "X achat(s) validé(s) et ajoutés aux dons disponibles"

- ✅ Suppression `verifierDonDirectExistant()` : Méthode obsolète

### 4. **app/controllers/AchatController.php**
- ✅ Modification `showBesoinsRestants()` :
  - Plus de paramètre `$villeId`
  - Plus de récupération des villes pour filtre
  - Plus de variables `villes` et `ville_id_selected` dans la vue

- ✅ Modification `simuler()` et `valider()` :
  - Paramètre changé : `besoin_keys[]` au lieu de `besoin_ids[]`
  
- ✅ Modification `valider()` :
  - Redirige vers `/dons` au lieu de `/achats/liste`

- ✅ Modification `liste()` :
  - Redirige maintenant vers `/dons` avec message info
  - Raison : Les achats sont maintenant dans la table dons

### 5. **app/views/achats/besoins_restants.php**
- ✅ Suppression section filtre par ville
- ✅ Suppression colonne "Ville" du tableau
- ✅ Ajout colonnes :
  - "Total Besoins" : Quantité totale des besoins restants
  - "Dons Dispo." : Quantité de dons disponibles (non dispatchés)
  - "Qté à Acheter" : Quantité réellement à acheter (besoin - dons)
  
- ✅ Changement des inputs : `besoin_keys[]` au lieu de `besoin_ids[]`
- ✅ Statuts badges :
  - "Couvert par dons" (gris) : Le besoin est déjà satisfait par les dons disponibles
  - "Achetable" (vert) : Peut être acheté
  - "Argent insuffisant" (rouge) : Pas assez d'argent

- ✅ Modifications messages :
  - Nouveau texte explicatif sur le processus
  - "Les achats validés seront ajoutés aux dons disponibles"
  - "Ces dons seront ensuite dispatchés aux villes via le système de dispatch"

### 6. **app/views/achats/simulation.php**
- ✅ Suppression colonne "Ville" du tableau
- ✅ Changement des inputs : `besoin_keys[]` au lieu de `besoin_ids[]`
- ✅ Modification message de confirmation : "Ils seront ajoutés aux dons disponibles"

---

## 📊 Flux de données mis à jour

```
BESOINS (par ville)
    ↓
Agrégation par type/libellé
    ↓
BESOINS RESTANTS AGRÉGÉS (sans ville)
    ↓
Vérification DONS DISPONIBLES
    ↓
Si besoin > dons → ACHAT POSSIBLE
    ↓
Validation → Création DON dans table `dons`
    ↓
DISPATCH (avec méthode choisie)
    ↓
Attribution aux VILLES
```

---

## 🚀 Test du nouveau système

### Étapes de test :

1. **Accéder à** : http://localhost:8000/achats/besoins-restants
   - ✅ Plus de filtre par ville
   - ✅ Plus de colonne "Ville"
   - ✅ Colonnes "Total Besoins", "Dons Dispo.", "Qté à Acheter" présentes

2. **Vérifier** :
   - Les besoins sont agrégés par type/libellé
   - Les dons disponibles sont affichés
   - Seule la quantité à acheter (besoin - dons) est calculée

3. **Simuler un achat** :
   - Sélectionner des besoins
   - Cliquer sur "Simuler les achats"
   - ✅ Plus de colonne "Ville" dans la simulation
   - ✅ Message : "Ils seront ajoutés aux dons disponibles"

4. **Valider l'achat** :
   - Confirmer la validation
   - ✅ Redirection vers `/dons`
   - ✅ Le nouveau don apparaît dans la liste

5. **Dispatcher les dons** :
   - Aller sur `/dispatch`
   - Choisir une méthode de dispatch
   - ✅ Les dons achetés sont maintenant disponibles pour dispatch

---

## 💡 Points importants

1. **Table achats** : Toujours présente dans la base mais plus utilisée dans la nouvelle logique. Peut être supprimée ou conservée pour historique.

2. **Route /achats/liste** : Redirige maintenant vers `/dons` car les achats sont dans la table dons.

3. **Logique de calcul** : 
   ```php
   quantite_a_acheter = quantite_restante - dons_disponibles
   montant_achat = quantite_a_acheter * prix_unitaire * (1 + frais_pourcentage)
   ```

4. **Vérification avant achat** : Le système vérifie automatiquement si des dons couvrent déjà le besoin.

---

## ✅ Avantages du nouveau système

- **Logique cohérente** : On achète globalement, pas par ville
- **Flexibilité** : Les dons achetés peuvent être dispatchés selon différentes méthodes
- **Optimisation** : Évite d'acheter ce qui est déjà disponible en dons
- **Transparence** : Affiche clairement les dons disponibles vs quantité à acheter
- **Intégrité** : Un seul point d'entrée pour les dons (achetés ou reçus)

---

*Document créé le 17/02/2026*
*Modifications système d'achat - Version 3*
