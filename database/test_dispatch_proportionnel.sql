-- ============================================
-- DONNÉES DE TEST POUR DISPATCH PROPORTIONNEL
-- Méthode du plus grand reste (décimales)
-- ============================================

USE bngrc;

-- Vider les tables pour un test propre
DELETE FROM dispatch;
DELETE FROM dons;
DELETE FROM besoins;
DELETE FROM villes;

-- Réinitialiser les auto_increment
ALTER TABLE villes AUTO_INCREMENT = 1;
ALTER TABLE besoins AUTO_INCREMENT = 1;
ALTER TABLE dons AUTO_INCREMENT = 1;
ALTER TABLE dispatch AUTO_INCREMENT = 1;

-- ============================================
-- SCENARIO 1 : RIZ avec décimales significatives
-- ============================================
-- 3 villes demandent du riz : 40, 35, 25 (total = 100)
-- Don de 77 kg de riz
-- Proportions : 30.8, 26.95, 19.25
-- Parties entières : 30, 26, 19 (total = 75)
-- Reste : 2 unités à distribuer
-- Décimales : 0.95 > 0.8 > 0.25
-- Résultat attendu : 30, 27, 20 (car 0.95 et 0.8 gagnent)

INSERT INTO villes (nom, region) VALUES
('Antananarivo', 'Analamanga'),
('Toamasina', 'Atsinanana'),
('Fianarantsoa', 'Haute Matsiatra');

INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(1, 'nature', 'riz', 2500, 40, '2026-02-17 10:00:00'),  -- 40/100 * 77 = 30.8
(2, 'nature', 'riz', 2500, 35, '2026-02-17 10:05:00'),  -- 35/100 * 77 = 26.95
(3, 'nature', 'riz', 2500, 25, '2026-02-17 10:10:00');  -- 25/100 * 77 = 19.25

INSERT INTO dons (type, libelle, quantite, date_saisie) VALUES
('nature', 'riz', 77, '2026-02-17 11:00:00');

-- ============================================
-- SCENARIO 2 : TÔLES avec décimales très proches
-- ============================================
-- 4 villes demandent des tôles : 10, 15, 20, 25 (total = 70)
-- Don de 23 tôles
-- Proportions : 3.286, 4.929, 6.571, 8.214
-- Parties entières : 3, 4, 6, 8 (total = 21)
-- Reste : 2 unités
-- Décimales : 0.929 > 0.571 > 0.286 > 0.214
-- Résultat attendu : 3, 5, 7, 8

INSERT INTO villes (nom, region) VALUES
('Mahajanga', 'Boeny');

INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(1, 'materiel', 'tôle', 35000, 10, '2026-02-17 11:00:00'),  -- 10/70 * 23 = 3.286
(2, 'materiel', 'tôle', 35000, 15, '2026-02-17 11:05:00'),  -- 15/70 * 23 = 4.929
(3, 'materiel', 'tôle', 35000, 20, '2026-02-17 11:10:00'),  -- 20/70 * 23 = 6.571
(4, 'materiel', 'tôle', 35000, 25, '2026-02-17 11:15:00');  -- 25/70 * 23 = 8.214

INSERT INTO dons (type, libelle, quantite, date_saisie) VALUES
('materiel', 'tôle', 23, '2026-02-17 12:00:00');

-- ============================================
-- SCENARIO 3 : HUILE avec gros reste
-- ============================================
-- 3 villes : 10, 15, 8 (total = 33)
-- Don de 100 litres
-- Proportions : 30.303, 45.455, 24.242
-- Parties entières : 30, 45, 24 (total = 99)
-- Reste : 1 unité
-- Décimales : 0.455 > 0.303 > 0.242
-- Résultat attendu : 30, 46, 24

INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(1, 'nature', 'huile', 8000, 10, '2026-02-17 12:00:00'),   -- 10/33 * 100 = 30.303
(2, 'nature', 'huile', 8000, 15, '2026-02-17 12:05:00'),   -- 15/33 * 100 = 45.455
(3, 'nature', 'huile', 8000, 8,  '2026-02-17 12:10:00');   -- 8/33 * 100 = 24.242

INSERT INTO dons (type, libelle, quantite, date_saisie) VALUES
('nature', 'huile', 100, '2026-02-17 13:00:00');

-- ============================================
-- SCENARIO 4 : ARGENT avec grands nombres
-- ============================================
-- 3 villes : 500000, 300000, 200000 (total = 1000000)
-- Don de 750000 Ar
-- Proportions : 375000, 225000, 150000 (pas de décimales !)
-- Résultat attendu : 375000, 225000, 150000

INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(1, 'argent', 'aide_financiere', 1, 500000, '2026-02-17 13:00:00'),
(2, 'argent', 'aide_financiere', 1, 300000, '2026-02-17 13:05:00'),
(3, 'argent', 'aide_financiere', 1, 200000, '2026-02-17 13:10:00');

INSERT INTO dons (type, libelle, quantite, date_saisie) VALUES
('argent', 'aide_financiere', 750000, '2026-02-17 14:00:00');

-- ============================================
-- RÉSUMÉ DES RÉSULTATS ATTENDUS
-- ============================================

/*
SCÉNARIO 1 - RIZ (77 kg pour 100 demandés)
------------------------------------------
Ville             | Demande | Proportion | Entier | Décimal | Final
Antananarivo      | 40      | 30.80      | 30     | 0.800   | 31 ✓ (2e plus grand)
Toamasina         | 35      | 26.95      | 26     | 0.950   | 27 ✓ (1er plus grand)
Fianarantsoa      | 25      | 19.25      | 19     | 0.250   | 19 ✗
TOTAL             | 100     | 77.00      | 75     | reste=2 | 77 ✓

SCÉNARIO 2 - TÔLES (23 pour 70 demandées)
-----------------------------------------
Ville             | Demande | Proportion | Entier | Décimal | Final
Antananarivo      | 10      | 3.286      | 3      | 0.286   | 3 ✗
Toamasina         | 15      | 4.929      | 4      | 0.929   | 5 ✓ (1er plus grand)
Fianarantsoa      | 20      | 6.571      | 6      | 0.571   | 7 ✓ (2e plus grand)
Mahajanga         | 25      | 8.214      | 8      | 0.214   | 8 ✗
TOTAL             | 70      | 23.00      | 21     | reste=2 | 23 ✓

SCÉNARIO 3 - HUILE (100 L pour 33 demandés)
-------------------------------------------
Ville             | Demande | Proportion | Entier | Décimal | Final
Antananarivo      | 10      | 30.303     | 30     | 0.303   | 30 ✗
Toamasina         | 15      | 45.455     | 45     | 0.455   | 46 ✓ (plus grand)
Fianarantsoa      | 8       | 24.242     | 24     | 0.242   | 24 ✗
TOTAL             | 33      | 100.00     | 99     | reste=1 | 100 ✓

SCÉNARIO 4 - ARGENT (750000 Ar pour 1000000 demandés)
-----------------------------------------------------
Ville             | Demande | Proportion | Entier | Décimal | Final
Antananarivo      | 500000  | 375000.0   | 375000 | 0.000   | 375000 ✓
Toamasina         | 300000  | 225000.0   | 225000 | 0.000   | 225000 ✓
Fianarantsoa      | 200000  | 150000.0   | 150000 | 0.000   | 150000 ✓
TOTAL             | 1000000 | 750000.0   | 750000 | reste=0 | 750000 ✓
*/

-- Pour voir les résultats après dispatch proportionnel :
-- SELECT * FROM dispatch ORDER BY don_id, ville_id;
