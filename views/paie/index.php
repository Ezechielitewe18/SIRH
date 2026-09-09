<?php $pageTitle = 'Gestion de la paie'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-money-bill-wave text-success"></i> Paie</h1>
        <div>
            <a href="<?= APP_URL ?>/paie/archives" class="btn btn-dark">
                <i class="fas fa-archive"></i> Archives
            </a>
            <a href="<?= APP_URL ?>/paie/parametres" class="btn btn-secondary">
                <i class="fas fa-cog"></i> Paramètres
            </a>
            <form method="POST" action="<?= APP_URL ?>/paie/generer" style="display:inline;" onsubmit="return confirm('Générer les bulletins de paie pour <?= $mois ?>/<?= $annee ?> ?')">
                <?= csrf_field() ?>
                <input type="hidden" name="mois" value="<?= $mois ?>">
                <input type="hidden" name="annee" value="<?= $annee ?>">
                <button type="submit" class="btn btn-success"><i class="fas fa-sync-alt"></i> Générer les bulletins</button>
            </form>
            <a href="<?= APP_URL ?>/export/paie?mois=<?= $mois ?>&annee=<?= $annee ?>" class="btn btn-info">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <form method="GET" action="<?= APP_URL ?>/paie" class="form-inline">
                        <label class="mr-2">Mois :</label>
                        <select name="mois" class="form-control mr-2">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= $m == $mois ? 'selected' : '' ?>>
                                <?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                        <select name="annee" class="form-control mr-2">
                            <?php for ($y = date('Y') - 2; $y <= date('Y'); $y++): ?>
                            <option value="<?= $y ?>" <?= $y == $annee ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <button class="btn btn-secondary"><i class="fas fa-filter"></i> Filtrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= count($bulletins) ?></h3>
                    <p>Bulletins</p>
                </div>
                <div class="icon"><i class="fas fa-file-invoice"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?= number_format($totalNet ?? 0, 0, ',', ' ') ?> FC</h3>
                    <p>Total net à payer</p>
                </div>
                <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?= number_format($totalBrut ?? 0, 0, ',', ' ') ?> FC</h3>
                    <p>Masse salariale brute</p>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?= number_format(($totalBrut ?? 0) - ($totalNet ?? 0), 0, ',', ' ') ?> FC</h3>
                    <p>Total retenues</p>
                </div>
                <div class="icon"><i class="fas fa-minus-circle"></i></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Employé</th>
                        <th>Service</th>
                        <th>Salaire base</th>
                        <th>Primes</th>
                        <th>Brut</th>
                        <th>Retenues</th>
                        <th>Net</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bulletins as $b): ?>
                    <tr>
                        <td><span class="badge badge-info"><?= htmlspecialchars($b['matricule']) ?></span></td>
                        <td><strong><?= htmlspecialchars($b['prenom'] . ' ' . $b['nom']) ?></strong></td>
                        <td><?= htmlspecialchars($b['nom_service'] ?? 'N/A') ?></td>
                        <td><?= number_format($b['salaire_base'], 0, ',', ' ') ?> FC</td>
                        <td><?= number_format($b['primes'], 0, ',', ' ') ?> FC</td>
                        <td><strong><?= number_format($b['total_brut'], 0, ',', ' ') ?> FC</strong></td>
                        <td class="text-danger">-<?= number_format($b['total_retenues'], 0, ',', ' ') ?> FC</td>
                        <td><span class="text-success font-weight-bold"><?= number_format($b['total_net'], 0, ',', ' ') ?> FC</span></td>
                        <td>
                            <span class="badge badge-<?= $b['statut'] === 'paye' ? 'success' : ($b['statut'] === 'valide' ? 'primary' : 'warning') ?>">
                                <?= ucfirst($b['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= APP_URL ?>/paie/detail/<?= $b['id_bulletin'] ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <?php if ($b['statut'] === 'brouillon'): ?>
                            <form method="POST" action="<?= APP_URL ?>/paie/valider/<?= $b['id_bulletin'] ?>" style="display:inline;">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-primary" title="Valider"><i class="fas fa-check"></i></button>
                            </form>
                            <?php endif; ?>
                            <?php if ($b['statut'] !== 'paye'): ?>
                            <form method="POST" action="<?= APP_URL ?>/paie/payer/<?= $b['id_bulletin'] ?>" style="display:inline;" onsubmit="return confirm('Marquer comme payé ?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-success" title="Marquer payé"><i class="fas fa-hand-holding-usd"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($bulletins)): ?>
                    <tr><td colspan="10" class="text-center text-muted">
                        Aucun bulletin pour <?= $mois ?>/<?= $annee ?>. 
                        Cliquez sur "Générer les bulletins".
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
