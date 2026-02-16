CREATE DATABASE IF NOT EXISTS takalo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE takalo;

-- Table utilisateur
CREATE TABLE IF NOT EXISTS utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    statut ENUM('simple', 'admin') DEFAULT 'simple'
);

INSERT INTO utilisateur(nom, prenom, email, password_hash, telephone) VALUES
('admin','admin','admin@example.com','admin', '012345689');
update utilisateur set statut = 'admin' where email = 'admin@example.com';

-- Table catégorie (pour normaliser)
CREATE TABLE IF NOT EXISTS categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- Table objet
CREATE TABLE IF NOT EXISTS objet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    categorie_id INT,
    proprietaire_id INT NOT NULL,
    etat_objet ENUM('neuf', 'tres_bon', 'bon', 'moyen', 'usage') DEFAULT 'bon',
    disponible BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proprietaire_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (categorie_id) REFERENCES categorie(id) ON DELETE SET NULL
);

-- Table pour gérer les propositions d'échange
CREATE TABLE IF NOT EXISTS proposition_echange (
    id INT AUTO_INCREMENT PRIMARY KEY,
    objet_propose_id INT NOT NULL,
    objet_demande_id INT NOT NULL,
    utilisateur_proposant_id INT NOT NULL,
    utilisateur_destinataire_id INT NOT NULL,
    statut ENUM('en_attente', 'accepte', 'refuse', 'annule') DEFAULT 'en_attente',
    date_proposition TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_reponse TIMESTAMP NULL,
    FOREIGN KEY (objet_propose_id) REFERENCES objet(id) ON DELETE CASCADE,
    FOREIGN KEY (objet_demande_id) REFERENCES objet(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_proposant_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_destinataire_id) REFERENCES utilisateur(id) ON DELETE CASCADE
);

-- Table historique des échanges réalisés
CREATE TABLE IF NOT EXISTS historique_echange (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proposition_id INT NOT NULL,
    date_echange TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proposition_id) REFERENCES proposition_echange(id)
);

-- Table pour les images multiples d'un objet
CREATE TABLE IF NOT EXISTS image_objet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    objet_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    ordre INT DEFAULT 0,
    FOREIGN KEY (objet_id) REFERENCES objet(id) ON DELETE CASCADE
);

-- Table pour les favoris/wishlist
-- CREATE TABLE IF NOT EXISTS favori (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     utilisateur_id INT NOT NULL,
--     objet_id INT NOT NULL,
--     date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
--     FOREIGN KEY (objet_id) REFERENCES objet(id) ON DELETE CASCADE,
--     UNIQUE KEY unique_favori (utilisateur_id, objet_id)
-- );

-- Insertion de catégories par défaut
INSERT INTO categorie (nom, description) VALUES
('Vêtements', 'Habits, chaussures, accessoires'),
('Livres', 'Romans, BD, magazines'),
('DVD/Blu-ray', 'Films et séries'),
('Jeux vidéo', 'Consoles et jeux'),
('Électronique', 'Appareils électroniques'),
('Sport', 'Équipements sportifs'),
('Décoration', 'Objets de décoration');