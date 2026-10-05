<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mon QR de présence — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="<?= APP_URL ?>/public/js/qrcode.min.js"></script>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 22px 18px 30px;
            color: #fff;
            position: relative;
            background: #05060f;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -2;
            background:
                radial-gradient(600px circle at 50% 8%, rgba(139, 92, 246, .30), transparent 60%),
                radial-gradient(500px circle at 85% 85%, rgba(59, 130, 246, .22), transparent 60%),
                linear-gradient(160deg, #05060f 0%, #0d0a1e 55%, #171034 100%);
        }

        .head { text-align: center; margin-bottom: 18px; }
        .brand {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
            background: linear-gradient(120deg, #e2e8f0, #c7d2fe 50%, #a78bfa);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        h1 { margin-top: 8px; font-size: 20px; font-weight: 700; }
        .sub { margin-top: 5px; font-size: 13px; color: #98a2c4; }

        .card {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, .05);
            -webkit-backdrop-filter: blur(18px);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 24px;
            padding: 22px 20px;
            box-shadow: 0 0 50px rgba(124, 58, 237, .20), inset 0 1px 0 rgba(255, 255, 255, .07);
            text-align: center;
        }

        .identite {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 16px;
        }
        .avatar {
            width: 40px; height: 40px;
            border-radius: 12px;
            display: grid; place-items: center;
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            font-weight: 800; font-size: 15px;
            flex-shrink: 0;
        }
        .identite .nom { font-size: 15px; font-weight: 700; text-align: left; line-height: 1.25; }
        .identite .meta { font-size: 11px; color: #98a2c4; text-align: left; }

        /* Cadre du QR */
        .qr-frame {
            background: #fff;
            border-radius: 18px;
            padding: 14px;
            display: inline-block;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
            position: relative;
        }
        #qrcode { width: 232px; height: 232px; }
        #qrcode img, #qrcode canvas { display: block; width: 232px !important; height: 232px !important; }

        /* Jauge 30s */
        .jauge {
            margin: 16px auto 8px;
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, .10);
            border-radius: 6px;
            overflow: hidden;
        }
        .jauge span {
            display: block;
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #a78bfa);
            border-radius: 6px;
            transition: width .5s linear;
        }
        .chrono { font-size: 12px; color: #98a2c4; }
        .chrono b { color: #c4b5fd; font-size: 15px; }

        .consigne {
            margin-top: 16px;
            font-size: 14px;
            line-height: 1.5;
            color: #e2e8f0;
        }

        .etat {
            margin-top: 16px;
            padding: 13px 14px;
            border-radius: 14px;
            font-size: 13px;
            line-height: 1.5;
            border: 1px solid transparent;
        }
        .etat.ok { background: rgba(34, 197, 94, .10); border-color: rgba(34, 197, 94, .25); color: #86efac; }
        .etat.info { background: rgba(59, 130, 246, .10); border-color: rgba(59, 130, 246, .25); color: #93c5fd; }
        .etat.warn { background: rgba(245, 158, 11, .10); border-color: rgba(245, 158, 11, .25); color: #fcd34d; }
        .etat b { display: block; font-size: 14px; margin-bottom: 2px; }

        .note {
            margin-top: 14px;
            font-size: 11px;
            color: #7c86ab;
            line-height: 1.5;
        }

        .actions { margin-top: 18px; display: flex; gap: 10px; }
        .btn {
            flex: 1;
            height: 44px;
            border-radius: 13px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform .2s ease, filter .2s ease;
        }
        .btn:active { transform: scale(.98); }
        .btn-outline {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .16);
            color: #e2e8f0;
        }
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            border: 1px solid rgba(255, 255, 255, .18);
            color: #fff;
        }

        .erreur {
            margin-top: 18px;
            padding: 13px 15px;
            border-radius: 13px;
            font-size: 13px;
            background: rgba(239, 68, 68, .10);
            border: 1px solid rgba(239, 68, 68, .25);
            color: #fca5a5;
        }

        @media (max-width: 400px) {
            #qrcode, #qrcode img, #qrcode canvas { width: 200px !important; height: 200px !important; }
            .qr-frame { padding: 12px; }
        }
    </style>
</head>
<body>

    <div class="head">
        <div class="brand">GLOBIT SAAS</div>
        <h1>Mon QR de présence</h1>
        <div class="sub">Présentez ce code à la réception</div>
    </div>

    <div class="card">
        <div class="identite">
            <div class="avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($employe['prenom'], 0, 1))) ?></div>
            <div>
                <div class="nom"><?= htmlspecialchars($employe['prenom'] . ' ' . $employe['nom']) ?></div>
                <div class="meta"><?= htmlspecialchars($employe['matricule']) ?><?= $employe['poste'] ? ' · ' . htmlspecialchars($employe['poste']) : '' ?></div>
            </div>
        </div>

        <div class="qr-frame">
            <div id="qrcode"></div>
        </div>

        <div class="jauge"><span id="barre"></span></div>
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
            <a class="btn btn-outline" href="<?= APP_URL ?>/presences">Présences</a>
            <button type="button" class="btn btn-primary" id="btnActualiser">Actualiser</button>
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
            document.getElementById('qrcode').innerHTML = '<div style="display:grid;place-items:center;height:100%;font-size:12px;color:#ef4444;text-align:center;padding:12px">Bibliothèque QR introuvable.<br>Rechargez la page (F5).</div>';
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