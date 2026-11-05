<?php
/**
 * Simulateur d'evenements de pointage — Phase 1.
 *
 * Objectif : tester le moteur sans aucun materiel.
 *
 * Le script travaille dans une TRANSACTION et fait un ROLLBACK a la fin :
 * aucune donnee reelle n'est conservee. Les identifiants auto-increment
 * peuvent avancer, sans consequence.
 *
 * Usage : php scripts/simuler_pointage.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Ce script s'execute en ligne de commande uniquement.\n");
}

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/models/DispositifModel.php';
require_once ROOT_PATH . '/models/CarteModel.php';
require_once ROOT_PATH . '/models/PointageModel.php';
require_once ROOT_PATH . '/models/QrcodeModel.php';

$db = Database::getInstance()->getConnection();
$moteur = new PointageModel();
$dispositifs = new DispositifModel();
$cartes = new CarteModel();
$qrcode = new QrcodeModel();

$total = 0;
$reussis = 0;

function verifier($libelle, $condition, $detail = '') {
    global $total, $reussis;
    $total++;
    if ($condition) {
        $reussis++;
        echo "  [OK]   {$libelle}\n";
    } else {
        echo "  [ECHEC] {$libelle}" . ($detail !== '' ? "  -> {$detail}" : '') . "\n";
    }
}

function creerEmploye($db, $matricule, $statut = 'actif') {
    $sql = "INSERT INTO employes (matricule, nom, prenom, sexe, date_embauche, statut)
            VALUES (:m, 'TEST', 'Simulateur', 'M', :d, :s)";
    $db->prepare($sql)->execute([
        'm' => $matricule,
        'd' => date('Y-m-d'),
        's' => $statut,
    ]);
    return (int) $db->lastInsertId();
}

echo "GLOBIT SIRH — Simulateur de pointage (Phase 1)\n";
echo "------------------------------------------------\n";

$db->beginTransaction();

try {
    // Horloge de reference du simulateur : 19:00 aujourd'hui, pour que les
    // scenarios du matin et du soir soient tous coherents.
    $moteur->fixerHorloge(strtotime(date('Y-m-d') . ' 19:00:00'));

    // --- Jeu de test -----------------------------------------------------
    $suffixe = substr((string) time(), -5);
    $empA = creerEmploye($db, 'SIM-A-' . $suffixe);
    $empB = creerEmploye($db, 'SIM-B-' . $suffixe);
    $empC = creerEmploye($db, 'SIM-C-' . $suffixe, 'suspendu');
    $empD = creerEmploye($db, 'SIM-D-' . $suffixe);

    $dev = $dispositifs->creer([
        'code' => 'SIM-RFID-' . $suffixe,
        'libelle' => 'Lecteur de simulation',
        'type' => 'rfid',
        'emplacement' => 'Banc de test',
    ]);
    if (!$dev['ok']) {
        throw new RuntimeException('Creation appareil impossible : ' . $dev['message']);
    }
    $idDev = $dev['id_dispositif'];

    $devBio = $dispositifs->creer([
        'code' => 'SIM-BIO-' . $suffixe,
        'libelle' => 'Terminal de simulation',
        'type' => 'biometrie',
        'emplacement' => 'Banc de test',
    ]);
    $idDevBio = $devBio['id_dispositif'];

    $uidA = '04A2' . $suffixe . 'B1';
    $uidC = '04C3' . $suffixe . 'B2';
    $carteA = $cartes->attribuer($empA, $uidA, 'Mifare Classic', 'Badge test A');
    $carteC = $cartes->attribuer($empC, $uidC, 'Mifare Classic', 'Badge test C');

    $aujourdhui = date('Y-m-d');
    $hier = date('Y-m-d', strtotime('-1 day'));

    // --- 1. Arrivee RFID ------------------------------------------------
    echo "\n1. Arrivee RFID a 08:03\n";
    $r = $moteur->traiter([
        'source' => 'rfid',
        'identifiant' => $uidA,
        'horodatage' => $aujourdhui . ' 08:03:12',
        'id_dispositif' => $idDev,
        'uid_externe' => 'EVT-A-001',
        'donnees_brutes' => ['verification' => 'ok', 'appareil' => 'sim'],
    ]);
    verifier('pointage accepte', $r['ok'] === true, $r['message'] ?? '');
    verifier('action = arrivee', ($r['action'] ?? '') === 'arrivee', $r['action'] ?? '');
    verifier('retard calcule sur 08:03 (3 min)', ($r['message'] ?? '') !== '' && strpos($r['message'], '3 min') !== false, $r['message'] ?? '');
    $idPresenceA = $r['id_presence'] ?? null;
    $stmt = $db->prepare("SELECT * FROM presences WHERE id_presence = :id");
    $stmt->execute(['id' => $idPresenceA]);
    $presenceA = $stmt->fetch();
    verifier('heure_arrivee = 08:03:12', ($presenceA['heure_arrivee'] ?? '') === '08:03:12', $presenceA['heure_arrivee'] ?? '');
    verifier('source = rfid', ($presenceA['source'] ?? '') === 'rfid', $presenceA['source'] ?? '');

    // --- 2. Anti-doublon materiel ---------------------------------------
    echo "\n2. Renvoi du meme evenement (coupure reseau)\n";
    $r = $moteur->traiter([
        'source' => 'rfid',
        'identifiant' => $uidA,
        'horodatage' => $aujourdhui . ' 08:03:12',
        'id_dispositif' => $idDev,
        'uid_externe' => 'EVT-A-001',
    ]);
    verifier('detecte comme doublon', ($r['code'] ?? '') === 'doublon', $r['code'] ?? '');
    $nb = $db->query("SELECT COUNT(*) AS n FROM pointages_evenements WHERE uid_externe = 'EVT-A-001'")->fetch();
    verifier('un seul evenement enregistre', (int) $nb['n'] === 1, 'n=' . $nb['n']);

    // --- 3. Depart -------------------------------------------------------
    echo "\n3. Depart RFID a 17:05\n";
    $r = $moteur->traiter([
        'source' => 'rfid',
        'identifiant' => $uidA,
        'horodatage' => $aujourdhui . ' 17:05:40',
        'id_dispositif' => $idDev,
        'uid_externe' => 'EVT-A-002',
    ]);
    verifier('action = depart', ($r['action'] ?? '') === 'depart', $r['action'] ?? '');
    $stmt->execute(['id' => $idPresenceA]);
    $presenceA = $stmt->fetch();
    verifier('heure_depart = 17:05:40', ($presenceA['heure_depart'] ?? '') === '17:05:40', $presenceA['heure_depart'] ?? '');

    // --- 4. Troisieme pointage (option B) --------------------------------
    echo "\n4. Troisieme pointage du jour (retour de pause)\n";
    $r = $moteur->traiter([
        'source' => 'rfid',
        'identifiant' => $uidA,
        'horodatage' => $aujourdhui . ' 18:30:00',
        'id_dispositif' => $idDev,
        'uid_externe' => 'EVT-A-003',
    ]);
    verifier('journee signalee complete', ($r['code'] ?? '') === 'journee_complete', $r['code'] ?? '');
    $stmt->execute(['id' => $idPresenceA]);
    $presenceA = $stmt->fetch();
    verifier('presences inchangee (heure_depart conservee)', ($presenceA['heure_depart'] ?? '') === '17:05:40', $presenceA['heure_depart'] ?? '');
    $evt = $db->prepare("SELECT * FROM pointages_evenements WHERE uid_externe = 'EVT-A-003'");
    $evt->execute();
    $evt = $evt->fetch();
    verifier('evenement conserve comme preuve', !empty($evt['id_evenement']), '');

    // --- 5. Evenement de la veille ---------------------------------------
    echo "\n5. Evenement date d'hier (resynchronisation)\n";
    $r = $moteur->traiter([
        'source' => 'rfid',
        'identifiant' => $uidA,
        'horodatage' => $hier . ' 08:00:00',
        'id_dispositif' => $idDev,
        'uid_externe' => 'EVT-A-004',
    ]);
    verifier('statut a_valider', ($r['statut'] ?? '') === 'a_valider', $r['statut'] ?? '');
    $cnt = $db->prepare("SELECT COUNT(*) AS n FROM presences WHERE id_employe = :e AND date_presence = :d");
    $cnt->execute(['e' => $empA, 'd' => $hier]);
    $cnt = $cnt->fetch();
    verifier('aucune ecriture silencieuse dans presences', (int) $cnt['n'] === 0, 'n=' . $cnt['n']);

    // --- 6. Biometrie + donnees sensibles --------------------------------
    echo "\n6. Biometrie avec gabarit dans la charge\n";
    $r = $moteur->traiter([
        'source' => 'biometrie',
        'identifiant' => 'SIM-B-' . $suffixe,
        'horodatage' => $aujourdhui . ' 08:10:00',
        'id_dispositif' => $idDevBio,
        'uid_externe' => 'EVT-B-001',
        'donnees_brutes' => [
            'resultat' => 'accepte',
            'score' => 98,
            'template' => 'BASE64_EMPREINTE_A_NE_PAS_STOCKER',
            'image_visage' => 'BASE64_IMAGE_A_NE_PAS_STOCKER',
        ],
    ]);
    verifier('pointage accepte', $r['ok'] === true, $r['message'] ?? '');
    $evt = $db->prepare("SELECT * FROM pointages_evenements WHERE uid_externe = 'EVT-B-001'");
    $evt->execute();
    $evt = $evt->fetch();
    verifier('gabarit biometrique absent de la base', strpos((string) $evt['donnees_brutes'], 'BASE64_EMPREINTE') === false, (string) $evt['donnees_brutes']);
    verifier('image de visage absente de la base', strpos((string) $evt['donnees_brutes'], 'BASE64_IMAGE') === false, (string) $evt['donnees_brutes']);
    verifier('resultat conserve', strpos((string) $evt['donnees_brutes'], 'accepte') !== false, (string) $evt['donnees_brutes']);

    // --- 7. Employe suspendu ---------------------------------------------
    echo "\n7. Employe suspendu\n";
    $r = $moteur->traiter([
        'source' => 'rfid',
        'identifiant' => $uidC,
        'horodatage' => $aujourdhui . ' 08:20:00',
        'id_dispositif' => $idDev,
        'uid_externe' => 'EVT-C-001',
    ]);
    verifier('pointage rejete', ($r['statut'] ?? '') === 'rejete', $r['statut'] ?? '');
    $cnt = $db->prepare("SELECT COUNT(*) AS n FROM presences WHERE id_employe = :e");
    $cnt->execute(['e' => $empC]);
    verifier('aucune presence creee', (int) $cnt->fetch()['n'] === 0, '');

    // --- 8. Carte inconnue ------------------------------------------------
    echo "\n8. Carte inconnue\n";
    $r = $moteur->traiter([
        'source' => 'rfid',
        'identifiant' => 'FFFFFFFF',
        'horodatage' => $aujourdhui . ' 08:25:00',
        'id_dispositif' => $idDev,
        'uid_externe' => 'EVT-X-001',
    ]);
    verifier('mis en attente', ($r['statut'] ?? '') === 'en_attente', $r['statut'] ?? '');

    // --- 9. QR (comportement existant) ------------------------------------
    echo "\n9. Pointage QR\n";
    $jeton = $qrcode->genererJeton($empD);
    $r = $moteur->traiter([
        'source' => 'qr',
        'identifiant' => $jeton['code'],
        'horodatage' => $aujourdhui . ' 08:30:00',
        'uid_externe' => 'EVT-D-001',
    ]);
    verifier('pointage QR accepte', $r['ok'] === true, $r['message'] ?? '');
    verifier('action = arrivee', ($r['action'] ?? '') === 'arrivee', $r['action'] ?? '');

    // --- 10. RFID sans appareil -------------------------------------------
    echo "\n10. RFID sans appareil declare\n";
    $r = $moteur->traiter([
        'source' => 'rfid',
        'identifiant' => $uidA,
        'horodatage' => $aujourdhui . ' 09:00:00',
        'uid_externe' => 'EVT-Y-001',
    ]);
    verifier('refuse', ($r['code'] ?? '') === 'dispositif_requis', $r['code'] ?? '');

    // --- 11. Appareil revoque ---------------------------------------------
    echo "\n11. Appareil revoque\n";
    $dispositifs->revoquer($idDev, 'hors_service', 'test simulateur');
    $r = $moteur->traiter([
        'source' => 'rfid',
        'identifiant' => $uidA,
        'horodatage' => $aujourdhui . ' 09:05:00',
        'id_dispositif' => $idDev,
        'uid_externe' => 'EVT-Z-001',
    ]);
    verifier('refuse', ($r['code'] ?? '') === 'dispositif_inactif', $r['code'] ?? '');

} catch (Throwable $e) {
    echo "\nERREUR : " . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
} finally {
    $db->rollBack();
    echo "\n------------------------------------------------\n";
    echo "Resultat : {$reussis}/{$total} verifications reussies.\n";
    echo "Transaction annulee (rollback) : la base n'a pas ete modifiee.\n";
}
