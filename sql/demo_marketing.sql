-- ============================================================================
--  GLOBIT SAS - SIRH - Jeu de donnees MARKETING (fictif)
--  Aucune donnee reelle : uniquement des personnes inventees, pour les
--  captures d'ecran, flyers et publications de la startup.
--  Usage : creer la base sirh_demo puis importer ce fichier.
--  Les comptes ont le mot de passe : Globit@2026  (comme les comptes auto)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `sirh_demo` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sirh_demo`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `annonces`, `api_tokens`, `bulletins`, `conges`, `conversations`,
  `employes`, `formations`, `formations_employes`, `journal_activite`, `messages`,
  `notifications`, `parametres_paie`, `primes`, `presences`, `services`, `utilisateurs`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- Services (fictifs)
-- ----------------------------------------------------------------------------
CREATE TABLE `services` (
  `id_service` int(11) NOT NULL AUTO_INCREMENT,
  `nom_service` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_service`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `services` VALUES
(1,'Direction Generale','Pilotage strategique de l\'entreprise',NOW(),NOW()),
(2,'Ressources Humaines','Recrutement, paie et suivi du personnel',NOW(),NOW()),
(3,'Comptabilite','Tenue de la comptabilite et des impots',NOW(),NOW()),
(4,'Informatique','Systemes, reseaux et support technique',NOW(),NOW()),
(5,'Commercial','Ventes et relation client',NOW(),NOW()),
(6,'Logistique','Approvisionnements et stocks',NOW(),NOW());

-- ----------------------------------------------------------------------------
-- Utilisateurs (mot de passe : Globit@2026 - hash bcrypt genere)
-- ----------------------------------------------------------------------------
CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT,
  `nom_complet` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','rh','employe') NOT NULL DEFAULT 'employe',
  `photo` varchar(255) DEFAULT NULL,
  `statut` enum('actif','inactif','suspendu') NOT NULL DEFAULT 'actif',
  `derniere_connexion` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `utilisateurs` (`id_utilisateur`,`nom_complet`,`email`,`mot_de_passe`,`role`,`statut`,`derniere_connexion`,`created_at`,`updated_at`) VALUES
(1,'Administrateur GLOBIT SAS','admin@sirh.local','__HASH_ADMIN__','admin','actif',NOW(),NOW(),NOW()),
(2,'Ruth Kalonji','ruth.kalonji@globit.com','__HASH_EMPLOYE__','rh','actif',NOW(),NOW(),NOW()),
(3,'Jean-Pierre Mukendi','jeanpierre.mukendi@globit.com','__HASH_EMPLOYE__','employe','actif',NOW(),NOW(),NOW()),
(4,'Marie Kanku','marie.kanku@globit.com','__HASH_EMPLOYE__','employe','actif',NOW(),NOW(),NOW()),
(5,'Patrick Ilunga','patrick.ilunga@globit.com','__HASH_EMPLOYE__','employe','actif',NOW(),NOW(),NOW()),
(6,'Chantal Mbayo','chantal.mbayo@globit.com','__HASH_EMPLOYE__','employe','actif',NOW(),NOW(),NOW()),
(7,'David Tshibangu','david.tshibangu@globit.com','__HASH_EMPLOYE__','employe','actif',NOW(),NOW(),NOW()),
(8,'Sarah Nsimba','sarah.nsimba@globit.com','__HASH_EMPLOYE__','employe','actif',NOW(),NOW(),NOW()),
(9,'Serge Lutumba','serge.lutumba@globit.com','__HASH_EMPLOYE__','employe','actif',NOW(),NOW(),NOW()),
(10,'Nathalie Bope','nathalie.bope@globit.com','__HASH_EMPLOYE__','employe','actif',NOW(),NOW(),NOW()),
(11,'Fabrice Kabangu','fabrice.kabangu@globit.com','__HASH_EMPLOYE__','employe','actif',NOW(),NOW(),NOW());

-- ----------------------------------------------------------------------------
-- Employes (tous fictifs)
-- ----------------------------------------------------------------------------
CREATE TABLE `employes` (
  `id_employe` int(11) NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `postnom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `sexe` enum('M','F') DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `poste` varchar(100) DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `salaire` decimal(10,2) DEFAULT 0.00,
  `statut` enum('actif','inactif','suspendu') DEFAULT 'actif',
  `id_service` int(11) DEFAULT NULL,
  `est_direction` tinyint(1) DEFAULT 0,
  `id_utilisateur` int(11) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `qr_secret` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_employe`),
  UNIQUE KEY `matricule` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `employes` (`id_employe`,`matricule`,`nom`,`postnom`,`prenom`,`sexe`,`date_naissance`,`lieu_naissance`,`adresse`,`telephone`,`email`,`poste`,`date_embauche`,`salaire`,`statut`,`id_service`,`est_direction`,`id_utilisateur`,`qr_secret`) VALUES
(1,'EMP-0001','MUKENDI','MUKENDI','Jean-Pierre','M','1985-04-12','Kinshasa','Av. de la Liberation','+243 81 000 0001','jeanpierre.mukendi@globit.com','Directeur General','2019-02-01',3500.00,'actif',1,1,3,'1111111111111111111111111111111111111111111111111111111111111111'),
(2,'EMP-0002','KANKU','KANKU','Marie','F','1990-07-23','Matadi','Rue des Ecoles','+243 81 000 0002','marie.kanku@globit.com','Responsable RH','2020-06-15',2200.00,'actif',2,0,4,'2222222222222222222222222222222222222222222222222222222222222222'),
(3,'EMP-0003','ILUNGA','ILUNGA','Patrick','M','1993-11-05','Lubumbashi','Av. Kasai','+243 81 000 0003','patrick.ilunga@globit.com','Comptable Principal','2021-01-11',1900.00,'actif',3,0,5,'3333333333333333333333333333333333333333333333333333333333333333'),
(4,'EMP-0004','MBAYO','MBAYO','Chantal','F','1994-02-18','Mbuji-Mayi','Rue de la Gare','+243 81 000 0004','chantal.mbayo@globit.com','Chargee de Clientele','2021-09-06',1500.00,'actif',5,0,6,'4444444444444444444444444444444444444444444444444444444444444444'),
(5,'EMP-0005','TSHIBANGU','TSHIBANGU','David','M','1996-09-30','Kinshasa','Ctre Gombe','+243 81 000 0005','david.tshibangu@globit.com','Technicien Informatique','2022-03-14',1700.00,'actif',4,0,7,'5555555555555555555555555555555555555555555555555555555555555555'),
(6,'EMP-0006','NSIMBA','NSIMBA','Sarah','F','1997-06-08','Kinshasa','Av. Kasa-Vubu','+243 81 000 0006','sarah.nsimba@globit.com','Assistante Administrative','2022-08-22',1300.00,'actif',2,0,8,'6666666666666666666666666666666666666666666666666666666666666666'),
(7,'EMP-0007','LUTUMBA','LUTUMBA','Serge','M','1988-12-19','Kananga','Route de Likasi','+243 81 000 0007','serge.lutumba@globit.com','Responsable Logistique','2019-11-04',2100.00,'actif',6,0,9,'7777777777777777777777777777777777777777777777777777777777777777'),
(8,'EMP-0008','BOPE','BOPE','Nathalie','F','1992-05-27','Matadi','Quartier Moussasa','+243 81 000 0008','nathalie.bope@globit.com','Vendeuse','2023-01-16',900.00,'actif',5,0,10,'8888888888888888888888888888888888888888888888888888888888888888'),
(9,'EMP-0009','KABANGU','KABANGU','Fabrice','M','1995-08-03','Kinshasa','CNganda','+243 81 000 0009','fabrice.kabangu@globit.com','Magasinier','2023-05-02',800.00,'actif',6,0,11,'9999999999999999999999999999999999999999999999999999999999999999');

-- ----------------------------------------------------------------------------
-- Presences : aujourd'hui + 10 jours d'historique (donnees simulees)
-- ----------------------------------------------------------------------------
CREATE TABLE `presences` (
  `id_presence` int(11) NOT NULL AUTO_INCREMENT,
  `id_employe` int(11) NOT NULL,
  `source` enum('qr','declaration','manuel') NOT NULL DEFAULT 'qr',
  `date_presence` date NOT NULL,
  `heure_arrivee` time DEFAULT NULL,
  `heure_depart` time DEFAULT NULL,
  `retard` int(11) DEFAULT 0,
  `statut` enum('present','retard','absent','conge','justifie') NOT NULL DEFAULT 'present',
  `validation` enum('validee','en_attente','rejetee') NOT NULL DEFAULT 'validee',
  `valide_par` int(11) DEFAULT NULL,
  `valide_le` datetime DEFAULT NULL,
  `justification` text DEFAULT NULL,
  `observation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_presence`),
  UNIQUE KEY `employe_jour` (`id_employe`,`date_presence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `presences` (`id_employe`,`source`,`date_presence`,`heure_arrivee`,`heure_depart`,`retard`,`statut`,`validation`,`valide_par`) VALUES
(1,'qr',CURDATE(),'07:52:00','17:06:00',0,'present','validee',2),
(2,'qr',CURDATE(),'07:58:00','17:14:00',0,'present','validee',2),
(3,'qr',CURDATE(),'09:47:00','17:02:00',107,'retard','validee',2),
(4,'qr',CURDATE(),'08:04:00','17:00:00',4,'present','validee',2),
(5,'qr',CURDATE(),'07:49:00','17:11:00',0,'present','validee',2),
(6,'qr',CURDATE(),'10:23:00',NULL,383,'retard','en_attente',NULL),
(7,'qr',CURDATE(),'07:55:00','16:58:00',0,'present','validee',2),
(8,'qr',CURDATE(),'08:41:00','17:03:00',41,'retard','validee',2),
(9,'qr',CURDATE(),'07:51:00','16:55:00',0,'present','validee',2);

INSERT INTO `presences` (`id_employe`,`source`,`date_presence`,`heure_arrivee`,`heure_depart`,`retard`,`statut`,`validation`,`valide_par`) VALUES
(1,'qr',DATE_SUB(CURDATE(),INTERVAL 1 DAY),'07:55:00','17:02:00',0,'present','validee',2),
(2,'qr',DATE_SUB(CURDATE(),INTERVAL 1 DAY),'08:01:00','17:00:00',1,'present','validee',2),
(3,'qr',DATE_SUB(CURDATE(),INTERVAL 1 DAY),'07:57:00','17:09:00',0,'present','validee',2),
(4,'qr',DATE_SUB(CURDATE(),INTERVAL 2 DAY),'08:12:00','17:01:00',12,'retard','validee',2),
(5,'qr',DATE_SUB(CURDATE(),INTERVAL 2 DAY),'07:50:00','16:59:00',0,'present','validee',2),
(6,'qr',DATE_SUB(CURDATE(),INTERVAL 2 DAY),'07:59:00','17:05:00',0,'present','validee',2),
(7,'qr',DATE_SUB(CURDATE(),INTERVAL 3 DAY),'08:05:00','17:00:00',5,'present','validee',2),
(8,'qr',DATE_SUB(CURDATE(),INTERVAL 3 DAY),'08:33:00','17:08:00',33,'retard','validee',2),
(9,'qr',DATE_SUB(CURDATE(),INTERVAL 3 DAY),'07:48:00','16:57:00',0,'present','validee',2),
(1,'qr',DATE_SUB(CURDATE(),INTERVAL 4 DAY),'07:53:00','17:01:00',0,'present','validee',2),
(3,'qr',DATE_SUB(CURDATE(),INTERVAL 4 DAY),'07:59:00','17:12:00',0,'present','validee',2),
(5,'qr',DATE_SUB(CURDATE(),INTERVAL 5 DAY),'07:47:00','17:00:00',0,'present','validee',2),
(7,'qr',DATE_SUB(CURDATE(),INTERVAL 5 DAY),'08:02:00','16:59:00',2,'present','validee',2),
(2,'qr',DATE_SUB(CURDATE(),INTERVAL 6 DAY),'07:56:00','17:07:00',0,'present','validee',2),
(4,'qr',DATE_SUB(CURDATE(),INTERVAL 6 DAY),'08:22:00','17:00:00',22,'retard','validee',2),
(9,'qr',DATE_SUB(CURDATE(),INTERVAL 7 DAY),'07:54:00','17:04:00',0,'present','validee',2),
(8,'qr',DATE_SUB(CURDATE(),INTERVAL 8 DAY),'08:00:00','17:00:00',0,'present','validee',2),
(6,'qr',DATE_SUB(CURDATE(),INTERVAL 9 DAY),'08:15:00','16:58:00',15,'retard','validee',2),
(1,'qr',DATE_SUB(CURDATE(),INTERVAL 10 DAY),'07:58:00','17:06:00',0,'present','validee',2);

-- ----------------------------------------------------------------------------
-- Conges (fictifs)
-- ----------------------------------------------------------------------------
CREATE TABLE `conges` (
  `id_conge` int(11) NOT NULL AUTO_INCREMENT,
  `id_employe` int(11) NOT NULL,
  `type_conge` varchar(50) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nombre_jours` int(11) NOT NULL DEFAULT 1,
  `motif` text DEFAULT NULL,
  `statut` enum('en_attente','approuve','rejete') NOT NULL DEFAULT 'en_attente',
  `motif_refus` text DEFAULT NULL,
  `approuve_par` int(11) DEFAULT NULL,
  `date_approbation` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_conge`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `conges` (`id_employe`,`type_conge`,`date_debut`,`date_fin`,`nombre_jours`,`motif`,`statut`,`approuve_par`,`date_approbation`) VALUES
(6,'Conge annuel',DATE_ADD(CURDATE(),INTERVAL 6 DAY),DATE_ADD(CURDATE(),INTERVAL 20 DAY),15,'Conge annuel demande','en_attente',NULL,NULL),
(4,'Conge maladie',DATE_SUB(CURDATE(),INTERVAL 4 DAY),DATE_SUB(CURDATE(),INTERVAL 2 DAY),3,'Certificat medical fourni','approuve',2,NOW()),
(8,'Permission',DATE_ADD(CURDATE(),INTERVAL 2 DAY),DATE_ADD(CURDATE(),INTERVAL 2 DAY),1,'Rendez-vous administratif','en_attente',NULL,NULL),
(3,'Conge annuel',DATE_ADD(CURDATE(),INTERVAL 30 DAY),DATE_ADD(CURDATE(),INTERVAL 44 DAY),15,'Conge annuel','approuve',2,NOW());

-- ----------------------------------------------------------------------------
-- Bulletins de paie (fictifs)
-- ----------------------------------------------------------------------------
CREATE TABLE `bulletins` (
  `id_bulletin` int(11) NOT NULL AUTO_INCREMENT,
  `id_employe` int(11) NOT NULL,
  `mois` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `salaire_base` decimal(12,2) NOT NULL DEFAULT 0.00,
  `primes` decimal(12,2) NOT NULL DEFAULT 0.00,
  `detail_primes` text DEFAULT NULL,
  `heures_supplementaires` int(11) NOT NULL DEFAULT 0,
  `montant_heures_sup` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_brut` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_retenues` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_net` decimal(12,2) NOT NULL DEFAULT 0.00,
  `statut` enum('brouillon','valide','paye') NOT NULL DEFAULT 'brouillon',
  `date_generation` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_bulletin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bulletins` (`id_employe`,`mois`,`annee`,`salaire_base`,`primes`,`heures_supplementaires`,`montant_heures_sup`,`total_brut`,`total_retenues`,`total_net`,`statut`,`date_generation`) VALUES
(1,9,2026,3500.00,250.00,6,105.00,3855.00,520.00,3335.00,'paye',CURDATE()),
(2,9,2026,2200.00,100.00,0,0.00,2300.00,310.00,1990.00,'paye',CURDATE()),
(3,9,2026,1900.00,0.00,4,70.00,1970.00,265.00,1705.00,'valide',CURDATE()),
(5,9,2026,1700.00,50.00,2,35.00,1785.00,240.00,1545.00,'valide',CURDATE()),
(7,9,2026,2100.00,0.00,0,0.00,2100.00,283.00,1817.00,'valide',CURDATE());

-- ----------------------------------------------------------------------------
-- Primes fixes par employe (fictives, alignees sur les bulletins ci-dessus)
-- ----------------------------------------------------------------------------
CREATE TABLE `primes` (
  `id_prime` int(11) NOT NULL AUTO_INCREMENT,
  `id_employe` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `montant` decimal(12,2) NOT NULL DEFAULT 0.00,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_prime`),
  KEY `idx_primes_employe` (`id_employe`),
  CONSTRAINT `fk_primes_employe` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id_employe`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `primes` (`id_employe`,`libelle`,`montant`,`actif`) VALUES
(1,'Prime de fonction',150.00,1),
(1,'Indemnite de risque',100.00,1),
(2,'Prime de rendement',100.00,1),
(5,'Prime de transport',50.00,1);

-- ----------------------------------------------------------------------------
-- Notifications, annonces et messages (fictifs)
-- ----------------------------------------------------------------------------
CREATE TABLE `notifications` (
  `id_notification` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `type_notif` varchar(50) DEFAULT 'info',
  `est_lu` tinyint(1) DEFAULT 0,
  `lien` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_notification`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notifications` (`id_utilisateur`,`titre`,`message`,`type_notif`,`est_lu`) VALUES
(1,'Demande de conge a valider','Marie Kanku a soumis une demande de conge annuel de 15 jours.','conge',0),
(1,'Pointage en attente de validation','Sarah Nsimba a pointe a 10h23 : 383 minutes de retard.','presence',0),
(1,'Bulletins de paie generes','5 bulletins de paie du mois sont pretos a etre valides.','paie',1),
(3,'Bienvenue sur GLOBIT SAS','Votre compte employe est actif. Votre QR de presence est disponible.','info',0),
(4,'Votre QR de presence est actif','Scannez-le a l\'accueil pour pointer. Il change toutes les 30 secondes.','info',0);

CREATE TABLE `annonces` (
  `id_annonce` int(11) NOT NULL AUTO_INCREMENT,
  `id_expediteur` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `contenu` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_annonce`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `annonces` (`id_expediteur`,`titre`,`contenu`) VALUES
(1,'Mise a jour de la paie','Les bulletins du mois sont disponibles dans votre espace. Merci de les verifier avant la fin du mois.'),
(1,'Rappel : reunion d\'equipe','Reunion d\'equipe vendredi a 09h00 en salle de conference. Votre presence est souhaitee.');

CREATE TABLE `conversations` (
  `id_conversation` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur_a` int(11) NOT NULL,
  `id_utilisateur_b` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_conversation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL AUTO_INCREMENT,
  `id_conversation` int(11) NOT NULL,
  `id_expediteur` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `est_lu` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `conversations` VALUES (1,1,2,NOW());
INSERT INTO `messages` (`id_conversation`,`id_expediteur`,`contenu`,`est_lu`) VALUES
(1,1,'Bonjour Ruth, peux-tu preparer les bulletins du mois ?',1),
(1,2,'Bonjour, ils sont prets. Je les depose dans le dossier Payroll.',0);

-- ----------------------------------------------------------------------------
-- Journal d'activite (fictif)
-- ----------------------------------------------------------------------------
CREATE TABLE `journal_activite` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` varchar(255) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `ip_adresse` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `journal_activite` (`id_utilisateur`,`action`,`details`,`module`,`ip_adresse`) VALUES
(1,'Generation de bulletins','5 bulletins de paie generes','paie','::1'),
(2,'Validation de conge','Conge maladie valide pour Chantal Mbayo','conges','::1'),
(1,'Export PDF','Export de la liste des employes','employes','::1');
-- ----------------------------------------------------------------------------
-- Tables de support : structure identique a la base de production
-- ----------------------------------------------------------------------------
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

INSERT INTO `parametres_paie` (`nom_parametre`,`valeur`,`type_parametre`,`description`) VALUES
('Taux IFP 20 pourcent',20.00,'pourcentage','Indemnite de fonction et de pres'),
('Taux IRB 10 pourcent',10.00,'pourcentage','Impot sur le revenu brut'),
('Taux CNSS 6 pourcent',6.00,'pourcentage','Caisse nationale de securite sociale'),
('Nombre de jours ouvrables',22,'jour','Base de calcul des heures'),
('Taux heure supplementaire',25.00,'pourcentage','Majoration des heures de nuit'),
('Prime de transport',100.00,'montant','Indemnite mensuelle de transport'),
('Seuil de paiement',500.00,'montant','Montant minimum pour declencher un virement');

INSERT INTO `formations` (`titre`,`description`,`duree`,`type_formation`,`statut`,`date_debut`,`date_fin`) VALUES
('Securite informatique','Proteger les donnees de l''entreprise','1 jour','interne','en_cours',CURDATE(),DATE_ADD(CURDATE(),INTERVAL 14 DAY)),
('Gestion du stress','Mieux gerer la pression au travail','2 jours','externe','planifiee',DATE_ADD(CURDATE(),INTERVAL 20 DAY),DATE_ADD(CURDATE(),INTERVAL 21 DAY));

INSERT INTO `formations_employes` (`id_formation`,`id_employe`,`statut`,`note`,`observation`) VALUES
(1,5,'en_cours',NULL,'Participant'),
(1,7,'en_cours',NULL,'Participant');