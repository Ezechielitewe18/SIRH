<?php $pageTitle = 'Rapport d\'absentéisme'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-user-clock text-warning"></i> Rapport d'absentéisme - <?= $mois ?>/<?= $annee ?></h1>
        <div>
            <a href="<?= APP_URL ?>/rapports" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
</section>

<section class="content">
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Matricule</th>
                        <th>Employé</th>
                        <th>Service</th>
                        <th>Jours présents</th>
                        <th>Jours absents</th>
                        <th>Taux d'absence</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $index => $s): ?>
                    <tr>
                        <td><span class="badge badge-<?= $s['taux_absence'] > 20 ? 'danger' : ($s['taux_absence'] > 10 ? 'warning' : 'success') ?>">#<?= $index + 1 ?></span></td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($s['matricule']) ?></span></td>
                        <td><strong><?= htmlspecialchars($s['nom']) ?></strong></td>
                        <td><?= htmlspecialchars($s['service'] ?? 'N/A') ?></td>
                        <td><?= $s['jours_presents'] ?></td>
                        <td class="text-danger"><?= $s['jours_absents'] ?></td>
                        <td>
                            <span class="badge badge-<?= $s['taux_absence'] > 20 ? 'danger' : ($s['taux_absence'] > 10 ? 'warning' : 'success') ?>">
                                <?= $s['taux_absence'] ?>%
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($stats)): ?>
                    <tr><td colspan="7" class="text-center text-muted">Aucune donnée</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
