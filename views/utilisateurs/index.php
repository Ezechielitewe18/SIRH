<?php $pageTitle = 'Gestion des utilisateurs'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-users text-primary"></i> Utilisateurs</h1>
        <a href="<?= APP_URL ?>/utilisateurs/create" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Nouvel utilisateur
        </a>
    </div>
</section>

<section class="content">
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Employé lié</th>
                        <th>Dernière connexion</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($u['nom_complet']) ?></strong></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge badge-<?= $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'rh' ? 'primary' : 'info') ?>">
                                <?= strtoupper($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['emp_nom']): ?>
                            <?= htmlspecialchars($u['emp_prenom'] . ' ' . $u['emp_nom']) ?> <small class="text-muted">(<?= htmlspecialchars($u['emp_matricule'] ?? '') ?>)</small>
                            <?php else: ?> <span class="text-muted">—</span> <?php endif; ?>
                        </td>
                        <td><?= $u['derniere_connexion'] ? date('d/m/Y H:i', strtotime($u['derniere_connexion'])) : 'Jamais' ?></td>
                        <td>
                            <span class="badge badge-<?= $u['statut'] === 'actif' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($u['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= APP_URL ?>/utilisateurs/edit/<?= $u['id_utilisateur'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="<?= APP_URL ?>/utilisateurs/toggle/<?= $u['id_utilisateur'] ?>" style="display:inline;">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-<?= $u['statut'] === 'actif' ? 'secondary' : 'success' ?>" title="<?= $u['statut'] === 'actif' ? 'Désactiver' : 'Activer' ?>">
                                    <i class="fas fa-<?= $u['statut'] === 'actif' ? 'ban' : 'check' ?>"></i>
                                </button>
                            </form>
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#resetModal<?= $u['id_utilisateur'] ?>" title="Réinitialiser mot de passe">
                                <i class="fas fa-key"></i>
                            </button>

                            <div class="modal fade" id="resetModal<?= $u['id_utilisateur'] ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" action="<?= APP_URL ?>/utilisateurs/resetPassword/<?= $u['id_utilisateur'] ?>">
                                            <?= csrf_field() ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title">Réinitialiser le mot de passe</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Réinitialiser le mot de passe de <strong><?= htmlspecialchars($u['nom_complet']) ?></strong></p>
                                                <div class="form-group">
                                                    <label>Nouveau mot de passe</label>
                                                    <input type="password" class="form-control" name="new_password" minlength="6" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Réinitialiser</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="7" class="text-center text-muted">Aucun utilisateur</td></tr>
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
