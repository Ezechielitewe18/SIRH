<?php $pageTitle = 'Cartes RFID'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>Cartes RFID</h1>
            <p class="text-muted mb-0">Badges attribués aux employés. Une carte n'est jamais supprimée : elle change de statut.</p>
        </div>
        <a href="<?= APP_URL ?>/cartes/create" class="btn btn-primary mt-2 mt-sm-0">
            <i class="fas fa-plus"></i> Attribuer une carte
        </a>
    </div>
</section>

<section class="content">
    <?php if (empty($cartes)): ?>
        <div class="alert alert-info">
            <i class="fas fa-id-card"></i> Aucune carte enregistrée. Attribuez un badge à un employé pour commencer.
        </div>
    <?php else: ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-id-card text-primary"></i> <?= count($cartes) ?> carte(s)</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Numéro de carte</th>
                        <th>Technologie</th>
                        <th>Libellé</th>
                        <th>Statut</th>
                        <th>Attribuée le</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $badges = [
                    'active' => 'success',
                    'perdue' => 'danger',
                    'desactivee' => 'secondary',
                    'remplacee' => 'warning',
                ];
                foreach ($cartes as $carte):
                    $statut = $carte['statut'];
                ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($carte['prenom'] . ' ' . $carte['nom']) ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($carte['matricule']) ?></small>
                        </td>
                        <td><code><?= htmlspecialchars($carte['uid_carte']) ?></code></td>
                        <td><?= htmlspecialchars($carte['technologie'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($carte['libelle'] ?? '—') ?></td>
                        <td>
                            <span class="badge badge-<?= $badges[$statut] ?? 'secondary' ?>">
                                <?= htmlspecialchars($statut) ?>
                            </span>
                            <?php if (!empty($carte['date_desactivation'])): ?>
                                <br><small class="text-muted"><?= htmlspecialchars(substr($carte['date_desactivation'], 0, 16)) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($carte['date_attribution']) ?></td>
                        <td class="text-right" style="min-width: 260px;">
                            <?php if ($statut === 'active'): ?>
                                <form method="POST" action="<?= APP_URL ?>/cartes/remplacer" class="form-inline justify-content-end mb-1">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id_carte" value="<?= (int) $carte['id_carte'] ?>">
                                    <input type="text" name="uid_nouvelle" class="form-control form-control-sm mr-1"
                                           placeholder="Nouveau numéro" required style="max-width: 140px;">
                                    <button class="btn btn-sm btn-outline-warning" title="Remplacer cette carte">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                </form>
                                <form method="POST" action="<?= APP_URL ?>/cartes/desactiver" class="form-inline justify-content-end"
                                      onsubmit="return confirm('Confirmer la désactivation de cette carte ?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id_carte" value="<?= (int) $carte['id_carte'] ?>">
                                    <select name="statut" class="form-control form-control-sm mr-1">
                                        <option value="desactivee">Désactivée</option>
                                        <option value="perdue">Perdue</option>
                                    </select>
                                    <input type="text" name="notes" class="form-control form-control-sm mr-1"
                                           placeholder="Motif (facultatif)" style="max-width: 130px;">
                                    <button class="btn btn-sm btn-outline-danger" title="Désactiver cette carte">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                            <?php elseif ($statut === 'perdue'): ?>
                                <form method="POST" action="<?= APP_URL ?>/cartes/remplacer" class="form-inline justify-content-end">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id_carte" value="<?= (int) $carte['id_carte'] ?>">
                                    <input type="text" name="uid_nouvelle" class="form-control form-control-sm mr-1"
                                           placeholder="Nouvelle carte" required style="max-width: 140px;">
                                    <button class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-exchange-alt"></i> Remplacer
                                    </button>
                                </form>
                            <?php elseif ($statut !== 'active'): ?>
                                <span class="text-muted small">Aucune action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
