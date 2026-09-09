<?php $pageTitle = 'Modifier l\'utilisateur'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1>Modifier l'utilisateur</h1>
</section>

<section class="content">
    <div class="card">
        <form method="POST" action="<?= APP_URL ?>/utilisateurs/edit/<?= $user['id_utilisateur'] ?>">
            <?= csrf_field() ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nom complet</label>
                            <input type="text" class="form-control" name="nom_complet" value="<?= htmlspecialchars($user['nom_complet']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Rôle</label>
                            <select class="form-control" name="role" <?= $user['id_utilisateur'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                <option value="employe" <?= $user['role'] === 'employe' ? 'selected' : '' ?>>Employé</option>
                                <option value="rh" <?= $user['role'] === 'rh' ? 'selected' : '' ?>>Responsable RH</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Statut</label>
                            <select class="form-control" name="statut" <?= $user['id_utilisateur'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                <option value="actif" <?= $user['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                                <option value="inactif" <?= $user['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nouveau mot de passe (optionnel)</label>
                            <input type="password" class="form-control" name="password" minlength="6" placeholder="Laisser vide pour ne pas changer">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                <a href="<?= APP_URL ?>/utilisateurs" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
