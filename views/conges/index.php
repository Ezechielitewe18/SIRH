<?php $pageTitle = 'Gestion des congés'; ?>
<?php ob_start();

$role = $_SESSION['user_role'] ?? '';
?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Congés</h1>
        <div>
            <a href="<?= APP_URL ?>/export/conges" class="btn btn-info">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="<?= APP_URL ?>/conges/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle demande
            </a>
        </div>
    </div>
</section>

<section class="content">
    <?php if (in_array($role, ['admin', 'rh'])): ?>
    <!-- Filtres -->
    <div class="card mb-3">
        <div class="card-body">
            <a href="<?= APP_URL ?>/conges" class="btn btn-outline-secondary btn-sm">Tous</a>
            <a href="<?= APP_URL ?>/conges?statut=en_attente" class="btn btn-outline-warning btn-sm">En attente</a>
            <a href="<?= APP_URL ?>/conges?statut=approuve" class="btn btn-outline-success btn-sm">Approuvés</a>
            <a href="<?= APP_URL ?>/conges?statut=refuse" class="btn btn-outline-danger btn-sm">Refusés</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <?php if (in_array($role, ['admin', 'rh'])): ?>
                        <th>Employé</th>
                        <?php endif; ?>
                        <th>Type</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Jours</th>
                        <th>Motif</th>
                        <th>Statut</th>
                        <?php if (in_array($role, ['admin', 'rh'])): ?>
                        <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($conges ?? [] as $c): ?>
                    <tr>
                        <?php if (in_array($role, ['admin', 'rh'])): ?>
                        <td>
                            <strong><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($c['nom_service'] ?? '') ?></small>
                        </td>
                        <?php endif; ?>
                        <td><span class="badge badge-info"><?= ucfirst($c['type_conge']) ?></span></td>
                        <td><?= date('d/m/Y', strtotime($c['date_debut'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($c['date_fin'])) ?></td>
                        <td><span class="badge badge-primary"><?= $c['nombre_jours'] ?></span></td>
                        <td><?= htmlspecialchars($c['motif']) ?></td>
                        <td>
                            <?php
                            $badgeClass = match($c['statut']) {
                                'approuve' => 'success',
                                'refuse' => 'danger',
                                default => 'warning'
                            };
                            ?>
                            <span class="badge badge-<?= $badgeClass ?>"><?= ucfirst(str_replace('_', ' ', $c['statut'])) ?></span>
                        </td>
                        <?php if (in_array($role, ['admin', 'rh'])): ?>
                        <td>
                            <?php if ($c['statut'] === 'en_attente'): ?>
                            <form method="POST" action="<?= APP_URL ?>/conges/approve/<?= $c['id_conge'] ?>" style="display:inline;">
                                <button class="btn btn-sm btn-success" title="Approuver"><i class="fas fa-check"></i></button>
                            </form>
                            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal<?= $c['id_conge'] ?>" title="Refuser">
                                <i class="fas fa-times"></i>
                            </button>

                            <!-- Modal de refus -->
                            <div class="modal fade" id="rejectModal<?= $c['id_conge'] ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" action="<?= APP_URL ?>/conges/reject/<?= $c['id_conge'] ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Refuser la demande</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Motif du refus</label>
                                                    <textarea class="form-control" name="motif_refus" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-danger">Refuser</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <?php if ($c['approuve_par']): ?>
                            <small class="text-muted">Par <?= htmlspecialchars($c['approbateur_nom'] ?? '') ?></small>
                            <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($conges)): ?>
                    <tr><td colspan="8" class="text-center text-muted">Aucune demande de congé</td></tr>
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
