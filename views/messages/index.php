<?php $pageTitle = 'Messages'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1><i class="fas fa-comments text-primary"></i> Messages</h1>
    <a href="<?= APP_URL ?>/messages/nouveau" class="btn btn-primary btn-sm" style="float:right;margin-top:-28px;">
        <i class="fas fa-plus"></i> Nouveau message
    </a>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-envelope"></i> Conversations</h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if (empty($conversations)): ?>
                        <div class="list-group-item text-center text-muted">Aucune conversation. Commencez un nouvel échange.</div>
                        <?php endif; ?>
                        <?php foreach ($conversations as $c): ?>
                        <a href="<?= APP_URL ?>/messages/conversation/<?= $c['id_conversation'] ?>"
                           class="list-group-item list-group-item-action <?= $c['non_lus'] > 0 ? 'font-weight-bold' : '' ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <div>
                                    <i class="fas fa-user-circle text-primary mr-1"></i>
                                    <?= htmlspecialchars($c['nom_interlocuteur']) ?>
                                    <?php if ($c['non_lus'] > 0): ?>
                                    <span class="badge badge-danger"><?= $c['non_lus'] ?> non lu<?= $c['non_lus'] > 1 ? 's' : '' ?></span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted">
                                    <?= $c['dernier_date'] ? date('d/m H:i', strtotime($c['dernier_date'])) : '' ?>
                                </small>
                            </div>
                            <div class="text-muted text-truncate" style="font-size: 13px;">
                                <i class="fas fa-reply fa-flip-horizontal text-secondary mr-1"></i>
                                <?= htmlspecialchars(mb_strimwidth($c['dernier_message'] ?? '', 0, 70, '…')) ?>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-bullhorn text-warning"></i> Annonces de la Direction</h3>
                    <?php if (in_array($_SESSION['user_role'], ['admin', 'rh'])): ?>
                    <a href="<?= APP_URL ?>/messages/annonces" class="btn btn-outline-warning btn-sm float-right">Gérer</a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if (empty($annonces)): ?>
                        <div class="list-group-item text-center text-muted">Aucune annonce</div>
                        <?php endif; ?>
                        <?php foreach (array_slice($annonces, 0, 5) as $a): ?>
                        <div class="list-group-item">
                            <h6 class="mb-1"><i class="fas fa-bullhorn text-warning mr-1"></i> <?= htmlspecialchars($a['titre']) ?></h6>
                            <p class="mb-1 text-muted" style="font-size:13px;"><?= htmlspecialchars(mb_strimwidth($a['contenu'], 0, 90, '…')) ?></p>
                            <small class="text-muted">Par <?= htmlspecialchars($a['nom_complet']) ?> · <?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>