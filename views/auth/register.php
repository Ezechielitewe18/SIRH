<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #fff;
            color: #1B1B2F;
            overflow-x: hidden;
        }

        .presentation {
            flex: 0 0 42%;
            background: linear-gradient(160deg, #102A54 0%, #0A0826 65%, #06051A 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 56px 60px;
            overflow: hidden;
            color: #fff;
        }

        .presentation::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(600px circle at 0% 100%, rgba(37,99,235,.35), transparent 60%),
                radial-gradient(500px circle at 100% 0%, rgba(56,189,248,.16), transparent 55%);
            pointer-events: none;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .30;
            animation: drift 18s ease-in-out infinite alternate;
            z-index: 1;
        }
        .shape-1 { width: 320px; height: 320px; background: #1D4ED8; top: -90px; left: -70px; }
        .shape-2 { width: 240px; height: 240px; background: #2563EB; bottom: -50px; left: 130px; animation-delay: -6s; }
        .shape-3 { width: 160px; height: 160px; background: #3B82F6; top: 42%; right: -40px; animation-delay: -11s; }

        .shape-line {
            position: absolute;
            border-radius: 50%;
            border: 1.5px solid rgba(147,197,253,.14);
            z-index: 1;
            animation: drift 24s ease-in-out infinite alternate;
        }
        .shape-line-1 { width: 380px; height: 380px; bottom: -140px; right: -60px; }
        .shape-line-2 { width: 240px; height: 240px; top: -60px; right: 60px; animation-delay: -8s; }

        @keyframes drift {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, -24px) scale(1.08); }
        }

        .presentation-inner {
            position: relative;
            z-index: 3;
            max-width: 400px;
        }

        .presentation-inner .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 3px;
            color: rgba(255,255,255,.85);
            margin-bottom: 42px;
        }

        .presentation-inner .logo .ico {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(37,99,235,.18);
            border: 1px solid rgba(147,197,253,.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #93C5FD;
        }

        .presentation-inner h1 {
            font-size: 34px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .presentation-inner h1 em {
            font-style: normal;
            background: linear-gradient(135deg, #93C5FD, #38BDF8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .presentation-inner p {
            font-size: 14.5px;
            line-height: 1.7;
            color: #9CA3AF;
            margin-bottom: 34px;
        }

        .btn-login {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 26px;
            border-radius: 12px;
            background: rgba(37,99,235,.12);
            border: 1.5px solid rgba(147,197,253,.28);
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all .25s ease;
        }

        .btn-login:hover {
            background: rgba(37,99,235,.24);
            border-color: rgba(147,197,253,.5);
            transform: translateY(-2px);
        }

        .form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
            background: #fff;
        }

        .card-register {
            width: 100%;
            max-width: 620px;
            background: #fff;
            border-radius: 48px 16px 16px 48px;
            padding: 48px 46px;
            box-shadow: 0 30px 70px -30px rgba(10, 8, 38, .45);
            border: 1px solid rgba(10, 8, 38, .06);
        }

        .type-selector {
            text-align: right;
            margin-bottom: 18px;
        }

        .type-selector select {
            font-family: 'Inter', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            color: #2563EB;
            background: rgba(37,99,235,.08);
            border: 1px solid rgba(37,99,235,.25);
            border-radius: 10px;
            padding: 8px 12px;
            outline: none;
            cursor: pointer;
            transition: all .25s ease;
        }

        .type-selector select:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 4px rgba(37,99,235,.14);
        }

        .card-register h2 {
            font-size: 26px;
            font-weight: 800;
            text-align: center;
            color: #0A0826;
            margin-bottom: 6px;
        }

        .card-register .subtitle {
            text-align: center;
            font-size: 13.5px;
            color: #8B8BA3;
            margin-bottom: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 20px;
        }

        .field { position: relative; }

        .field label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #55556C;
            margin-bottom: 7px;
        }

        .field input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1.5px solid #E3E3EE;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #1B1B2F;
            background: #FBFBFE;
            outline: none;
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease, background .25s ease;
        }

        .field input::placeholder { color: #B4B4C6; }

        .field .icon {
            position: absolute;
            left: 14px;
            bottom: 14px;
            color: #9A9AB0;
            font-size: 14px;
            pointer-events: none;
            transition: color .25s ease;
        }

        .field input:focus {
            border-color: #2563EB;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(37,99,235,.13), 0 8px 22px -8px rgba(37,99,235,.5);
            transform: translateY(-1px);
        }

        .field input:focus + .icon { color: #2563EB; }

        .btn-register {
            width: 100%;
            margin-top: 24px;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563EB, #3B82F6);
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all .25s ease;
            box-shadow: 0 12px 26px -10px rgba(37,99,235,.6);
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 34px -12px rgba(37,99,235,.7);
        }

        .alert-danger {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #B91C1C;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .alert-danger ul { margin-left: 18px; }

        @media (max-width: 900px) {
            body { flex-direction: column; }

            .presentation {
                flex: none;
                padding: 40px 28px 46px;
            }

            .presentation-inner h1 { font-size: 26px; }
            .presentation-inner p { margin-bottom: 22px; }

            .form-side { padding: 28px 16px 48px; }

            .card-register {
                border-radius: 24px;
                padding: 32px 20px;
                max-width: 480px;
            }

            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="presentation">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape-line shape-line-1"></div>
        <div class="shape-line shape-line-2"></div>

        <div class="presentation-inner">
            <div class="logo">
                <span class="ico"><i class="fas fa-users-cog"></i></span>
                <?= APP_NAME ?> SIRH
            </div>
            <h1>Rejoignez l'équipe<br><em>Globit.</em></h1>
            <p>
                Créez votre espace pour accéder à vos présences, congés,
                bulletins de paie et toute la vie RH de l'entreprise,
                où que vous soyez.
            </p>
            <a href="<?= APP_URL ?>/login" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </a>
        </div>
    </div>

    <div class="form-side">
        <div class="card-register">
            <div class="type-selector">
                <select name="role" form="registerForm">
                    <option value="employe">Employé</option>
                    <option value="rh">Responsable RH</option>
                </select>
            </div>

            <h2>Candidature chez Globit</h2>
            <p class="subtitle">Renseignez vos informations pour créer votre compte</p>

            <?php if (!empty($errors)): ?>
            <div class="alert-danger">
                <ul>
                    <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form id="registerForm" method="POST" action="">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <div class="field">
                        <label>Nom complet</label>
                        <i class="icon fas fa-user-circle"></i>
                        <input type="text" name="nom_complet" placeholder="Votre nom complet" value="<?= htmlspecialchars($_POST['nom_complet'] ?? '') ?>" required>
                    </div>
                    <div class="field">
                        <label>Email professionnel</label>
                        <i class="icon fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="prenom.nom@globit.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="field">
                        <label>Mot de passe</label>
                        <i class="icon fas fa-lock"></i>
                        <input type="password" name="password" placeholder="6 caractères min." required>
                    </div>
                    <div class="field">
                        <label>Confirmer le mot de passe</label>
                        <i class="icon fas fa-lock"></i>
                        <input type="password" name="password_confirm" placeholder="Répétez le mot de passe" required>
                    </div>
                </div>
                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i> Créer mon compte
                </button>
            </form>
        </div>
    </div>

</body>
</html>