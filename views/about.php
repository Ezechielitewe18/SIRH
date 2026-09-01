<?php $pageTitle = 'À propos / Droits d\'auteur'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1><i class="fas fa-info-circle text-primary"></i> À propos de <?= APP_NAME ?></h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-users-cog text-primary"></i> <?= APP_NAME ?></h3>
                </div>
                <div class="card-body">
                    <p class="lead">
                        <strong><?= APP_NAME ?></strong> est un Système d'Information des Ressources Humaines (SIRH)
                        conçu pour faciliter la gestion du personnel au sein des organisations.
                    </p>
                    <p>
                        Cette plateforme permet de centraliser les informations des employés, de gérer les services,
                        de suivre les présences et les absences, de traiter les demandes de congé et de produire
                        des statistiques fiables pour accompagner la prise de décision.
                    </p>
                    <hr>
                    <h5><i class="fas fa-cube text-primary"></i> Modules disponibles</h5>
                    <ul>
                        <li>Gestion des employés (dossiers numériques)</li>
                        <li>Gestion des services</li>
                        <li>Gestion des présences et pointage</li>
                        <li>Gestion des demandes de congé</li>
                        <li>Tableau de bord et statistiques (graphiques)</li>
                        <li>Gestion des utilisateurs et des rôles</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-copyright"></i> Droits d'auteur</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h4 class="text-center"><?= AUTHOR_NAME ?></h4>
                    <p class="text-center text-muted">Concepteur & Développeur</p>
                    <hr>
                    <p><?= COPYRIGHT ?></p>
                    <p class="text-muted">
                        Ce logiciel est protégé par les lois relatives à la propriété intellectuelle.
                        Toute reproduction, distribution ou utilisation non autorisée
                        est strictement interdite.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/app.php';
?>
