USE sirh;

ALTER TABLE presences
    ADD COLUMN source ENUM('qrcode','declaration','manuel') NOT NULL DEFAULT 'manuel' AFTER id_employe,
    ADD COLUMN validation ENUM('auto','en_attente','validee','rejetee') NOT NULL DEFAULT 'auto' AFTER statut,
    ADD COLUMN valide_par INT DEFAULT NULL AFTER validation,
    ADD COLUMN valide_le DATETIME DEFAULT NULL AFTER valide_par,
    ADD COLUMN justification VARCHAR(255) DEFAULT NULL AFTER valide_le;

ALTER TABLE employes
    ADD COLUMN est_direction TINYINT(1) NOT NULL DEFAULT 0 AFTER id_service;
