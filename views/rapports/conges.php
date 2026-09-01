<?php $pageTitle = 'Rapport des congés'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-calendar-check text-success"></i> Rapport des congés</h1>
        <a href="<?= APP_URL ?>/rapports" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-lg-4">
            <div class="small-box bg-success">
                <div class="inner"><h3><?= $congesApprouves ?></h3><p>Congés approuvés</p></div>
                <div class="icon"><i class="fas fa-check"></i></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="small-box bg-warning">
                <div class="inner"><h3><?= $congesRefuses ?></h3><p>Congés refusés</p></div>
                <div class="icon"><i class="fas fa-times"></i></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="small-box bg-info">
                <div class="inner"><h3><?= $totalJoursApprouves ?></h3><p>Jours approuvés au total</p></div>
                <div class="icon"><i class="fas fa-calendar"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Répartition par type</h3></div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Type</th><th>Nombre</th></tr></thead>
                        <tbody>
                            <?php foreach ($types as $type => $nb): ?>
                            <tr><td><span class="badge badge-info"><?= ucfirst($type) ?></span></td><td><?= $nb ?></td></tr>
                            <?php endforeach; ?>
                            <?php if (empty($types)): ?>
                            <tr><td colspan="2" class="text-center text-muted">Aucune donnée</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Détail des congés approuvés</h3></div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm">
                        <thead><tr><th>Employé</th><th>Type</th><th>Jours</th><th>Période</th></tr></thead>
                        <tbody>
                            <?php foreach ($conges as $c): ?>
                            <?php if ($c['statut'] === 'approuve'): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                                <td><?= ucfirst($c['type_conge']) ?></td>
                                <td><?= $c['nombre_jours'] ?></td>
                                <td><?= date('d/m/Y', strtotime($c['date_debut'])) ?> → <?= date('d/m/Y', strtotime($c['date_fin'])) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
