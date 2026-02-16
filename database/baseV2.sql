-- ============================================
-- BASE DE DONNEES : BNGRC - VERSION 2
-- Mise à jour pour la fonctionnalité ACHATS
-- ============================================
-- Ce fichier contient UNIQUEMENT les modifications
-- à appliquer sur la base existante (base.sql)
-- ============================================

USE bngrc;

-- ============================================
-- TABLE CONFIG : Configuration système
-- ============================================
-- Stocke les paramètres configurables du système

CREATE TABLE IF NOT EXISTS config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cle VARCHAR(50) UNIQUE NOT NULL,
    valeur VARCHAR(255) NOT NULL,
    description TEXT,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insérer le paramètre des frais d'achat (10% par défaut)
INSERT INTO config (cle, valeur, description) VALUES
('frais_achat_pourcentage', '10', 'Pourcentage de frais ajouté lors des achats (ex: 10 pour 10%)');

-- ============================================
-- TABLE ACHATS : Historique des achats
-- ============================================
-- Enregistre tous les achats effectués avec les dons en argent

CREATE TABLE IF NOT EXISTS achats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    besoin_id INT NOT NULL,
    type ENUM('nature','materiel') NOT NULL,
    libelle VARCHAR(100) NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    montant_total DECIMAL(10,2) NOT NULL COMMENT 'Prix sans frais',
    frais_achat DECIMAL(10,2) NOT NULL COMMENT 'Montant des frais',
    montant_final DECIMAL(10,2) NOT NULL COMMENT 'Prix total avec frais',
    pourcentage_frais DECIMAL(5,2) NOT NULL COMMENT 'Pourcentage appliqué',
    statut ENUM('simulation', 'valide') DEFAULT 'valide',
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ville_id) REFERENCES villes(id) ON DELETE CASCADE,
    FOREIGN KEY (besoin_id) REFERENCES besoins(id) ON DELETE CASCADE,
    
    INDEX idx_ville (ville_id),
    INDEX idx_besoin (besoin_id),
    INDEX idx_statut (statut)
);

-- ============================================
-- MODIFICATION TABLE DISPATCH
-- ============================================
-- Ajouter une colonne pour identifier la source du dispatch

ALTER TABLE dispatch 
ADD COLUMN source ENUM('don_direct', 'achat') DEFAULT 'don_direct' 
COMMENT 'Indique si le dispatch provient d''un don direct ou d''un achat';

ALTER TABLE dispatch 
ADD COLUMN achat_id INT NULL 
COMMENT 'Référence à l''achat si source=achat';

ALTER TABLE dispatch 
ADD FOREIGN KEY (achat_id) REFERENCES achats(id) ON DELETE CASCADE;

-- Index pour optimiser les requêtes
ALTER TABLE dispatch ADD INDEX idx_source (source);
ALTER TABLE dispatch ADD INDEX idx_achat (achat_id);

-- ============================================
-- VUES UTILES
-- ============================================

-- Vue : Besoins restants (non satisfaits par les dispatches)
CREATE OR REPLACE VIEW v_besoins_restants AS
SELECT 
    b.id AS besoin_id,
    b.ville_id,
    v.nom AS ville_nom,
    v.region,
    b.type,
    b.libelle,
    b.prix_unitaire,
    b.quantite AS quantite_besoin,
    COALESCE(SUM(d.quantite_attribuee), 0) AS quantite_satisfaite,
    (b.quantite - COALESCE(SUM(d.quantite_attribuee), 0)) AS quantite_restante,
    (b.prix_unitaire * (b.quantite - COALESCE(SUM(d.quantite_attribuee), 0))) AS montant_restant
FROM besoins b
INNER JOIN villes v ON b.ville_id = v.id
LEFT JOIN dispatch d ON d.ville_id = b.ville_id 
    AND d.libelle = b.libelle
WHERE b.type IN ('nature', 'materiel')  -- Seuls les besoins achetables
GROUP BY b.id, b.ville_id, v.nom, v.region, b.type, b.libelle, b.prix_unitaire, b.quantite
HAVING quantite_restante > 0
ORDER BY v.nom, b.type;

-- Vue : Dons en argent disponibles (non encore utilisés)
CREATE OR REPLACE VIEW v_argent_disponible AS
SELECT 
    SUM(d.quantite) AS montant_total_dons,
    COALESCE(SUM(a.montant_final), 0) AS montant_utilise_achats,
    (SUM(d.quantite) - COALESCE(SUM(a.montant_final), 0)) AS montant_disponible
FROM dons d
LEFT JOIN achats a ON a.statut = 'valide'
WHERE d.type = 'argent';

-- Vue : Statistiques récapitulatives
CREATE OR REPLACE VIEW v_recapitulatif AS
SELECT 
    -- Besoins totaux
    SUM(b.prix_unitaire * b.quantite) AS besoins_totaux_montant,
    SUM(b.quantite) AS besoins_totaux_quantite,
    
    -- Besoins satisfaits (dispatches)
    COALESCE(SUM(d.quantite_attribuee * b.prix_unitaire), 0) AS besoins_satisfaits_montant,
    COALESCE(SUM(d.quantite_attribuee), 0) AS besoins_satisfaits_quantite,
    
    -- Besoins restants
    (SUM(b.prix_unitaire * b.quantite) - COALESCE(SUM(d.quantite_attribuee * b.prix_unitaire), 0)) AS besoins_restants_montant,
    (SUM(b.quantite) - COALESCE(SUM(d.quantite_attribuee), 0)) AS besoins_restants_quantite,
    
    -- Pourcentage de satisfaction
    ROUND(
        (COALESCE(SUM(d.quantite_attribuee * b.prix_unitaire), 0) * 100.0) / 
        NULLIF(SUM(b.prix_unitaire * b.quantite), 0), 
        2
    ) AS pourcentage_satisfaction
FROM besoins b
LEFT JOIN dispatch d ON d.ville_id = b.ville_id AND d.libelle = b.libelle;

-- ============================================
-- DONNÉES DE TEST
-- ============================================

-- Simuler quelques achats pour tester
-- (Décommenter si vous voulez des données de test)

/*
INSERT INTO achats (ville_id, besoin_id, type, libelle, quantite, prix_unitaire, montant_total, frais_achat, montant_final, pourcentage_frais) 
VALUES
(1, 1, 'nature', 'riz', 50, 2500, 125000, 12500, 137500, 10),
(2, 4, 'nature', 'riz', 30, 2500, 75000, 7500, 82500, 10);
*/

-- ============================================
-- FIN DU SCRIPT baseV2.sql
-- ============================================
