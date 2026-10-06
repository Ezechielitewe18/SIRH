<?php
/**
 * Migration GLOBIT SAS : primes fixes par employe + detail des primes sur le bulletin.
 * A executer une seule fois : php scripts/migration_primes.php
 */
require_once __DIR__ . '/../config/database.php';

$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$messages = [];

$existe = $db->query("SHOW TABLES LIKE 'primes'")->fetch();
if (!$existe) {
    $db->exec("CREATE TABLE `primes` (
      `id_prime` int(11) NOT NULL AUTO_INCREMENT,
      `id_employe` int(11) NOT NULL,
      `libelle` varchar(100) NOT NULL,
      `montant` decimal(12,2) NOT NULL DEFAULT 0.00,
      `actif` tinyint(1) NOT NULL DEFAULT 1,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id_prime`),
      KEY `idx_primes_employe` (`id_employe`),
      CONSTRAINT `fk_primes_employe` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id_employe`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $messages[] = "Table `primes` creee.";
} else {
    $messages[] = "Table `primes` deja presente.";
}

$colonnes = $db->query("SHOW COLUMNS FROM `bulletins` LIKE 'detail_primes'")->fetchAll();
if (empty($colonnes)) {
    $db->exec("ALTER TABLE `bulletins` ADD COLUMN `detail_primes` text DEFAULT NULL AFTER `primes`");
    $messages[] = "Colonne `bulletins`.`detail_primes` ajoutee.";
} else {
    $messages[] = "Colonne `bulletins`.`detail_primes` deja presente.";
}

foreach ($messages as $m) {
    echo "  - $m\n";
}
echo "Migration terminee.\n";