<?php
$pageTitle = 'Gestion de la paie';
$moisLabel = str_pad((string) ($mois ?? date('m')), 2, '0', STR_PAD_LEFT) . '/' . ($annee ?? date('Y'));
$nbBulletins = count($bulletins ?? []);
$totalRetenues = ($totalBrut ?? 0) - ($totalNet ?? 0);
ob_start();
?>

<section class="content-header">
    <div class="sirh-page-header">
        <div class="sirh-page-heading">
            <h1 class="sirh-page-title"><i class="fas fa-money-bill-wave" aria-hidden="true"></i> Paie</h1>
            <p class="sirh-page-subtitle">Bulletins de paie de la période <?= htmlspecialchars($moisLabel) ?>.</p>
        </div>
        <div class="sirh-page-actions">
            <a href="<?= APP_URL ?>/paie/archives" class="btn btn-outline-secondary btn-sm sirh-desktop-only">
                <i class="fas fa-archive" aria-hidden="true"></i> Archives
            </a>
            <a href="<?= APP_URL ?>/paie/parametres" class="btn btn-outline-secondary btn-sm sirh-desktop-only">
                <i class="fas fa-cog" aria-hidden="true"></i> Paramètres
            </a>
            <a href="<?= APP_URL ?>/export/paie?mois=<?= (int) $mois ?>&annee=<?= (int) $annee ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-file-excel" aria-hidden="true"></i> Export Excel
            </a>
            <form method="POST" action="<?= APP_URL ?>/paie/generer" class="d-inline"
                  onsubmit="return confirm('Générer les bulletins de paie pour <?= htmlspecialchars($moisLabel) ?> ?')">
                <?= csrf_field() ?>
                <input type="hidden" name="mois" value="<?= (int) $mois ?>">
                <input type="hidden" name="annee" value="<?= (int) $annee ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sync-alt" aria-hidden="true"></i> Générer les bulletins
                </button>
            </form>
            <div class="dropdown sirh-mobile-only">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-h" aria-hidden="true"></i> Plus d'actions
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="<?= APP_URL ?>/paie/archives" class="dropdown-item"><i class="fas fa-archive" aria-hidden="true"></i> Archives</a>
                    <a href="<?= APP_URL ?>/paie/parametres" class="dropdown-item"><i class="fas fa-cog" aria-hidden="true"></i> Paramètres</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="sirh-toolbar">
        <form method="GET" action="<?= APP_URL ?>/paie" class="sirh-toolbar-group" aria-label="Choisir la période de paie">
            <label class="sirh-toolbar-label" for="paie-mois">Période</label>
            <select id="paie-mois" name="mois" class="form-control form-control-sm" style="width:auto;">
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
            <button class="btn btn-secondary btn-sm" type="submit"><i class="fas fa-filter" aria-hidden="true"></i> Filtrer</button>
        </form>
        <div class="sirh-toolbar-group">
            <span class="sirh-muted" style="font-size:.84rem;">
                <i class="fas fa-file-invoice" aria-hidden="true"></i>
                <?= $nbBulletins ?> bulletin<?= $nbBulletins > 1 ? 's' : '' ?>
            </span>
        </div>
    </div>

    <div class="sirh-stat-grid">
        <article class="sirh-stat sirh-stat--info">
            <div class="sirh-stat-top">
                <div>
                    <p class="sirh-stat-label">Bulletins générés</p>
                    <p class="sirh-stat-value"><?= $nbBulletins ?></p>
                </div>
                <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-file-invoice"></i></span>
            </div>
        </article>

        <article class="sirh-stat sirh-stat--success">
            <div class="sirh-stat-top">
                <div>
                    <p class="sirh-stat-label">Total net à payer</p>
                    <p class="sirh-stat-value" style="font-size:1.4rem;">
                        <?= number_format($totalNet ?? 0, 0, ',', ' ') ?> <small>FC</small>
                    </p>
                </div>
                <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-hand-holding-usd"></i></span>
            </div>
        </article>

        <article class="sirh-stat sirh-stat--warning">
            <div class="sirh-stat-top">
                <div>
                    <p class="sirh-stat-label">Masse salariale brute</p>
                    <p class="sirh-stat-value" style="font-size:1.4rem;">
                        <?= number_format($totalBrut ?? 0, 0, ',', ' ') ?> <small>FC</small>
                    </p>
                </div>
                <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-coins"></i></span>
            </div>
        </article>

        <article class="sirh-stat sirh-stat--danger">
            <div class="sirh-stat-top">
                <div>
                    <p class="sirh-stat-label">Total retenues</p>
                    <p class="sirh-stat-value" style="font-size:1.4rem;">
                        <?= number_format($totalRetenues, 0, ',', ' ') ?> <small>FC</small>
                    </p>
                </div>
                <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-minus-circle"></i></span>
            </div>
        </article>
    </div>

    <div class="card">
        <?php if ($nbBulletins > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover sirh-table-wide">
                <thead>
                    <tr>
                        <th scope="col">Matricule</th>
                        <th scope="col">Employé</th>
                        <th scope="col">Service</th>
                        <th scope="col" class="sirh-col-amount">Salaire base</th>
                        <th scope="col" class="sirh-col-amount">Primes</th>
                        <th scope="col" class="sirh-col-amount">Brut</th>
                        <th scope="col" class="sirh-col-amount">Retenues</th>
                        <th scope="col" class="sirh-col-amount">Net</th>
                        <th scope="col">Statut</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bulletins as $b): ?>
                    <tr>
                        <td><span class="sirh-matricule"><?= htmlspecialchars($b['matricule']) ?></span></td>
                        <td>
                            <a href="<?= APP_URL ?>/paie/detail/<?= $b['id_bulletin'] ?>" class="sirh-cell-strong">
                                <?= htmlspecialchars($b['prenom'] . ' ' . $b['nom']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($b['nom_service'] ?? 'N/A') ?></td>
                        <td class="sirh-cell-amount"><?= number_format($b['salaire_base'], 0, ',', ' ') ?> FC</td>
                        <td class="sirh-cell-amount"><?= number_format($b['primes'], 0, ',', ' ') ?> FC</td>
                        <td class="sirh-cell-amount"><?= number_format($b['total_brut'], 0, ',', ' ') ?> FC</td>
                        <td class="sirh-cell-amount" style="color:var(--sirh-danger);">-<?= number_format($b['total_retenues'], 0, ',', ' ') ?> FC</td>
                        <td class="sirh-cell-amount" style="color:var(--sirh-success);font-weight:700;"><?= number_format($b['total_net'], 0, ',', ' ') ?> FC</td>
                        <td>
                            <span class="sirh-status sirh-status--<?= $b['statut'] === 'paye' ? 'success' : ($b['statut'] === 'valide' ? 'info' : 'warning') ?>">
                                <?= ucfirst($b['statut']) ?>
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="d-inline-flex align-items-center" style="gap:5px;">
                                <a href="<?= APP_URL ?>/paie/detail/<?= $b['id_bulletin'] ?>" class="btn btn-sm btn-info btn-icon" title="Voir le bulletin" aria-label="Voir le bulletin de <?= htmlspecialchars($b['prenom'] . ' ' . $b['nom']) ?>">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </a>
                                <?php if ($b['statut'] === 'brouillon'): ?>
                                <form method="POST" action="<?= APP_URL ?>/paie/valider/<?= $b['id_bulletin'] ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-primary btn-icon" title="Valider le bulletin" aria-label="Valider le bulletin de <?= htmlspecialchars($b['prenom'] . ' ' . $b['nom']) ?>">
                                        <i class="fas fa-check" aria-hidden="true"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <?php if ($b['statut'] !== 'paye'): ?>
                                <form method="POST" action="<?= APP_URL ?>/paie/payer/<?= $b['id_bulletin'] ?>" class="d-inline" onsubmit="return confirm('Marquer ce bulletin comme payé ?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-success btn-icon" title="Marquer comme payé" aria-label="Marquer le bulletin de <?= htmlspecialchars($b['prenom'] . ' ' . $b['nom']) ?> comme payé">
                                        <i class="fas fa-hand-holding-usd" aria-hidden="true"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="sirh-empty">
            <span class="sirh-empty-icon" aria-hidden="true"><i class="fas fa-file-invoice-dollar"></i></span>
            <p class="sirh-empty-title">Aucun bulletin pour <?= htmlspecialchars($moisLabel) ?></p>
            <p class="sirh-empty-text">Générez les bulletins de la période pour préparer la paie du mois.</p>
            <form method="POST" action="<?= APP_URL ?>/paie/generer" onsubmit="return confirm('Générer les bulletins de paie pour <?= htmlspecialchars($moisLabel) ?> ?')">
                <?= csrf_field() ?>
                <input type="hidden" name="mois" value="<?= (int) $mois ?>">
                <input type="hidden" name="annee" value="<?= (int) $annee ?>">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-sync-alt" aria-hidden="true"></i> Générer les bulletins</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
