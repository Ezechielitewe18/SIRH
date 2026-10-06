<?php $pageTitle = 'Mon bulletin de paie'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Bulletin de paie <?= str_pad((string)$bulletin['mois'], 2, '0', STR_PAD_LEFT) ?>/<?= htmlspecialchars($bulletin['annee']) ?></h1>
        <a href="<?= APP_URL ?>/paie/mes-bulletins" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Mes bulletins</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-tie"></i> Mes informations</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-center"><?= htmlspecialchars($employeFiche['prenom'] . ' ' . $employeFiche['nom']) ?></h4>
                    <p class="text-center text-muted">
                        <span class="badge badge-info"><?= htmlspecialchars($employeFiche['matricule']) ?></span>
                    </p>
                    <table class="table table-sm">
                        <tr><th>Service</th><td><?= htmlspecialchars($employeFiche['nom_service'] ?? 'N/A') ?></td></tr>
                        <tr><th>Poste</th><td><?= htmlspecialchars($employeFiche['poste'] ?? 'N/A') ?></td></tr>
                        <tr><th>Période</th><td><?= str_pad((string)$bulletin['mois'], 2, '0', STR_PAD_LEFT) ?>/<?= htmlspecialchars($bulletin['annee']) ?></td></tr>
                        <tr><th>Statut</th><td><span class="badge badge-<?= $bulletin['statut'] === 'paye' ? 'success' : 'warning' ?>"><?= ucfirst(htmlspecialchars($bulletin['statut'])) ?></span></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Détail de mon salaire</h3>
                </div>
                <div class="card-body">
                    <h5 class="text-primary mb-3">Gains</h5>
                    <table class="table table-bordered">
                        <tr><th>Salaire de base</th><td class="text-right"><?= number_format($bulletin['salaire_base'], 2, ',', ' ') ?> FC</td></tr>
<?php
$detailPrimes = [];
if (!empty($bulletin['detail_primes'])) {
    $detailPrimes = json_decode($bulletin['detail_primes'], true) ?: [];
}
// Bulletin genere avant l'ajout du detail : une seule ligne de synthese.
if (!$detailPrimes && (float)$bulletin['primes'] > 0) {
    $detailPrimes = [['libelle' => 'Primes (logement + transport)', 'montant' => (float)$bulletin['primes']]];
}
foreach ($detailPrimes as $ligne):
    $estPerso = stripos($ligne['libelle'], 'Indemnite logement') === false
        && stripos($ligne['libelle'], 'Prime transport') === false
        && stripos($ligne['libelle'], 'Primes (logement + transport)') === false;
?>
                        <tr<?= $estPerso ? ' class="table-light"' : '' ?>>
                            <th><?= htmlspecialchars($ligne['libelle']) ?></th>
                            <td class="text-right"><?= number_format($ligne['montant'], 2, ',', ' ') ?> FC</td>
                        </tr>
<?php endforeach; ?>
                        <tr>
                            <th>Total primes</th>
                            <td class="text-right"><?= number_format($bulletin['primes'], 2, ',', ' ') ?> FC</td>
                        </tr>
                        <tr><th>Heures supplémentaires (<?= number_format($bulletin['heures_supplementaires'], 1, ',', ' ') ?> h)</th><td class="text-right"><?= number_format($bulletin['montant_heures_sup'], 2, ',', ' ') ?> FC</td></tr>
                        <tr class="bg-light">
                            <th><strong>Total brut</strong></th>
                            <td class="text-right text-primary font-weight-bold"><?= number_format($bulletin['total_brut'], 2, ',', ' ') ?> FC</td>
                        </tr>
                    </table>

                    <h5 class="text-danger mb-3">Retenues</h5>
                    <table class="table table-bordered">
                        <tr><th>Total des retenues</th><td class="text-right text-danger">- <?= number_format($bulletin['total_retenues'], 2, ',', ' ') ?> FC</td></tr>
                    </table>

                    <div class="alert alert-success text-center" style="font-size:20px;font-weight:bold;">
                        Net à payer : <?= number_format($bulletin['total_net'], 2, ',', ' ') ?> FC
                    </div>

<?php if ($bulletin['statut'] === 'paye'): ?>
                    <p class="text-center text-success mb-0">
                        <i class="fas fa-check-circle"></i> Votre salaire de cette période a été payé.
                    </p>
<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
