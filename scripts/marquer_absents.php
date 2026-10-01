<?php
/**
 * Marque absents les employés actifs sans pointage à partir de
 * LIMITE_DECLARATION (10h00). Action volontaire : elle passe $force = true,
 * car l'application ne cree plus aucune ligne automatiquement.
 * Usage CLI : php scripts/marquer_absents.php
 */
define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/models/PresenceModel.php';

$pm = new PresenceModel();
$count = $pm->marquerAbsentsAvantLimite(true);

echo date('Y-m-d H:i:s') . " - Absences marquees : {$count}\n";