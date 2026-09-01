<?php $pageTitle = 'Nouvelle formation'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1>Nouvelle formation</h1>
</section>

<section class="content">
    <div class="card">
        <form method="POST" action="<?= APP_URL ?>/formations/create">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Titre de la formation *</label>
                            <input type="text" class="form-control" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Type</label>
                            <input type="text" class="form-control" name="type_formation" placeholder="Ex: Informatique, Management..." value="<?= htmlspecialchars($_POST['type_formation'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Durée</label>
                            <input type="text" class="form-control" name="duree" placeholder="Ex: 2 jours, 1 semaine" value="<?= htmlspecialchars($_POST['duree'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date de début</label>
                            <input type="date" class="form-control" name="date_debut" value="<?= $_POST['date_debut'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date de fin</label>
                            <input type="date" class="form-control" name="date_fin" value="<?= $_POST['date_fin'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Statut</label>
                            <select class="form-control" name="statut">
                                <option value="planifiee">Planifiée</option>
                                <option value="en_cours">En cours</option>
                                <option value="terminee">Terminée</option>
                                <option value="annulee">Annulée</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                <a href="<?= APP_URL ?>/formations" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
