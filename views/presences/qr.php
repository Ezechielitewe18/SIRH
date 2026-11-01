<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#102a43">
    <title>Mon QR de présence — <?= htmlspecialchars(APP_NAME) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=fallback">
    <script src="<?= APP_URL ?>/public/js/qrcode.min.js"></script>
    <style>
        :root {
            --qr-navy: #102a43;
            --qr-navy-deep: #071b2f;
            --qr-blue: #1769aa;
            --qr-cyan: #0e8fa8;
            --qr-canvas: #f4f7fb;
            --qr-line: #dce5ef;
            --qr-text: #172b4d;
            --qr-muted: #5b6b7f;
            --qr-success: #16805d;
            --qr-success-soft: #e7f6f0;
            --qr-warning: #a96500;
            --qr-warning-soft: #fff6e5;
            --qr-info: #12578c;
            --qr-info-soft: #eaf3fb;
            --qr-danger: #bf3c4c;
            --qr-danger-soft: #fff0f1;
        }
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { min-height: 100%; }
        body {
            font-family: "Source Sans Pro", system-ui, -apple-system, "Segoe UI", sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 18px 32px;
            color: var(--qr-text);
            background: var(--qr-canvas);
        }

        .qr-top {
            width: 100%;
            max-width: 440px;
            margin: 0 -18px 22px;
            padding: 26px 22px 30px;
            background: linear-gradient(150deg, var(--qr-navy-deep) 0%, #0d2f4d 58%, #114a72 100%);
            color: #fff;
            border-radius: 0 0 22px 22px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .qr-top::after {
            content: "";
            position: absolute;
            right: -90px;
            bottom: -110px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(14, 143, 168, .45), transparent 68%);
        }
        .qr-brand {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: .82rem;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #cfe3f2;
        }
        .qr-brand i { color: #7fd6e6; }
        .qr-top h1 {
            position: relative;
            z-index: 1;
            margin-top: 12px;
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: -.01em;
        }
        .qr-top .sub {
            position: relative;
            z-index: 1;
            margin-top: 6px;
            color: #a9c6dd;
            font-size: .88rem;
        }

        .card {
            width: 100%;
            max-width: 404px;
            background: #fff;
            border: 1px solid var(--qr-line);
            border-radius: 16px;
            padding: 22px 20px;
            box-shadow: 0 18px 44px rgba(7, 27, 47, .10);
            text-align: center;
        }

        .identite {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 11px;
            margin-bottom: 18px;
            text-align: left;
            max-width: 100%;
        }
        .identite > div:last-child { min-width: 0; }
        .avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(140deg, var(--qr-navy), #175a8c);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .identite .nom {
            font-size: .96rem;
            font-weight: 700;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }
        .identite .meta {
            font-size: .76rem;
            color: var(--qr-muted);
            margin-top: 2px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .qr-frame {
            background: #fff;
            border: 1px solid var(--qr-line);
            border-radius: 14px;
            padding: 12px;
            display: inline-block;
            box-shadow: inset 0 0 0 6px #f8fbfe;
        }
        #qrcode { width: 232px; height: 232px; }
        #qrcode img, #qrcode canvas { display: block; width: 232px !important; height: 232px !important; }

        .jauge {
            margin: 18px auto 8px;
            width: 100%;
            height: 6px;
            background: #e6eef6;
            border-radius: 6px;
            overflow: hidden;
        }
        .jauge span {
            display: block;
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, var(--qr-blue), var(--qr-cyan));
            border-radius: 6px;
            transition: width .5s linear;
        }
        .chrono { font-size: .78rem; color: var(--qr-muted); }
        .chrono b { color: var(--qr-blue); font-size: .95rem; }

        .consigne {
            margin-top: 16px;
            font-size: .86rem;
            line-height: 1.5;
            color: #33475f;
        }

        .etat {
            margin-top: 16px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: .82rem;
            line-height: 1.45;
            border: 1px solid transparent;
            text-align: left;
        }
        .etat.ok { background: var(--qr-success-soft); border-color: #bfe7d6; color: #126347; }
        .etat.info { background: var(--qr-info-soft); border-color: #c6ddef; color: var(--qr-info); }
        .etat.warn { background: var(--qr-warning-soft); border-color: #f0d9a8; color: var(--qr-warning); }
        .etat b { display: block; font-size: .86rem; margin-bottom: 2px; }

        .note {
            margin-top: 14px;
            font-size: .72rem;
            color: #7b8a9c;
            line-height: 1.5;
            text-align: left;
        }

        .actions { margin-top: 18px; display: flex; gap: 10px; }
        .btn {
            flex: 1;
            min-height: 44px;
            border-radius: 9px;
            font-family: inherit;
            font-size: .88rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: background-color .15s ease, border-color .15s ease;
        }
        .btn:focus-visible { outline: 2px solid var(--qr-cyan); outline-offset: 2px; }
        .btn-outline {
            background: #fff;
            border: 1px solid #c3d2e2;
            color: #33475f;
        }
        .btn-outline:hover { background: #f4f8fc; border-color: #a8bccf; }
        .btn-primary {
            background: var(--qr-blue);
            border: 1px solid var(--qr-blue);
            color: #fff;
        }
        .btn-primary:hover { background: #12578c; border-color: #12578c; }

        .erreur {
            margin-top: 16px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: .82rem;
            background: var(--qr-danger-soft);
            border: 1px solid #f6c8cf;
            color: #9d2738;
            max-width: 404px;
            width: 100%;
        }

        @media (max-width: 400px) {
            #qrcode, #qrcode img, #qrcode canvas { width: 200px !important; height: 200px !important; }
            .qr-frame { padding: 10px; }
            .qr-top h1 { font-size: 1.3rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>

    <header class="qr-top">
        <span class="qr-brand"><i class="fas fa-users-cog" aria-hidden="true"></i> <?= htmlspecialchars(APP_NAME) ?></span>
        <h1>Mon QR de présence</h1>
        <p class="sub">Présentez ce code à la réception</p>
    </header>

    <div class="card">
        <div class="identite">
            <div class="avatar" aria-hidden="true"><?= htmlspecialchars(mb_strtoupper(mb_substr($employe['prenom'], 0, 1))) ?></div>
            <div>
                <div class="nom"><?= htmlspecialchars($employe['prenom'] . ' ' . $employe['nom']) ?></div>
                <div class="meta"><?= htmlspecialchars($employe['matricule']) ?><?= $employe['poste'] ? ' · ' . htmlspecialchars($employe['poste']) : '' ?></div>
            </div>
        </div>

        <div class="qr-frame">
            <div id="qrcode" role="img" aria-label="Code QR de pointage personnel"></div>
        </div>

        <div class="jauge" aria-hidden="true"><span id="barre"></span></div>
        <div class="chrono">Changement de code dans <b id="restant"><?= QR_PERIODE ?></b> s</div>

        <div class="consigne">Scannez ce QR avec le poste de la réception : votre <strong>arrivée</strong> sera enregistrée à l'instant du scan. Un second scan pointera votre <strong>départ</strong>.</div>

        <?php if (!$maPresence): ?>
        <div class="etat info">
            <b>Aucune présence enregistrée aujourd'hui</b>
            Votre arrivée n'est pas encore pointée.
        </div>
        <?php elseif ($maPresence['statut'] === 'absent'): ?>
        <div class="etat warn">
            <b>Marqué absent aujourd'hui</b>
            Présentez ce QR à la réception : la Direction pourra enregistrer votre arrivée tardive.
        </div>
        <?php elseif (empty($maPresence['heure_arrivee'])): ?>
        <div class="etat warn">
            <b>Marqué absent aujourd'hui</b>
            Présentez ce QR à la réception pour régulariser.
        </div>
        <?php elseif (empty($maPresence['heure_depart'])): ?>
        <div class="etat ok">
            <b>Arrivée pointée à <?= htmlspecialchars(substr($maPresence['heure_arrivee'], 0, 5)) ?></b>
            <?= $maPresence['retard'] > 0 ? 'Retard : ' . (int)$maPresence['retard'] . ' min.' : 'À l\'heure.' ?>
            Un second scan à la réception pointera votre départ.
        </div>
        <?php else: ?>
        <div class="etat ok">
            <b>Journée pointée</b>
            Arrivée <?= htmlspecialchars(substr($maPresence['heure_arrivee'], 0, 5)) ?> · Départ <?= htmlspecialchars(substr($maPresence['heure_depart'], 0, 5)) ?>.
        </div>
        <?php endif; ?>

        <div class="note">Sécurité : ce code est signé par le serveur et expire après <?= QR_PERIODE ?> secondes. Une photo ou un envoi à distance ne peut pas servir à pointer une présence.</div>

        <div class="actions">
            <a class="btn btn-outline" href="<?= APP_URL ?>/presences"><i class="fas fa-arrow-left" aria-hidden="true"></i> Présences</a>
            <button type="button" class="btn btn-primary" id="btnActualiser"><i class="fas fa-rotate" aria-hidden="true"></i> Actualiser</button>
        </div>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
    <div class="erreur"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <script>
    var QR_URL = <?= json_encode(APP_URL . '/presences/qr-code') ?>;
    var QR_DUREE = <?= (int) QR_PERIODE ?>;
    var SERVEUR_NOW = <?= (int) round(microtime(true) * 1000) ?>;
    var OFFSET = SERVEUR_NOW - Date.now();

    function maintenant() { return Date.now() + OFFSET; }

    var expireLe = null;

    function dessiner(code) {
        var box = document.getElementById('qrcode');
        box.innerHTML = '';
        new QRCode(box, {
            text: code,
            width: 232,
            height: 232,
            colorDark: '#0b0f1a',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
    }

    function charger() {
        if (typeof QRCode === 'undefined') {
            document.getElementById('qrcode').innerHTML = '<div style="display:grid;place-items:center;height:100%;font-size:12px;color:#bf3c4c;text-align:center;padding:12px">Bibliothèque QR introuvable.<br>Rechargez la page (F5).</div>';
            return;
        }
        fetch(QR_URL, { credentials: 'same-origin', cache: 'no-store' })
            .then(function (r) { return r.json(); })
            .then(function (j) {
                if (!j || !j.success) return;
                expireLe = j.expire_le * 1000;
                dessiner(j.code);
                tick();
            })
            .catch(function () { });
    }

    function tick() {
        if (!expireLe) return;
        var restant = Math.max(0, (expireLe - maintenant()) / 1000);
        document.getElementById('restant').textContent = Math.ceil(restant);
        document.getElementById('barre').style.width = (restant / QR_DUREE * 100) + '%';
        if (restant <= 1.5) {
            charger();
        }
    }

    document.getElementById('btnActualiser').addEventListener('click', charger);
    charger();
    setInterval(tick, 500);
    </script>

</body>
</html>
