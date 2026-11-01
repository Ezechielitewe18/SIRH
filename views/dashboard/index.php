<?php
$pageTitle = 'Tableau de bord';
$role = $_SESSION['user_role'] ?? '';
$isManager = in_array($role, ['admin', 'rh'], true);
$moisLabel = str_pad((string) ($mois ?? date('m')), 2, '0', STR_PAD_LEFT) . '/' . ($annee ?? date('Y'));
ob_start();
?>

<section class="content-header">
    <div class="sirh-page-header">
        <div class="sirh-page-heading">
            <h1 class="sirh-page-title"><i class="fas fa-tachometer-alt" aria-hidden="true"></i> Tableau de bord</h1>
            <p class="sirh-page-subtitle">Vue d'ensemble de l'activité RH pour la période <?= htmlspecialchars($moisLabel) ?>.</p>
        </div>
        <div class="sirh-page-actions">
            <form method="GET" action="<?= APP_URL ?>/dashboard" class="sirh-toolbar-group" aria-label="Filtrer la période du tableau de bord">
                <label class="sirh-toolbar-label" for="dash-mois">Période</label>
                <select id="dash-mois" name="mois" class="form-control form-control-sm" style="width:auto;">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m == ($mois ?? date('m')) ? 'selected' : '' ?>>
                        <?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>
                    </option>
                    <?php endfor; ?>
                </select>
                <select name="annee" class="form-control form-control-sm" style="width:auto;" aria-label="Année">
                    <?php for ($y = date('Y') - 2; $y <= date('Y'); $y++): ?>
                    <option value="<?= $y ?>" <?= $y == ($annee ?? date('Y')) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button class="btn btn-secondary btn-sm" type="submit"><i class="fas fa-filter" aria-hidden="true"></i> Appliquer</button>
            </form>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <h2 class="sirh-section-title"><i class="fas fa-chart-simple" aria-hidden="true"></i> Indicateurs clés</h2>
        <div class="sirh-stat-grid">
            <article class="sirh-stat sirh-stat--info">
                <div class="sirh-stat-top">
                    <div>
                        <p class="sirh-stat-label">Employés actifs</p>
                        <p class="sirh-stat-value"><?= (int) ($totalEmployees ?? 0) ?></p>
                    </div>
                    <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-user-tie"></i></span>
                </div>
                <?php if ($isManager): ?>
                <a href="<?= APP_URL ?>/employees" class="sirh-stat-footer">Voir les employés <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                <?php endif; ?>
            </article>

            <article class="sirh-stat sirh-stat--success">
                <div class="sirh-stat-top">
                    <div>
                        <p class="sirh-stat-label">Présents aujourd'hui</p>
                        <p class="sirh-stat-value"><?= (int) ($todayStats['presents'] ?? 0) ?></p>
                    </div>
                    <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-check-circle"></i></span>
                </div>
                <a href="<?= APP_URL ?>/presences" class="sirh-stat-footer">Voir les présences <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </article>

            <article class="sirh-stat sirh-stat--warning">
                <div class="sirh-stat-top">
                    <div>
                        <p class="sirh-stat-label">En retard</p>
                        <p class="sirh-stat-value"><?= (int) ($todayStats['en_retard'] ?? 0) ?></p>
                    </div>
                    <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-clock"></i></span>
                </div>
                <a href="<?= APP_URL ?>/presences" class="sirh-stat-footer">Voir les retards <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </article>

            <article class="sirh-stat sirh-stat--neutral">
                <div class="sirh-stat-top">
                    <div>
                        <p class="sirh-stat-label">Taux de présence</p>
                        <p class="sirh-stat-value"><?= (int) ($tauxPresence ?? 0) ?>%</p>
                    </div>
                    <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-percentage"></i></span>
                </div>
                <a href="<?= APP_URL ?>/presences" class="sirh-stat-footer">Détail du jour <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </article>

            <article class="sirh-stat sirh-stat--danger">
                <div class="sirh-stat-top">
                    <div>
                        <p class="sirh-stat-label">Congés en attente</p>
                        <p class="sirh-stat-value"><?= (int) ($pendingLeaves ?? 0) ?></p>
                    </div>
                    <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-calendar-times"></i></span>
                </div>
                <a href="<?= APP_URL ?>/conges?statut=en_attente" class="sirh-stat-footer">Traiter les demandes <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </article>

            <article class="sirh-stat sirh-stat--success">
                <div class="sirh-stat-top">
                    <div>
                        <p class="sirh-stat-label">Congés approuvés</p>
                        <p class="sirh-stat-value"><?= (int) ($approvedLeaves ?? 0) ?></p>
                    </div>
                    <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-calendar-check"></i></span>
                </div>
                <a href="<?= APP_URL ?>/conges?statut=approuve" class="sirh-stat-footer">Voir les congés <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </article>

            <article class="sirh-stat sirh-stat--info">
                <div class="sirh-stat-top">
                    <div>
                        <p class="sirh-stat-label">Congés en cours</p>
                        <p class="sirh-stat-value"><?= count($congesEnCours ?? []) ?></p>
                    </div>
                    <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-plane"></i></span>
                </div>
                <a href="<?= APP_URL ?>/conges" class="sirh-stat-footer">Voir le calendrier <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </article>

            <article class="sirh-stat sirh-stat--violet">
                <div class="sirh-stat-top">
                    <div>
                        <p class="sirh-stat-label">Paie nette (<?= htmlspecialchars($moisLabel) ?>)</p>
                        <p class="sirh-stat-value" style="font-size:1.35rem;"><?= number_format($totalPaieNet ?? 0, 0, ',', ' ') ?> <small>FC</small></p>
                    </div>
                    <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-money-bill-wave"></i></span>
                </div>
                <a href="<?= APP_URL . ($role === 'employe' ? '/paie/mes-bulletins' : '/paie') ?>" class="sirh-stat-footer">Voir la paie <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </article>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar" aria-hidden="true"></i> Employés par service</h3>
                    </div>
                    <div class="card-body">
                        <div class="sirh-chart-wrap"><canvas id="chartService" aria-label="Graphique des employés par service" role="img"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie" aria-hidden="true"></i> Répartition par sexe</h3>
                    </div>
                    <div class="card-body">
                        <div class="sirh-chart-wrap"><canvas id="chartGender" aria-label="Graphique de répartition par sexe" role="img"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line" aria-hidden="true"></i> Présences (<?= htmlspecialchars($moisLabel) ?>)</h3>
                    </div>
                    <div class="card-body">
                        <div class="sirh-chart-wrap"><canvas id="chartPresence" aria-label="Graphique des présences du mois" role="img"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-area" aria-hidden="true"></i> Évolution de la paie (12 mois)</h3>
                    </div>
                    <div class="card-body">
                        <div class="sirh-chart-wrap"><canvas id="chartPaie" aria-label="Graphique d'évolution de la paie sur douze mois" role="img"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-plane" aria-hidden="true"></i> Congés en cours</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach (array_slice($congesEnCours ?? [], 0, 5) as $c): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></strong><br>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($c['date_debut'])) ?> → <?= date('d/m/Y', strtotime($c['date_fin'])) ?></small>
                                </div>
                                <span class="badge badge-info"><?= (int) $c['nombre_jours'] ?> j</span>
                            </li>
                            <?php endforeach; ?>
                            <?php if (empty($congesEnCours)): ?>
                            <li class="list-group-item">
                                <div class="sirh-empty" style="padding:26px 16px;">
                                    <span class="sirh-empty-icon" aria-hidden="true"><i class="fas fa-plane"></i></span>
                                    <p class="sirh-empty-title">Aucun congé en cours</p>
                                    <p class="sirh-empty-text">Les employés actuellement en congé apparaîtront ici.</p>
                                </div>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie" aria-hidden="true"></i> Congés par type</h3>
                    </div>
                    <div class="card-body">
                        <div class="sirh-chart-wrap" style="height:220px;"><canvas id="chartCongeType" aria-label="Graphique des congés par type" role="img"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar" aria-hidden="true"></i> Dernières demandes</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach (array_slice($recentLeaves ?? [], 0, 5) as $leave): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($leave['prenom'] . ' ' . $leave['nom']) ?></strong><br>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($leave['date_debut'])) ?> au <?= date('d/m/Y', strtotime($leave['date_fin'])) ?></small>
                                </div>
                                <span class="sirh-status sirh-status--<?= $leave['statut'] === 'approuve' ? 'success' : ($leave['statut'] === 'refuse' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst(str_replace('_', ' ', $leave['statut'])) ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                            <?php if (empty($recentLeaves)): ?>
                            <li class="list-group-item">
                                <div class="sirh-empty" style="padding:26px 16px;">
                                    <span class="sirh-empty-icon" aria-hidden="true"><i class="fas fa-calendar"></i></span>
                                    <p class="sirh-empty-title">Aucune demande</p>
                                    <p class="sirh-empty-text">Les demandes de congé récentes apparaîtront ici.</p>
                                </div>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();

$extraScripts = '<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
const sirhChartFont = { family: "Source Sans Pro, system-ui, sans-serif", size: 12 };
const sirhChartColor = "#5b6b7f";
const sirhChartGrid = "#e9eff5";

const serviceData = ' . json_encode($employeesByService ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
new Chart(document.getElementById("chartService"), {
    type: "bar",
    data: {
        labels: serviceData.map(d => d.nom_service),
        datasets: [{
            label: "Employés",
            data: serviceData.map(d => d.nombre),
            backgroundColor: ["#1769aa","#16805d","#b76e00","#bf3c4c","#0e7490","#6d4fa1"],
            borderRadius: 4,
            maxBarThickness: 42
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0, color: sirhChartColor }, grid: { color: sirhChartGrid } },
            x: { ticks: { color: sirhChartColor }, grid: { display: false } }
        }
    }
});

const genderData = ' . json_encode($employeesByGender ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
new Chart(document.getElementById("chartGender"), {
    type: "doughnut",
    data: {
        labels: genderData.map(d => d.sexe === "M" ? "Hommes" : "Femmes"),
        datasets: [{ data: genderData.map(d => d.total), backgroundColor: ["#1769aa", "#6d4fa1"], borderWidth: 0 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: "bottom", labels: { color: sirhChartColor, font: sirhChartFont, padding: 16, usePointStyle: true } } } }
});

const presenceData = ' . json_encode($monthlyPresence ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
new Chart(document.getElementById("chartPresence"), {
    type: "line",
    data: {
        labels: presenceData.map(d => d.jour),
        datasets: [
            { label: "Présents", data: presenceData.map(d => d.presents), borderColor: "#16805d", backgroundColor: "rgba(22,128,93,.08)", fill: true, tension: .3, pointRadius: 2 },
            { label: "En retard", data: presenceData.map(d => d.en_retard), borderColor: "#b76e00", backgroundColor: "rgba(183,110,0,.06)", fill: false, tension: .3, pointRadius: 2 }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: "bottom", labels: { color: sirhChartColor, font: sirhChartFont, usePointStyle: true } } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0, color: sirhChartColor }, grid: { color: sirhChartGrid } },
            x: { ticks: { color: sirhChartColor, maxTicksLimit: 10 }, grid: { display: false } }
        }
    }
});

const paieData = ' . json_encode($paieEvolution ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
new Chart(document.getElementById("chartPaie"), {
    type: "bar",
    data: {
        labels: paieData.map(d => d.mois_label),
        datasets: [
            { label: "Net", data: paieData.map(d => d.total_net), backgroundColor: "#16805d", borderRadius: 3 },
            { label: "Brut", data: paieData.map(d => d.total_brut), backgroundColor: "#1769aa", borderRadius: 3 }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: "bottom", labels: { color: sirhChartColor, font: sirhChartFont, usePointStyle: true } } },
        scales: {
            y: { beginAtZero: true, ticks: { color: sirhChartColor }, grid: { color: sirhChartGrid } },
            x: { ticks: { color: sirhChartColor }, grid: { display: false } }
        }
    }
});

const congeTypeData = ' . json_encode($congesParType ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
new Chart(document.getElementById("chartCongeType"), {
    type: "doughnut",
    data: {
        labels: Object.keys(congeTypeData),
        datasets: [{ data: Object.values(congeTypeData), backgroundColor: ["#1769aa","#16805d","#b76e00","#bf3c4c","#0e7490"], borderWidth: 0 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: "bottom", labels: { color: sirhChartColor, font: sirhChartFont, padding: 12, usePointStyle: true } } } }
});
</script>';

require __DIR__ . '/../layouts/app.php';
?>
