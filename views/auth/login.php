<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0A0826;
            min-height: 100vh;
            display: flex;
            color: #fff;
        }

        /* ─── Branding (Left) ─── */
        .brand {
            flex: 0 0 45%;
            background: linear-gradient(160deg, #4F46E5 0%, #1E1B4B 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 70px;
            overflow: hidden;
        }

        .brand .wave {
            position: absolute;
            right: -1px;
            top: 0;
            height: 100%;
            width: 150px;
            z-index: 2;
        }

        .brand .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: .35;
            animation: float 14s ease-in-out infinite alternate;
        }
        .brand .orb-1 { width: 340px; height: 340px; background: #6366F1; top: -100px; left: -80px; }
        .brand .orb-2 { width: 200px; height: 200px; background: #3B82F6; bottom: -60px; left: 140px; animation-delay: -5s; }

        @keyframes float {
            0%   { transform: translate(0,0) scale(1); }
            100% { transform: translate(25px,-18px) scale(1.06); }
        }

        .brand-inner { position: relative; z-index: 3; max-width: 420px; }

        .brand-inner .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 52px;
            opacity: 0;
            animation: fadeUp .7s ease .1s forwards;
        }
        .logo-ic {
            width: 44px; height: 44px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 18px;
        }
        .logo-tx {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .brand-inner h1 {
            font-size: 42px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -1px;
            margin-bottom: 14px;
            opacity: 0;
            animation: fadeUp .7s ease .25s forwards;
        }
        .brand-inner h1 em {
            font-style: normal;
            background: linear-gradient(135deg, #93C5FD, #38BDF8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand-inner .sub {
            font-size: 15px;
            color: #9CA3AF;
            line-height: 1.6;
            margin-bottom: 52px;
            opacity: 0;
            animation: fadeUp .7s ease .35s forwards;
        }

        .perks { display: flex; flex-direction: column; gap: 14px; }
        .perk {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 12px;
            backdrop-filter: blur(6px);
            transition: all .35s ease;
            opacity: 0;
            animation: fadeUp .6s ease forwards;
        }
        .perk:nth-child(1) { animation-delay: .5s; }
        .perk:nth-child(2) { animation-delay: .65s; }
        .perk:nth-child(3) { animation-delay: .8s; }

        .perk:hover {
            background: rgba(255,255,255,.09);
            border-color: rgba(255,255,255,.14);
            transform: translateX(4px);
        }

        .perk-ic {
            width: 40px; height: 40px;
            background: rgba(59,130,246,.15);
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 16px;
            color: #60A5FA;
            flex-shrink: 0;
        }
        .perk-tx { font-size: 13px; font-weight: 600; color: #fff; }
        .perk-tx small { display: block; font-size: 11px; font-weight: 400; color: #9CA3AF; margin-top: 2px; }

        /* ─── Form (Right) ─── */
        .form-side {
            flex: 1;
            display: grid;
            place-items: center;
            padding: 40px;
            background: #0A0826;
        }

        .form-box { width: 100%; max-width: 380px; }

        .form-top {
            text-align: center;
            margin-bottom: 36px;
            opacity: 0;
            animation: fadeUp .6s ease .15s forwards;
        }

        .lock-ic {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #2563EB, #3B82F6);
            border-radius: 14px;
            display: inline-grid;
            place-items: center;
            font-size: 22px;
            margin-bottom: 20px;
            position: relative;
            animation: pulse 3.5s ease-in-out infinite;
        }
        .lock-ic::after {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: 20px;
            background: rgba(37,99,235,.25);
            filter: blur(16px);
            z-index: -1;
            animation: pulse 3.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { transform: scale(1); opacity: .8; }
            50%     { transform: scale(1.05); opacity: 1; }
        }

        .form-top h2 { font-size: 26px; font-weight: 800; letter-spacing: -.5px; margin-bottom: 6px; }
        .form-top small { font-size: 13px; color: #9CA3AF; }

        .error-msg {
            background: rgba(239,68,68,.08);
            border: 1px solid rgba(239,68,68,.15);
            color: #FCA5A5;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: shake .45s ease;
        }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20% { transform: translateX(-5px); }
            40% { transform: translateX(5px); }
            60% { transform: translateX(-3px); }
            80% { transform: translateX(3px); }
        }

        .field { margin-bottom: 18px; opacity: 0; animation: fadeUp .5s ease forwards; }
        .field:nth-child(1) { animation-delay: .3s; }
        .field:nth-child(2) { animation-delay: .4s; }

        .field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #9CA3AF;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .inp {
            display: flex;
            align-items: center;
            background: #fff;
            border: 2px solid transparent;
            border-radius: 10px;
            transition: all .3s ease;
        }
        .inp:hover { border-color: #D1D5DB; }
        .inp:focus-within {
            border-color: #2563EB;
            box-shadow: 0 0 0 4px rgba(37,99,235,.12);
        }

        .inp i {
            width: 46px;
            text-align: center;
            color: #9CA3AF;
            font-size: 15px;
            transition: color .3s;
        }
        .inp:focus-within i { color: #2563EB; }

        .inp input {
            flex: 1;
            border: none;
            outline: none;
            background: none;
            padding: 14px 14px 14px 0;
            font-size: 14px;
            color: #1F2937;
            font-family: inherit;
        }
        .inp input::placeholder { color: #9CA3AF; }

        .eye {
            width: 46px;
            display: grid;
            place-items: center;
            background: none;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            font-size: 15px;
            transition: color .2s;
        }
        .eye:hover { color: #6B7280; }

        .opts {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 26px;
            opacity: 0;
            animation: fadeUp .5s ease .5s forwards;
        }

        .chk {
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            font-size: 12px;
            color: #9CA3AF;
        }
        .chk input { accent-color: #2563EB; cursor: pointer; }

        .forgot {
            font-size: 12px;
            color: #2563EB;
            text-decoration: none;
            font-weight: 600;
            transition: color .2s;
        }
        .forgot:hover { color: #3B82F6; }

        .btn-main {
            width: 100%;
            padding: 14px;
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            transition: all .35s ease;
            opacity: 0;
            animation: fadeUp .5s ease .6s forwards;
        }
        .btn-main::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #3B82F6, #6366F1);
            opacity: 0;
            transition: opacity .35s;
        }
        .btn-main:hover::before { opacity: 1; }
        .btn-main:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(37,99,235,.35);
        }
        .btn-main span, .btn-main i { position: relative; z-index: 1; }
        .btn-main:hover i { animation: nudge .45s ease; }

        @keyframes nudge {
            0%,100% { transform: translateX(0); }
            40% { transform: translateX(5px); }
            60% { transform: translateX(-2px); }
        }

        .divi {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 22px 0;
            opacity: 0;
            animation: fadeUp .5s ease .7s forwards;
        }
        .divi::before, .divi::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #374151;
        }
        .divi span { font-size: 11px; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; }

        .btn-google {
            width: 100%;
            padding: 14px;
            background: #fff;
            color: #1F2937;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all .3s ease;
            opacity: 0;
            animation: fadeUp .5s ease .8s forwards;
        }
        .btn-google:hover {
            background: #F9FAFB;
            border-color: #D1D5DB;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,.08);
        }
        .btn-google svg { width: 18px; height: 18px; }

        .bottom {
            text-align: center;
            margin-top: 30px;
            padding-top: 22px;
            border-top: 1px solid rgba(255,255,255,.06);
            opacity: 0;
            animation: fadeUp .5s ease .9s forwards;
        }
        .bottom span { font-size: 12px; color: #9CA3AF; }
        .bottom a { color: #2563EB; text-decoration: none; font-weight: 600; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1024px) {
            .brand { display: none; }
            body { background: linear-gradient(160deg, #1E1B4B, #0A0826); }
        }
        @media (max-width: 480px) {
            .form-side { padding: 24px; }
            .form-top h2 { font-size: 22px; }
        }
    </style>
</head>
<body>

    <section class="brand">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>

        <svg class="wave" viewBox="0 0 150 800" preserveAspectRatio="none">
            <path d="M0,0 C50,120 10,220 60,340 C110,460 10,560 70,660 C100,720 40,780 60,800 L0,800Z" fill="#0A0826"/>
        </svg>

        <div class="brand-inner">
            <div class="logo">
                <div class="logo-ic"><i class="fas fa-users-gear"></i></div>
                <span class="logo-tx">Globit</span>
            </div>

            <h1>Bienvenue<br>sur votre <em>espace RH</em></h1>
            <p class="sub">Pilotez vos ressources humaines depuis une seule plateforme.</p>

            <div class="perks">
                <div class="perk">
                    <div class="perk-ic"><i class="fas fa-chart-pie"></i></div>
                    <div class="perk-tx">Statistiques en direct<small>Indicateurs et rapports en temps réel</small></div>
                </div>
                <div class="perk">
                    <div class="perk-ic"><i class="fas fa-shield-halved"></i></div>
                    <div class="perk-tx">Sécurité de niveau entreprise<small>Données chiffrées et rôles contrôlés</small></div>
                </div>
                <div class="perk">
                    <div class="perk-ic"><i class="fas fa-bolt"></i></div>
                    <div class="perk-tx">Performance optimale<small>Interface rapide et intuitive</small></div>
                </div>
            </div>
        </div>
    </section>

    <main class="form-side">
        <div class="form-box">

            <div class="form-top">
                <div class="lock-ic"><i class="fas fa-lock"></i></div>
                <h2>Connexion</h2>
                <small>Entrez vos identifiants pour continuer</small>
            </div>

            <?php if (!empty($error)): ?>
            <div class="error-msg">
                <i class="fas fa-circle-exclamation"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <?= csrf_field() ?>

                <div class="field">
                    <label>Adresse e-mail</label>
                    <div class="inp">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="admin@globit.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                    </div>
                </div>

                <div class="field">
                    <label>Mot de passe</label>
                    <div class="inp">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="pw" placeholder="••••••••" required>
                        <button type="button" class="eye" onclick="togglePw()">
                            <i class="fas fa-eye" id="eye"></i>
                        </button>
                    </div>
                </div>

                <div class="opts">
                    <label class="chk">
                        <input type="checkbox" name="remember"> Se souvenir de moi
                    </label>
                    <a href="#" class="forgot">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn-main">
                    <span>Continuer</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="divi"><span>ou</span></div>

            <button type="button" class="btn-google" disabled>
                <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Continuer avec Google
            </button>

            <div class="bottom">
                <span>Besoin d'aide ?</span> <a href="#">Contacter le support</a>
            </div>

        </div>
    </main>

    <script>
    function togglePw() {
        var i = document.getElementById('pw');
        var e = document.getElementById('eye');
        if (i.type === 'password') { i.type = 'text'; e.className = 'fas fa-eye-slash'; }
        else { i.type = 'password'; e.className = 'fas fa-eye'; }
    }
    </script>

</body>
</html>
