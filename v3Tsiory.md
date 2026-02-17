## Pages créées (version 3)
- `app/views/dispatch/resultats.php` - Page complète d'affichage des résultats de dispatch avec 3 sections (attributions, détails par ville, récapitulatif)
- `database/verifier_dispatch.sql` - Script SQL de vérification des dispatches
- `EXPLICATION_DISPATCH.md` - Documentation complète du système de dispatch

## Pages modifiées (version 3)

### 1. **`app/views/dispatch/index.php`**
**Modifications :**
- Retiré le bouton "Réinitialiser" et son code JavaScript complet (déplacé vers resultats.php)
- Retiré la div alertReset et le script de gestion
- Interface simplifiée avec 3 cartes cliquables pour sélectionner la méthode

### 2. **`app/views/dispatch/resultats.php`**
**Modifications :**
- Ajout colonne "Date de saisie" conditionnelle avec format dd/mm/yyyy hh:mm (visible uniquement pour méthode par date)
- Ajout colonne "Qté demandée" conditionnelle (visible uniquement pour méthode ordre croissant)
- Ajout section "Détails des Besoins par Ville" avec tableau complet :
  - Ville (groupée visuellement)
  - Type (badge coloré)
  - Libellé
  - Qté demandée
  - Qté reçue
  - Taux de satisfaction par besoin
- Ajout bouton "Réinitialiser ces résultats" en bas de page avec Ajax
- Modification du JavaScript de réinitialisation :
  - Confirmation avant suppression
  - Redirection vers /dispatch après succès (au lieu de reload)
  - Messages d'erreur/succès avec alertes temporaires

### 3. **`app/services/DispatchService.php`**
**Modifications :**

**Méthode dispatchParDate() :**
- Ajout du tri des attributions par date_saisie ASC (ordre chronologique)
- Ajout de date_saisie dans le tableau $resultats['attributions']
- Utilisation de getStatistiquesParVilleAvecDate() au lieu de getStatistiquesParVille()
- Tri du récapitulatif par_ville par premiere_date ASC
- Ajout de $resultats['details_par_ville'] = $this->getDetailsParVille()

**Méthode dispatchOrdreCroissant() :**
- Ajout du tri des attributions par quantite_demandee ASC (petites quantités en premier)
- Ajout de quantite_demandee dans le tableau $resultats['attributions']
- Tri du récapitulatif par_ville par demandee ASC (petites demandes en premier)
- Ajout de $resultats['details_par_ville'] = $this->getDetailsParVille()

**Méthode dispatchProportionnel() :**
- Ajout du tri des attributions par proportion DESC (grandes proportions en premier)
- Ajout de proportion dans le tableau $resultats['attributions']
- Tri du récapitulatif par_ville par demandee DESC (grandes demandes en premier)
- Ajout de $resultats['details_par_ville'] = $this->getDetailsParVille()

**Méthode getStatistiquesParVille() :**
- Modification du calcul : SUM(b.quantite) au lieu de SUM(b.quantite * b.prix_unitaire)
- Modification du calcul : SUM(d.quantite_attribuee) au lieu de SUM(d.quantite_attribuee * b.prix_unitaire)
- Calcul basé sur les quantités pures, pas sur la valeur monétaire

**Méthode getStatistiquesParVilleAvecDate() (nouvelle) :**
- Même calcul que getStatistiquesParVille() avec quantités pures
- Ajout de MIN(b.date_saisie) as premiere_date pour permettre le tri chronologique des villes

**Méthode getDetailsParVille() (nouvelle) :**
- Requête SQL pour récupérer les détails de chaque besoin par ville
- Colonnes : ville_nom, type, libelle, quantite_demandee, quantite_recue
- GROUP BY sur ville et besoin individuel
- ORDER BY ville_nom, type, libelle

### 4. **`app/controllers/DispatchController.php`**
**Modifications :**
- Méthode afficherResultats() : Ajout de 'details_par_ville' => $resultats['details_par_ville'] ?? [] dans le render
- Cette variable est maintenant transmise à la vue pour afficher le tableau détaillé

### 5. **`app/routes.php`** (version 3 initiale - pas de modifications supplémentaires)
- Routes dispatch : GET /dispatch, /dispatch/par-date, /dispatch/ordre-croissant, /dispatch/proportionnel
- Route POST /dispatch/reinitialiser

### 6. **`app/repositories/BesoinRepository.php`** (version 3 initiale - pas de modifications supplémentaires)
- getByTypeAndLibelleOrderByDate() - Tri par date_saisie ASC
- getByTypeAndLibelleOrderByQuantiteAsc() - Tri par quantite ASC

## Fonctionnalités implémentées (version 3 complète)

### 1. **Tri intelligent des résultats selon la méthode**
- **Par date :** 
  - Attributions triées chronologiquement (date_saisie ASC)
  - Récapitulatif par ville trié par date de première saisie (FIFO)
  - Colonne "Date de saisie" visible dans le tableau
  
- **Ordre croissant :** 
  - Attributions triées par quantité demandée ASC (petites en premier)
  - Récapitulatif par ville trié par quantité totale demandée ASC
  - Colonne "Qté demandée" visible dans le tableau
  
- **Proportionnel :** 
  - Attributions triées par proportion DESC (grandes proportions en premier)
  - Récapitulatif par ville trié par quantité totale demandée DESC

### 2. **Affichage détaillé des résultats**
Trois sections dans la page de résultats :

**a) Tableau des attributions :**
- Don #, Type, Libellé, Ville
- Date de saisie (si méthode par date)
- Qté demandée (si méthode ordre croissant)
- Quantité attribuée, Statut

**b) Détails des Besoins par Ville (NOUVEAU) :**
- Ville (groupée), Type (badge), Libellé
- Qté demandée, Qté reçue
- Taux de satisfaction individuel par besoin
- Permet de voir en détail : "Antananarivo a demandé 100 riz et reçu 40 (40%)"

**c) Récapitulatif par Ville :**
- Quantité totale demandée (toutes catégories confondues)
- Quantité totale reçue
- Taux de satisfaction global de la ville

### 3. **Affichage conditionnel des colonnes**
- Colonne "Date de saisie" : visible uniquement pour dispatch par date
- Colonne "Qté demandée" : visible uniquement pour dispatch ordre croissant
- Adaptation automatique du colspan pour "Aucune attribution"

### 4. **Bouton réinitialiser**
- Positionné en bas de la page de résultats (avec bouton Retour)
- Style : btn-danger (rouge) pour indiquer action destructive
- Confirmation obligatoire avant suppression
- Appel Ajax à POST /dispatch/reinitialiser
- Redirection automatique vers /dispatch après succès (1.5s)
- Messages d'erreur/succès avec alertes temporaires (3s)
- Désactivation du bouton pendant l'opération avec spinner

### 5. **Calcul des statistiques (quantités pures)**
**Important : Basé uniquement sur les quantités, pas sur l'argent**
- Quantité demandée = SUM(quantite) de tous les besoins
- Quantité reçue = SUM(quantite_attribuee) de tous les dispatches
- Taux de satisfaction = (recue / demandee) × 100
- Exemple : 100 riz + 30 tôle + 50 huile = 180 unités demandées

### 6. **Affichage des restes**
- Section "Quantités non distribuées" si restes > 0
- Liste des articles avec quantités non attribuées
- Causé par FLOOR() dans méthode proportionnelle

## Structure des données retournées

Chaque méthode de dispatch retourne un tableau avec :
```php
[
    'attributions' => [
        ['don_id', 'type', 'libelle', 'ville_nom', 'quantite_attribuee', 
         'date_saisie' (si par date), 
         'quantite_demandee' (si ordre croissant),
         'proportion' (si proportionnel)]
    ],
    'restes' => [
        ['type', 'libelle', 'quantite']
    ],
    'stats' => [
        'total_dons', 'dons_attribues', 'quantite_distribuee', 'quantite_reste'
    ],
    'par_ville' => [
        ['nom', 'demandee', 'recue', 'premiere_date' (si par date)]
    ],
    'details_par_ville' => [
        ['ville_nom', 'type', 'libelle', 'quantite_demandee', 'quantite_recue']
    ]
]
```