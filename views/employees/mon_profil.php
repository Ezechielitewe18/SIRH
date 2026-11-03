<?php
$pageTitle = 'Mon profil';
$nomComplet = trim($employee['prenom'] . ' ' . $employee['nom']);
$initiale = mb_strtoupper(mb_substr($employee['prenom'] ?: 'U', 0, 1));
$sexeLabel = $employee['sexe'] === 'M' ? 'Masculin' : 'Féminin';
$statutLabel = ['actif' => 'Actif', 'inactif' => 'Inactif', 'suspendu' => 'Suspendu'][$employee['statut']] ?? $employee['statut'];
$statutClass = $employee['statut'] === 'actif' ? 'success' : ($employee['statut'] === 'suspendu' ? 'warning' : 'neutral');
ob_start();
?>

<section class="content-header">
    <div class="sirh-page-header">
        <div class="sirh-page-heading">
            <h1 class="sirh-page-title"><i class="fas fa-id-card" aria-hidden="true"></i> Mon profil</h1>
            <p class="sirh-page-subtitle">Vos informations personnelles et professionnelles, en lecture seule.</p>
        </div>
        <div class="sirh-page-actions">
            <a href="<?= APP_URL ?>/presences/qr" class="btn btn-primary"><i class="fas fa-qrcode" aria-hidden="true"></i> Mon QR</a>
        </div>
    </div>
</section>

<section class="content sirh-employee-space">

    <div class="sirh-profile-hero">
        <div class="sirh-profile-avatar" aria-hidden="true"><?= htmlspecialchars($initiale) ?></div>
        <div class="sirh-profile-hero__body">
            <h2><?= htmlspecialchars($nomComplet) ?></h2>
            <p><?= htmlspecialchars($employee['poste'] ?: 'Poste non défini') ?><?= !empty($employee['nom_service']) ? ' · ' . htmlspecialchars($employee['nom_service']) : '' ?></p>
            <div class="sirh-profile-tags">
                <span class="badge badge-<?= htmlspecialchars($statutClass) ?>"><?= htmlspecialchars($statutLabel) ?></span>
                <span class="sirh-profile-tag"><i class="fas fa-hashtag" aria-hidden="true"></i> <?= htmlspecialchars($employee['matricule']) ?></span>
                <?php if (!empty($employee['date_embauche'])): ?>
                <span class="sirh-profile-tag"><i class="fas fa-calendar-check" aria-hidden="true"></i> Depuis le <?= date('d/m/Y', strtotime($employee['date_embauche'])) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="sirh-profile-grid">
        <section class="sirh-panel" aria-labelledby="titre-perso">
            <h2 class="sirh-section-title" id="titre-perso"><i class="fas fa-user" aria-hidden="true"></i> Informations personnelles</h2>
            <dl class="sirh-detail-list">
                <div><dt>Nom</dt><dd><?= htmlspecialchars($employee['nom']) ?></dd></div>
                <div><dt>Post-nom</dt><dd><?= htmlspecialchars($employee['postnom'] ?: '—') ?></dd></div>
                <div><dt>Prénom</dt><dd><?= htmlspecialchars($employee['prenom']) ?></dd></div>
                <div><dt>Sexe</dt><dd><?= htmlspecialchars($sexeLabel) ?></dd></div>
                <div><dt>Date de naissance</dt><dd><?= !empty($employee['date_naissance']) ? date('d/m/Y', strtotime($employee['date_naissance'])) : '—' ?></dd></div>
                <div><dt>Lieu de naissance</dt><dd><?= htmlspecialchars($employee['lieu_naissance'] ?: '—') ?></dd></div>
                <div><dt>Adresse</dt><dd><?= htmlspecialchars($employee['adresse'] ?: '—') ?></dd></div>
                <div><dt>Téléphone</dt><dd><?= htmlspecialchars($employee['telephone'] ?: '—') ?></dd></div>
                <div><dt>Email</dt><dd><?= htmlspecialchars($employee['email'] ?: '—') ?></dd></div>
            </dl>
        </section>

        <div class="sirh-employee-side">
            <section class="sirh-panel" aria-labelledby="titre-pro">
                <h2 class="sirh-section-title" id="titre-pro"><i class="fas fa-briefcase" aria-hidden="true"></i> Informations professionnelles</h2>
                <dl class="sirh-detail-list">
                    <div><dt>Matricule</dt><dd><?= htmlspecialchars($employee['matricule']) ?></dd></div>
                    <div><dt>Service</dt><dd><?= htmlspecialchars($employee['nom_service'] ?: '—') ?></dd></div>
                    <div><dt>Poste</dt><dd><?= htmlspecialchars($employee['poste'] ?: '—') ?></dd></div>
                    <div><dt>Date d'embauche</dt><dd><?= date('d/m/Y', strtotime($employee['date_embauche'])) ?></dd></div>
                    <div><dt>Salaire de base</dt><dd><?= $employee['salaire'] ? number_format((float) $employee['salaire'], 2, ',', ' ') . ' FC' : '—' ?></dd></div>
                </dl>
            </section>

            <section class="sirh-panel" aria-labelledby="titre-compte">
                <h2 class="sirh-section-title" id="titre-compte"><i class="fas fa-shield-halved" aria-hidden="true"></i> Mon compte</h2>
                <dl class="sirh-detail-list">
                    <div><dt>Email de connexion</dt><dd><?= htmlspecialchars($compte['email'] ?? ($_SESSION['user_email'] ?? '—')) ?></dd></div>
                    <div><dt>Rôle</dt><dd><?= htmlspecialchars(ucfirst($compte['role'] ?? ($_SESSION['user_role'] ?? ''))) ?></dd></div>
                    <div><dt>Dernière connexion</dt><dd><?= !empty($compte['derniere_connexion']) ? date('d/m/Y à H:i', strtotime($compte['derniere_connexion'])) : '—' ?></dd></div>
                </dl>
            </section>

            <section class="sirh-panel" aria-labelledby="titre-primes">
                <h2 class="sirh-section-title" id="titre-primes"><i class="fas fa-star" aria-hidden="true"></i> Mes primes fixes</h2>
                <?php if (empty($primes)): ?>
                <p class="sirh-inline-note">Aucune prime fixe enregistrée.</p>
                <?php else: ?>
                <ul class="sirh-prime-list">
                    <?php foreach ($primes as $prime): ?>
                    <li><span><?= htmlspecialchars($prime['libelle']) ?></span><strong><?= number_format((float) $prime['montant'], 2, ',', ' ') ?> FC</strong></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <div class="sirh-profile-note">
        <i class="fas fa-circle-info" aria-hidden="true"></i>
        <p>Une information est incorrecte ? Contactez le service RH pour la mettre à jour. Cette fiche est en lecture seule.</p>
        <a href="<?= APP_URL ?>/messages/nouveau" class="btn btn-outline-secondary btn-sm"><i class="fas fa-comment-dots" aria-hidden="true"></i> Écrire aux RH</a>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
