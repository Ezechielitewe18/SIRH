<?php $pageTitle = 'Archives des bulletins de paie'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-archive text-primary"></i> Archives des bulletins de paie</h1>
        <a href="<?= APP_URL ?>/paie" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour à la paie</a>
    </div>
</section>

<section class="content">
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Période</th>
                        <th>Bulletins</th>
                        <th>Payés</th>
                        <th>Validés</th>
                        <th>Brouillons</th>
                        <th>Total brut</th>
                        <th>Total net</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($archives as $a): ?>
                    <?php
                    $totalBrut = (float)($a['total_brut'] ?? 0);
                    $totalNet = (float)($a['total_net'] ?? 0);
                    ?>
                    <tr>
                        <td>
                            <span class="badge badge-dark"><?= strtoupper(str_pad($a['mois'], 2, '0', STR_PAD_LEFT)) ?></span>
                            / <?= $a['annee'] ?>
                        </td>
                        <td><span class="badge badge-info"><?= $a['nb_bulletins'] ?></span></td>
                        <td><span class="badge badge-success"><?= $a['payes'] ?></span></td>
                        <td><span class="badge badge-primary"><?= $a['valides'] ?></span></td>
                        <td><span class="badge badge-warning"><?= $a['brouillons'] ?></span></td>
                        <td><?= number_format($totalBrut, 0, ',', ' ') ?> FC</td>
                        <td><span class="text-success font-weight-bold"><?= number_format($totalNet, 0, ',', ' ') ?> FC</span></td>
                        <td>
                            <a href="<?= APP_URL ?>/paie?mois=<?= $a['mois'] ?>&annee=<?= $a['annee'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Voir les bulletins
                            </a>
                            <a href="<?= APP_URL ?>/export/paie?mois=<?= $a['mois'] ?>&annee=<?= $a['annee'] ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-file-excel"></i> Export
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($archives)): ?>
                    <tr><td colspan="8" class="text-center text-muted">
                        Aucun bulletin archivé. Générez des bulletins de paie pour commencer.
                    </td></tr>
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