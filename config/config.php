<?php
date_default_timezone_set('Africa/Kinshasa');

define('APP_NAME', 'GLOBIT SAS');

$scheme = 'http';
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
    $scheme = 'https';
}
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = isset($_SERVER['SCRIPT_NAME'])
    ? rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/')
    : '/SIRH';
define('APP_URL', $scheme . '://' . $host . $basePath);
define('APP_HOST', $host);
$isHttps = ($scheme === 'https');

define('APP_VERSION', '1.0.0');

define('HEURE_DEBUT', '08:00');
define('HEURE_FIN', '17:00');
// Limite d'arrivee : au-dela, le scan reste accepte mais compte comme retard.
define('LIMITE_DECLARATION', '10:00');

// Creation automatique des lignes "absent" apres la limite d'arrivee.
// false (par defaut) : rien n'est ecrit pour un employe qui n'a pas scanne,
// il n'apparait dans la journee qu'apres son premier scan reussi.
define('MARQUER_ABSENTS_AUTO', false);

define('JOURS_CONGE_ANNUEL', 30);

// QR dynamique de presence : duree de validite d'un jeton (secondes)
define('QR_PERIODE', 30);
// Fenetre de tolerance (en periodes) acceptee a la validation du scan
define('QR_FENETRE', 1);

// Titulaire des droits d'auteur : la startup GLOBIT SAS (aucun nom de personne physique).
define('COMPANY_NAME', 'GLOBIT SAS');
define('AUTHOR_NAME', COMPANY_NAME);
define('COPYRIGHT', '© ' . date('Y') . ' ' . COMPANY_NAME . ' - Tous droits réservés.');

// Controles de securite des connexions (anti force brute)
define('LOGIN_MAX_ECHECS', 5);
define('LOGIN_MAX_ECHECS_IP', 10);
define('LOGIN_FENETRE_MINUTES', 15);

// Deconnexion automatique apres inactivite (secondes)
define('SESSION_INACTIVITE_SECONDES', 1800);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', $isHttps ? '1' : '0');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'secure' => $isHttps,
        'samesite' => 'Strict',
    ]);
    session_start();
}

// Expiration automatique de la session apres inactivite
if (session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['user_id'])) {
    if (!empty($_SESSION['last_activity'])
        && (time() - (int) $_SESSION['last_activity'] > (int) SESSION_INACTIVITE_SECONDES)) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        if (!headers_sent()) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }
    $_SESSION['last_activity'] = time();
}

// En-tetes de securite envoyes sur chaque reponse
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com; font-src 'self' data: https://cdnjs.cloudflare.com https://fonts.gstatic.com; img-src 'self' data: blob:; connect-src 'self'; object-src 'none'; base-uri 'self'; frame-ancestors 'none'; form-action 'self'");
    header('Permissions-Policy: camera=(self), microphone=(), geolocation=(), payment=()');
    if ($isHttps) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function csrf_verify() {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
