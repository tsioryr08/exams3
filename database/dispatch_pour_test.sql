-- ============================================
-- SCRIPT : Dispatcher les dons pour test achats
-- ============================================
-- À exécuter APRÈS base.sql

USE bngrc;

-- ============================================
-- DISPATCHER TOUS LES DONS DIRECTS
-- ============================================

-- 1. Dispatcher le riz (120 kg disponible)
INSERT INTO dispatch (don_id, ville_id, libelle, quantite_attribuee) VALUES
(1, 1, 'riz', 100),  -- Antananarivo : 100 kg (besoin satisfait)
(1, 2, 'riz', 20);   -- Toamasina : 20 kg (reste 60 kg de besoin)
-- Don riz ÉPUISÉ (120 - 120 = 0)

-- 2. Dispatcher les tôles (25 disponibles)
INSERT INTO dispatch (don_id, ville_id, libelle, quantite_attribuee) VALUES
(2, 1, 'tôle', 25);  -- Antananarivo : 25 (reste 5 de besoin)
-- Don tôle ÉPUISÉ (25 - 25 = 0)

-- 3. Dispatcher l'huile (60 L disponible)
INSERT INTO dispatch (don_id, ville_id, libelle, quantite_attribuee) VALUES
(3, 1, 'huile', 50), -- Antananarivo : 50 L (besoin satisfait)
(3, 4, 'huile', 10); -- Mahajanga : 10 L (reste 30 L de besoin)
-- Don huile ÉPUISÉ (60 - 60 = 0)

-- 4. Dispatcher les clous (300 disponibles)
INSERT INTO dispatch (don_id, ville_id, libelle, quantite_attribuee) VALUES
(4, 2, 'clou', 300); -- Toamasina : 300 (reste 200 de besoin)
-- Don clou ÉPUISÉ (300 - 300 = 0)

-- ============================================
-- ÉTAT APRÈS DISPATCH
-- ============================================
/*
✅ TOUS LES DONS DIRECTS ÉPUISÉS

💰 ARGENT DISPONIBLE : 1 500 000 Ar

🛒 BESOINS ACHETABLES (avec l'argent) :

Antananarivo :
  ✅ tôle : 5 restantes = 175 000 + 10% = 192 500 Ar

Toamasina :
  ✅ riz : 60 kg restants = 150 000 + 10% = 165 000 Ar
  ✅ clou : 200 restants = 40 000 + 10% = 44 000 Ar

Fianarantsoa :
  ✅ riz : 60 kg = 150 000 + 10% = 165 000 Ar
  ✅ tôle : 20 = 700 000 + 10% = 770 000 Ar

Mahajanga :
  ✅ huile : 30 L restants = 240 000 + 10% = 264 000 Ar

TOTAL POSSIBLE : ~1 600 500 Ar
MAIS attention : argent limité à 1 500 000 Ar !
*/

-- ============================================
-- VÉRIFICATIONS
-- ============================================

-- Voir les besoins restants
SELECT 
    v.nom AS ville,
    b.libelle,
    b.quantite AS besoin_total,
    COALESCE(SUM(d.quantite_attribuee), 0) AS deja_recu,
    (b.quantite - COALESCE(SUM(d.quantite_attribuee), 0)) AS restant
FROM besoins b
JOIN villes v ON v.id = b.ville_id
LEFT JOIN dispatch d ON d.ville_id = b.ville_id AND d.libelle = b.libelle
WHERE b.type IN ('nature', 'materiel')
GROUP BY b.id
HAVING restant > 0
ORDER BY v.nom;

-- Vérifier les dons épuisés
SELECT 
    d.libelle,
    d.quantite AS total,
    COALESCE(SUM(disp.quantite_attribuee), 0) AS dispatche,
    (d.quantite - COALESCE(SUM(disp.quantite_attribuee), 0)) AS restant
FROM dons d
LEFT JOIN dispatch disp ON disp.don_id = d.id
WHERE d.type IN ('nature', 'materiel')
GROUP BY d.id;
-- Résultat attendu : TOUS les restants = 0
