-- ============================================
-- DONNÉES DE TEST - Projet BNGRC
-- ============================================
-- Script d'insertion des données de test
-- À exécuter après base.sql et baseV2.sql
-- ============================================

USE bngrc;

-- Nettoyer les données existantes (optionnel)
-- DELETE FROM dispatch;
-- DELETE FROM achats;
-- DELETE FROM dons;
-- DELETE FROM besoins;
-- DELETE FROM villes;
-- DELETE FROM config;

-- ============================================
-- INSERTION DE LA CONFIGURATION
-- ============================================

INSERT INTO config (cle, valeur, description) VALUES
('frais_achat_pourcentage', '10', 'Pourcentage de frais ajouté lors des achats (ex: 10 pour 10%)');

-- ============================================
-- INSERTION DES VILLES
-- ============================================

INSERT INTO villes (nom, region, date_creation) VALUES
('Toamasina', 'Atsinanana', '2026-02-15 08:00:00'),
('Mananjary', 'Vatovavy', '2026-02-15 08:00:00'),
('Farafangana', 'Atsimo-Atsinanana', '2026-02-15 08:00:00'),
('Nosy Be', 'Diana', '2026-02-15 08:00:00'),
('Morondava', 'Menabe', '2026-02-15 08:00:00');

-- ============================================
-- INSERTION DES BESOINS (par ordre chronologique strict)
-- ============================================

-- Ordre 1: Toamasina - Bâche
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(1, 'materiel', 'Bâche', 15000, 200, '2026-02-15 00:00:01');

-- Ordre 2: Nosy Be - Tôle
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(4, 'materiel', 'Tôle', 25000, 40, '2026-02-15 01:10:00');

-- Ordre 3: Mananjary - Argent
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(2, 'argent', 'Argent', 1, 6000000, '2026-02-15 02:20:00');

-- Ordre 4: Toamasina - Eau
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(1, 'nature', 'Eau (L)', 1000, 1500, '2026-02-15 03:30:00');

-- Ordre 5: Nosy Be - Riz
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(4, 'nature', 'Riz (kg)', 3000, 300, '2026-02-15 04:40:00');

-- Ordre 6: Mananjary - Tôle
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(2, 'materiel', 'Tôle', 25000, 80, '2026-02-15 05:50:00');

-- Ordre 7: Nosy Be - Argent
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(4, 'argent', 'Argent', 1, 4000000, '2026-02-15 07:00:00');

-- Ordre 8: Farafangana - Bâche
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(3, 'materiel', 'Bâche', 15000, 150, '2026-02-15 08:10:00');

-- Ordre 9: Mananjary - Riz
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(2, 'nature', 'Riz (kg)', 3000, 500, '2026-02-15 09:20:00');

-- Ordre 10: Farafangana - Argent
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(3, 'argent', 'Argent', 1, 8000000, '2026-02-15 10:30:00');

-- Ordre 11: Morondava - Riz
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(5, 'nature', 'Riz (kg)', 3000, 700, '2026-02-15 11:40:00');

-- Ordre 12: Toamasina - Argent
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(1, 'argent', 'Argent', 1, 12000000, '2026-02-15 12:50:00');

-- Ordre 13: Morondava - Argent
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(5, 'argent', 'Argent', 1, 10000000, '2026-02-15 14:00:00');

-- Ordre 14: Farafangana - Eau
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(3, 'nature', 'Eau (L)', 1000, 1000, '2026-02-15 15:10:00');

-- Ordre 15: Morondava - Bâche
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(5, 'materiel', 'Bâche', 15000, 180, '2026-02-15 16:20:00');

-- Ordre 16: Toamasina - Groupe électrogène
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(1, 'materiel', 'Groupe électrogène', 2250000, 3, '2026-02-15 17:30:00');

-- Ordre 17: Toamasina - Riz
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(1, 'nature', 'Riz (kg)', 3000, 800, '2026-02-15 18:40:00');

-- Ordre 18: Nosy Be - Haricots
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(4, 'nature', 'Haricots', 4000, 200, '2026-02-15 19:50:00');

-- Ordre 19: Mananjary - Clous
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(2, 'materiel', 'Clous (kg)', 8000, 60, '2026-02-15 21:00:00');

-- Ordre 20: Morondava - Eau
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(5, 'nature', 'Eau (L)', 1000, 1200, '2026-02-15 22:10:00');

-- Ordre 21: Farafangana - Riz
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(3, 'nature', 'Riz (kg)', 3000, 600, '2026-02-15 23:20:00');

-- Ordre 22: Morondava - Bois
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(5, 'materiel', 'Bois', 10000, 150, '2026-02-16 00:30:00');

-- Ordre 23: Toamasina - Tôle
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(1, 'materiel', 'Tôle', 25000, 120, '2026-02-16 01:40:00');

-- Ordre 24: Nosy Be - Clous
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(4, 'materiel', 'Clous (kg)', 8000, 30, '2026-02-16 02:50:00');

-- Ordre 25: Mananjary - Huile
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(2, 'nature', 'Huile (L)', 6000, 120, '2026-02-16 04:00:00');

-- Ordre 26: Farafangana - Bois
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite, date_saisie) VALUES
(3, 'materiel', 'Bois', 10000, 100, '2026-02-16 05:10:00');

-- ============================================
-- INSERTION DES DONS (exemples)
-- ============================================

INSERT INTO dons (type, libelle, quantite, date_saisie) VALUES
('nature', 'Riz (kg)', 2000, '2026-02-14 10:00:00'),
('nature', 'Eau (L)', 3000, '2026-02-14 11:00:00'),
('nature', 'Huile (L)', 150, '2026-02-14 12:00:00'),
('nature', 'Haricots', 250, '2026-02-14 13:00:00'),
('materiel', 'Tôle', 200, '2026-02-14 14:00:00'),
('materiel', 'Bâche', 400, '2026-02-14 15:00:00'),
('materiel', 'Clous (kg)', 100, '2026-02-14 16:00:00'),
('materiel', 'Bois', 300, '2026-02-14 17:00:00'),
('materiel', 'Groupe électrogène', 5, '2026-02-14 18:00:00'),
('argent', 'Argent', 50000000, '2026-02-14 19:00:00');

-- ============================================
-- FIN DU SCRIPT
-- ============================================