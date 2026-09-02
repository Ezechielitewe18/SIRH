<?php
/**
 * Configuration générale du SIRH
 */
define('APP_NAME', 'GLOBIT');

// URL de base dynamique : localhost sur le PC, IP LAN sur le téléphone,
// domaine HTTPS derrière un tunnel (PWA). Evite les formulaires qui postent ver localhost.
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

// Heure de travail
define('HEURE_DEBUT', '08:00');
define('HEURE_FIN', '17:00');

// Congés
define('JOURS_CONGE_ANNUEL', 30);

// Droits d'auteur
define('AUTHOR_NAME', 'Ezechiel Itewe Nzukumayi');
define('COPYRIGHT', '© ' . date('Y') . ' GLOBIT - Tous droits réservés. Développé par ' . AUTHOR_NAME);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
