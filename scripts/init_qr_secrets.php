<?php
/**
 * Genere les secrets QR dynamiques manquants pour chaque employe actif.
 * Un secret = 64 caracteres hex (256 bits), unique par employe, stocke en base.
 * Jamais affiche cote employe : il sert a signer le jeton QR de 30 secondes.
 *
 * Usage CLI : php scripts/init_qr_secrets.php            (cree les secrets manquants)
 *             php scripts/init_qr_secrets.php --all      (regenere tous les secrets)
 *             php scripts/init_qr_secrets.php --id=3     (regenere le secret d'un employe)
 */
define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Database.php';

$pdo = Database::getInstance()->getConnection();

$all = in_array('--all', $argv ?? [], true);
$idCible = null;
foreach (($argv ?? []) as $arg) {
    if (strpos($arg, '--id=') === 0) {
        $idCible = (int) substr($arg, 5);
    }
}

if ($idCible) {
    $sql = "SELECT id_employe FROM employes WHERE id_employe = :id";
} elseif ($all) {
    $sql = "SELECT id_employe FROM employes ORDER BY id_employe";
} else {
    $sql = "SELECT id_employe FROM employes WHERE qr_secret IS NULL OR qr_secret = '' ORDER BY id_employe";
}

$stmt = $pdo->query($sql);
$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

$count = 0;
foreach ($ids as $id) {
    $secret = bin2hex(random_bytes(32));
    $upd = $pdo->prepare("UPDATE employes SET qr_secret = :secret WHERE id_employe = :id");
    $upd->execute(['secret' => $secret, 'id' => $id]);
    $count++;
}

echo date('Y-m-d H:i:s') . " - Secrets QR generes : {$count}\n";