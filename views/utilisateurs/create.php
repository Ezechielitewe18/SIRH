<?php $pageTitle = 'Nouvel utilisateur'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1>Nouvel utilisateur</h1>
</section>

<section class="content">
    <div class="card">
        <form method="POST" action="<?= APP_URL ?>/utilisateurs/create">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nom complet *</label>
                            <input type="text" class="form-control" name="nom_complet" value="<?= htmlspecialchars($_POST['nom_complet'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mot de passe * (min 6 caractères)</label>
                            <input type="password" class="form-control" name="password" minlength="6" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Rôle</label>
                            <select class="form-control" name="role">
                                <option value="employe">Employé</option>
                                <option value="rh">Responsable RH</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Associer à un employé (optionnel)</label>
                            <select class="form-control" name="id_employe">
                                <option value="">-- Aucun --</option>
                                <?php foreach ($employesSansCompte as $e): ?>
                                <option value="<?= $e['id_employe'] ?>"><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?> (<?= $e['matricule'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Créer</button>
                <a href="<?= APP_URL ?>/utilisateurs" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
