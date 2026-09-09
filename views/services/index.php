<?php $pageTitle = 'Gestion des services'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Services</h1>
        <a href="<?= APP_URL ?>/services/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un service
        </a>
    </div>
</section>

<section class="content">
    <div class="row">
        <?php foreach ($services ?? [] as $service): ?>
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-building text-primary"></i> <?= htmlspecialchars($service['nom_service']) ?></h3>
                </div>
                <div class="card-body">
                    <p><?= htmlspecialchars($service['description'] ?? 'Aucune description') ?></p>
                    <span class="badge badge-info"><?= $service['nombre_employes'] ?> employé(s)</span>
                </div>
                <div class="card-footer">
                    <a href="<?= APP_URL ?>/services/edit/<?= $service['id_service'] ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <form method="POST" action="<?= APP_URL ?>/services/delete/<?= $service['id_service'] ?>" style="display:inline;" onsubmit="return confirm('Supprimer ce service ?')">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($services)): ?>
        <div class="col-12">
            <div class="alert alert-info">Aucun service enregistré</div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
