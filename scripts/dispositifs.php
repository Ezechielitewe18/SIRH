<?php
/**
 * Outil de gestion des appareils de pointage (ligne de commande).
 *
 * Disponible en attendant l'ecran d'administration de la phase 3.
 *
 *   php scripts/dispositifs.php lister
 *   php scripts/dispositifs.php creer --code=PORTIQUE-1 --libelle="Portique entree" --type=rfid
 *   php scripts/dispositifs.php revoquer --id=3 --motif="appareil perdu"
 *   php scripts/dispositifs.php regenerer --id=3
 *   php scripts/dispositifs.php tester --id=3
 *
 * Le jeton n'est affiche qu'a la creation et a la regeneration.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Ce script s'execute en ligne de commande uniquement.\n");
}

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/models/DispositifModel.php';

$options = [];
foreach (array_slice($argv, 2) as $arg) {
    if (preg_match('/^--([a-zA-Z0-9_-]+)=(.*)$/', $arg, $m)) {
        $options[$m[1]] = $m[2];
    } elseif (preg_match('/^--([a-zA-Z0-9_-]+)$/', $arg, $m)) {
        $options[$m[1]] = true;
    }
}

$commande = $argv[1] ?? 'lister';
$model = new DispositifModel();

switch ($commande) {
    case 'creer':
        $resultat = $model->creer([
            'code' => $options['code'] ?? '',
            'libelle' => $options['libelle'] ?? '',
            'type' => $options['type'] ?? 'qr',
            'marque' => $options['marque'] ?? null,
            'modele' => $options['modele'] ?? null,
            'numero_serie' => $options['serie'] ?? null,
            'emplacement' => $options['emplacement'] ?? null,
        ]);
        if (!$resultat['ok']) {
            echo "ECHEC : {$resultat['message']}\n";
            exit(1);
        }
        echo "Appareil cree (id {$resultat['id_dispositif']}).\n";
        echo "Jeton (a confier a l'appareil, non stocke en clair) :\n";
        echo "  {$resultat['token']}\n";
        echo "\nExemple d'en-tete : Authorization: Device {$resultat['token']}\n";
        break;

    case 'revoquer':
        $id = (int) ($options['id'] ?? 0);
        $resultat = $model->revoquer($id, 'hors_service', $options['motif'] ?? null);
        echo ($resultat['ok'] ? 'OK' : 'ECHEC') . ' : ' . $resultat['message'] . "\n";
        break;

    case 'regenerer':
        $id = (int) ($options['id'] ?? 0);
        $resultat = $model->regenererJeton($id);
        if (!$resultat['ok']) {
            echo "ECHEC : {$resultat['message']}\n";
            exit(1);
        }
        echo "Nouveau jeton :\n  {$resultat['token']}\n";
        break;

    case 'tester':
        $id = (int) ($options['id'] ?? 0);
        $dispositif = $model->findById($id);
        if (!$dispositif) {
            echo "Appareil introuvable.\n";
            exit(1);
        }
        $model->marquerTeste($id);
        echo "Test de reception enregistre pour {$dispositif['libelle']} ({$dispositif['code']}).\n";
        break;

    case 'lister':
    default:
        $lignes = $model->lister();
        if (!$lignes) {
            echo "Aucun appareil declare.\n";
            break;
        }
        printf("%-4s %-20s %-30s %-10s %-14s %-19s %s\n",
            'ID', 'CODE', 'LIBELLE', 'TYPE', 'STATUT', 'DERNIER CONTACT', 'TESTE LE');
        foreach ($lignes as $d) {
            printf("%-4s %-20s %-30s %-10s %-14s %-19s %s\n",
                $d['id_dispositif'],
                $d['code'],
                mb_substr($d['libelle'], 0, 30),
                $d['type'],
                $d['statut'],
                $d['dernier_contact'] ?? '-',
                $d['teste_le'] ?? '-');
        }
        break;
}
