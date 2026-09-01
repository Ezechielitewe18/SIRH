<?php $pageTitle = 'Notifications'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1><i class="fas fa-bell text-warning"></i> Notifications</h1>
</section>

<section class="content">
    <div class="card">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $n): ?>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><i class="fas fa-<?= $n['type_notif'] === 'conge' ? 'calendar' : ($n['type_notif'] === 'paie' ? 'money-bill' : 'info-circle') ?> text-primary"></i> <?= htmlspecialchars($n['titre']) ?></h5>
                            <p class="mb-1"><?= htmlspecialchars($n['message']) ?></p>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></small>
                        </div>
                        <?php if ($n['lien']): ?>
                        <a href="<?= $n['lien'] ?>" class="btn btn-sm btn-outline-primary">Voir</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($notifications)): ?>
                <div class="list-group-item text-center text-muted">Aucune notification</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
