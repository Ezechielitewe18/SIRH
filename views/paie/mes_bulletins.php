<?php $pageTitle = 'Mes bulletins de paie'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Mes bulletins de paie</h1>
        <a href="<?= APP_URL ?>/dashboard" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
</section>

<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Historique de mes paies</h3>
        </div>
        <div class="card-body p-0">
<?php if (empty($mesBulletins)): ?>
            <div class="text-center text-muted" style="padding:40px 20px">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p class="mb-0">Aucun bulletin de paie pour le moment.</p>
            </div>
<?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Brut</th>
                            <th>Retenues</th>
                            <th>Net à payer</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach ($mesBulletins as $b): ?>
                        <tr>
                            <td><?= str_pad((string)$b['mois'], 2, '0', STR_PAD_LEFT) ?>/<?= htmlspecialchars($b['annee']) ?></td>
                            <td><?= number_format($b['total_brut'], 2, ',', ' ') ?> FC</td>
                            <td class="text-danger">- <?= number_format($b['total_retenues'], 2, ',', ' ') ?> FC</td>
                            <td class="font-weight-bold"><?= number_format($b['total_net'], 2, ',', ' ') ?> FC</td>
                            <td>
<?php
$statuts = ['brouillon' => 'Brouillon', 'valide' => 'Validé', 'paye' => 'Payé'];
$couleurs = ['brouillon' => 'secondary', 'valide' => 'warning', 'paye' => 'success'];
$statut = $b['statut'];
?>
                                <span class="badge badge-<?= $couleurs[$statut] ?? 'secondary' ?>"><?= $statuts[$statut] ?? htmlspecialchars($statut) ?></span>
                            </td>
                            <td class="text-right">
<?php if ($statut !== 'brouillon'): ?>
                                <a href="<?= APP_URL ?>/paie/ma-fiche/<?= $b['id_bulletin'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Détail
                                </a>
<?php else: ?>
                                <span class="text-muted small">En préparation</span>
<?php endif; ?>
                            </td>
                        </tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
<?php endif; ?>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
