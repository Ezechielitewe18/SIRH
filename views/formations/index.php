<?php $pageTitle = 'Gestion des formations'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-graduation-cap text-primary"></i> Formations</h1>
        <a href="<?= APP_URL ?>/formations/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle formation
        </a>
    </div>
</section>

<section class="content">
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Durée</th>
                        <th>Dates</th>
                        <th>Inscrits</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($formations as $f): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($f['titre']) ?></strong></td>
                        <td><?= htmlspecialchars($f['type_formation'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($f['duree'] ?? 'N/A') ?></td>
                        <td>
                            <?php if ($f['date_debut']): ?>
                            <?= date('d/m/Y', strtotime($f['date_debut'])) ?> 
                            <?php if ($f['date_fin']): ?> au <?= date('d/m/Y', strtotime($f['date_fin'])) ?><?php endif; ?>
                            <?php else: ?> N/A <?php endif; ?>
                        </td>
                        <td><span class="badge badge-primary"><?= $f['nb_inscrits'] ?></span></td>
                        <td>
                            <span class="badge badge-<?= $f['statut'] === 'terminee' ? 'success' : ($f['statut'] === 'en_cours' ? 'info' : ($f['statut'] === 'annulee' ? 'danger' : 'warning')) ?>">
                                <?= ucfirst($f['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= APP_URL ?>/formations/show/<?= $f['id_formation'] ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <form method="POST" action="<?= APP_URL ?>/formations/delete/<?= $f['id_formation'] ?>" style="display:inline;" onsubmit="return confirm('Supprimer cette formation ?')">
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($formations)): ?>
                    <tr><td colspan="7" class="text-center text-muted">Aucune formation enregistrée</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
