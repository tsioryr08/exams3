-- ============================================
-- BASE DE DONNEES : BNGRC - Suivi des dons
-- ============================================

DROP DATABASE IF EXISTS bngrc;
CREATE DATABASE bngrc;
USE bngrc;

-- ============================================
-- TABLE VILLES
-- ============================================

CREATE TABLE villes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    region VARCHAR(100) NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLE BESOINS
-- ============================================

CREATE TABLE besoins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    type ENUM('nature','materiel','argent') NOT NULL,
    libelle VARCHAR(100) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    quantite INT NOT NULL,
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ville_id) REFERENCES villes(id)
        ON DELETE CASCADE
);

-- ============================================
-- TABLE DONS
-- ============================================

CREATE TABLE dons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('nature','materiel','argent') NOT NULL,
    libelle VARCHAR(100) NOT NULL,
    quantite INT NOT NULL,
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLE DISPATCH
-- ============================================

CREATE TABLE dispatch (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_id INT NOT NULL,
    ville_id INT NOT NULL,
    libelle VARCHAR(100) NOT NULL,
    quantite_attribuee INT NOT NULL,
    date_dispatch DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (don_id) REFERENCES dons(id)
        ON DELETE CASCADE,
    FOREIGN KEY (ville_id) REFERENCES villes(id)
        ON DELETE CASCADE
);

-- ============================================
-- DONNEES DE TEST
-- ============================================

-- VILLES
INSERT INTO villes (nom, region) VALUES
('Antananarivo', 'Analamanga'),
('Toamasina', 'Atsinanana'),
('Fianarantsoa', 'Haute Matsiatra'),
('Mahajanga', 'Boeny');

-- BESOINS
INSERT INTO besoins (ville_id, type, libelle, prix_unitaire, quantite) VALUES
-- Antananarivo
(1, 'nature', 'riz', 2500, 100),
(1, 'nature', 'huile', 8000, 50),
(1, 'materiel', 'tôle', 35000, 30),

-- Toamasina
(2, 'nature', 'riz', 2500, 80),
(2, 'materiel', 'clou', 200, 500),
(2, 'argent', 'aide_financiere', 1, 1000000),

-- Fianarantsoa
(3, 'nature', 'riz', 2500, 60),
(3, 'materiel', 'tôle', 35000, 20),

-- Mahajanga
(4, 'nature', 'huile', 8000, 40),
(4, 'argent', 'aide_financiere', 1, 500000);

-- DONS (avec ordre logique de date)
INSERT INTO dons (type, libelle, quantite, date_saisie) VALUES
('nature', 'riz', 120, '2026-02-16 13:00:00'),
('materiel', 'tôle', 25, '2026-02-16 14:00:00'),
('nature', 'huile', 60, '2026-02-16 15:00:00'),
('argent', 'aide_financiere', 1500000, '2026-02-16 16:00:00'),
('materiel', 'clou', 300, '2026-02-16 17:00:00');
