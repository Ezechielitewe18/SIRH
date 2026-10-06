<?php
/**
 * Routeur de captures marketing — usage STRICTEMENT local.
 *
 * Il ne sert que de support aux captures d'ecran de marketing/captures :
 *
 *   set SIRH_DB=sirh_demo
 *   set CAPTURE_LOGIN=1
 *   set CAPTURE_USER_ROLE=admin
 *   php -S 127.0.0.1:8123 -t C:\xampp\htdocs\SIRH scripts\capture_router.php
 *
 * Sans CAPTURE_LOGIN=1, aucun compte n'est ouvert : le routeur se contente de
 * router les requetes comme index.php le ferait. Il n'est jamais charge par
 * Apache (seul php -S le reference).
 *
 * Retourne false pour laisser PHP servir les fichiers statiques (css, js, png).
 */

$root = dirname(__DIR__);

// php -S positionne SCRIPT_NAME sur ce routeur : on le corrige pour que
// APP_URL (calculee dans config/config.php) vaille http://hote:port.
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Bascule de base (ex. sirh_demo = jeu fictif) avant de charger database.php.
$dbOverride = getenv('SIRH_DB');
if ($dbOverride !== false && $dbOverride !== '') {
    define('DB_NAME', $dbOverride);
}

require_once $root . '/config/config.php';
require_once $root . '/config/database.php';

if (getenv('CAPTURE_LOGIN') === '1' && empty($_SESSION['user_id'])) {
    $role = getenv('CAPTURE_USER_ROLE') ?: 'admin';
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $sql = 'SELECT id_utilisateur, nom_complet, email, role
            FROM utilisateurs WHERE role = :role AND statut = "actif"
            ORDER BY id_utilisateur LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':role' => $role]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id']   = $user['id_utilisateur'];
        $_SESSION['user_name'] = $user['nom_complet'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        $emp = $pdo->prepare('SELECT id_employe FROM employes
                              WHERE id_utilisateur = :id AND statut = "actif"
                              ORDER BY id_employe LIMIT 1');
        $emp->execute([':id' => $user['id_utilisateur']]);
        $employe = $emp->fetch(PDO::FETCH_ASSOC);
        if ($employe) {
            $_SESSION['employee_id'] = $employe['id_employe'];
        }
    }
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// mobile.php et demo.php sont des pages autonomes : on les charge ici pour
// qu'elles benefitient de la session ouverte par le routeur.
if ($path === '/mobile.php' || $path === '/demo.php') {
    require $root . $path;
    return true;
}

if ($path !== '/' && is_file($root . $path)) {
    return false;
}

require $root . '/index.php';
