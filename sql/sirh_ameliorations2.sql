-- =============================================
-- SIRH/GLOBIT - Tables pour les améliorations
-- 10. Utilisateurs avancée, 11. Journal d'activité, 8. QR Code
-- =============================================
USE sirh;

-- =============================================
-- Table: journal_activite
-- Journal d'audit des actions
-- =============================================
CREATE TABLE IF NOT EXISTS journal_activite (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT DEFAULT NULL,
    module VARCHAR(50) DEFAULT NULL,
    ip_adresse VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================
-- Table: cartes_employes
-- Cartes QR Code / d'identification des employés
-- =============================================
CREATE TABLE IF NOT EXISTS cartes_employes (
    id_carte INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    code_qr VARCHAR(64) NOT NULL UNIQUE,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_employe) REFERENCES employes(id_employe) ON DELETE CASCADE
) ENGINE=InnoDB;
