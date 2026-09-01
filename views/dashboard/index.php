<?php $pageTitle = 'Tableau de bord'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1>Tableau de bord</h1>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- Cards statistiques -->
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
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $pendingLeaves ?? 0 ?></h3>
                        <p>Congés en attente</p>
                    </div>
                    <div class="icon"><i class="fas fa-calendar-times"></i></div>
                    <a href="<?= APP_URL ?>/conges" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3><?= count($employeesByService ?? []) ?></h3>
                        <p>Services</p>
                    </div>
                    <div class="icon"><i class="fas fa-building"></i></div>
                    <a href="<?= APP_URL ?>/services" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-indigo">
                    <div class="inner">
                        <h3><?= $totalFormations ?? 0 ?></h3>
                        <p>Formations</p>
                    </div>
                    <div class="icon"><i class="fas fa-graduation-cap"></i></div>
                    <a href="<?= APP_URL ?>/formations" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-teal">
                    <div class="inner">
                        <h3><?= number_format($totalPaieNet ?? 0, 0, ',', ' ') ?> FC</h3>
                        <p>Paie nette du mois</p>
                    </div>
                    <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    <a href="<?= APP_URL ?>/paie" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
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
        </div>

        <div class="row">
            <!-- Graphique: Employés par service -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> Employés par service</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartService" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Graphique: Répartition par sexe -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Répartition par sexe</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartGender" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Statistiques présences mensuelles -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line"></i> Présences du mois</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPresence" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Dernières demandes de congé -->
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
// Données pour le graphique par service
const serviceData = ' . json_encode($employeesByService ?? []) . ';
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

// Données pour le graphique par sexe
const genderData = ' . json_encode($employeesByGender ?? []) . ';
new Chart(document.getElementById("chartGender"), {
    type: "doughnut",
    data: {
        labels: genderData.map(d => d.sexe === "M" ? "Hommes" : "Femmes"),
        datasets: [{ data: genderData.map(d => d.total), backgroundColor: ["#007bff", "#e83e8c"] }]
    },
    options: { responsive: true }
});

// Données pour le graphique de présences
const presenceData = ' . json_encode($monthlyPresence ?? []) . ';
new Chart(document.getElementById("chartPresence"), {
    type: "line",
    data: {
        labels: presenceData.map(d => d.jour),
        datasets: [
            { label: "Présents", data: presenceData.map(d => d.presents), borderColor: "#28a745", fill: false },
            { label: "En retard", data: presenceData.map(d => d.en_retard), borderColor: "#ffc107", fill: false }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>';

require __DIR__ . '/../layouts/app.php';
?>
