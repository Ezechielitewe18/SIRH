-- =============================================
-- SIRH - Système d'Information des Ressources Humaines
-- Base de données MySQL
-- =============================================

CREATE DATABASE IF NOT EXISTS sirh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sirh;

-- =============================================
-- Table: utilisateurs
-- =============================================
CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom_complet VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'rh', 'employe') NOT NULL DEFAULT 'employe',
    photo VARCHAR(255) DEFAULT NULL,
    statut ENUM('actif', 'inactif') NOT NULL DEFAULT 'actif',
    derniere_connexion DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Table: services
-- =============================================
CREATE TABLE IF NOT EXISTS services (
    id_service INT AUTO_INCREMENT PRIMARY KEY,
    nom_service VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Table: employes
-- =============================================
CREATE TABLE IF NOT EXISTS employes (
    id_employe INT AUTO_INCREMENT PRIMARY KEY,
    matricule VARCHAR(20) NOT NULL UNIQUE,
    nom VARCHAR(50) NOT NULL,
    postnom VARCHAR(50) DEFAULT NULL,
    prenom VARCHAR(50) NOT NULL,
    sexe ENUM('M', 'F') NOT NULL,
    date_naissance DATE DEFAULT NULL,
    lieu_naissance VARCHAR(100) DEFAULT NULL,
    adresse VARCHAR(255) DEFAULT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    poste VARCHAR(100) DEFAULT NULL,
    date_embauche DATE NOT NULL,
    salaire DECIMAL(10,2) DEFAULT NULL,
    statut ENUM('actif', 'inactif', 'suspendu') NOT NULL DEFAULT 'actif',
    id_service INT DEFAULT NULL,
    id_utilisateur INT DEFAULT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_service) REFERENCES services(id_service) ON DELETE SET NULL,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================
-- Table: presences
-- =============================================
CREATE TABLE IF NOT EXISTS presences (
    id_presence INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    date_presence DATE NOT NULL,
    heure_arrivee TIME DEFAULT NULL,
    heure_depart TIME DEFAULT NULL,
    retard INT DEFAULT 0 COMMENT 'Retard en minutes',
    statut ENUM('present', 'absent', 'retard', 'conge') NOT NULL DEFAULT 'present',
    observation TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_employe) REFERENCES employes(id_employe) ON DELETE CASCADE,
    UNIQUE KEY unique_presence (id_employe, date_presence)
) ENGINE=InnoDB;

-- =============================================
-- Table: conges
-- =============================================
CREATE TABLE IF NOT EXISTS conges (
    id_conge INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    type_conge ENUM('annuel', 'maladie', 'maternite', 'paternite', 'exceptionnel', 'autre') NOT NULL DEFAULT 'annuel',
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    nombre_jours INT NOT NULL,
    motif TEXT DEFAULT NULL,
    statut ENUM('en_attente', 'approuve', 'refuse') NOT NULL DEFAULT 'en_attente',
    motif_refus TEXT DEFAULT NULL,
    approuve_par INT DEFAULT NULL,
    date_approbation DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_employe) REFERENCES employes(id_employe) ON DELETE CASCADE,
    FOREIGN KEY (approuve_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================
-- Données initiales
-- =============================================

-- Insérer des services par défaut
INSERT INTO services (nom_service, description) VALUES
('Direction Générale', 'Direction générale de l''entreprise'),
('Ressources Humaines', 'Gestion du personnel et des ressources humaines'),
('Informatique', 'Service des technologies de l''information'),
('Finance', 'Service financier et comptable'),
('Marketing', 'Service marketing et communication'),
('Administration', 'Service administratif');

-- Insérer un administrateur par défaut
INSERT INTO utilisateurs (nom_complet, email, mot_de_passe, role) VALUES
('Administrateur', 'admin@sirh.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Mot de passe par défaut: password
