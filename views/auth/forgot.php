<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: grid;
            place-items: center;
            color: #fff;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
            background: #05060f;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -3;
            background:
                radial-gradient(720px circle at 12% 18%, rgba(139, 92, 246, .28), transparent 55%),
                radial-gradient(620px circle at 88% 82%, rgba(59, 130, 246, .24), transparent 55%),
                radial-gradient(560px circle at 75% 8%, rgba(168, 85, 247, .16), transparent 52%),
                radial-gradient(520px circle at 20% 88%, rgba(99, 102, 241, .14), transparent 55%),
                linear-gradient(152deg, #05060f 0%, #0d0a1e 55%, #151032 100%);
        }

        .glow {
            position: fixed;
            z-index: -2;
            border-radius: 50%;
            filter: blur(90px);
            opacity: .5;
            pointer-events: none;
        }
        .glow-a { width: 320px; height: 320px; top: -90px; left: -110px; background: radial-gradient(circle, #7c3aed, transparent 70%); animation: drift 16s ease-in-out infinite alternate; }
        .glow-b { width: 280px; height: 280px; bottom: -70px; right: -90px; background: radial-gradient(circle, #4f46e5, transparent 70%); animation: drift 20s ease-in-out infinite alternate-reverse; }

        @keyframes drift {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(28px, -22px) scale(1.08); }
        }

        .card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, .045);
            -webkit-backdrop-filter: blur(20px);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 28px;
            padding: 42px 40px 38px;
            box-shadow:
                0 0 70px rgba(124, 58, 237, .18),
                inset 0 1px 0 rgba(255, 255, 255, .07);
            animation: fadeIn .6s ease both;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .head { text-align: center; margin-bottom: 28px; }
        .head .title { font-size: 22px; font-weight: 800; }
        .head .sub { margin-top: 8px; font-size: 13px; color: #98a2c4; }

        .msg {
            background: rgba(34, 197, 94, .10);
            border: 1px solid rgba(34, 197, 94, .22);
            color: #86efac;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: center;
        }
        .err {
            background: rgba(239, 68, 68, .10);
            border: 1px solid rgba(239, 68, 68, .22);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: center;
        }

        .field { margin-bottom: 18px; }
        .field label { display: block; font-size: 13px; font-weight: 600; color: #b6bede; margin-bottom: 8px; letter-spacing: .2px; }
        .field input {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            font-size: 14px;
            font-family: inherit;
            color: #fff;
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            outline: none;
            transition: border-color .25s ease, box-shadow .25s ease, background .25s ease;
        }
        .field input::placeholder { color: #5d6689; }
        .field input:focus {
            border-color: rgba(139, 92, 246, .7);
            background: rgba(139, 92, 246, .06);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, .14);
        }

        .btn-login {
            width: 100%;
            height: 50px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 14px;
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 45%, #4f46e5 100%);
            color: #fff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: transform .25s ease, box-shadow .25s ease, filter .25s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 34px rgba(109, 40, 217, .45);
            filter: brightness(1.06);
        }

        .back { text-align: center; margin-top: 22px; font-size: 13px; color: #8f98ba; }
        .back a { color: #a78bfa; font-weight: 600; text-decoration: none; transition: color .2s ease; }
        .back a:hover { color: #c4b5fd; text-decoration: underline; }

        @media (max-width: 480px) {
            body { padding: 16px; }
            .card { padding: 32px 22px 26px; border-radius: 22px; }
        }
        /* Page de récupération alignée sur l'identité de l'application. */
        body { color: #172b4d; background: #f4f7fb; }
        body::before { background: #f4f7fb; }.glow { display: none; }
        .card { background: #fff; border: 1px solid #dce5ef; border-radius: 14px; box-shadow: 0 18px 42px rgba(16,42,67,.10); }
        .head .title { color: #102a43; }.head .sub, .back { color: #64748b; }
        .field label { color: #334e68; }.field input { color: #172b4d; background: #fff; border-color: #c8d5e1; border-radius: 8px; }.field input::placeholder { color: #94a3b8; }.field input:focus { border-color: #1769aa; background: #fff; box-shadow: 0 0 0 4px rgba(23,105,170,.13); }
        .btn-login { background: #1769aa; border-color: #1769aa; border-radius: 8px; letter-spacing: .4px; }.btn-login:hover { background: #10598f; box-shadow: 0 8px 18px rgba(16,89,143,.22); filter: none; }
        .back a { color: #1769aa; }.msg { background: #e7f6f0; border-color: #bfe7d6; color: #126347; }.err { background: #fff0f1; border-color: #f6c8cf; color: #9d2738; }
    </style>
</head>
<body>

    <div class="glow glow-a"></div>
    <div class="glow glow-b"></div>

    <div class="card">

        <div class="head">
            <div class="title">Forget Password</div>
            <div class="sub">Entrez votre adresse e-mail pour réinitialiser votre mot de passe.</div>
        </div>

        <?php if (!empty($message)): ?>
        <div class="msg"><?= htmlspecialchars($message) ?></div>
        <?php elseif (!empty($erreur)): ?>
        <div class="err"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <?= csrf_field() ?>

            <div class="field">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" placeholder="example@gmail.com" required autofocus autocomplete="email">
            </div>

            <button type="submit" class="btn-login">Send</button>
        </form>

        <div class="back">
            <a href="<?= APP_URL ?>/login">Back to Login</a>
        </div>

    </div>

</body>
</html>
