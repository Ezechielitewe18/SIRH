-- =============================================
-- SIRH/GLOBIT - Présence "Déclaration + validation RH"
-- Pointage QR réservé à la Direction Générale
-- =============================================
USE sirh;

-- Workflow de présence :
--   source      : qrcode | declaration | manuel
--   validation  : auto (QR Direction) | en_attente (déclaration employé)
--                 | validee | rejetee (décision RH/directeur)
ALTER TABLE presences
    ADD COLUMN source ENUM('qrcode','declaration','manuel') NOT NULL DEFAULT 'manuel' AFTER id_employe,
    ADD COLUMN validation ENUM('auto','en_attente','validee','rejetee') NOT NULL DEFAULT 'auto' AFTER statut,
    ADD COLUMN valide_par INT DEFAULT NULL AFTER validation,
    ADD COLUMN valide_le DATETIME DEFAULT NULL AFTER valide_par,
    ADD COLUMN justification VARCHAR(255) DEFAULT NULL AFTER valide_le;

-- Employé de la Direction Générale (pointage QR autorisé)
ALTER TABLE employes
    ADD COLUMN est_direction TINYINT(1) NOT NULL DEFAULT 0 AFTER id_service;