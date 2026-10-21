<?php
/**
 * Jeu de donnees visuel local pour le tableau de bord.
 *
 * Usage (depuis la racine du projet) : php scripts/seed_demo_dashboard.php
 * Les donnees creees sont identifiables par leur email demo.dashboard+…@globit.test.
 * Le script est idempotent et n'ecrase aucune donnee existante.
 */
require_once __DIR__ . '/../config/database.php';

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
$db = new PDO($dsn, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$today = date('Y-m-d');
$year = (int) date('Y');
$month = (int) date('m');
$samples = [
    ['Aline', 'Mbuyi', 'F', 'Directrice générale', 1, 3200],
    ['David', 'Kabasele', 'M', 'Responsable RH', 2, 2400],
    ['Mireille', 'Kasongo', 'F', 'Chargée RH', 2, 1650],
    ['Junior', 'Mabiala', 'M', 'Développeur senior', 3, 2800],
    ['Sarah', 'Ilunga', 'F', 'Développeuse web', 3, 2200],
    ['Patrick', 'Kanku', 'M', 'Comptable', 4, 1900],
    ['Nadine', 'Tshala', 'F', 'Analyste financière', 4, 2100],
    ['Christian', 'Banza', 'M', 'Chargé marketing', 5, 1750],
    ['Esther', 'Lukusa', 'F', 'Assistante administrative', 6, 1500],
    ['Claude', 'Mwamba', 'M', 'Agent administratif', 6, 1400],
];

$findEmployee = $db->prepare('SELECT id_employe, salaire FROM employes WHERE email = :email');
$insertEmployee = $db->prepare(
    'INSERT INTO employes (matricule, nom, prenom, sexe, email, poste, date_embauche, salaire, statut, id_service, est_direction)
     VALUES (:matricule, :nom, :prenom, :sexe, :email, :poste, :date_embauche, :salaire, "actif", :id_service, :est_direction)'
);

$demoEmployees = [];
foreach ($samples as $index => [$firstName, $lastName, $gender, $job, $serviceId, $salary]) {
    $email = sprintf('demo.dashboard+%02d@globit.test', $index + 1);
    $findEmployee->execute(['email' => $email]);
    $employee = $findEmployee->fetch();
    if (!$employee) {
        $insertEmployee->execute([
            'matricule' => sprintf('DEMO-%d-%03d', $year, $index + 1),
            'nom' => $lastName,
            'prenom' => $firstName,
            'sexe' => $gender,
            'email' => $email,
            'poste' => $job,
            'date_embauche' => sprintf('%d-%02d-01', $year - 1, ($index % 9) + 1),
            'salaire' => $salary,
            'id_service' => $serviceId,
            'est_direction' => $index === 0 ? 1 : 0,
        ]);
        $employee = ['id_employe' => (int) $db->lastInsertId(), 'salaire' => $salary];
    }
    $demoEmployees[] = ['id' => (int) $employee['id_employe'], 'salary' => (float) $employee['salaire']];
}

$hasPresence = $db->prepare('SELECT id_presence FROM presences WHERE id_employe = :id AND date_presence = :date');
$insertPresence = $db->prepare(
    'INSERT INTO presences (id_employe, source, date_presence, heure_arrivee, heure_depart, retard, statut, validation, valide_le)
     VALUES (:id, "manuel", :date, :arrival, :departure, :late, :status, "validee", NOW())'
);
foreach ($demoEmployees as $index => $employee) {
    $hasPresence->execute(['id' => $employee['id'], 'date' => $today]);
    if ($hasPresence->fetch()) {
        continue;
    }
    $late = in_array($index, [3, 7], true) ? 18 + $index : 0;
    $insertPresence->execute([
        'id' => $employee['id'],
        'date' => $today,
        'arrival' => $late ? '08:2' . ($index - 2) . ':00' : '07:5' . ($index % 5) . ':00',
        'departure' => $index < 6 ? '17:15:00' : null,
        'late' => $late,
        'status' => $late ? 'retard' : 'present',
    ]);
}

$hasLeave = $db->prepare('SELECT id_conge FROM conges WHERE motif = :motif');
$insertLeave = $db->prepare(
    'INSERT INTO conges (id_employe, type_conge, date_debut, date_fin, nombre_jours, motif, statut, approuve_par, date_approbation)
     VALUES (:employee, :type, :start, :end, :days, :motif, :status, :approver, :approved_at)'
);
$leaves = [
    [0, 'annuel', date('Y-m-d', strtotime('-2 days')), date('Y-m-d', strtotime('+3 days')), 6, 'DÉMO — Congé annuel en cours', 'approuve'],
    [4, 'maladie', date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')), 3, 'DÉMO — Congé maladie en cours', 'approuve'],
    [6, 'annuel', date('Y-m-d', strtotime('+7 days')), date('Y-m-d', strtotime('+11 days')), 5, 'DÉMO — Demande de congé annuel', 'en_attente'],
    [8, 'exceptionnel', date('Y-m-d', strtotime('+14 days')), date('Y-m-d', strtotime('+15 days')), 2, 'DÉMO — Demande exceptionnelle', 'en_attente'],
    [2, 'paternite', date('Y-m-d', strtotime('+21 days')), date('Y-m-d', strtotime('+30 days')), 10, 'DÉMO — Demande de congé paternité', 'en_attente'],
];
foreach ($leaves as [$employeeIndex, $type, $start, $end, $days, $reason, $status]) {
    $hasLeave->execute(['motif' => $reason]);
    if ($hasLeave->fetch()) {
        continue;
    }
    $insertLeave->execute([
        'employee' => $demoEmployees[$employeeIndex]['id'],
        'type' => $type,
        'start' => $start,
        'end' => $end,
        'days' => $days,
        'motif' => $reason,
        'status' => $status,
        'approver' => $status === 'approuve' ? 6 : null,
        'approved_at' => $status === 'approuve' ? date('Y-m-d H:i:s') : null,
    ]);
}

$hasPayslip = $db->prepare('SELECT id_bulletin FROM bulletins WHERE id_employe = :employee AND mois = :month AND annee = :year');
$insertPayslip = $db->prepare(
    'INSERT INTO bulletins (id_employe, mois, annee, salaire_base, primes, heures_supplementaires, montant_heures_sup, total_brut, total_retenues, total_net, statut, date_generation)
     VALUES (:employee, :month, :year, :base, :bonus, 0, 0, :gross, :deductions, :net, "paye", NOW())'
);
for ($offset = 0; $offset < 8; $offset++) {
    $period = (new DateTimeImmutable(sprintf('%d-%02d-01', $year, $month)))->modify('-' . $offset . ' months');
    $employee = $demoEmployees[$offset % count($demoEmployees)];
    $periodMonth = (int) $period->format('m');
    $periodYear = (int) $period->format('Y');
    $hasPayslip->execute(['employee' => $employee['id'], 'month' => $periodMonth, 'year' => $periodYear]);
    if ($hasPayslip->fetch()) {
        continue;
    }
    $base = $employee['salary'];
    $bonus = round($base * .18, 2);
    $gross = $base + $bonus;
    $deductions = round($gross * .095, 2);
    $insertPayslip->execute([
        'employee' => $employee['id'], 'month' => $periodMonth, 'year' => $periodYear,
        'base' => $base, 'bonus' => $bonus, 'gross' => $gross,
        'deductions' => $deductions, 'net' => $gross - $deductions,
    ]);
}

echo "Jeu de démonstration du tableau de bord prêt.\n";
