<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
</head>
<body class="hold-transition register-page">
<div class="register-box">
    <div class="register-logo">
        <b style="color: #007bff;"><i class="fas fa-users-cog"></i> <?= APP_NAME ?></b>
    </div>
    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">Créer un compte utilisateur</p>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <?= csrf_field() ?>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Nom complet" name="nom_complet" value="<?= htmlspecialchars($_POST['nom_complet'] ?? '') ?>" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-user"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Mot de passe (min. 6 caractères)" name="password" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Confirmer le mot de passe" name="password_confirm" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Créer le compte</button>
                    </div>
                </div>
            </form>

            <p class="mt-3 mb-1">
                <a href="<?= APP_URL ?>/login">Déjà inscrit ? Se connecter</a>
            </p>
            <p class="text-center text-muted" style="font-size: 12px; margin-top: 15px;">
                © <?= date('Y') ?> <?= APP_NAME ?> - Développé par <?= AUTHOR_NAME ?>
            </p>
        </div>
    </div>
</div>
</body>
</html>
