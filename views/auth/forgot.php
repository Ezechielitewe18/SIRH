<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#102a43">
    <title>Mot de passe oublié — <?= htmlspecialchars(APP_NAME) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=fallback">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/auth.css">
</head>
<body>

<main class="auth-shell">
    <section class="auth-brand" aria-label="<?= htmlspecialchars(APP_NAME) ?>">
        <div>
            <span class="auth-brand-mark"><i class="fas fa-users-cog" aria-hidden="true"></i> <?= htmlspecialchars(APP_NAME) ?></span>
        </div>
        <div>
            <h1>Réinitialisation<br>du mot de passe</h1>
            <p>Indiquez l'adresse e-mail associée à votre compte. Les instructions de réinitialisation vous seront transmises.</p>
        </div>
        <p class="auth-brand-foot">Logiciel édité par <?= htmlspecialchars(COMPANY_NAME) ?> — Tous droits réservés.</p>
    </section>

    <section class="auth-panel">
        <h2>Mot de passe oublié</h2>
        <p class="auth-lead">Saisissez votre adresse e-mail professionnelle.</p>

        <?php if (!empty($message)): ?>
        <div class="auth-alert auth-alert--success" role="status">
            <i class="fas fa-circle-check" aria-hidden="true"></i>
            <span><?= htmlspecialchars($message) ?></span>
        </div>
        <?php elseif (!empty($erreur)): ?>
        <div class="auth-alert auth-alert--danger" role="alert">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            <span><?= htmlspecialchars($erreur) ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?= csrf_field() ?>

            <div class="auth-field">
                <label for="email">Adresse e-mail</label>
                <div class="auth-input">
                    <i class="fas fa-envelope" aria-hidden="true"></i>
                    <input type="email" id="email" name="email" placeholder="prenom.nom@entreprise.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus autocomplete="email">
                </div>
            </div>

            <button type="submit" class="auth-submit">
                <i class="fas fa-paper-plane" aria-hidden="true"></i> Envoyer les instructions
            </button>
        </form>

        <p class="auth-foot">
            <a href="<?= APP_URL ?>/login"><i class="fas fa-arrow-left" aria-hidden="true"></i> Revenir à la connexion</a>
        </p>
    </section>
</main>

</body>
</html>
