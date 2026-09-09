<?php $pageTitle = 'Détails de l\'employé'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1>Fiche employé</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-user-circle fa-5x text-muted"></i>
                    </div>
                    <h3 class="profile-username text-center"><?= htmlspecialchars($employee['prenom'] . ' ' . $employee['nom']) ?></h3>
                    <p class="text-muted text-center"><?= htmlspecialchars($employee['matricule']) ?></p>
                    <p class="text-muted text-center">
                        <span class="badge badge-<?= $employee['statut'] === 'actif' ? 'success' : 'warning' ?>">
                            <?= ucfirst($employee['statut']) ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informations personnelles et professionnelles</h3>
                    <div class="float-right">
                        <a href="<?= APP_URL ?>/employees/edit/<?= $employee['id_employe'] ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th style="width:30%">Matricule</th><td><?= htmlspecialchars($employee['matricule']) ?></td></tr>
                        <tr><th>Nom</th><td><?= htmlspecialchars($employee['nom']) ?></td></tr>
                        <tr><th>Post-nom</th><td><?= htmlspecialchars($employee['postnom'] ?? 'N/A') ?></td></tr>
                        <tr><th>Prénom</th><td><?= htmlspecialchars($employee['prenom']) ?></td></tr>
                        <tr><th>Sexe</th><td><?= $employee['sexe'] === 'M' ? 'Masculin' : 'Féminin' ?></td></tr>
                        <tr><th>Date de naissance</th><td><?= $employee['date_naissance'] ? date('d/m/Y', strtotime($employee['date_naissance'])) : 'N/A' ?></td></tr>
                        <tr><th>Lieu de naissance</th><td><?= htmlspecialchars($employee['lieu_naissance'] ?? 'N/A') ?></td></tr>
                        <tr><th>Adresse</th><td><?= htmlspecialchars($employee['adresse'] ?? 'N/A') ?></td></tr>
                        <tr><th>Téléphone</th><td><?= htmlspecialchars($employee['telephone'] ?? 'N/A') ?></td></tr>
                        <tr><th>Email professionnel</th><td>
                            <?php if (!empty($employee['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($employee['email']) ?>">
                                <i class="fas fa-envelope"></i> <?= htmlspecialchars($employee['email']) ?>
                            </a>
                            <?php else: ?>N/A<?php endif; ?>
                        </td></tr>
                        <tr><th>Compte utilisateur</th><td>
                            <?php if (!empty($employee['id_utilisateur'])): ?>
                            <span class="badge badge-success"><i class="fas fa-check"></i> Actif</span>
                            <?php else: ?>
                            <span class="badge badge-warning"><i class="fas fa-times"></i> Non créé</span>
                            <?php endif; ?>
                        </td></tr>
                        <tr><th>Service</th><td><?= htmlspecialchars($employee['nom_service'] ?? 'N/A') ?></td></tr>
                        <tr><th>Poste</th><td><?= htmlspecialchars($employee['poste'] ?? 'N/A') ?></td></tr>
                        <tr><th>Date d'embauche</th><td><?= date('d/m/Y', strtotime($employee['date_embauche'])) ?></td></tr>
                        <tr><th>Salaire</th><td><?= $employee['salaire'] ? number_format($employee['salaire'], 2, ',', ' ') . ' FC' : 'N/A' ?></td></tr>
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
