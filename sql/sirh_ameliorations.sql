-- =============================================
-- SIRH/GLOBIT - Tables pour les améliorations
-- 1. Paie, 3. Formations, 5. Notifications
-- =============================================
USE sirh;

-- =============================================
-- Table: parametres_paie
-- Paramètres globaux de paie (congé taxe, heures supp, etc.)
-- =============================================
CREATE TABLE IF NOT EXISTS parametres_paie (
    id_parametre INT AUTO_INCREMENT PRIMARY KEY,
    nom_parametre VARCHAR(100) NOT NULL,
    valeur DECIMAL(10,2) NOT NULL DEFAULT 0,
    type_parametre VARCHAR(50) NOT NULL COMMENT 'pourcentage, montant, heure',
    description VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Table: bulletins
-- Bulletins de paie par employé et par mois
-- =============================================
CREATE TABLE IF NOT EXISTS bulletins (
    id_bulletin INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    mois INT NOT NULL,
    annee INT NOT NULL,
    salaire_base DECIMAL(10,2) NOT NULL DEFAULT 0,
    primes DECIMAL(10,2) NOT NULL DEFAULT 0,
    heures_supplementaires DECIMAL(10,2) NOT NULL DEFAULT 0,
    montant_heures_sup DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_brut DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_retenues DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_net DECIMAL(10,2) NOT NULL DEFAULT 0,
    statut ENUM('brouillon', 'valide', 'paye') NOT NULL DEFAULT 'brouillon',
    date_generation DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_employe) REFERENCES employes(id_employe) ON DELETE CASCADE,
    UNIQUE KEY unique_bulletin (id_employe, mois, annee)
) ENGINE=InnoDB;

-- =============================================
-- Table: formations
-- Catalogue des formations
-- =============================================
CREATE TABLE IF NOT EXISTS formations (
    id_formation INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    duree VARCHAR(50) DEFAULT NULL COMMENT 'Ex: 2 jours, 1 semaine',
    type_formation VARCHAR(50) DEFAULT NULL,
    statut ENUM('planifiee', 'en_cours', 'terminee', 'annulee') NOT NULL DEFAULT 'planifiee',
    date_debut DATE DEFAULT NULL,
    date_fin DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Table: formations_employes
-- Association employés -> formations (inscriptions)
-- =============================================
CREATE TABLE IF NOT EXISTS formations_employes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_formation INT NOT NULL,
    id_employe INT NOT NULL,
    statut ENUM('inscrit', 'present', 'absent', 'termine') NOT NULL DEFAULT 'inscrit',
    note INT DEFAULT NULL,
    observation TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_formation) REFERENCES formations(id_formation) ON DELETE CASCADE,
    FOREIGN KEY (id_employe) REFERENCES employes(id_employe) ON DELETE CASCADE,
    UNIQUE KEY unique_inscription (id_formation, id_employe)
) ENGINE=InnoDB;

-- =============================================
-- Table: notifications
-- Notifications internes aux utilisateurs
-- =============================================
CREATE TABLE IF NOT EXISTS notifications (
    id_notification INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    titre VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    type_notif VARCHAR(50) DEFAULT NULL COMMENT 'conge, paie, formation, systeme',
    est_lu TINYINT(1) NOT NULL DEFAULT 0,
    lien VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- Données initiales: paramètres de paie
-- =============================================
INSERT INTO parametres_paie (nom_parametre, valeur, type_parametre, description) VALUES
('Taxe professionnelle', 1.00, 'pourcentage', 'Pourcentage de l''impôt professionnel sur le brut'),
('Prestation sociale', 3.50, 'pourcentage', 'Cotisation sociale (fond social) sur le brut'),
('Pension retraite', 5.00, 'pourcentage', 'Cotisation retraite sur le brut'),
('Indemnité logement', 20.00, 'pourcentage', 'Prime logement en pourcentage du salaire de base'),
('Prime transport', 10.00, 'pourcentage', 'Prime transport en pourcentage du salaire de base'),
('Taux heure supplémentaire', 150.00, 'pourcentage', 'Majoration heures supplémentaires en % du taux horaire'),
('Heures travail / mois', 176, 'heure', 'Nombre d''heures mensuelles de travail');
