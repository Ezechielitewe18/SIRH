<?php $pageTitle = 'Nouveau message'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1><i class="fas fa-plus-circle text-primary"></i> Nouveau message</h1>
    <a href="<?= APP_URL ?>/messages" class="btn btn-secondary btn-sm" style="float:right;margin-top:-28px;">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</section>

<section class="content">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <form method="post" action="<?= APP_URL ?>/messages/nouveau">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Destinataire <span class="text-danger">*</span></label>
                            <select name="id_destinataire" class="form-control" required>
                                <option value="">— Choisir un destinataire —</option>
                                <?php foreach ($personnel as $p): ?>
                                <option value="<?= $p['id_utilisateur'] ?>">
                                    <?= htmlspecialchars($p['nom_complet']) ?> (<?= ucfirst($p['role']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Message <span class="text-danger">*</span></label>
                            <textarea name="contenu" class="form-control" rows="5" placeholder="Votre message..." required></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>