<?php
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = parse_url(APP_URL, PHP_URL_PATH) ?: '';
if ($basePath !== '' && strpos($requestPath, $basePath) === 0) {
    $requestPath = substr($requestPath, strlen($basePath));
}
$requestPath = trim($requestPath, '/');
$segments = $requestPath === '' ? [] : explode('/', $requestPath);
$currentModule = $segments[0] ?? 'dashboard';
$currentAction = $segments[1] ?? '';

$userName = $_SESSION['user_name'] ?? '';
$userRole = $_SESSION['user_role'] ?? '';
$unreadNotifications = 0;
$unreadMessages = 0;
try {
    $unreadNotifications = (new NotificationModel())->getUnreadCount($_SESSION['user_id']);
    $unreadMessages = (new MessageModel())->getUnreadCount($_SESSION['user_id']);
} catch (Exception $e) {
    // A navigation counter must never prevent the application from rendering.
}

/* Pages principales : pas de fleche de retour. Toute autre page est secondaire. */
$primaryPages = ['', 'dashboard', 'employees', 'services', 'presences', 'conges', 'paie', 'formations', 'rapports', 'utilisateurs', 'journal', 'messages', 'notifications', 'guide', 'mon-profil', 'cartes', 'dispositifs'];
$isSecondary = !in_array($requestPath, $primaryPages, true);

$backFallbacks = [
    'employees' => '/employees',
    'services' => '/services',
    'presences' => '/presences',
    'conges' => '/conges',
    'paie' => ($userRole === 'employe' ? '/paie/mes-bulletins' : '/paie'),
    'formations' => '/formations',
    'rapports' => '/rapports',
    'utilisateurs' => '/utilisateurs',
    'journal' => '/journal',
    'messages' => '/messages',
    'notifications' => '/notifications',
    'cartes' => '/cartes',
    'dispositifs' => '/dispositifs',
    'about' => '/dashboard',
];
if ($currentModule === 'paie' && $currentAction === 'ma-fiche') {
    $backFallbacks['paie'] = '/paie/mes-bulletins';
}
$backFallback = $backFallbacks[$currentModule] ?? '/dashboard';

$isManager = in_array($userRole, ['admin', 'rh'], true);
$isAdmin = $userRole === 'admin';
$isEmploye = $userRole === 'employe';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#102a43">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed sirh-page-<?= htmlspecialchars($currentModule) ?><?= $isEmploye ? ' sirh-has-bottomnav' : '' ?>">
<a class="sirh-skip-link" href="#contenu-principal">Aller au contenu principal</a>
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light" aria-label="Navigation principale">
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Ouvrir ou fermer le menu"><i class="fas fa-bars"></i></a>
            </li>
            <?php if ($isSecondary): ?>
            <li class="nav-item">
                <button type="button" class="sirh-back-button" data-sirh-back data-fallback="<?= APP_URL . $backFallback ?>" aria-label="Revenir a la page precedente">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                </button>
            </li>
            <?php endif; ?>
            <li class="nav-item d-flex align-items-center">
                <span class="sirh-mobile-title"><?= htmlspecialchars($pageTitle ?? 'Espace RH') ?></span>
            </li>
            <li class="nav-item d-none d-sm-flex align-items-center ml-2">
                <span class="sirh-top-context"><strong><?= htmlspecialchars($pageTitle ?? 'Espace RH') ?></strong>GLOBIT SAS · Système RH</span>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link sirh-icon-button" href="<?= APP_URL ?>/messages" aria-label="Messages<?= $unreadMessages > 0 ? ' (' . $unreadMessages . ' non lus)' : '' ?>">
                    <i class="far fa-comment-alt" aria-hidden="true"></i>
                    <?php if ($unreadMessages > 0): ?><span class="badge badge-danger navbar-badge"><?= $unreadMessages ?></span><?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link sirh-icon-button" href="<?= APP_URL ?>/notifications" aria-label="Notifications<?= $unreadNotifications > 0 ? ' (' . $unreadNotifications . ' non lues)' : '' ?>">
                    <i class="far fa-bell" aria-hidden="true"></i>
                    <?php if ($unreadNotifications > 0): ?><span class="badge badge-danger navbar-badge"><?= $unreadNotifications ?></span><?php endif; ?>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle fa-lg" aria-hidden="true"></i><span class="sirh-user-name"><?= htmlspecialchars($userName) ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <span class="dropdown-header"><?= htmlspecialchars($userName) ?><br><small><?= ucfirst(htmlspecialchars($userRole)) ?></small></span>
                    <div class="dropdown-divider"></div>
                    <a href="<?= APP_URL ?>/guide" class="dropdown-item">
                        <i class="fas fa-book-open" aria-hidden="true"></i> Guide d'utilisation
                    </a>
                    <a href="<?= APP_URL ?>/about" class="dropdown-item">
                        <i class="fas fa-info-circle" aria-hidden="true"></i> À propos / Droits d'auteur
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?= APP_URL ?>/logout" class="dropdown-item">
                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Déconnexion
                    </a>
                </div>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="<?= APP_URL ?>/dashboard" class="brand-link text-center" aria-label="GLOBIT SAS — Tableau de bord">
            <span class="brand-text font-weight-bold">
                <i class="fas fa-users-cog" aria-hidden="true"></i> <?= APP_NAME ?>
            </span>
        </a>

        <div class="sidebar">
            <nav class="mt-2" aria-label="Menu des modules">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-header">Vue d'ensemble</li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/dashboard" class="nav-link <?= $currentModule === 'dashboard' ? 'active' : '' ?>" <?= $currentModule === 'dashboard' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-tachometer-alt" aria-hidden="true"></i>
                            <p>Tableau de bord</p>
                        </a>
                    </li>

                    <?php if ($isManager): ?>
                    <li class="nav-header">Gestion RH</li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/employees" class="nav-link <?= $currentModule === 'employees' ? 'active' : '' ?>" <?= $currentModule === 'employees' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-user-tie" aria-hidden="true"></i>
                            <p>Employés</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if ($isAdmin): ?>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/services" class="nav-link <?= $currentModule === 'services' ? 'active' : '' ?>" <?= $currentModule === 'services' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-building" aria-hidden="true"></i>
                            <p>Services</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-header">Temps et activités</li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/presences" class="nav-link <?= $currentModule === 'presences' && !in_array($currentAction, ['qr', 'scan', 'lecteur'], true) ? 'active' : '' ?>" <?= $currentModule === 'presences' && !in_array($currentAction, ['qr', 'scan', 'lecteur'], true) ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-fingerprint" aria-hidden="true"></i>
                            <p>Présences</p>
                        </a>
                    </li>

                    <?php if ($isManager): ?>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/presences/scan" class="nav-link <?= $currentModule === 'presences' && $currentAction === 'scan' ? 'active' : '' ?>" <?= $currentModule === 'presences' && $currentAction === 'scan' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-qrcode text-info" aria-hidden="true"></i>
                            <p>Poste de réception</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/presences/lecteur" class="nav-link <?= $currentModule === 'presences' && $currentAction === 'lecteur' ? 'active' : '' ?>" <?= $currentModule === 'presences' && $currentAction === 'lecteur' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-id-card text-info" aria-hidden="true"></i>
                            <p>Pointage RFID</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/conges" class="nav-link <?= $currentModule === 'conges' ? 'active' : '' ?>" <?= $currentModule === 'conges' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-calendar-alt" aria-hidden="true"></i>
                            <p>Congés</p>
                        </a>
                    </li>

                    <?php if ($isEmploye): ?>
                    <li class="nav-header">Mon espace</li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/presences/qr" class="nav-link <?= $currentModule === 'presences' && $currentAction === 'qr' ? 'active' : '' ?>" <?= $currentModule === 'presences' && $currentAction === 'qr' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-qrcode text-info" aria-hidden="true"></i>
                            <p>Mon QR de présence</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/mon-profil" class="nav-link <?= $currentModule === 'mon-profil' ? 'active' : '' ?>" <?= $currentModule === 'mon-profil' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-id-card text-primary" aria-hidden="true"></i>
                            <p>Mon profil</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/paie/mes-bulletins" class="nav-link <?= in_array($currentAction, ['mes-bulletins', 'ma-fiche'], true) ? 'active' : '' ?>" <?= in_array($currentAction, ['mes-bulletins', 'ma-fiche'], true) ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-file-invoice-dollar text-success" aria-hidden="true"></i>
                            <p>Mes bulletins</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if ($isManager): ?>
                    <li class="nav-header">Pilotage</li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/paie" class="nav-link <?= $currentModule === 'paie' && $currentAction !== 'archives' ? 'active' : '' ?>" <?= $currentModule === 'paie' && $currentAction !== 'archives' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-money-bill-wave text-warning" aria-hidden="true"></i>
                            <p>Paie</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/paie/archives" class="nav-link <?= $currentModule === 'paie' && $currentAction === 'archives' ? 'active' : '' ?>" <?= $currentModule === 'paie' && $currentAction === 'archives' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-archive text-secondary" aria-hidden="true"></i>
                            <p>Archives paie</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/formations" class="nav-link <?= $currentModule === 'formations' ? 'active' : '' ?>" <?= $currentModule === 'formations' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-graduation-cap text-info" aria-hidden="true"></i>
                            <p>Formations</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/rapports" class="nav-link <?= $currentModule === 'rapports' ? 'active' : '' ?>" <?= $currentModule === 'rapports' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-chart-bar text-success" aria-hidden="true"></i>
                            <p>Rapports</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if ($isAdmin): ?>
                    <li class="nav-header">Administration</li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/cartes" class="nav-link <?= $currentModule === 'cartes' ? 'active' : '' ?>" <?= $currentModule === 'cartes' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-id-card text-info" aria-hidden="true"></i>
                            <p>Cartes RFID</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/dispositifs" class="nav-link <?= $currentModule === 'dispositifs' ? 'active' : '' ?>" <?= $currentModule === 'dispositifs' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-broadcast-tower text-info" aria-hidden="true"></i>
                            <p>Dispositifs</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/utilisateurs" class="nav-link <?= $currentModule === 'utilisateurs' ? 'active' : '' ?>" <?= $currentModule === 'utilisateurs' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-users-cog text-warning" aria-hidden="true"></i>
                            <p>Utilisateurs</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/journal" class="nav-link <?= $currentModule === 'journal' ? 'active' : '' ?>" <?= $currentModule === 'journal' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-history text-secondary" aria-hidden="true"></i>
                            <p>Journal d'activité</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-header">Communication</li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/messages" class="nav-link <?= $currentModule === 'messages' ? 'active' : '' ?>" <?= $currentModule === 'messages' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-comments text-info" aria-hidden="true"></i>
                            <p>Messages
                                <?php if ($unreadMessages > 0): ?>
                                <span class="badge badge-danger badge-pill float-right"><?= $unreadMessages ?></span>
                                <?php endif; ?>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/notifications" class="nav-link <?= $currentModule === 'notifications' ? 'active' : '' ?>" <?= $currentModule === 'notifications' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-bell" aria-hidden="true"></i>
                            <p>Notifications
                                <?php if ($unreadNotifications > 0): ?>
                                <span class="badge badge-danger badge-pill float-right"><?= $unreadNotifications ?></span>
                                <?php endif; ?>
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/guide" class="nav-link <?= $currentModule === 'guide' ? 'active' : '' ?>" <?= $currentModule === 'guide' ? 'aria-current="page"' : '' ?>>
                            <i class="nav-icon fas fa-book-open text-warning" aria-hidden="true"></i>
                            <p>Guide d'utilisation</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <main class="content-wrapper" id="contenu-principal">
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible sirh-flash" role="status" data-sirh-autohide>
            <i class="fas fa-check-circle" aria-hidden="true"></i>
            <span><?= htmlspecialchars($_SESSION['success']) ?></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">&times;</button>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible sirh-flash" role="alert">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            <span><?= htmlspecialchars($_SESSION['error']) ?></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">&times;</button>
        </div>
        <?php unset($_SESSION['error']); endif; ?>

        <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger alert-dismissible sirh-flash" role="alert">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            <div>
                <ul class="mb-0 pl-3">
                    <?php foreach ($_SESSION['errors'] as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">&times;</button>
        </div>
        <?php unset($_SESSION['errors']); endif; ?>

        <?php echo $content; ?>
    </main>

    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            v<?= APP_VERSION ?>
        </div>
        <strong>&copy; <?= date('Y') ?> <span style="color: var(--sirh-blue); font-weight: 700;"><?= COMPANY_NAME ?></span></strong> — Système d'Information des Ressources Humaines (SIRH)
        <div class="text-muted text-center" style="font-size: 12px; margin-top: 5px;">
            Logiciel édité par la startup <?= COMPANY_NAME ?> — Tous droits réservés.
        </div>
    </footer>
</div>

<?php if ($isEmploye): ?>
<nav class="sirh-bottom-nav" aria-label="Navigation rapide de l'espace salarié">
    <a href="<?= APP_URL ?>/dashboard" class="sirh-bottom-nav__item <?= $currentModule === 'dashboard' ? 'is-active' : '' ?>" <?= $currentModule === 'dashboard' ? 'aria-current="page"' : '' ?>>
        <i class="fas fa-house" aria-hidden="true"></i>
        <span>Accueil</span>
    </a>
    <a href="<?= APP_URL ?>/presences" class="sirh-bottom-nav__item <?= $currentModule === 'presences' && $currentAction !== 'qr' ? 'is-active' : '' ?>" <?= $currentModule === 'presences' && $currentAction !== 'qr' ? 'aria-current="page"' : '' ?>>
        <i class="fas fa-fingerprint" aria-hidden="true"></i>
        <span>Présences</span>
    </a>
    <a href="<?= APP_URL ?>/presences/qr" class="sirh-bottom-nav__qr <?= $currentModule === 'presences' && $currentAction === 'qr' ? 'is-active' : '' ?>" <?= $currentModule === 'presences' && $currentAction === 'qr' ? 'aria-current="page"' : '' ?> aria-label="Afficher mon QR de présence">
        <span class="sirh-bottom-nav__qr-inner"><i class="fas fa-qrcode" aria-hidden="true"></i></span>
        <span class="sirh-bottom-nav__qr-label">Mon QR</span>
    </a>
    <a href="<?= APP_URL ?>/conges" class="sirh-bottom-nav__item <?= $currentModule === 'conges' ? 'is-active' : '' ?>" <?= $currentModule === 'conges' ? 'aria-current="page"' : '' ?>>
        <i class="fas fa-calendar-alt" aria-hidden="true"></i>
        <span>Congés</span>
    </a>
    <a href="<?= APP_URL ?>/mon-profil" class="sirh-bottom-nav__item <?= $currentModule === 'mon-profil' ? 'is-active' : '' ?>" <?= $currentModule === 'mon-profil' ? 'aria-current="page"' : '' ?>>
        <i class="fas fa-id-card" aria-hidden="true"></i>
        <span>Profil</span>
    </a>
</nav>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="<?= APP_URL ?>/public/js/app.js"></script>
<?php if (isset($extraScripts)): ?>
    <?= $extraScripts ?>
<?php endif; ?>
</body>
</html>
