<?php
$pageTitle = 'Gestion des congés';
$role = $_SESSION['user_role'] ?? '';
$isManager = in_array($role, ['admin', 'rh'], true);
$statutActif = $statut ?? null;
$total = count($conges ?? []);
$filtres = [
    ['cle' => null, 'label' => 'Tous', 'mod' => 'all'],
    ['cle' => 'en_attente', 'label' => 'En attente', 'mod' => 'warning'],
    ['cle' => 'approuve', 'label' => 'Approuvés', 'mod' => 'success'],
    ['cle' => 'refuse', 'label' => 'Refusés', 'mod' => 'danger'],
];
ob_start();
?>

<section class="content-header">
    <div class="sirh-page-header">
        <div class="sirh-page-heading">
            <h1 class="sirh-page-title"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Congés</h1>
            <p class="sirh-page-subtitle">
                <?= $total ?> demande<?= $total > 1 ? 's' : '' ?>
                <?php if ($statutActif): ?>
                    · filtre « <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $statutActif))) ?> »
                <?php endif; ?>
            </p>
        </div>
        <div class="sirh-page-actions">
            <?php if ($isManager): ?>
            <a href="<?= APP_URL ?>/export/conges" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-file-excel" aria-hidden="true"></i> Export Excel
            </a>
            <?php endif; ?>
            <a href="<?= APP_URL ?>/conges/create" class="btn btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i> Nouvelle demande
            </a>
        </div>
    </div>
</section>

<section class="content">
    <?php if ($isManager): ?>
    <div class="sirh-toolbar">
        <div class="sirh-filter-group" role="group" aria-label="Filtrer les demandes de congé par statut">
            <?php foreach ($filtres as $f): ?>
            <?php $actif = $statutActif === $f['cle']; ?>
            <a href="<?= APP_URL ?>/conges<?= $f['cle'] ? '?statut=' . urlencode($f['cle']) : '' ?>"
               class="sirh-filter sirh-filter--<?= $f['mod'] ?>"
               aria-pressed="<?= $actif ? 'true' : 'false' ?>"
               <?= $actif ? 'aria-current="true"' : '' ?>>
                <?= $f['label'] ?>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="sirh-toolbar-group">
            <span class="sirh-muted" style="font-size:.84rem;">
                <i class="fas fa-list-check" aria-hidden="true"></i>
                <?= $total ?> résultat<?= $total > 1 ? 's' : '' ?>
            </span>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <?php if (!empty($conges)): ?>
        <div class="table-responsive">
            <table class="table table-hover sirh-table-wide">
                <thead>
                    <tr>
                        <?php if ($isManager): ?><th scope="col">Employé</th><?php endif; ?>
                        <th scope="col">Type</th>
                        <th scope="col">Période</th>
                        <th scope="col">Jours</th>
                        <th scope="col">Motif</th>
                        <th scope="col">Statut</th>
                        <?php if ($isManager): ?><th scope="col" class="text-right">Actions</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($conges as $c): ?>
                    <?php
                    $badge = match ($c['statut']) {
                        'approuve' => 'success',
                        'refuse' => 'danger',
                        default => 'warning',
                    };
                    ?>
                    <tr>
                        <?php if ($isManager): ?>
                        <td>
                            <span class="sirh-cell-strong"><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></span>
                            <span class="sirh-cell-sub"><?= htmlspecialchars($c['nom_service'] ?? 'Service non défini') ?></span>
                        </td>
                        <?php endif; ?>
                        <td><span class="badge badge-info"><?= htmlspecialchars(ucfirst($c['type_conge'])) ?></span></td>
                        <td>
                            <span class="sirh-nowrap"><?= date('d/m/Y', strtotime($c['date_debut'])) ?></span>
                            <span class="sirh-muted">→</span>
                            <span class="sirh-nowrap"><?= date('d/m/Y', strtotime($c['date_fin'])) ?></span>
                        </td>
                        <td><span class="badge badge-primary"><?= (int) $c['nombre_jours'] ?> j</span></td>
                        <td style="max-width:260px;">
                            <?php if (!empty($c['motif'])): ?>
                            <span title="<?= htmlspecialchars($c['motif']) ?>"><?= htmlspecialchars($c['motif']) ?></span>
                            <?php else: ?>
                            <span class="sirh-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="sirh-status sirh-status--<?= $badge ?>">
                                <?= ucfirst(str_replace('_', ' ', $c['statut'])) ?>
                            </span>
                            <?php if ($c['statut'] === 'refuse' && !empty($c['motif_refus'])): ?>
                            <span class="sirh-cell-sub" title="<?= htmlspecialchars($c['motif_refus']) ?>"><?= htmlspecialchars($c['motif_refus']) ?></span>
                            <?php endif; ?>
                        </td>
                        <?php if ($isManager): ?>
                        <td class="text-right">
                            <?php if ($c['statut'] === 'en_attente'): ?>
                            <div class="d-inline-flex align-items-center" style="gap:5px;">
                                <form method="POST" action="<?= APP_URL ?>/conges/approve/<?= $c['id_conge'] ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-success" title="Approuver la demande" aria-label="Approuver la demande de <?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?>">
                                        <i class="fas fa-check" aria-hidden="true"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" title="Refuser la demande"
                                        aria-label="Refuser la demande de <?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?>"
                                        data-toggle="modal" data-target="#modalRefusConge"
                                        data-id="<?= $c['id_conge'] ?>"
                                        data-nom="<?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?>">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                </button>
                            </div>
                            <?php elseif (!empty($c['approuve_par'])): ?>
                            <small class="sirh-muted">Par <?= htmlspecialchars($c['approbateur_nom'] ?? '') ?></small>
                            <?php else: ?>
                            <span class="sirh-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="sirh-empty">
            <span class="sirh-empty-icon" aria-hidden="true"><i class="fas fa-calendar-xmark"></i></span>
            <?php if ($statutActif): ?>
            <p class="sirh-empty-title">Aucune demande dans ce filtre</p>
            <p class="sirh-empty-text">Aucune demande de congé ne correspond au statut « <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $statutActif))) ?> ».</p>
            <a href="<?= APP_URL ?>/conges" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list" aria-hidden="true"></i> Voir toutes les demandes</a>
            <?php else: ?>
            <p class="sirh-empty-title">Aucune demande de congé</p>
            <p class="sirh-empty-text">Les demandes déposées par les employés apparaîtront ici pour validation.</p>
            <a href="<?= APP_URL ?>/conges/create" class="btn btn-primary btn-sm"><i class="fas fa-plus" aria-hidden="true"></i> Nouvelle demande</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($isManager): ?>
<div class="modal fade" id="modalRefusConge" tabindex="-1" role="dialog" aria-labelledby="modalRefusTitre" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="" id="formRefusConge">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRefusTitre">Refuser la demande de <span id="refusCongeNom"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label for="refusCongeMotif">Motif du refus (obligatoire)</label>
                        <textarea class="form-control" name="motif_refus" id="refusCongeMotif" rows="3" required placeholder="Expliquez la raison du refus…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-times" aria-hidden="true"></i> Refuser la demande</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
ob_start();
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('modalRefusConge');
    if (!modal) { return; }
    var base = <?= json_encode(APP_URL . '/conges/reject/') ?>;
    modal.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        if (!btn) { return; }
        document.getElementById('formRefusConge').setAttribute('action', base + btn.getAttribute('data-id'));
        document.getElementById('refusCongeNom').textContent = btn.getAttribute('data-nom') || '';
    });
    modal.addEventListener('hidden.bs.modal', function () {
        document.getElementById('refusCongeMotif').value = '';
    });
});
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
