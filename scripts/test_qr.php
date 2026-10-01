<?php
/**
 * Test CLI du QR dynamique (etape 2) - usage : php scripts/test_qr.php
 */
define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/models/QrcodeModel.php';
require_once ROOT_PATH . '/models/PresenceModel.php';

$qr = new QrcodeModel();
$pm = new PresenceModel();

$row = $qr->find(['statut' => 'actif'], 'id_employe ASC');
if (empty($row)) { echo "Aucun employe actif\n"; exit(1); }
$emp = $row[0];
$id = $emp['id_employe'];
echo "Employe test : #{$id} {$emp['prenom']} {$emp['nom']} ({$emp['matricule']})\n\n";

// 1. Secret
$secret = $qr->getSecret($id);
echo "1. Secret present : " . ($secret ? 'oui (' . strlen($secret) . ' car.)' : 'NON') . "\n";
echo "   ensureSecret stable : " . ($qr->ensureSecret($id) === $secret ? 'oui' : 'NON') . "\n";

// 2. Jeton
$t = $qr->genererJeton($id);
echo "\n2. Jeton : {$t['code']}\n";
echo "   Periode={$t['periode']}  Restant={$t['restant']}s\n";
$parts = explode('|', $t['code']);
echo "   Format SIRHQR1|id|periode|signature : " . (count($parts) === 4 ? 'ok' : 'KO') . "\n";

// 3. Validation d'un jeton valide
$v = $qr->validerJeton($t['code']);
echo "\n3. Validation jeton frais : " . ($v['success'] ? 'OK' : 'KO - ' . $v['message']) . "\n";

// 4. Jeton falsifie (signature modifiee)
$bad = $t['code'] . 'X';
$v4 = $qr->validerJeton($bad);
echo "4. Jeton falsifie refuse : " . (!$v4['success'] ? 'OK - ' . $v4['message'] : 'KO') . "\n";

// 5. Jeton d'un autre employe (signature croisee)
if (isset($row[1])) {
    $t2 = $qr->genererJeton($row[1]['id_employe']);
    $v5 = $qr->validerJeton($t2['code']);
    $mauvaisId = explode('|', $t['code'])[1];
    $falsifie = 'SIRHQR1|' . $mauvaisId . '|' . explode('|', $t2['code'])[2] . '|' . explode('|', $t2['code'])[3];
    $v5b = $qr->validerJeton($falsifie);
    echo "5. Signature d'un autre secret refusee : " . (!$v5b['success'] ? 'OK - ' . $v5b['message'] : 'KO') . "\n";
}

// 6. Jeton expire (periode ancienne)
$ancien = 'SIRHQR1|' . $id . '|' . ($t['periode'] - 3) . '|abc';
$v6 = $qr->validerJeton($ancien);
echo "6. Jeton hors fenetre refuse : " . (!$v6['success'] ? 'OK - ' . $v6['message'] : 'KO') . "\n";

// 7. Format invalide
$v7 = $qr->validerJeton('hello world');
echo "7. Format invalide refuse : " . (!$v7['success'] ? 'OK - ' . $v7['message'] : 'KO') . "\n";

// 8. Simulation du pointage : arrivee puis depart
$p = $pm->findTodayByEmployee($id);
if (empty($p)) {
    echo "\n8. Scan 1 (arrivee) :\n";
    $r1 = $pm->scanPointage($id, 1);
    echo "   " . ($r1['success'] ? 'OK - ' . $r1['message'] : 'KO - ' . $r1['message']) . "\n";
    echo "   source=" . ($r1['presence']['source'] ?? '?') . " validation=" . ($r1['presence']['validation'] ?? '?') . "\n";

    echo "\n9. Scan 2 (depart) :\n";
    $r2 = $pm->scanPointage($id, 1);
    echo "   " . ($r2['success'] ? 'OK - ' . $r2['message'] : 'KO - ' . $r2['message']) . "\n";

    echo "\n10. Scan 3 (refus, journee complete) :\n";
    $r3 = $pm->scanPointage($id, 1);
    echo "    " . (!$r3['success'] ? 'OK - ' . $r3['message'] : 'KO') . "\n";

    // 11. Scan du lendemain simule avec employe 1 (retard apres 10h)
    $hier = date('Y-m-d', strtotime('-1 day'));
    echo "\n11. Nettoyage : suppression du pointage de test du jour\n";
    $del = $pm->delete($r1['presence']['id_presence']);
    echo "    Supprime : " . ($del ? 'oui' : 'non') . "\n";
} else {
    echo "\nUne presence existe deja pour aujourd'hui - scan non simule (id " . $p[0]['id_presence'] . ")\n";
}

echo "\nTermine.\n";