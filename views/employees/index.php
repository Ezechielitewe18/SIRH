<?php
$pageTitle = 'Gestion des employés';
$total = count($employees ?? []);
$hasSearch = !empty($search);
ob_start();
?>

<section class="content-header">
    <div class="sirh-page-header">
        <div class="sirh-page-heading">
            <h1 class="sirh-page-title"><i class="fas fa-user-tie" aria-hidden="true"></i> Employés</h1>
            <p class="sirh-page-subtitle">
                <?php if ($hasSearch): ?>
                <?= $total ?> résultat<?= $total > 1 ? 's' : '' ?> pour « <?= htmlspecialchars($search) ?> ».
                <?php else: ?>
                <?= $total ?> employé<?= $total > 1 ? 's' : '' ?> enregistré<?= $total > 1 ? 's' : '' ?>.
                <?php endif; ?>
            </p>
        </div>
        <div class="sirh-page-actions">
            <a href="<?= APP_URL ?>/export/employees/excel" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-file-excel" aria-hidden="true"></i> Excel
            </a>
            <a href="<?= APP_URL ?>/export/employees/pdf" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-file-pdf" aria-hidden="true"></i> PDF
            </a>
            <a href="<?= APP_URL ?>/employees/create" class="btn btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i> Ajouter un employé
            </a>
        </div>
    </div>
</section>

<section class="content">
    <div class="sirh-toolbar">
        <form method="GET" action="<?= APP_URL ?>/employees" class="sirh-search-form" role="search">
            <div class="input-group sirh-search-input">
                <div class="input-group-prepend">
                    <span class="input-group-text" aria-hidden="true"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" class="form-control" placeholder="Rechercher un employé, un matricule, un service…"
                       name="search" value="<?= htmlspecialchars($search ?? '') ?>" aria-label="Rechercher un employé">
            </div>
            <button class="btn btn-secondary" type="submit">
                <i class="fas fa-search sirh-mobile-only" aria-hidden="true"></i>
                <span class="sirh-desktop-only">Rechercher</span>
            </button>
            <?php if ($hasSearch): ?>
            <a href="<?= APP_URL ?>/employees" class="sirh-search-clear" aria-label="Effacer la recherche" title="Effacer la recherche">
                <i class="fas fa-times" aria-hidden="true"></i>
            </a>
            <?php endif; ?>
        </form>
        <div class="sirh-toolbar-group">
            <span class="sirh-muted" style="font-size:.84rem;">
                <i class="fas fa-table" aria-hidden="true"></i>
                <?= $total ?> ligne<?= $total > 1 ? 's' : '' ?>
            </span>
        </div>
    </div>

    <div class="card">
        <?php if (!empty($employees)): ?>
        <div class="table-responsive">
            <table class="table table-hover sirh-table-wide">
                <thead>
                    <tr>
                        <th scope="col">Matricule</th>
                        <th scope="col">Nom complet</th>
                        <th scope="col">Email professionnel</th>
                        <th scope="col">Service</th>
                        <th scope="col">Poste</th>
                        <th scope="col">Téléphone</th>
                        <th scope="col">Statut</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><span class="sirh-matricule"><?= htmlspecialchars($emp['matricule']) ?></span></td>
                        <td>
                            <a href="<?= APP_URL ?>/employees/show/<?= $emp['id_employe'] ?>" class="sirh-cell-strong">
                                <?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?>
                            </a>
                            <?php if (!empty($emp['postnom'])): ?>
                            <span class="sirh-cell-sub"><?= htmlspecialchars($emp['postnom']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($emp['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($emp['email']) ?>" class="sirh-email" title="<?= htmlspecialchars($emp['email']) ?>">
                                <?= htmlspecialchars($emp['email']) ?>
                            </a>
                            <?php else: ?>
                            <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($emp['nom_service'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($emp['poste'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($emp['telephone'] ?? 'N/A') ?></td>
                        <td>
                            <span class="sirh-status sirh-status--<?= $emp['statut'] === 'actif' ? 'success' : ($emp['statut'] === 'suspendu' ? 'warning' : 'neutral') ?>">
                                <?= ucfirst($emp['statut']) ?>
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="d-inline-flex align-items-center" style="gap:5px;">
                                <a href="<?= APP_URL ?>/employees/show/<?= $emp['id_employe'] ?>" class="btn btn-sm btn-info btn-icon" aria-label="Voir la fiche de <?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?>" title="Voir">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </a>
                                <a href="<?= APP_URL ?>/employees/edit/<?= $emp['id_employe'] ?>" class="btn btn-sm btn-warning btn-icon" aria-label="Modifier <?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?>" title="Modifier">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </a>
                                <form method="POST" action="<?= APP_URL ?>/employees/delete/<?= $emp['id_employe'] ?>" style="display:inline;" onsubmit="return confirm('Supprimer définitivement cet employé ?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-danger btn-icon" aria-label="Supprimer <?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?>" title="Supprimer">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="sirh-empty">
            <span class="sirh-empty-icon" aria-hidden="true"><i class="fas fa-user-slash"></i></span>
            <?php if ($hasSearch): ?>
            <p class="sirh-empty-title">Aucun employé trouvé</p>
            <p class="sirh-empty-text">Aucun employé ne correspond à « <?= htmlspecialchars($search) ?> ». Vérifiez l'orthographe ou élargissez la recherche.</p>
            <a href="<?= APP_URL ?>/employees" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times" aria-hidden="true"></i> Effacer la recherche</a>
            <?php else: ?>
            <p class="sirh-empty-title">Aucun employé enregistré</p>
            <p class="sirh-empty-text">Commencez par créer le premier dossier employé pour alimenter les présences, les congés et la paie.</p>
            <a href="<?= APP_URL ?>/employees/create" class="btn btn-primary btn-sm"><i class="fas fa-plus" aria-hidden="true"></i> Ajouter un employé</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
