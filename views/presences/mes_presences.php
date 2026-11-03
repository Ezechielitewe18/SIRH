<?php
$pageTitle = 'Mes présences';
$moisNoms = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
    7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];
$moisLabel = sprintf('%02d/%04d', $mois, $annee);
$statuts = [
    'present' => ['Présent', 'success'],
    'retard' => ['Retard', 'warning'],
    'absent' => ['Absent', 'danger'],
    'conge' => ['Congé', 'info'],
    'justifie' => ['Justifié', 'info'],
];
$validations = [
    'auto' => ['Automatique', 'success'],
    'validee' => ['Validée', 'success'],
    'en_attente' => ['En attente', 'warning'],
    'rejetee' => ['Rejetée', 'danger'],
];
$joursTravailles = (int) $stats['presents'] + (int) $stats['retards'];
$prenom = $employe['prenom'] ?? ($_SESSION['user_name'] ?? '');
$initiale = mb_strtoupper(mb_substr($prenom ?: 'U', 0, 1));
ob_start();
?>

<section class="content-header">
    <div class="sirh-page-header">
        <div class="sirh-page-heading">
            <h1 class="sirh-page-title"><i class="fas fa-fingerprint" aria-hidden="true"></i> Mes présences</h1>
            <p class="sirh-page-subtitle">Votre historique personnel pour <?= htmlspecialchars($moisLabel) ?>.</p>
        </div>
        <div class="sirh-page-actions">
            <form method="GET" action="<?= APP_URL ?>/presences" class="sirh-toolbar-group" aria-label="Filtrer la période">
                <label class="sirh-toolbar-label" for="pres-mois">Période</label>
                <select id="pres-mois" name="mois" class="form-control form-control-sm" style="width:auto;">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= (int) $mois === $m ? 'selected' : '' ?>><?= $moisNoms[$m] ?></option>
                    <?php endfor; ?>
                </select>
                <select id="pres-annee" name="annee" class="form-control form-control-sm" style="width:auto;" aria-label="Année">
                    <?php for ($y = (int) date('Y') - 2; $y <= (int) date('Y'); $y++): ?>
                    <option value="<?= $y ?>" <?= (int) $annee === $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="fas fa-filter" aria-hidden="true"></i></button>
            </form>
            <a href="<?= APP_URL ?>/presences/qr" class="btn btn-primary">
                <i class="fas fa-qrcode" aria-hidden="true"></i> Mon QR
            </a>
        </div>
    </div>
</section>

<section class="content sirh-employee-space">

    <div class="sirh-today-card">
        <div class="sirh-today-card__identity">
            <span class="sirh-employee-avatar" aria-hidden="true"><?= htmlspecialchars($initiale) ?></span>
            <div>
                <strong><?= htmlspecialchars($prenom) ?></strong>
                <span><?= date('d/m/Y') ?></span>
            </div>
        </div>
        <?php if (!$maPresence): ?>
        <div class="sirh-today-card__state">
            <span class="sirh-status-dot" aria-hidden="true"></span>
            <div><strong>Pas encore pointé</strong><span>Présentez votre QR à la réception.</span></div>
        </div>
        <?php elseif ($maPresence['statut'] === 'absent'): ?>
        <div class="sirh-today-card__state sirh-today-card__state--alert">
            <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
            <div><strong>Absence enregistrée</strong><span>Un scan à la réception régularise votre arrivée.</span></div>
        </div>
        <?php elseif (empty($maPresence['heure_depart'])): ?>
        <div class="sirh-today-card__state sirh-today-card__state--ok">
            <i class="fas fa-circle-check" aria-hidden="true"></i>
            <div><strong>En poste depuis <?= htmlspecialchars(substr($maPresence['heure_arrivee'], 0, 5)) ?></strong><span>Second scan = départ.</span></div>
        </div>
        <?php else: ?>
        <div class="sirh-today-card__state">
            <i class="fas fa-flag-checkered" aria-hidden="true"></i>
            <div><strong>Journée terminée</strong><span><?= htmlspecialchars(substr($maPresence['heure_arrivee'], 0, 5)) ?> → <?= htmlspecialchars(substr($maPresence['heure_depart'], 0, 5)) ?></span></div>
        </div>
        <?php endif; ?>
    </div>

    <div class="sirh-stat-grid sirh-employee-stats">
        <article class="sirh-stat-card">
            <div class="sirh-stat-icon" style="--stat-color: var(--sirh-success); --stat-bg: var(--sirh-success-soft);"><i class="fas fa-calendar-check" aria-hidden="true"></i></div>
            <div class="sirh-stat-body"><p class="sirh-stat-label">Jours pointés</p><p class="sirh-stat-value"><?= $joursTravailles ?></p><p class="sirh-stat-meta"><?= (int) $stats['jours_pointes'] ?> jour(s) au registre</p></div>
        </article>
        <article class="sirh-stat-card">
            <div class="sirh-stat-icon" style="--stat-color: var(--sirh-blue); --stat-bg: var(--sirh-blue-soft);"><i class="fas fa-clock" aria-hidden="true"></i></div>
            <div class="sirh-stat-body"><p class="sirh-stat-label">Heures</p><p class="sirh-stat-value"><?= htmlspecialchars(number_format((float) $stats['heures_travail'], 1, ',', ' ')) ?> h</p><p class="sirh-stat-meta">temps de présence</p></div>
        </article>
        <article class="sirh-stat-card">
            <div class="sirh-stat-icon" style="--stat-color: var(--sirh-warning); --stat-bg: var(--sirh-warning-soft);"><i class="fas fa-hourglass-half" aria-hidden="true"></i></div>
            <div class="sirh-stat-body"><p class="sirh-stat-label">Retards</p><p class="sirh-stat-value"><?= (int) $stats['minutes_retard'] ?> min</p><p class="sirh-stat-meta"><?= (int) $stats['retards'] ?> jour(s)</p></div>
        </article>
        <article class="sirh-stat-card">
            <div class="sirh-stat-icon" style="--stat-color: var(--sirh-danger); --stat-bg: var(--sirh-danger-soft);"><i class="fas fa-user-clock" aria-hidden="true"></i></div>
            <div class="sirh-stat-body"><p class="sirh-stat-label">Absences</p><p class="sirh-stat-value"><?= (int) $stats['absents'] ?></p><p class="sirh-stat-meta"><?= (int) $stats['justifies'] ?> justifiée(s)</p></div>
        </article>
    </div>

    <section class="sirh-panel" aria-labelledby="titre-historique">
        <div class="sirh-panel__head">
            <h2 class="sirh-section-title" id="titre-historique"><i class="fas fa-list-check" aria-hidden="true"></i> Historique de <?= htmlspecialchars($moisNoms[(int) $mois] ?? $mois) ?></h2>
            <span class="sirh-muted" style="font-size:.82rem;"><?= count($mesPresences) ?> ligne(s)</span>
        </div>

        <?php if (empty($mesPresences)): ?>
        <div class="sirh-empty-state">
            <i class="fas fa-calendar-day" aria-hidden="true"></i>
            <p>Aucune présence enregistrée sur cette période.</p>
            <span>Vos pointages apparaîtront ici après votre premier scan.</span>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover sirh-table-medium">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Arrivée</th>
                        <th scope="col">Départ</th>
                        <th scope="col">Retard</th>
                        <th scope="col">Statut</th>
                        <th scope="col">Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mesPresences as $p): ?>
                    <?php
                    $s = $statuts[$p['statut']] ?? [$p['statut'], 'neutral'];
                    $v = $validations[$p['validation']] ?? [$p['validation'], 'neutral'];
                    ?>
                    <tr>
                        <td class="sirh-nowrap"><span class="sirh-cell-strong"><?= date('d/m/Y', strtotime($p['date_presence'])) ?></span></td>
                        <td><?= !empty($p['heure_arrivee']) ? htmlspecialchars(substr($p['heure_arrivee'], 0, 5)) : '—' ?></td>
                        <td><?= !empty($p['heure_depart']) ? htmlspecialchars(substr($p['heure_depart'], 0, 5)) : '—' ?></td>
                        <td><?= (int) $p['retard'] > 0 ? '+' . (int) $p['retard'] . ' min' : '—' ?></td>
                        <td><span class="badge badge-<?= htmlspecialchars($s[1]) ?>"><?= htmlspecialchars($s[0]) ?></span></td>
                        <td><span class="badge badge-<?= htmlspecialchars($v[1]) ?>"><?= htmlspecialchars($v[0]) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <p class="sirh-privacy-note">
            <i class="fas fa-lock" aria-hidden="true"></i>
            Seules vos propres présences sont affichées sur cette page.
        </p>
    </section>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
