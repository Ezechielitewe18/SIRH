<?php $pageTitle = 'Annonces'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1><i class="fas fa-bullhorn text-warning"></i> Annonces de la Direction</h1>
    <a href="<?= APP_URL ?>/messages" class="btn btn-secondary btn-sm" style="float:right;margin-top:-28px;">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</section>

<section class="content">
    <?php if (in_array($_SESSION['user_role'], ['admin', 'rh'])): ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus"></i> Publier une annonce</h3>
        </div>
        <form method="post" action="<?= APP_URL ?>/messages/annonces">
            <?= csrf_field() ?>
            <div class="card-body">
                <div class="form-group">
                    <label>Titre</label>
                    <input type="text" name="titre" class="form-control" placeholder="Ex : Réunion générale vendredi" required>
                </div>
                <div class="form-group">
                    <label>Contenu</label>
                    <textarea name="contenu" class="form-control" rows="4" placeholder="Détails de l'annonce..." required></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning"><i class="fas fa-bullhorn"></i> Publier</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> Historique des annonces</h3>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <?php if (empty($annonces)): ?>
                <div class="list-group-item text-center text-muted">Aucune annonce publiée</div>
                <?php endif; ?>
                <?php foreach ($annonces as $a): ?>
                <div class="list-group-item">
                    <h5 class="mb-1"><i class="fas fa-bullhorn text-warning mr-1"></i> <?= htmlspecialchars($a['titre']) ?></h5>
                    <p class="mb-1"><?= nl2br(htmlspecialchars($a['contenu'])) ?></p>
                    <small class="text-muted">Par <?= htmlspecialchars($a['nom_complet']) ?> · <?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>