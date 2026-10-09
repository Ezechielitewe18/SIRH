<?php
/**
 * Migration GLOBIT SAS : securisation des connexions.
 * Cree la table tentatives_connexion (anti force brute).
 * A executer sur chaque base existante : php scripts/migration_securite.php
 */
require_once __DIR__ . '/../config/database.php';

$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$existe = $db->query("SHOW TABLES LIKE 'tentatives_connexion'")->fetch();
if (!$existe) {
    $db->exec("CREATE TABLE `tentatives_connexion` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `email` varchar(100) NOT NULL,
      `ip_adresse` varchar(45) NOT NULL,
      `ajoutee_le` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `idx_tent_email` (`email`),
      KEY `idx_tent_ip` (`ip_adresse`),
      KEY `idx_tent_date` (`ajoutee_le`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Table tentatives_connexion creee.\n";
} else {
    echo "Table tentatives_connexion deja presente.\n";
}
echo "Migration securite terminee.\n";