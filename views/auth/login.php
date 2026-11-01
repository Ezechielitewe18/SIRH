<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#102a43">
    <title>Connexion — <?= htmlspecialchars(APP_NAME) ?></title>
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
            <h1>Système d'Information<br>des Ressources Humaines</h1>
            <p>Centralisez les dossiers du personnel, les présences, les congés et la paie dans un seul espace sécurisé.</p>
        </div>
        <ul class="auth-features">
            <li><i class="fas fa-fingerprint" aria-hidden="true"></i><span>Pointage par QR code et suivi des présences en temps réel.</span></li>
            <li><i class="fas fa-calendar-check" aria-hidden="true"></i><span>Demandes de congé et validations hiérarchisées.</span></li>
            <li><i class="fas fa-file-invoice-dollar" aria-hidden="true"></i><span>Bulletins de paie calculés automatiquement.</span></li>
        </ul>
        <p class="auth-brand-foot">Logiciel édité par <?= htmlspecialchars(COMPANY_NAME) ?> — Tous droits réservés.</p>
    </section>

    <section class="auth-panel">
        <h2>Connexion</h2>
        <p class="auth-lead">Accédez à votre espace de travail avec vos identifiants professionnels.</p>

        <?php if (!empty($error)): ?>
        <div class="auth-alert auth-alert--danger" role="alert">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            <span><?= htmlspecialchars($error) ?></span>
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

            <div class="auth-field">
                <label for="password">Mot de passe</label>
                <div class="auth-input">
                    <i class="fas fa-lock" aria-hidden="true"></i>
                    <input type="password" id="password" name="password" placeholder="Votre mot de passe" required autocomplete="current-password">
                </div>
            </div>

            <div class="auth-row">
                <a href="<?= APP_URL ?>/forgot-password">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="auth-submit">
                <i class="fas fa-arrow-right-to-bracket" aria-hidden="true"></i> Se connecter
            </button>
        </form>

        <p class="auth-foot">
            Besoin d'un compte ? Contactez votre administrateur RH.
        </p>
    </section>
</main>

</body>
</html>
