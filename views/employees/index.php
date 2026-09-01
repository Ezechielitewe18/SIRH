<?php $pageTitle = 'Gestion des employés'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Employés</h1>
        <div>
            <a href="<?= APP_URL ?>/export/employees/excel" class="btn btn-info">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="<?= APP_URL ?>/export/employees/pdf" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="<?= APP_URL ?>/employees/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un employé
            </a>
        </div>
    </div>
</section>

<section class="content">
    <div class="card">
        <div class="card-header">
            <form method="GET" action="<?= APP_URL ?>/employees" class="form-inline">
                <div class="input-group" style="width: 350px;">
                    <input type="text" class="form-control" placeholder="Rechercher..." name="search" value="<?= htmlspecialchars($search ?? '') ?>">
                    <div class="input-group-append">
                        <button class="btn btn-secondary" type="submit"><i class="fas fa-search"></i></button>
                        <?php if (!empty($search)): ?>
                        <a href="<?= APP_URL ?>/employees" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom complet</th>
                        <th>Sexe</th>
                        <th>Service</th>
                        <th>Poste</th>
                        <th>Téléphone</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees ?? [] as $emp): ?>
                    <tr>
                        <td><span class="badge badge-info"><?= htmlspecialchars($emp['matricule']) ?></span></td>
                        <td><strong><?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?></strong></td>
                        <td><?= $emp['sexe'] === 'M' ? 'Masculin' : 'Féminin' ?></td>
                        <td><?= htmlspecialchars($emp['nom_service'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($emp['poste'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($emp['telephone'] ?? 'N/A') ?></td>
                        <td>
                            <span class="badge badge-<?= $emp['statut'] === 'actif' ? 'success' : ($emp['statut'] === 'suspendu' ? 'warning' : 'secondary') ?>">
                                <?= ucfirst($emp['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= APP_URL ?>/employees/show/<?= $emp['id_employe'] ?>" class="btn btn-sm btn-info" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= APP_URL ?>/employees/edit/<?= $emp['id_employe'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="<?= APP_URL ?>/employees/delete/<?= $emp['id_employe'] ?>" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?')">
                                <button class="btn btn-sm btn-danger" title="Supprimer"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($employees)): ?>
                    <tr><td colspan="8" class="text-center text-muted">Aucun employé trouvé</td></tr>
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
