<?php $pageTitle = 'Ajouter un service'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1>Ajouter un service</h1>
</section>

<section class="content">
    <div class="card">
        <form method="POST" action="<?= APP_URL ?>/services/create">
            <div class="card-body">
                <div class="form-group">
                    <label>Nom du service *</label>
                    <input type="text" class="form-control" name="nom_service" value="<?= htmlspecialchars($_POST['nom_service'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                <a href="<?= APP_URL ?>/services" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
