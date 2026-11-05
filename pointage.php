<?php
/**
 * Recoit les evenements de pointage envoyes par un appareil declare
 * (QR, RFID/NFC, terminal biometrique).
 *
 * Endpoint independant, comme api.php : il ne passe pas par index.php et
 * n'utilise donc pas de session ni de jeton CSRF. Chaque requete doit
 * presenter le jeton de l'appareil :
 *
 *   Authorization: Device <jeton>
 *
 * Test de disponibilite (sans jeton) :
 *   GET pointage.php?action=sante
 */

define('ROOT_PATH', __DIR__);

error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/models/DispositifModel.php';
require_once ROOT_PATH . '/models/CarteModel.php';
require_once ROOT_PATH . '/models/PointageModel.php';

function pointage_json($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function pointage_erreur($message, $code = 400) {
    pointage_json(['ok' => false, 'message' => $message], $code);
}

function pointage_jeton() {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if ($header === '' && function_exists('getallheaders')) {
        $headers = getallheaders();
        $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    }
    if (preg_match('/^(?:Device|Bearer)\s+(.+)$/i', trim((string) $header), $m)) {
        return trim($m[1]);
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (($_GET['action'] ?? '') === 'sante') {
        pointage_json([
            'ok' => true,
            'service' => 'pointage',
            'version' => '1.0',
            'heure_serveur' => date('Y-m-d H:i:s'),
            'fuseau' => date_default_timezone_get(),
        ]);
    }
    pointage_erreur('Methode non autorisee : envoyez un POST JSON.', 405);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    pointage_erreur('Methode non autorisee.', 405);
}

// --- 1. Authentification de l'appareil ------------------------------------
$token = pointage_jeton();
if (!$token) {
    header('WWW-Authenticate: Device realm="GLOBIT SIRH"');
    pointage_erreur('Jeton d\'appareil manquant.', 401);
}

$dispositifModel = new DispositifModel();
$dispositif = $dispositifModel->authentifier($token);
if (!$dispositif) {
    pointage_erreur('Appareil inconnu ou revoque.', 401);
}

// --- 2. Lecture de la charge ----------------------------------------------
$brut = file_get_contents('php://input');
if ($brut === false || strlen($brut) > 65536) {
    pointage_erreur('Charge trop volumineuse (64 Ko maximum).', 413);
}

$body = json_decode($brut, true);
if (!is_array($body)) {
    pointage_erreur('Corps de requete JSON invalide.', 400);
}

// --- 3. Coherence entre l'appareil et la source ---------------------------
$typeAppareil = $dispositif['type'];
$source = strtolower(trim((string) ($body['source'] ?? '')));

if (in_array($typeAppareil, ['qr', 'rfid', 'biometrie'], true)) {
    if ($source !== '' && $source !== $typeAppareil) {
        pointage_erreur('Cet appareil est declare de type "' . $typeAppareil . '" : source "' . $source . '" refusee.', 403);
    }
    $source = $typeAppareil;
} elseif ($source === '') {
    pointage_erreur('Champ "source" requis.', 400);
}

// --- 4. Traitement ---------------------------------------------------------
$moteur = new PointageModel();
$resultat = $moteur->traiter([
    'source' => $source,
    'identifiant' => $body['identifiant'] ?? '',
    'horodatage' => $body['horodatage'] ?? null,
    'uid_externe' => $body['uid_externe'] ?? '',
    'id_dispositif' => (int) $dispositif['id_dispositif'],
    'donnees_brutes' => $body['donnees_brutes'] ?? null,
]);

$resultat['dispositif'] = $dispositif['code'];

// --- 5. Code HTTP selon le resultat ---------------------------------------
$http = 200;
if (!empty($resultat['http'])) {
    $http = (int) $resultat['http'];
} else {
    switch ($resultat['statut'] ?? '') {
        case 'en_attente':
        case 'a_valider':
            $http = 202;
            break;
        case 'rejete':
            $http = 400;
            break;
    }
}

pointage_json($resultat, $http);
