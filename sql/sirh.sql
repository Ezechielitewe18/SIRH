-- ============================================================================
--  GLOBIT SIRH - Base de donnees complete
--  Systeme d'Information des Ressources Humaines
--  Contenu : base + 15 tables + donnees de demonstration
--  Fichier unique : importer tel quel dans phpMyAdmin (onglet Importer)
--  Encodage : UTF-8 (utf8mb4)
-- ============================================================================

-- Creation automatique de la base (ne renvoie pas d'erreur si elle existe)
CREATE DATABASE IF NOT EXISTS `sirh` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sirh`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `annonces`;
CREATE TABLE `annonces` (
  `id_annonce` int(11) NOT NULL AUTO_INCREMENT,
  `id_expediteur` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `contenu` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_annonce`),
  KEY `id_expediteur` (`id_expediteur`),
  CONSTRAINT `annonces_ibfk_1` FOREIGN KEY (`id_expediteur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `annonces` WRITE;
UNLOCK TABLES;
DROP TABLE IF EXISTS `api_tokens`;
CREATE TABLE `api_tokens` (
  `id_token` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id_token`),
  UNIQUE KEY `token` (`token`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `api_tokens_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `api_tokens` WRITE;
UNLOCK TABLES;
DROP TABLE IF EXISTS `bulletins`;
CREATE TABLE `bulletins` (
  `id_bulletin` int(11) NOT NULL AUTO_INCREMENT,
  `id_employe` int(11) NOT NULL,
  `mois` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `salaire_base` decimal(10,2) NOT NULL DEFAULT 0.00,
  `primes` decimal(10,2) NOT NULL DEFAULT 0.00,
  `heures_supplementaires` decimal(10,2) NOT NULL DEFAULT 0.00,
  `montant_heures_sup` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_brut` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_retenues` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_net` decimal(10,2) NOT NULL DEFAULT 0.00,
  `statut` enum('brouillon','valide','paye') NOT NULL DEFAULT 'brouillon',
  `date_generation` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_bulletin`),
  UNIQUE KEY `unique_bulletin` (`id_employe`,`mois`,`annee`),
  CONSTRAINT `bulletins_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id_employe`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `bulletins` WRITE;
UNLOCK TABLES;
DROP TABLE IF EXISTS `conges`;
CREATE TABLE `conges` (
  `id_conge` int(11) NOT NULL AUTO_INCREMENT,
  `id_employe` int(11) NOT NULL,
  `type_conge` enum('annuel','maladie','maternite','paternite','exceptionnel','autre') NOT NULL DEFAULT 'annuel',
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nombre_jours` int(11) NOT NULL,
  `motif` text DEFAULT NULL,
  `statut` enum('en_attente','approuve','refuse') NOT NULL DEFAULT 'en_attente',
  `motif_refus` text DEFAULT NULL,
  `approuve_par` int(11) DEFAULT NULL,
  `date_approbation` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_conge`),
  KEY `id_employe` (`id_employe`),
  KEY `approuve_par` (`approuve_par`),
  CONSTRAINT `conges_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id_employe`) ON DELETE CASCADE,
  CONSTRAINT `conges_ibfk_2` FOREIGN KEY (`approuve_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `conges` WRITE;
UNLOCK TABLES;
DROP TABLE IF EXISTS `conversations`;
CREATE TABLE `conversations` (
  `id_conversation` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur_a` int(11) NOT NULL,
  `id_utilisateur_b` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_conversation`),
  UNIQUE KEY `unique_pair` (`id_utilisateur_a`,`id_utilisateur_b`),
  KEY `id_utilisateur_b` (`id_utilisateur_b`),
  CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`id_utilisateur_a`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`id_utilisateur_b`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `conversations` WRITE;
UNLOCK TABLES;
DROP TABLE IF EXISTS `employes`;
CREATE TABLE `employes` (
  `id_employe` int(11) NOT NULL AUTO_INCREMENT,
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `postnom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) NOT NULL,
  `sexe` enum('M','F') NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `poste` varchar(100) DEFAULT NULL,
  `date_embauche` date NOT NULL,
  `salaire` decimal(10,2) DEFAULT NULL,
  `statut` enum('actif','inactif','suspendu') NOT NULL DEFAULT 'actif',
  `id_service` int(11) DEFAULT NULL,
  `est_direction` tinyint(1) NOT NULL DEFAULT 0,
  `id_utilisateur` int(11) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `qr_secret` varchar(64) DEFAULT NULL COMMENT 'Secret HMAC du QR dynamique de presence',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_employe`),
  UNIQUE KEY `matricule` (`matricule`),
  KEY `id_service` (`id_service`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `employes_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `services` (`id_service`) ON DELETE SET NULL,
  CONSTRAINT `employes_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `employes` WRITE;
INSERT INTO `employes` VALUES (2,'EMP-2026-0001','TSHIBUABUA','TSHIBUABUA','PERLE','F','2006-05-20','Kinshasa','05 LULUA BANDALUGWA','0850867191','perle.tshibuabua@globit.com','ADMINISTRATION','2026-09-13',1200.00,'actif',6,0,3,NULL,NULL,'2026-09-13 09:59:27','2026-09-13 09:59:27');
UNLOCK TABLES;
DROP TABLE IF EXISTS `formations`;
CREATE TABLE `formations` (
  `id_formation` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `duree` varchar(50) DEFAULT NULL COMMENT 'Ex: 2 jours, 1 semaine',
  `type_formation` varchar(50) DEFAULT NULL,
  `statut` enum('planifiee','en_cours','terminee','annulee') NOT NULL DEFAULT 'planifiee',
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_formation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `formations` WRITE;
UNLOCK TABLES;
DROP TABLE IF EXISTS `formations_employes`;
CREATE TABLE `formations_employes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formation` int(11) NOT NULL,
  `id_employe` int(11) NOT NULL,
  `statut` enum('inscrit','present','absent','termine') NOT NULL DEFAULT 'inscrit',
  `note` int(11) DEFAULT NULL,
  `observation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_inscription` (`id_formation`,`id_employe`),
  KEY `id_employe` (`id_employe`),
  CONSTRAINT `formations_employes_ibfk_1` FOREIGN KEY (`id_formation`) REFERENCES `formations` (`id_formation`) ON DELETE CASCADE,
  CONSTRAINT `formations_employes_ibfk_2` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id_employe`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `formations_employes` WRITE;
UNLOCK TABLES;
DROP TABLE IF EXISTS `journal_activite`;
CREATE TABLE `journal_activite` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `ip_adresse` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `journal_activite_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `journal_activite` WRITE;
INSERT INTO `journal_activite` VALUES (1,1,'Réinitialisation mot de passe','Mot de passe réinitialisé pour l\'utilisateur #3','utilisateurs','::1','2026-09-13 10:02:58');
UNLOCK TABLES;
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL AUTO_INCREMENT,
  `id_conversation` int(11) NOT NULL,
  `id_expediteur` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `est_lu` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_message`),
  KEY `id_conversation` (`id_conversation`),
  KEY `id_expediteur` (`id_expediteur`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`id_conversation`) REFERENCES `conversations` (`id_conversation`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`id_expediteur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `messages` WRITE;
UNLOCK TABLES;
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id_notification` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `type_notif` varchar(50) DEFAULT NULL COMMENT 'conge, paie, formation, systeme',
  `est_lu` tinyint(1) NOT NULL DEFAULT 0,
  `lien` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_notification`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `notifications` WRITE;
INSERT INTO `notifications` VALUES (2,1,'Déclaration de présence à valider','PERLE TSHIBUABUA a déclaré son arrivée. Une validation est requise.','presence',1,'http://localhost/SIRH/presences','2026-09-13 10:05:45');
UNLOCK TABLES;
DROP TABLE IF EXISTS `parametres_paie`;
CREATE TABLE `parametres_paie` (
  `id_parametre` int(11) NOT NULL AUTO_INCREMENT,
  `nom_parametre` varchar(100) NOT NULL,
  `valeur` decimal(10,2) NOT NULL DEFAULT 0.00,
  `type_parametre` varchar(50) NOT NULL COMMENT 'pourcentage, montant, heure',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_parametre`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `parametres_paie` WRITE;
INSERT INTO `parametres_paie` VALUES (1,'Taxe professionnelle',1.00,'pourcentage','Pourcentage de l\'impôt professionnel sur le brut','2026-09-11 10:59:13','2026-09-17 11:06:04'),(2,'Prestation sociale',3.50,'pourcentage','Cotisation sociale (fond social) sur le brut','2026-09-11 10:59:13','2026-09-11 10:59:13'),(3,'Pension retraite',5.00,'pourcentage','Cotisation retraite sur le brut','2026-09-11 10:59:13','2026-09-11 10:59:13'),(4,'Indemnité logement',20.00,'pourcentage','Prime logement en pourcentage du salaire de base','2026-09-11 10:59:13','2026-09-17 11:06:04'),(5,'Prime transport',10.00,'pourcentage','Prime transport en pourcentage du salaire de base','2026-09-11 10:59:13','2026-09-11 10:59:13'),(6,'Taux heure supplémentaire',150.00,'pourcentage','Majoration heures supplémentaires en % du taux horaire','2026-09-11 10:59:13','2026-09-17 11:06:05'),(7,'Heures travail / mois',176.00,'heure','Nombre d\'heures mensuelles de travail','2026-09-11 10:59:13','2026-09-11 10:59:13');
UNLOCK TABLES;
DROP TABLE IF EXISTS `presences`;
CREATE TABLE `presences` (
  `id_presence` int(11) NOT NULL AUTO_INCREMENT,
  `id_employe` int(11) NOT NULL,
  `source` enum('declaration','manuel','qr') NOT NULL DEFAULT 'manuel',
  `date_presence` date NOT NULL,
  `heure_arrivee` time DEFAULT NULL,
  `heure_depart` time DEFAULT NULL,
  `retard` int(11) DEFAULT 0 COMMENT 'Retard en minutes',
  `statut` enum('present','absent','retard','conge','justifie') NOT NULL DEFAULT 'present',
  `validation` enum('auto','en_attente','validee','rejetee') NOT NULL DEFAULT 'auto',
  `valide_par` int(11) DEFAULT NULL,
  `valide_le` datetime DEFAULT NULL,
  `justification` varchar(255) DEFAULT NULL,
  `observation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_presence`),
  UNIQUE KEY `unique_presence` (`id_employe`,`date_presence`),
  CONSTRAINT `presences_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id_employe`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `presences` WRITE;
INSERT INTO `presences` VALUES (2,2,'declaration','2026-09-13','12:05:45',NULL,245,'retard','validee',1,'2026-09-13 12:07:26',NULL,NULL,'2026-09-13 10:05:45','2026-09-13 10:07:26');
UNLOCK TABLES;
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id_service` int(11) NOT NULL AUTO_INCREMENT,
  `nom_service` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_service`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `services` WRITE;
INSERT INTO `services` VALUES (1,'Direction Générale','Direction générale de l\'entreprise','2026-09-11 10:59:05','2026-09-17 11:06:05'),(2,'Ressources Humaines','Gestion du personnel et des ressources humaines','2026-09-11 10:59:05','2026-09-11 10:59:05'),(3,'Informatique','Service des technologies de l\'information','2026-09-11 10:59:05','2026-09-11 10:59:05'),(4,'Finance','Service financier et comptable','2026-09-11 10:59:05','2026-09-11 10:59:05'),(5,'Marketing','Service marketing et communication','2026-09-11 10:59:05','2026-09-11 10:59:05'),(6,'Administration','Service administratif','2026-09-11 10:59:05','2026-09-11 10:59:05');
UNLOCK TABLES;
DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT,
  `nom_complet` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','rh','employe') NOT NULL DEFAULT 'employe',
  `photo` varchar(255) DEFAULT NULL,
  `statut` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `derniere_connexion` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `utilisateurs` WRITE;
INSERT INTO `utilisateurs` VALUES (1,'Administrateur','admin@sirh.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin',NULL,'actif','2026-09-17 13:06:43','2026-09-11 10:59:05','2026-09-17 11:06:43'),(3,'PERLE TSHIBUABUA','perle.tshibuabua@globit.com','$2y$10$/1mU9jzUfhxgreNuUE3hoepMPVk10zjdwaS.BaeqLtHv7qNyyIl6m','employe',NULL,'actif','2026-09-13 12:03:34','2026-09-13 09:59:27','2026-09-13 10:03:34');
UNLOCK TABLES;

SET UNIQUE_CHECKS = 1;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
--  Fin de l'export - GLOBIT SIRH v1.0
-- ============================================================================
