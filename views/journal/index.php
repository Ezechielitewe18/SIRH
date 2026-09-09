<?php $pageTitle = 'Journal d\'activité'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-history text-secondary"></i> Journal d'activité</h1>
        <form method="POST" action="<?= APP_URL ?>/journal/clear" onsubmit="return confirm('Vider tout le journal ?')">
            <?= csrf_field() ?>
            <button class="btn btn-danger"><i class="fas fa-trash"></i> Vider le journal</button>
        </form>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <form method="GET" action="<?= APP_URL ?>/journal" class="form-inline mr-auto">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control" placeholder="Rechercher..." name="search" value="<?= htmlspecialchars($search ?? '') ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="submit"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                        <div class="ml-2">
                            <a href="<?= APP_URL ?>/journal" class="btn btn-outline-secondary btn-sm <?= empty($module) ? 'active' : '' ?>">Tous</a>
                            <?php foreach ($modulesCounter as $m): ?>
                            <a href="<?= APP_URL ?>/journal?module=<?= urlencode($m['module']) ?>" class="btn btn-outline-secondary btn-sm <?= $module === $m['module'] ? 'active' : '' ?>">
                                <?= ucfirst($m['module']) ?> (<?= $m['total'] ?>)
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-sm text-nowrap">
                        <thead>
                            <tr>
                                <th>Date / Heure</th>
                                <th>Utilisateur</th>
                                <th>Module</th>
                                <th>Action</th>
                                <th>Détails</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $l): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i:s', strtotime($l['created_at'])) ?></td>
                                <td><?= htmlspecialchars($l['nom_complet'] ?? 'Système') ?></td>
                                <td><span class="badge badge-secondary"><?= htmlspecialchars($l['module']) ?></span></td>
                                <td><?= htmlspecialchars($l['action']) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($l['details']) ?></td>
                                <td><small><?= htmlspecialchars($l['ip_adresse']) ?></small></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($logs)): ?>
                            <tr><td colspan="6" class="text-center text-muted">Aucune activité enregistrée</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
