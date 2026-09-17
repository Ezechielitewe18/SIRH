<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../core/Database.php';

$db = Database::getInstance()->getConnection();

function fixText($s) {
    if ($s === null || $s === '') return $s;
    // Ne toucher que si la chaîne contient les caractères mojibake CP850
    if (!preg_match('/[\x{251C}\x{00AE}\x{2514}\x{2510}\x{2502}\x{2551}\x{2524}\x{252C}\x{253C}\x{2500}\x{2518}]/u', $s)) return $s;
    $converted = @mb_convert_encoding($s, 'CP850', 'UTF-8');
    if ($converted === false || $converted === '') return $s;
    // Vérifier que la conversion a retiré les caractères box-drawing (zoom)
    if (preg_match('/[\x{2500}-\x{257F}]/u', $converted)) return $s;
    return $converted;
}

$stmt = $db->query("SELECT table_name, column_name, data_type
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND data_type IN ('varchar','text','tinytext','mediumtext','longtext','char')
    ORDER BY table_name, column_name");

$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalFixed = 0;

foreach ($cols as $col) {
    $table = $col['table_name'];
    $column = $col['column_name'];
    try {
        $rows = $db->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        continue;
    }
    if (empty($rows)) continue;

    // Trouver la clé primaire
    $pk = null;
    foreach (array_keys($rows[0]) as $c) {
        if ($c === 'id' || $c === 'id_' . $table || $c === $table . '_id') { $pk = $c; break; }
    }
    if ($pk === null) $pk = array_keys($rows[0])[0];

    foreach ($rows as $row) {
        if (!isset($row[$column])) continue;
        $value = $row[$column];
        $fixed = fixText($value);
        if ($fixed !== $value) {
            $upd = $db->prepare("UPDATE `$table` SET `$column` = :v WHERE `$pk` = :pk");
            $upd->execute(['v' => $fixed, 'pk' => $row[$pk]]);
            $totalFixed++;
            echo "  Fix [$table.$column] id={$row[$pk]}: '$value' -> '$fixed'\n";
        }
    }
}

echo "\nTotal: $totalFixed valeur(s) corrigée(s).\n";