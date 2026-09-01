<?php
/**
 * Configuration générale du SIRH
 */
define('APP_NAME', 'GLOBIT');
define('APP_URL', 'http://localhost/SIRH');
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
