-- RÔLE DU FICHIER:
-- Créer la base de données minimale du projet et ajouter des données de test.

CREATE DATABASE IF NOT EXISTS base_de_donnees_guichet_sud_kivu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE base_de_donnees_guichet_sud_kivu;

-- Citoyens enregistrés
CREATE TABLE IF NOT EXISTS citoyens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_complet VARCHAR(150) NOT NULL,
    numero_piece VARCHAR(50) NOT NULL UNIQUE,
    date_naissance DATE NOT NULL,
    ville VARCHAR(80) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Types de documents disponibles
CREATE TABLE IF NOT EXISTS types_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    libelle VARCHAR(150) NOT NULL
);

-- Etat des conversations Telegram
CREATE TABLE IF NOT EXISTS sessions_telegram (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chat_id BIGINT NOT NULL UNIQUE,
    etat VARCHAR(50) NOT NULL,
    citoyen_id INT NULL,
    date_mise_a_jour DATETIME NOT NULL,
    CONSTRAINT fk_session_citoyen FOREIGN KEY (citoyen_id) REFERENCES citoyens(id) ON DELETE SET NULL
);

-- Documents PDF générés temporairement
CREATE TABLE IF NOT EXISTS documents_generes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(64) NOT NULL UNIQUE,
    citoyen_id INT NOT NULL,
    type_document_id INT NOT NULL,
    chemin_fichier VARCHAR(255) NOT NULL,
    date_expiration DATETIME NOT NULL,
    date_creation DATETIME NOT NULL,
    CONSTRAINT fk_document_citoyen FOREIGN KEY (citoyen_id) REFERENCES citoyens(id) ON DELETE CASCADE,
    CONSTRAINT fk_document_type FOREIGN KEY (type_document_id) REFERENCES types_documents(id) ON DELETE CASCADE
);

-- Administrateurs du systeme
CREATE TABLE IF NOT EXISTS administrateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_complet VARCHAR(150) NOT NULL,
    identifiant VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Historique des demandes recues via Telegram (pour l'administrateur)
CREATE TABLE IF NOT EXISTS demandes_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chat_id BIGINT NOT NULL,
    username_telegram VARCHAR(100) NULL,
    prenom_telegram VARCHAR(100) NULL,
    nom_telegram VARCHAR(100) NULL,
    citoyen_id INT NOT NULL,
    nom_citoyen VARCHAR(150) NOT NULL,
    numero_piece VARCHAR(50) NOT NULL,
    document_demande VARCHAR(150) NOT NULL,
    statut VARCHAR(30) NOT NULL,
    date_heure_demande DATETIME NOT NULL,
    CONSTRAINT fk_demande_citoyen FOREIGN KEY (citoyen_id) REFERENCES citoyens(id) ON DELETE CASCADE
);

-- Données de test
INSERT INTO citoyens (nom_complet, numero_piece, date_naissance, ville)
VALUES
('Awa Kone', 'OP-14862992', '1999-04-12', 'Bukavu'),
('Yao Kouassi', '33644907501', '1995-09-30', 'Goma')
ON DUPLICATE KEY UPDATE nom_complet = VALUES(nom_complet);

-- Administrateur par defaut (identifiant: asnath, mot de passe: 1234)
INSERT INTO administrateurs (nom_complet, identifiant, mot_de_passe)
VALUES
('Asnath', 'asnath', '$2y$10$ll60SOoH6rbFFWiV8u1NBu8i7IPTqur73ShwonNPaNWDXKdmhPafG')
ON DUPLICATE KEY UPDATE nom_complet = VALUES(nom_complet);

INSERT INTO types_documents (code, libelle)
VALUES
('attestation_residence', 'Attestation de residence'),
('certificat_scolarite', 'Certificat de scolarite'),
('attestation_naissance', 'Attestation de naissance'),
('attestation_bonne_vie', 'Attestation de bonne vie et moeurs'),
('certificat_celibat', 'Certificat de celibat')
ON DUPLICATE KEY UPDATE libelle = VALUES(libelle);
