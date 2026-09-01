<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b style="color: #007bff;"><i class="fas fa-users-cog"></i> <?= APP_NAME ?></b>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Connectez-vous pour commencer</p>

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Mot de passe" name="password" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
                    </div>
                </div>
            </form>

            <p class="mt-3 mb-1">
                <a href="<?= APP_URL ?>/register">Créer un compte administrateur</a>
            </p>
            <p class="text-center text-muted" style="font-size: 12px; margin-top: 15px;">
                © <?= date('Y') ?> <?= APP_NAME ?> - Développé par <?= AUTHOR_NAME ?>
            </p>
        </div>
    </div>
</div>
</body>
</html>
