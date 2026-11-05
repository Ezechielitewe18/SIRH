-- ============================================================================
-- GLOBIT SIRH — Phase 1 : fondations du pointage multi-sources
-- ----------------------------------------------------------------------------
-- Script ADDITIF. Il ne supprime aucune table et ne modifie aucune donnee.
-- Seule modification sur l'existant : l'enum `presences.source` recoit
-- 'rfid' et 'biometrie'.
--
-- A executer APRES une sauvegarde complete de la base.
-- Base : MariaDB 10.4+ / MySQL 5.7+
-- ============================================================================

SET NAMES utf8mb4;

-- ----------------------------------------------------------------------------
-- 1. presences : ajout des deux nouvelles sources
--    (aucune autre colonne n'est touchee)
-- ----------------------------------------------------------------------------
ALTER TABLE `presences`
  MODIFY COLUMN `source`
  ENUM('declaration','manuel','qr','rfid','biometrie')
  NOT NULL DEFAULT 'manuel';


-- ----------------------------------------------------------------------------
-- 2. dispositifs : registre des appareils autorises a envoyer un pointage
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dispositifs` (
  `id_dispositif` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL COMMENT 'Identifiant technique choisi (ex: PORTIQUE-ENTREE)',
  `libelle` varchar(100) NOT NULL COMMENT 'Nom lisible pour le RH',
  `type` enum('qr','rfid','biometrie','autre') NOT NULL DEFAULT 'qr',
  `marque` varchar(80) DEFAULT NULL,
  `modele` varchar(80) DEFAULT NULL,
  `numero_serie` varchar(80) DEFAULT NULL,
  `emplacement` varchar(120) DEFAULT NULL,
  `jeton_hash` char(64) NOT NULL COMMENT 'SHA-256 du jeton appareil (jamais le jeton en clair)',
  `statut` enum('actif','maintenance','hors_service') NOT NULL DEFAULT 'actif',
  `dernier_contact` datetime DEFAULT NULL,
  `teste_le` datetime DEFAULT NULL COMMENT 'Date du test de reception reussi',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_dispositif`),
  UNIQUE KEY `uk_dispositif_code` (`code`),
  UNIQUE KEY `uk_dispositif_jeton` (`jeton_hash`),
  KEY `idx_dispositif_statut` (`statut`),
  KEY `idx_dispositif_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Appareils de pointage declares. Un appareil non declare est refuse.';


-- ----------------------------------------------------------------------------
-- 3. cartes : badges RFID/NFC attribues aux employes
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cartes` (
  `id_carte` int(11) NOT NULL AUTO_INCREMENT,
  `uid_carte` varchar(64) NOT NULL COMMENT 'Numero lu par le lecteur (hex ou decimal selon l appareil)',
  `technologie` varchar(40) DEFAULT NULL COMMENT 'Mifare Classic, DESFire, NFC... (ne pas supposer interchangeable)',
  `id_employe` int(11) NOT NULL,
  `libelle` varchar(80) DEFAULT NULL COMMENT 'Repere libre : badge entree, badge atelier...',
  `statut` enum('active','perdue','desactivee','remplacee') NOT NULL DEFAULT 'active',
  `date_attribution` date NOT NULL,
  `date_desactivation` datetime DEFAULT NULL,
  `remplacee_par` int(11) DEFAULT NULL COMMENT 'id_carte de la carte de remplacement',
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_carte`),
  UNIQUE KEY `uk_carte_uid` (`uid_carte`),
  KEY `idx_carte_employe` (`id_employe`),
  KEY `idx_carte_statut` (`statut`),
  CONSTRAINT `fk_carte_employe` FOREIGN KEY (`id_employe`)
    REFERENCES `employes` (`id_employe`) ON DELETE CASCADE,
  CONSTRAINT `fk_carte_remplacee_par` FOREIGN KEY (`remplacee_par`)
    REFERENCES `cartes` (`id_carte`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Badges RFID/NFC. Une carte n est jamais supprimee : elle change de statut.';


-- ----------------------------------------------------------------------------
-- 4. pointages_evenements : la preuve brute de chaque evenement recu
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pointages_evenements` (
  `id_evenement` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_employe` int(11) DEFAULT NULL COMMENT 'NULL tant que l identifiant n est pas reconnu',
  `id_dispositif` int(11) DEFAULT NULL COMMENT 'NULL pour un pointage manuel sans appareil',
  `id_carte` int(11) DEFAULT NULL,
  `source` enum('qr','rfid','biometrie','manuel') NOT NULL,
  `type_evenement` enum('arrivee','depart','inconnu') NOT NULL DEFAULT 'inconnu',
  `horodatage` datetime NOT NULL COMMENT 'Heure reelle du geste, donnee par l appareil',
  `horodatage_recu` datetime NOT NULL COMMENT 'Heure de reception par le serveur',
  `uid_externe` varchar(100) NOT NULL COMMENT 'Numero unique fourni par l appareil',
  `identifiant_lu` varchar(191) DEFAULT NULL COMMENT 'Valeur brute lue : jeton QR, UID carte, matricule',
  `statut_traitement` enum('recu','traite','doublon','rejete','en_attente','a_valider')
    NOT NULL DEFAULT 'recu',
  `id_presence` int(11) DEFAULT NULL COMMENT 'Ligne du cahier propre produite par cet evenement',
  `message` varchar(255) DEFAULT NULL,
  `donnees_brutes` text DEFAULT NULL COMMENT 'Charge d origine, nettoyee de toute donnee biometrique',
  `cle_dedup` varchar(180) AS (CONCAT(COALESCE(`id_dispositif`, 0), ':', `uid_externe`)) STORED
    COMMENT 'Cle technique anti-doublon : appareil + numero d evenement',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_evenement`),
  UNIQUE KEY `uk_evenement_dedup` (`cle_dedup`),
  KEY `idx_evenement_employe_date` (`id_employe`, `horodatage`),
  KEY `idx_evenement_statut` (`statut_traitement`),
  KEY `idx_evenement_horodatage` (`horodatage`),
  KEY `idx_evenement_dispositif` (`id_dispositif`),
  CONSTRAINT `fk_evt_employe` FOREIGN KEY (`id_employe`)
    REFERENCES `employes` (`id_employe`) ON DELETE SET NULL,
  CONSTRAINT `fk_evt_dispositif` FOREIGN KEY (`id_dispositif`)
    REFERENCES `dispositifs` (`id_dispositif`) ON DELETE SET NULL,
  CONSTRAINT `fk_evt_carte` FOREIGN KEY (`id_carte`)
    REFERENCES `cartes` (`id_carte`) ON DELETE SET NULL,
  CONSTRAINT `fk_evt_presence` FOREIGN KEY (`id_presence`)
    REFERENCES `presences` (`id_presence`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Journal brut des evenements de pointage. Source de verite, jamais modifiee.';


-- ----------------------------------------------------------------------------
-- 5. Verification
-- ----------------------------------------------------------------------------
SELECT 'dispositifs' AS table_creee, COUNT(*) AS lignes FROM `dispositifs`
UNION ALL SELECT 'cartes', COUNT(*) FROM `cartes`
UNION ALL SELECT 'pointages_evenements', COUNT(*) FROM `pointages_evenements`;
