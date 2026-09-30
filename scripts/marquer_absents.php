<?php
/**
 * Marque automatiquement absents les employés actifs sans déclaration
 * à partir de LIMITE_DECLARATION (10h00) chaque jour.
 * Usage CLI : php scripts/marquer_absents.php
 * Usage web : /scripts/marquer_absents.php  (à protéger ou planifier côté serveur)
 */
define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/models/PresenceModel.php';

$pm = new PresenceModel();
$count = $pm->marquerAbsentsAvantLimite();

echo date('Y-m-d H:i:s') . " - Absences marquees automatiquement : {$count}\n";