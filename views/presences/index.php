<?php $pageTitle = 'Gestion des présences'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1>Présences</h1>
</section>

<section class="content">
    <!-- Pointage rapide -->
    <?php if (isset($_SESSION['employee_id'])): ?>
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-sign-in-alt"></i> Pointage</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= APP_URL ?>/presences/checkin" class="d-inline">
                        <button type="submit" class="btn btn-success btn-lg" id="btnCheckin">
                            <i class="fas fa-sign-in-alt"></i> Arrivée
                        </button>
                    </form>
                    <form method="POST" action="<?= APP_URL ?>/presences/checkout" class="d-inline ml-2">
                        <button type="submit" class="btn btn-warning btn-lg" id="btnCheckout">
                            <i class="fas fa-sign-out-alt"></i> Départ
                        </button>
                    </form>
                    <div id="currentTime" class="mt-2 text-muted"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Registre des présences</h3>
                <div class="d-flex align-items-center">
                    <a href="<?= APP_URL ?>/export/presences?date=<?= $date ?>" class="btn btn-info btn-sm mr-2">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <form method="GET" action="<?= APP_URL ?>/presences" class="form-inline">
                        <input type="date" class="form-control mr-2" name="date" value="<?= htmlspecialchars($date ?? date('Y-m-d')) ?>">
                        <button class="btn btn-secondary" type="submit"><i class="fas fa-filter"></i> Filtrer</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom complet</th>
                        <th>Service</th>
                        <th>Arrivée</th>
                        <th>Départ</th>
                        <th>Retard</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($presences ?? [] as $p): ?>
                    <tr>
                        <td><span class="badge badge-info"><?= htmlspecialchars($p['matricule']) ?></span></td>
                        <td><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></td>
                        <td><?= htmlspecialchars($p['nom_service'] ?? 'N/A') ?></td>
                        <td><?= $p['heure_arrivee'] ? date('H:i', strtotime($p['heure_arrivee'])) : '-' ?></td>
                        <td><?= $p['heure_depart'] ? date('H:i', strtotime($p['heure_depart'])) : '-' ?></td>
                        <td><?= $p['retard'] > 0 ? $p['retard'] . ' min' : '-' ?></td>
                        <td>
                            <span class="badge badge-<?= $p['statut'] === 'present' ? 'success' : ($p['statut'] === 'retard' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($p['statut']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($presences)): ?>
                    <tr><td colspan="7" class="text-center text-muted">Aucune présence enregistrée pour cette date</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php $extraScripts = '<script>
function updateClock() {
    const now = new Date();
    document.getElementById("currentTime").textContent = "Heure actuelle: " + now.toLocaleTimeString("fr-FR");
}
setInterval(updateClock, 1000);
updateClock();
</script>';

$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
