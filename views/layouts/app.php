<?php $currentPage = strtok(basename($_SERVER['REQUEST_URI']), '?'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? APP_NAME ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <span class="dropdown-header"><?= ucfirst($_SESSION['user_role'] ?? '') ?></span>
                    <div class="dropdown-divider"></div>
                    <a href="<?= APP_URL ?>/about" class="dropdown-item">
                        <i class="fas fa-info-circle"></i> À propos / Droits d'auteur
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?= APP_URL ?>/logout" class="dropdown-item">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </div>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="<?= APP_URL ?>/dashboard" class="brand-link text-center">
            <span class="brand-text font-weight-bold" style="color: #fff;">
                <i class="fas fa-users-cog"></i> <?= APP_NAME ?>
            </span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/dashboard" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Tableau de bord</p>
                        </a>
                    </li>

                    <?php if (in_array($_SESSION['user_role'], ['admin', 'rh'])): ?>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/employees" class="nav-link <?= strpos($currentPage, 'employee') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user-tie"></i>
                            <p>Employés</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/services" class="nav-link <?= $currentPage === 'services' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Services</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/presences" class="nav-link <?= $currentPage === 'presences' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-fingerprint"></i>
                            <p>Présences</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/conges" class="nav-link <?= $currentPage === 'conges' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Congés</p>
                        </a>
                    </li>

                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/paie" class="nav-link <?= strpos($currentPage, 'paie') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-money-bill-wave text-warning"></i>
                            <p>Paie</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/paie/archives" class="nav-link <?= strpos($currentPage, 'archives') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-archive text-secondary"></i>
                            <p>Archives paie</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/formations" class="nav-link <?= strpos($currentPage, 'formation') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-graduation-cap text-info"></i>
                            <p>Formations</p>
                        </a>
                    </li>

                    <?php if (in_array($_SESSION['user_role'], ['admin', 'rh'])): ?>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/rapports" class="nav-link <?= strpos($currentPage, 'rapport') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-chart-bar text-success"></i>
                            <p>Rapports</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/cartes" class="nav-link <?= strpos($currentPage, 'carte') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-qrcode text-primary"></i>
                            <p>Cartes QR</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/utilisateurs" class="nav-link <?= strpos($currentPage, 'utilisateur') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users-cog text-warning"></i>
                            <p>Utilisateurs</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/journal" class="nav-link <?= strpos($currentPage, 'journal') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-history text-secondary"></i>
                            <p>Journal d'activité</p>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>

                    <li class="nav-item mt-2" style="border-top: 1px solid rgba(255,255,255,.1);">
                        <a href="<?= APP_URL ?>/messages" class="nav-link <?= strpos($currentPage, 'message') !== false ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-comments text-info"></i>
                            <p>Messages
                                <?php
                                try {
                                    $msgModel = new MessageModel();
                                    $msgunread = $msgModel->getUnreadCount($_SESSION['user_id']);
                                    if ($msgunread > 0): ?>
                                    <span class="badge badge-danger badge-pill float-right"><?= $msgunread ?></span>
                                    <?php endif;
                                } catch (Exception $e) {}
                                ?>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item" style="border-top: 1px solid rgba(255,255,255,.1);">
                        <a href="<?= APP_URL ?>/notifications" class="nav-link <?= $currentPage === 'notifications' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-bell"></i>
                            <p>Notifications
                                <?php
                                try {
                                    $notifModel = new NotificationModel();
                                    $unread = $notifModel->getUnreadCount($_SESSION['user_id']);
                                    if ($unread > 0): ?>
                                    <span class="badge badge-danger badge-pill float-right"><?= $unread ?></span>
                                    <?php endif;
                                } catch (Exception $e) {}
                                ?>
                            </p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible" style="margin: 15px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible" style="margin: 15px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); endif; ?>

        <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger alert-dismissible" style="margin: 15px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul class="mb-0">
                <?php foreach ($_SESSION['errors'] as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); endif; ?>

        <?php echo $content; ?>
    </div>

    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            v<?= APP_VERSION ?>
        </div>
        <strong>&copy; <?= date('Y') ?> <span style="color: #007bff; font-weight: 700;"><?= APP_NAME ?></span></strong> - Système d'Information des Ressources Humaines
        <div class="text-muted text-center" style="font-size: 12px; margin-top: 5px;">
            Tous droits réservés. Développé par <?= AUTHOR_NAME ?>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="<?= APP_URL ?>/public/js/app.js"></script>
<?php if (isset($extraScripts)): ?>
    <?= $extraScripts ?>
<?php endif; ?>
</body>
</html>
