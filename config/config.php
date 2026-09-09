<?php
define('APP_NAME', 'GLOBIT');

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

define('APP_VERSION', '1.0.0');

define('HEURE_DEBUT', '08:00');
define('HEURE_FIN', '17:00');

define('JOURS_CONGE_ANNUEL', 30);

define('AUTHOR_NAME', 'Ezechiel Itewe Nzukumayi');
define('COPYRIGHT', '© ' . date('Y') . ' GLOBIT - Tous droits réservés. Développé par ' . AUTHOR_NAME);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', '0');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'secure' => false,
        'samesite' => 'Strict',
    ]);
    session_start();
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
