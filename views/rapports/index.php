<?php $pageTitle = 'Rapports et statistiques'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1><i class="fas fa-chart-pie text-primary"></i> Rapports et statistiques</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <form method="GET" action="<?= APP_URL ?>/rapports" class="form-inline">
                        <label class="mr-2">Mois :</label>
                        <select name="mois" class="form-control mr-2">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= $m == $mois ? 'selected' : '' ?>><?= str_pad($m, 2, '0', STR_PAD_LEFT) ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="annee" class="form-control mr-2">
                            <?php for ($y = date('Y') - 2; $y <= date('Y'); $y++): ?>
                            <option value="<?= $y ?>" <?= $y == $annee ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <button class="btn btn-secondary"><i class="fas fa-filter"></i> Appliquer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner"><h3><?= $totalEmployes ?></h3><p>Employés actifs</p></div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner"><h3><?= $tauxPresence ?>%</h3><p>Taux de présentéisme</p></div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner"><h3><?= $totalJoursConges ?></h3><p>Jours de congé approuvés</p></div>
                <div class="icon"><i class="fas fa-calendar"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner"><h3><?= number_format($masseSalariale, 0, ',', ' ') ?> FC</h3><p>Masse salariale brut</p></div>
                <div class="icon"><i class="fas fa-money-bill"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-bar"></i> Employés par service</h3></div>
                <div class="card-body">
                    <canvas id="chartService" height="260"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-pie"></i> Répartition par sexe</h3></div>
                <div class="card-body">
                    <canvas id="chartGender" height="260"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-line"></i> Présences du mois <?= $mois ?>/<?= $annee ?></h3></div>
                <div class="card-body">
                    <canvas id="chartPresence" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Statut des congés</h3></div>
                <div class="card-body">
                    <canvas id="chartConge" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <a href="<?= APP_URL ?>/rapports/absentisme?mois=<?= $mois ?>&annee=<?= $annee ?>" class="btn btn-block btn-outline-primary btn-lg mb-3">
                <i class="fas fa-user-clock"></i> Rapport d'absentéisme
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= APP_URL ?>/rapports/conges?mois=<?= $mois ?>&annee=<?= $annee ?>" class="btn btn-block btn-outline-success btn-lg mb-3">
                <i class="fas fa-calendar-check"></i> Rapport des congés
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= APP_URL ?>/paie?mois=<?= $mois ?>&annee=<?= $annee ?>" class="btn btn-block btn-outline-warning btn-lg mb-3">
                <i class="fas fa-money-bill-wave"></i> Rapport de paie
            </a>
        </div>
    </div>
</section>

<?php $extraScripts = '<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
const serviceData = ' . json_encode($employeesByService ?? []) . ';
new Chart(document.getElementById("chartService"), {
    type: "bar",
    data: {
        labels: serviceData.map(d => d.nom_service),
        datasets: [{ label: "Employés", data: serviceData.map(d => d.nombre),
            backgroundColor: ["#007bff","#28a745","#ffc107","#dc3545","#17a2b8","#6f42c1"] }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

const genderData = ' . json_encode($employeesByGender ?? []) . ';
new Chart(document.getElementById("chartGender"), {
    type: "doughnut",
    data: { labels: genderData.map(d => d.sexe === "M" ? "Hommes" : "Femmes"),
        datasets: [{ data: genderData.map(d => d.total), backgroundColor: ["#007bff","#e83e8c"] }] },
    options: { responsive: true }
});

const presenceData = ' . json_encode($monthlyPresence ?? []) . ';
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

new Chart(document.getElementById("chartConge"), {
    type: "pie",
    data: {
        labels: ["Approuvés", "Refusés", "En attente"],
        datasets: [{ data: [' . $congesApprouves . ', ' . $congesRefuses . ', ' . $congesEnAttente . '],
            backgroundColor: ["#28a745","#dc3545","#ffc107"] }]
    },
    options: { responsive: true }
});
</script>';

$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
