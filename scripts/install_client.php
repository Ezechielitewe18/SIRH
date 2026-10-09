<?php
/**
 * GLOBIT SAS - SIRH : installateur client (ligne de commande)
 *
 * Cree une base de donnees dediee au client, importe le schema complet,
 * genere config/database.php et cree le compte administrateur.
 *
 * Le dump sql/sirh.sql contient un "CREATE DATABASE/USE `sirh`" en dur :
 * ce script les neutralise afin de ne jamais ecraser la base de developpement.
 *
 * Usage :
 *   php scripts/install_client.php \
 *     --db-host=localhost --db-name=sirh_client --db-user=root --db-pass=secret \
 *     --admin-nom="Administrateur CLIENT" --admin-email=admin@client.com \
 *     --admin-pass="MotDePasseFort" [--clean] [--force] [--yes]
 *
 * Options :
 *   --web-path Chemin web de l'installation (defaut "/"). Ex. si le projet tourne
 *             dans http://localhost/SIRH_client/, passer --web-path=/SIRH_client/.
 *   --clean   Supprime les donnees de demonstration (services, employes, etc.)
 *             et ne conserve que le compte administrateur et les parametres de paie.
 *   --force   Autorise l'import dans une base existante non vide.
 *   --yes     Ne demande aucune confirmation (mode non interactif).
 *
 * Sans valeur fournie, le script pose la question en interactif.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Ce script doit etre execute en ligne de commande (php scripts/install_client.php).\n");
    exit(1);
}

$root = dirname(__DIR__);
$dumpFile = $root . '/sql/sirh.sql';
$configFile = $root . '/config/database.php';

function out($msg = '') { fwrite(STDOUT, $msg . "\n"); }
function err($msg) { fwrite(STDERR, $msg . "\n"); }

function prompt($label, $default = null) {
    $suffix = ($default !== null && $default !== '') ? " [$default]" : '';
    fwrite(STDOUT, $label . $suffix . ' : ');
    $line = fgets(STDIN);
    if ($line === false) { return (string)$default; }
    $line = trim($line);
    return ($line === '') ? (string)$default : $line;
}

function promptHidden($label) {
    fwrite(STDOUT, $label . ' : ');
    if (DIRECTORY_SEPARATOR !== '\\' && function_exists('shell_exec')) {
        @shell_exec('stty -echo');
        $line = fgets(STDIN);
        @shell_exec('stty echo');
        fwrite(STDOUT, "\n");
    } else {
        $line = fgets(STDIN);
    }
    return ($line === false) ? '' : trim($line);
}

function arg(array $options, $name, $default = null) {
    return array_key_exists($name, $options) ? $options[$name] : $default;
}

$options = [];
$flags = [];
foreach (array_slice($argv, 1) as $a) {
    if (preg_match('/^--([^=]+)=(.*)$/s', $a, $m)) {
        $options[$m[1]] = $m[2];
    } elseif (preg_match('/^--(.+)$/', $a, $m)) {
        $flags[$m[1]] = true;
    } else {
        err("Argument inconnu : $a");
        exit(1);
    }
}

$clean = isset($flags['clean']);
$force = isset($flags['force']);
$yes = isset($flags['yes']);
$webPath = '/' . trim((string)arg($options, 'web-path', '/'), '/') . '/';

out('');
out('============================================================');
out('  GLOBIT SAS - SIRH : installation d\'une base client');
out('============================================================');
out('');

if (!is_file($dumpFile)) {
    err("Fichier introuvable : $dumpFile");
    exit(1);
}

$dbHost = arg($options, 'db-host');
$dbPort = arg($options, 'db-port', '3306');
$dbName = arg($options, 'db-name');
$dbUser = arg($options, 'db-user');
$dbPass = array_key_exists('db-pass', $options) ? $options['db-pass'] : null;

if ($dbHost === null)  { $dbHost = prompt('Hote MySQL', 'localhost'); }
if ($dbUser === null)  { $dbUser = prompt('Utilisateur MySQL', 'root'); }
if ($dbPass === null)  { $dbPass = promptHidden('Mot de passe MySQL (vide si aucun)'); }
if ($dbName === null)  { $dbName = prompt('Nom de la base a creer (ex. sirh_client)'); }

if (!preg_match('/^[A-Za-z0-9_]+$/', (string)$dbName)) {
    err('Nom de base invalide : utilisez uniquement lettres, chiffres et underscore.');
    exit(1);
}
if ($dbName === 'sirh' || $dbName === 'sirh_demo') {
    err("Refus d'installer dans \"$dbName\" : nom reserve (base de developpement / demonstration).");
    err('Choisissez un nom dedie, par exemple "sirh_nomduclient".');
    exit(1);
}

$adminNom = arg($options, 'admin-nom');
$adminEmail = arg($options, 'admin-email');
$adminPass = array_key_exists('admin-pass', $options) ? $options['admin-pass'] : null;

if ($adminNom === null)   { $adminNom = prompt('Nom complet de l\'administrateur', 'Administrateur'); }
if ($adminEmail === null) { $adminEmail = prompt('Email de l\'administrateur'); }
if ($adminPass === null)  { $adminPass = promptHidden('Mot de passe de l\'administrateur'); }

if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
    err('Email administrateur invalide.');
    exit(1);
}
if (strlen((string)$adminPass) < 8) {
    err('Le mot de passe administrateur doit contenir au moins 8 caracteres.');
    exit(1);
}

$dsnServer = "mysql:host=$dbHost;port=$dbPort;charset=utf8mb4";
try {
    $server = new PDO($dsnServer, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    err('Connexion au serveur MySQL impossible : ' . $e->getMessage());
    exit(1);
}

$existe = $server->query('SHOW DATABASES LIKE ' . $server->quote($dbName))->fetch();
$nonVide = false;
if ($existe) {
    $check = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $tables = $check->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    $nonVide = count($tables) > 0;
    if ($nonVide && !$force) {
        err("La base \"$dbName\" existe deja et contient " . count($tables) . ' table(s).');
        err('Ajoutez --force pour la reimporter (les donnees existantes seront ecrasees).');
        exit(1);
    }
}

if (!$yes) {
    out('');
    out('Recapitulatif :');
    out("  Serveur          : $dbHost:$dbPort");
    out("  Base             : $dbName" . ($nonVide ? '  (sera ecrasee)' : '  (nouvelle)'));
    out("  Admin            : $adminNom <$adminEmail>");
    out('  Nettoyage demo   : ' . ($clean ? 'oui' : 'non'));
    $rep = prompt('Confirmer l\'installation ? (o/N)', 'N');
    if (!in_array(strtolower($rep), ['o', 'oui', 'y', 'yes'], true)) {
        out('Installation annulee.');
        exit(0);
    }
}

out('');
out('[1/6] Creation de la base...');
$server->exec('CREATE DATABASE IF NOT EXISTS `' . $dbName . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
out("  Base \"$dbName\" prete.");

try {
    $pdo = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
    ]);
} catch (PDOException $e) {
    err('Connexion a la base cible impossible : ' . $e->getMessage());
    exit(1);
}

out('[2/6] Import du schema et des donnees...');
$sql = file_get_contents($dumpFile);
$sql = preg_replace('/^\s*CREATE\s+DATABASE\b[^;]*;\s*$/im', '', $sql);
$sql = preg_replace('/^\s*USE\s+`[^`]+`\s*;\s*$/im', '', $sql);
try {
    $pdo->exec($sql);
} catch (PDOException $e) {
    err('Echec de l\'import : ' . $e->getMessage());
    err('Alternative : importez manuellement sql/sirh.sql via phpMyAdmin (apres avoir retire la ligne USE).');
    exit(1);
}
$tableCount = count($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN));
out("  $tableCount tables importees.");

if ($clean) {
    out('[3/6] Suppression des donnees de demonstration...');
    $purge = [
        'annonces', 'api_tokens', 'bulletins', 'conges', 'conversations',
        'employes', 'formations', 'formations_employes', 'journal_activite',
        'messages', 'notifications', 'primes', 'presences', 'services', 'utilisateurs',
    ];
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach ($purge as $t) {
        $pdo->exec('TRUNCATE TABLE `' . $t . '`');
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    out('  Donnees de demonstration supprimees (parametres de paie conserves).');
} else {
    out('[3/6] Conservation des donnees de demonstration.');
}

out('[4/6] Creation du compte administrateur...');
$hash = password_hash($adminPass, PASSWORD_DEFAULT);
$adminId = $pdo->query("SELECT id_utilisateur FROM utilisateurs WHERE role = 'admin' ORDER BY id_utilisateur LIMIT 1")->fetchColumn();
if ($adminId) {
    $pdo->prepare('DELETE FROM utilisateurs WHERE email = ? AND id_utilisateur <> ?')->execute([$adminEmail, $adminId]);
    $pdo->prepare("UPDATE utilisateurs SET nom_complet = ?, email = ?, mot_de_passe = ?, role = 'admin', statut = 'actif' WHERE id_utilisateur = ?")
        ->execute([$adminNom, $adminEmail, $hash, $adminId]);
    out("  Compte administrateur mis a jour (id $adminId).");
} else {
    $pdo->prepare("INSERT INTO utilisateurs (nom_complet, email, mot_de_passe, role, statut) VALUES (?, ?, ?, 'admin', 'actif')")
        ->execute([$adminNom, $adminEmail, $hash]);
    out('  Compte administrateur cree.');
}

out('[5/6] Ecriture de config/database.php...');
if (is_file($configFile)) {
    $backup = sys_get_temp_dir() . '/database_' . DB_NAME . '_' . date('Ymd-His') . '.bak';
    if (@copy($configFile, $backup)) {
        out('  Sauvegarde : ' . $backup);
    }
}
$esc = static function ($v) { return str_replace("'", "\\'", (string)$v); };
$content = "<?php\n"
    . "/**\n"
    . " * Configuration de la base de donnees - generee par scripts/install_client.php\n"
    . ' * Client : ' . $adminNom . ' - ' . date('Y-m-d H:i:s') . "\n"
    . " */\n"
    . "define('DB_HOST', '" . $esc($dbHost) . "');\n"
    . "define('DB_NAME', '" . $esc($dbName) . "');\n"
    . "define('DB_USER', '" . $esc($dbUser) . "');\n"
    . "define('DB_PASS', '" . $esc($dbPass) . "');\n"
    . "define('DB_CHARSET', 'utf8mb4');\n";
if (file_put_contents($configFile, $content) === false) {
    err('Impossible d\'ecrire ' . $configFile);
    exit(1);
}
out('  Configuration enregistree.');

$htaccess = $root . '/.htaccess';
if (is_file($htaccess)) {
    $ht = file_get_contents($htaccess);
    if (@copy($htaccess, sys_get_temp_dir() . '/htaccess_' . date('Ymd-His') . '.bak')) {
        out('  Sauvegarde : ' . sys_get_temp_dir() . '/htaccess_' . date('Ymd-His') . '.bak');
    }
    if (preg_match('/^\s*RewriteBase\s+\S+/m', $ht)) {
        $ht = preg_replace('/^\s*RewriteBase\s+\S+.*$/m', 'RewriteBase ' . $webPath, $ht);
    } elseif (preg_match('/^\s*RewriteEngine\s+On.*$/m', $ht)) {
        $ht = preg_replace('/^(\s*RewriteEngine\s+On.*)$/m', "$1\nRewriteBase " . $webPath, $ht);
    }
    file_put_contents($htaccess, $ht);
    out('  RewriteBase regle sur ' . $webPath);
}

out('[6/6] Verification...');
$ok = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'admin'")->fetchColumn();
out("  Comptes administrateur actifs : $ok");
$uploads = $root . '/public/uploads';
if (is_dir($uploads)) { @chmod($uploads, 0775); }
out('  OK.');

out('');
out('============================================================');
out('  Installation terminee.');
out('============================================================');
out('');
out('A faire ensuite :');
out('  1. Pointer le domaine / dossier web vers la racine du projet (index.php).');
out('  2. Verifier que le fichier .htaccess est bien pris en compte (Apache + mod_rewrite).');
out('  3. Activer HTTPS (certificat Let\'s Encrypt) puis se connecter avec le compte admin.');
out('  4. Planifier une sauvegarde automatique (mysqldump) via une tache cron.');
out('  5. En production, retirer ou proteger : scripts/, sql/, .git/');
out('     et ce script d\'installation.');
out('');
