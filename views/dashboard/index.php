<?php $pageTitle = 'Tableau de bord'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1><i class="fas fa-tachometer-alt text-primary"></i> Tableau de bord</h1>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" action="<?= APP_URL ?>/dashboard" class="form-inline">
                    <label class="mr-2"><i class="fas fa-calendar-alt"></i> Période :</label>
                    <select name="mois" class="form-control form-control-sm mr-2">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $m == ($mois ?? date('m')) ? 'selected' : '' ?>>
                            <?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                    <select name="annee" class="form-control form-control-sm mr-2">
                        <?php for ($y = date('Y') - 2; $y <= date('Y'); $y++): ?>
                        <option value="<?= $y ?>" <?= $y == ($annee ?? date('Y')) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                    <button class="btn btn-secondary btn-sm"><i class="fas fa-filter"></i> Appliquer</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $totalEmployees ?? 0 ?></h3>
                        <p>Employés actifs</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-tie"></i></div>
                    <a href="<?= APP_URL ?>/employees" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $todayStats['presents'] ?? 0 ?></h3>
                        <p>Présents aujourd'hui</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <a href="<?= APP_URL ?>/presences" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= $todayStats['en_retard'] ?? 0 ?></h3>
                        <p>En retard</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <a href="<?= APP_URL ?>/presences" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3><?= $tauxPresence ?? 0 ?>%</h3>
                        <p>Taux de présence</p>
                    </div>
                    <div class="icon"><i class="fas fa-percentage"></i></div>
                    <a href="<?= APP_URL ?>/presences" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $pendingLeaves ?? 0 ?></h3>
                        <p>Congés en attente</p>
                    </div>
                    <div class="icon"><i class="fas fa-calendar-times"></i></div>
                    <a href="<?= APP_URL ?>/conges" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-maroon">
                    <div class="inner">
                        <h3><?= $approvedLeaves ?? 0 ?></h3>
                        <p>Congés approuvés</p>
                    </div>
                    <div class="icon"><i class="fas fa-calendar-check"></i></div>
                    <a href="<?= APP_URL ?>/conges" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-teal">
                    <div class="inner">
                        <h3><?= count($congesEnCours ?? []) ?></h3>
                        <p>Congés en cours</p>
                    </div>
                    <div class="icon"><i class="fas fa-plane"></i></div>
                    <a href="<?= APP_URL ?>/conges" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3><?= number_format($totalPaieNet ?? 0, 0, ',', ' ') ?> FC</h3>
                        <p>Paie nette (<?= $mois ?>/<?= $annee ?>)</p>
                    </div>
                    <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    <a href="<?= APP_URL ?>/paie" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> Employés par service</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartService" height="260"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Répartition par sexe</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartGender" height="260"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line"></i> Présences (<?= $mois ?>/<?= $annee ?>)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPresence" height="220"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-area"></i> Évolution de la paie (12 mois)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPaie" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-plane"></i> Congés en cours</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach (array_slice($congesEnCours ?? [], 0, 5) as $c): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></strong><br>
                                    <small class="text-muted"><?= $c['date_debut'] ?> → <?= $c['date_fin'] ?></small>
                                </div>
                                <span class="badge badge-info"><?= $c['nombre_jours'] ?>j</span>
                            </li>
                            <?php endforeach; ?>
                            <?php if (empty($congesEnCours)): ?>
                            <li class="list-group-item text-center text-muted">Aucun congé en cours</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Congés par type</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartCongeType" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar"></i> Dernières demandes</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach (array_slice($recentLeaves ?? [], 0, 5) as $leave): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($leave['prenom'] . ' ' . $leave['nom']) ?></strong><br>
                                    <small class="text-muted"><?= $leave['date_debut'] ?> au <?= $leave['date_fin'] ?></small>
                                </div>
                                <span class="badge badge-<?= $leave['statut'] === 'approuve' ? 'success' : ($leave['statut'] === 'refuse' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst(str_replace('_', ' ', $leave['statut'])) ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                            <?php if (empty($recentLeaves)): ?>
                            <li class="list-group-item text-center text-muted">Aucune demande</li>
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
const serviceData = ' . json_encode($employeesByService ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
new Chart(document.getElementById("chartService"), {
    type: "bar",
    data: {
        labels: serviceData.map(d => d.nom_service),
        datasets: [{
            label: "Employés",
            data: serviceData.map(d => d.nombre),
            backgroundColor: ["#007bff","#28a745","#ffc107","#dc3545","#17a2b8","#6f42c1"],
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

const genderData = ' . json_encode($employeesByGender ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
new Chart(document.getElementById("chartGender"), {
    type: "doughnut",
    data: {
        labels: genderData.map(d => d.sexe === "M" ? "Hommes" : "Femmes"),
        datasets: [{ data: genderData.map(d => d.total), backgroundColor: ["#007bff", "#e83e8c"] }]
    },
    options: { responsive: true }
});

const presenceData = ' . json_encode($monthlyPresence ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
new Chart(document.getElementById("chartPresence"), {
    type: "line",
    data: {
        labels: presenceData.map(d => d.jour),
        datasets: [
            { label: "Présents", data: presenceData.map(d => d.presents), borderColor: "#28a745", fill: false, tension: .3 },
            { label: "En retard", data: presenceData.map(d => d.en_retard), borderColor: "#ffc107", fill: false, tension: .3 }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

const paieData = ' . json_encode($paieEvolution ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
new Chart(document.getElementById("chartPaie"), {
    type: "bar",
    data: {
        labels: paieData.map(d => d.mois_label),
        datasets: [
            { label: "Net", data: paieData.map(d => d.total_net), backgroundColor: "#28a745" },
            { label: "Brut", data: paieData.map(d => d.total_brut), backgroundColor: "#ffc107" }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

const congeTypeData = ' . json_encode($congesParType ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
new Chart(document.getElementById("chartCongeType"), {
    type: "doughnut",
    data: {
        labels: Object.keys(congeTypeData),
        datasets: [{ data: Object.values(congeTypeData), backgroundColor: ["#007bff","#28a745","#ffc107","#dc3545","#17a2b8"] }]
    },
    options: { responsive: true }
});
</script>';

require __DIR__ . '/../layouts/app.php';
?>