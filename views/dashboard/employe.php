<?php
$pageTitle = 'Mon espace';
$prenom = $employe['prenom'] ?? ($_SESSION['user_name'] ?? '');
$nomComplet = $employe ? trim($employe['prenom'] . ' ' . $employe['nom']) : ($_SESSION['user_name'] ?? 'Utilisateur');
$initiale = mb_strtoupper(mb_substr($prenom ?: 'U', 0, 1));
$moisLabel = sprintf('%02d/%04d', $mois, $annee);

$jourEtat = 'attente';
$jourTitre = 'Aucun pointage aujourd\'hui';
$jourTexte = 'Présentez votre QR à la réception pour enregistrer votre arrivée.';
if ($presenceJour) {
    if ($presenceJour['statut'] === 'absent') {
        $jourEtat = 'alerte';
        $jourTitre = 'Absence enregistrée';
        $jourTexte = 'Présentez votre QR à la réception : la Direction pourra régulariser votre arrivée.';
    } elseif (empty($presenceJour['heure_depart'])) {
        $jourEtat = 'en-poste';
        $jourTitre = 'Vous êtes en poste';
        $jourTexte = 'Arrivée pointée à ' . substr($presenceJour['heure_arrivee'], 0, 5) . '. Un second scan à la réception pointera votre départ.';
    } else {
        $jourEtat = 'termine';
        $jourTitre = 'Journée terminée';
        $jourTexte = 'Arrivée ' . substr($presenceJour['heure_arrivee'], 0, 5) . ' · Départ ' . substr($presenceJour['heure_depart'], 0, 5) . '.';
    }
}

$statuts = [
    'present' => ['Présent', 'success'],
    'retard' => ['Retard', 'warning'],
    'absent' => ['Absent', 'danger'],
    'conge' => ['Congé', 'info'],
    'justifie' => ['Justifié', 'info'],
];
$joursTravailles = (int) $stats['presents'] + (int) $stats['retards'];

$moisNoms = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
    7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];
$bulletinLabel = '';
$bulletinStatut = '';
if ($dernierBulletin) {
    $bulletinLabel = ($moisNoms[(int) $dernierBulletin['mois']] ?? $dernierBulletin['mois']) . ' ' . $dernierBulletin['annee'];
    $bulletinStatut = ['brouillon' => 'En préparation', 'valide' => 'Validé', 'paye' => 'Payé'][$dernierBulletin['statut']] ?? '';
}
ob_start();
?>

<section class="content-header">
    <div class="sirh-page-header">
        <div class="sirh-page-heading">
            <h1 class="sirh-page-title"><i class="fas fa-house-user" aria-hidden="true"></i> Mon espace</h1>
            <p class="sirh-page-subtitle">Votre activité personnelle pour <?= htmlspecialchars($moisLabel) ?>.</p>
        </div>
        <div class="sirh-page-actions">
            <form method="GET" action="<?= APP_URL ?>/dashboard" class="sirh-toolbar-group" aria-label="Filtrer la période">
                <label class="sirh-toolbar-label" for="emp-mois">Période</label>
                <select id="emp-mois" name="mois" class="form-control form-control-sm" style="width:auto;">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= (int) $mois === $m ? 'selected' : '' ?>><?= $moisNoms[$m] ?></option>
                    <?php endfor; ?>
                </select>
                <select id="emp-annee" name="annee" class="form-control form-control-sm" style="width:auto;" aria-label="Année">
                    <?php for ($y = (int) date('Y') - 2; $y <= (int) date('Y'); $y++): ?>
                    <option value="<?= $y ?>" <?= (int) $annee === $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="fas fa-filter" aria-hidden="true"></i> Afficher</button>
            </form>
        </div>
    </div>
</section>

<section class="content sirh-employee-space">

    <div class="sirh-employee-hero sirh-employee-hero--<?= htmlspecialchars($jourEtat) ?>">
        <div class="sirh-employee-hero__main">
            <div class="sirh-employee-avatar" aria-hidden="true"><?= htmlspecialchars($initiale) ?></div>
            <div class="sirh-employee-hero__identity">
                <p class="sirh-employee-hero__eyebrow">Bonjour</p>
                <h2><?= htmlspecialchars($nomComplet) ?></h2>
                <p class="sirh-employee-hero__meta">
                    <?= htmlspecialchars($employe['matricule'] ?? '') ?>
                    <?php if (!empty($employe['poste'])): ?> · <?= htmlspecialchars($employe['poste']) ?><?php endif; ?>
                    <?php if (!empty($employe['nom_service'])): ?> · <?= htmlspecialchars($employe['nom_service']) ?><?php endif; ?>
                </p>
            </div>
        </div>
        <div class="sirh-employee-hero__status">
            <span class="sirh-status-dot" aria-hidden="true"></span>
            <div>
                <strong><?= htmlspecialchars($jourTitre) ?></strong>
                <span><?= htmlspecialchars($jourTexte) ?></span>
            </div>
        </div>
    </div>

    <a class="sirh-qr-cta" href="<?= APP_URL ?>/presences/qr">
        <span class="sirh-qr-cta__icon" aria-hidden="true"><i class="fas fa-qrcode"></i></span>
        <span class="sirh-qr-cta__body">
            <strong>Afficher mon QR de présence</strong>
            <span>Présentez-le à la réception : le scan enregistre votre arrivée, puis votre départ.</span>
        </span>
        <i class="fas fa-chevron-right sirh-qr-cta__arrow" aria-hidden="true"></i>
    </a>

    <div class="sirh-stat-grid sirh-employee-stats">
        <article class="sirh-stat-card">
            <div class="sirh-stat-icon" style="--stat-color: var(--sirh-success); --stat-bg: var(--sirh-success-soft);"><i class="fas fa-calendar-check" aria-hidden="true"></i></div>
            <div class="sirh-stat-body">
                <p class="sirh-stat-label">Jours pointés</p>
                <p class="sirh-stat-value"><?= $joursTravailles ?></p>
                <p class="sirh-stat-meta">sur <?= (int) $stats['jours_pointes'] ?> jour(s) enregistré(s)</p>
            </div>
        </article>
        <article class="sirh-stat-card">
            <div class="sirh-stat-icon" style="--stat-color: var(--sirh-blue); --stat-bg: var(--sirh-blue-soft);"><i class="fas fa-clock" aria-hidden="true"></i></div>
            <div class="sirh-stat-body">
                <p class="sirh-stat-label">Heures de travail</p>
                <p class="sirh-stat-value"><?= htmlspecialchars(number_format((float) $stats['heures_travail'], 1, ',', ' ')) ?> h</p>
                <p class="sirh-stat-meta">arrivée et départ pointés</p>
            </div>
        </article>
        <article class="sirh-stat-card">
            <div class="sirh-stat-icon" style="--stat-color: var(--sirh-warning); --stat-bg: var(--sirh-warning-soft);"><i class="fas fa-hourglass-half" aria-hidden="true"></i></div>
            <div class="sirh-stat-body">
                <p class="sirh-stat-label">Retards cumulés</p>
                <p class="sirh-stat-value"><?= (int) $stats['minutes_retard'] ?> min</p>
                <p class="sirh-stat-meta"><?= (int) $stats['retards'] ?> jour(s) en retard</p>
            </div>
        </article>
        <article class="sirh-stat-card">
            <div class="sirh-stat-icon" style="--stat-color: var(--sirh-danger); --stat-bg: var(--sirh-danger-soft);"><i class="fas fa-user-clock" aria-hidden="true"></i></div>
            <div class="sirh-stat-body">
                <p class="sirh-stat-label">Absences</p>
                <p class="sirh-stat-value"><?= (int) $stats['absents'] ?></p>
                <p class="sirh-stat-meta"><?= (int) $stats['justifies'] ?> justifiée(s)</p>
            </div>
        </article>
    </div>

    <div class="sirh-employee-columns">
        <section class="sirh-panel" aria-labelledby="titre-pointages">
            <div class="sirh-panel__head">
                <h2 class="sirh-section-title" id="titre-pointages"><i class="fas fa-clock-rotate-left" aria-hidden="true"></i> Mes derniers pointages</h2>
                <a href="<?= APP_URL ?>/presences" class="sirh-link-more">Tout voir <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </div>
            <?php if (empty($dernieres)): ?>
            <div class="sirh-empty-state">
                <i class="fas fa-calendar-day" aria-hidden="true"></i>
                <p>Aucun pointage enregistré pour le moment.</p>
                <span>Votre premier scan à la réception apparaîtra ici.</span>
            </div>
            <?php else: ?>
            <ul class="sirh-timeline">
                <?php foreach ($dernieres as $p): ?>
                <?php $s = $statuts[$p['statut']] ?? [$p['statut'], 'neutral']; ?>
                <li class="sirh-timeline__item">
                    <span class="sirh-timeline__dot sirh-timeline__dot--<?= htmlspecialchars($s[1]) ?>" aria-hidden="true"></span>
                    <div class="sirh-timeline__body">
                        <strong><?= date('d/m/Y', strtotime($p['date_presence'])) ?></strong>
                        <span class="badge badge-<?= htmlspecialchars($s[1]) ?>"><?= htmlspecialchars($s[0]) ?></span>
                        <p>
                            <?= !empty($p['heure_arrivee']) ? 'Arrivée ' . htmlspecialchars(substr($p['heure_arrivee'], 0, 5)) : 'Aucune arrivée' ?>
                            <?= !empty($p['heure_depart']) ? ' · Départ ' . htmlspecialchars(substr($p['heure_depart'], 0, 5)) : '' ?>
                        </p>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </section>

        <div class="sirh-employee-side">
            <section class="sirh-panel" aria-labelledby="titre-conges">
                <div class="sirh-panel__head">
                    <h2 class="sirh-section-title" id="titre-conges"><i class="fas fa-umbrella-beach" aria-hidden="true"></i> Mes congés</h2>
                    <a href="<?= APP_URL ?>/conges" class="sirh-link-more">Gérer <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="sirh-mini-stats">
                    <div><b><?= (int) $congesEnAttente ?></b><span>en attente</span></div>
                    <div><b><?= (int) $congesApprouves ?></b><span>approuvés</span></div>
                </div>
                <?php if ($congesEnCours): ?>
                <p class="sirh-inline-note sirh-inline-note--info">
                    <i class="fas fa-plane-departure" aria-hidden="true"></i>
                    En congé du <?= date('d/m/Y', strtotime($congesEnCours['date_debut'])) ?> au <?= date('d/m/Y', strtotime($congesEnCours['date_fin'])) ?>.
                </p>
                <?php else: ?>
                <p class="sirh-inline-note">Aucun congé en cours. <a href="<?= APP_URL ?>/conges/create">Faire une demande</a></p>
                <?php endif; ?>
            </section>

            <section class="sirh-panel" aria-labelledby="titre-bulletin">
                <div class="sirh-panel__head">
                    <h2 class="sirh-section-title" id="titre-bulletin"><i class="fas fa-file-invoice-dollar" aria-hidden="true"></i> Dernier bulletin</h2>
                    <a href="<?= APP_URL ?>/paie/mes-bulletins" class="sirh-link-more">Tout voir <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <?php if ($dernierBulletin): ?>
                <div class="sirh-payslip">
                    <div>
                        <span class="sirh-payslip__period"><?= htmlspecialchars($bulletinLabel) ?></span>
                        <span class="sirh-payslip__status badge badge-<?= $dernierBulletin['statut'] === 'paye' ? 'success' : ($dernierBulletin['statut'] === 'valide' ? 'info' : 'warning') ?>"><?= htmlspecialchars($bulletinStatut) ?></span>
                    </div>
                    <strong><?= number_format((float) $dernierBulletin['total_net'], 2, ',', ' ') ?> FC</strong>
                    <a href="<?= APP_URL ?>/paie/ma-fiche/<?= (int) $dernierBulletin['id_bulletin'] ?>">Consulter le bulletin</a>
                </div>
                <?php else: ?>
                <p class="sirh-inline-note">Aucun bulletin disponible pour le moment.</p>
                <?php endif; ?>
            </section>

            <section class="sirh-panel sirh-panel--compact" aria-labelledby="titre-liens">
                <h2 class="sirh-section-title" id="titre-liens"><i class="fas fa-bolt" aria-hidden="true"></i> Accès rapides</h2>
                <div class="sirh-link-grid">
                    <a href="<?= APP_URL ?>/mon-profil"><i class="fas fa-id-card" aria-hidden="true"></i> Mon profil</a>
                    <a href="<?= APP_URL ?>/paie/mes-bulletins"><i class="fas fa-file-invoice" aria-hidden="true"></i> Mes bulletins</a>
                    <a href="<?= APP_URL ?>/messages"><i class="fas fa-comments" aria-hidden="true"></i> Mes messages</a>
                    <a href="<?= APP_URL ?>/presences/qr"><i class="fas fa-qrcode" aria-hidden="true"></i> Mon QR</a>
                </div>
            </section>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
