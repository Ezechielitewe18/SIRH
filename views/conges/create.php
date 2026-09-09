<?php $pageTitle = 'Nouvelle demande de congé'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1>Nouvelle demande de congé</h1>
</section>

<section class="content">
    <div class="card">
        <form method="POST" action="<?= APP_URL ?>/conges/create">
            <?= csrf_field() ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Type de congé *</label>
                            <select class="form-control" name="type_conge" required>
                                <option value="annuel">Congé annuel</option>
                                <option value="maladie">Congé maladie</option>
                                <option value="maternite">Congé maternité</option>
                                <option value="paternite">Congé paternité</option>
                                <option value="exceptionnel">Congé exceptionnel</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date de début *</label>
                            <input type="date" class="form-control" name="date_debut" value="<?= $_POST['date_debut'] ?? '' ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date de fin *</label>
                            <input type="date" class="form-control" name="date_fin" value="<?= $_POST['date_fin'] ?? '' ?>" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Motif *</label>
                            <textarea class="form-control" name="motif" rows="3" required placeholder="Décrivez le motif de votre demande..."><?= htmlspecialchars($_POST['motif'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Soumettre la demande</button>
                <a href="<?= APP_URL ?>/conges" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
