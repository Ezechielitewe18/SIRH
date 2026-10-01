<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan de présence — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="<?= APP_URL ?>/public/js/jsQR.js"></script>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: #fff;
            padding: 20px;
            background: #05060f;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -2;
            background:
                radial-gradient(650px circle at 15% 5%, rgba(139, 92, 246, .26), transparent 60%),
                radial-gradient(550px circle at 90% 90%, rgba(59, 130, 246, .20), transparent 60%),
                linear-gradient(160deg, #05060f 0%, #0d0a1e 60%, #141031 100%);
        }

        .wrap { max-width: 1120px; margin: 0 auto; }

        .top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }
        .brand {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
            background: linear-gradient(120deg, #e2e8f0, #c7d2fe 50%, #a78bfa);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .top h1 { margin-top: 6px; font-size: 21px; font-weight: 700; }
        .top .meta { margin-top: 4px; font-size: 13px; color: #98a2c4; }

        .pill {
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .14);
            color: #c4b5fd;
        }
        .pill a { color: #c4b5fd; text-decoration: none; }
        .pill a:hover { text-decoration: underline; }

        .grid { display: grid; grid-template-columns: 1.35fr 1fr; gap: 18px; align-items: start; }

        .card {
            background: rgba(255, 255, 255, .045);
            -webkit-backdrop-filter: blur(18px);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 0 50px rgba(124, 58, 237, .15), inset 0 1px 0 rgba(255, 255, 255, .06);
        }
        .card h2 { font-size: 15px; font-weight: 700; margin-bottom: 14px; color: #e2e8f0; }

        /* Caméra */
        .camera {
            position: relative;
            background: #0b0f1a;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .10);
            aspect-ratio: 4 / 3;
            display: grid;
            place-items: center;
        }
        .camera video { width: 100%; height: 100%; object-fit: cover; display: block; }
        .camera canvas { display: none; }
        .camera .placeholder {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            text-align: center;
            padding: 24px;
            font-size: 13px;
            color: #98a2c4;
            line-height: 1.6;
        }
        .camera.on .placeholder { display: none; }
        .reticle {
            position: absolute;
            inset: 16%;
            border: 2px dashed rgba(196, 181, 253, .55);
            border-radius: 18px;
            pointer-events: none;
            opacity: 0;
            transition: opacity .3s ease;
        }
        .camera.on .reticle { opacity: 1; }

        .actions { display: flex; gap: 10px; margin-top: 14px; }
        .btn {
            height: 44px;
            padding: 0 18px;
            border-radius: 13px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, .16);
            background: rgba(255, 255, 255, .06);
            color: #e2e8f0;
            transition: transform .2s ease, filter .2s ease;
        }
        .btn:active { transform: scale(.98); }
        .btn-primary {
            flex: 1;
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            border-color: rgba(255, 255, 255, .18);
            color: #fff;
        }

        /* Lecteur / saisie manuelle */
        .saisie { margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(255, 255, 255, .08); }
        .saisie label { display: block; font-size: 12px; color: #98a2c4; margin-bottom: 7px; }
        .saisie-row { display: flex; gap: 10px; }
        .saisie input {
            flex: 1;
            height: 44px;
            padding: 0 14px;
            font-size: 13px;
            font-family: inherit;
            color: #fff;
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 12px;
            outline: none;
        }
        .saisie input:focus { border-color: rgba(139, 92, 246, .7); box-shadow: 0 0 0 4px rgba(139, 92, 246, .14); }
        .aide { margin-top: 8px; font-size: 11px; color: #7c86ab; line-height: 1.5; }

        /* Résultat */
        .resultat {
            margin-top: 16px;
            border-radius: 16px;
            padding: 18px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .04);
            min-height: 128px;
            display: grid;
            place-items: center;
        }
        .resultat .attente { font-size: 13px; color: #7c86ab; }
        .resultat.ok { background: rgba(34, 197, 94, .10); border-color: rgba(34, 197, 94, .32); }
        .resultat.err { background: rgba(239, 68, 68, .10); border-color: rgba(239, 68, 68, .32); }
        .resultat .nom { font-size: 21px; font-weight: 800; margin-bottom: 3px; }
        .resultat .info { font-size: 12px; color: #98a2c4; }
        .resultat .msg { font-size: 15px; font-weight: 700; }
        .resultat.ok .msg { color: #86efac; }
        .resultat.err .msg { color: #fca5a5; }
        .badge {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .badge.arrivee { background: rgba(34, 197, 94, .18); color: #86efac; }
        .badge.depart { background: rgba(59, 130, 246, .18); color: #93c5fd; }

        /* Journal */
        .journal { margin-top: 14px; }
        .ligne {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .07);
            margin-bottom: 8px;
            font-size: 13px;
        }
        .ligne .who { font-weight: 600; }
        .ligne .when { font-size: 12px; color: #98a2c4; white-space: nowrap; }
        .vide { font-size: 12px; color: #7c86ab; text-align: center; padding: 22px 8px; line-height: 1.6; }
        .compteur {
            display: flex;
            gap: 14px;
            margin-bottom: 14px;
        }
        .compteur div {
            flex: 1;
            text-align: center;
            padding: 12px 8px;
            border-radius: 14px;
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .08);
        }
        .compteur b { display: block; font-size: 26px; font-weight: 800; }
        .compteur span { font-size: 11px; color: #98a2c4; }
        .c-arr b { color: #86efac; }
        .c-dep b { color: #93c5fd; }

        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrap">

    <div class="top">
        <div>
            <div class="brand">GLOBIT</div>
            <h1>Scan de présence — Réception</h1>
            <div class="meta">Présentez le QR de l'employé dans le cadre. 1<sup>er</sup> scan = arrivée, 2<sup>e</sup> = départ.</div>
        </div>
        <div class="pill">Session <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?> · <a href="<?= APP_URL ?>/presences">Présences</a></div>
    </div>

    <div class="grid">

        <!-- Caméra + saisie -->
        <div class="card">
            <h2>Scanner un QR</h2>

            <div class="camera" id="camera">
                <video id="video" playsinline muted></video>
                <canvas id="canvas"></canvas>
                <div class="reticle"></div>
                <div class="placeholder" id="placeholder">
                    Caméra inactive.<br>Cliquez sur « Activer la caméra » pour scanner le QR de l'employé.
                </div>
            </div>

            <div class="actions">
                <button type="button" class="btn btn-primary" id="btnCamera">Activer la caméra</button>
                <button type="button" class="btn" id="btnPause" disabled>Pause</button>
            </div>

            <div class="saisie">
                <label>Lecteur code-barres / saisie manuelle du code</label>
                <div class="saisie-row">
                    <input type="text" id="codeInput" placeholder="Le code s'inscrit ici via le lecteur…" autocomplete="off">
                    <button type="button" class="btn" id="btnValider">Pointer</button>
                </div>
                <div class="aide">Le lecteur USB se comporte comme un clavier : scannez le QR, puis « Pointer ». Le code n'est jamais affiché sur le téléphone de l'employé.</div>
            </div>

            <div class="resultat" id="resultat">
                <div class="attente" id="attente">En attente d'un QR…</div>
            </div>
        </div>

        <!-- Journal du jour -->
        <div class="card">
            <h2>Journal du jour</h2>

            <div class="compteur">
                <div class="c-arr"><b><?= count(array_filter($scans, fn($s) => !empty($s['heure_arrivee']))) ?></b><span>arrivées</span></div>
                <div class="c-dep"><b><?= count(array_filter($scans, fn($s) => !empty($s['heure_depart']))) ?></b><span>départs</span></div>
            </div>

            <div class="journal" id="journal">
                <?php if (empty($scans)): ?>
                <div class="vide">Aucun pointage par QR aujourd'hui.<br>Les scans apparaîtront ici en temps réel.</div>
                <?php else: foreach ($scans as $s): ?>
                <div class="ligne">
                    <span class="who"><?= htmlspecialchars($s['prenom'] . ' ' . $s['nom']) ?></span>
                    <span class="when">
                        <?= !empty($s['heure_arrivee']) ? htmlspecialchars(substr($s['heure_arrivee'], 0, 5)) : '—' ?>
                        <?php if (!empty($s['heure_depart'])): ?> → <?= htmlspecialchars(substr($s['heure_depart'], 0, 5)) ?><?php endif; ?>
                    </span>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
var SCAN_URL = <?= json_encode(APP_URL . '/presences/scan') ?>;
var CSRF = <?= json_encode(csrf_token()) ?>;

/* Son de confirmation */
function bip(ok) {
    try {
        var ctx = new (window.AudioContext || window.webkitAudioContext)();
        var osc = ctx.createOscillator();
        var gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.value = ok ? 880 : 220;
        gain.gain.setValueAtTime(0.08, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.25);
        osc.start(); osc.stop(ctx.currentTime + 0.25);
    } catch (e) {}
}

function afficher(html, etat) {
    var box = document.getElementById('resultat');
    box.className = 'resultat' + (etat ? ' ' + etat : '');
    box.innerHTML = html;
}

/* Traitement d'un code */
function pointer(code) {
    var body = new URLSearchParams();
    body.append('code', code);
    body.append('csrf_token', CSRF);

    afficher('<div class="attente">Validation…</div>', '');

    fetch(SCAN_URL, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: body.toString()
    })
        .then(function (r) { return r.json(); })
        .then(function (j) {
            bip(j.success);
            if (j.success) {
                var e = j.employe || {};
                afficher(
                    '<div><div class="nom">' + (e.prenom || '') + ' ' + (e.nom || '') + '</div>' +
                    '<div class="info">' + (e.matricule || '') + '</div>' +
                    '<div class="msg">' + j.message + '</div>' +
                    '<div class="badge ' + (j.action || '') + '">' + (j.action === 'depart' ? 'Départ' : 'Arrivée') + '</div></div>',
                    'ok'
                );
                rechargerJournal();
            } else {
                afficher('<div><div class="msg">' + j.message + '</div></div>', 'err');
            }
            setTimeout(function () {
                afficher('<div class="attente">En attente d\'un QR…</div>', '');
                if (actif) scanner(true);
            }, 2500);
        })
        .catch(function () {
            bip(false);
            afficher('<div><div class="msg">Erreur réseau. Réessayez.</div></div>', 'err');
        });
}

/* Journal */
function rechargerJournal() {
    fetch(window.location.href, { credentials: 'same-origin', cache: 'no-store' })
        .then(function (r) { return r.text(); })
        .then(function (html) {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var j = doc.getElementById('journal');
            var c = doc.querySelector('.compteur');
            if (j) document.getElementById('journal').innerHTML = j.innerHTML;
            if (c) document.querySelector('.compteur').innerHTML = c.innerHTML;
        })
        .catch(function () {});
}

/* Caméra + décodage jsQR */
var video = document.getElementById('video');
var canvas = document.getElementById('canvas');
var ctx = canvas.getContext('2d', { willReadFrequently: true });
var actif = false;
var stream = null;
var dernierScan = 0;

function scanner(respecterPause) {
    if (!actif) return;
    requestAnimationFrame(function () { scanner(true); });
    if (respecterPause && Date.now() - dernierScan < 900) return;
    if (!video.videoWidth) return;

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    var img = ctx.getImageData(0, 0, canvas.width, canvas.height);
    var res = window.jsQR(img.data, img.width, img.height, { inversionAttempts: 'attemptBoth' });
    if (res && res.data) {
        dernierScan = Date.now();
        actif = false;
        document.getElementById('btnPause').disabled = true;
        pointer(res.data);
    }
}

document.getElementById('btnCamera').addEventListener('click', function () {
    if (stream) {
        stream.getTracks().forEach(function (t) { t.stop(); });
        stream = null;
        actif = false;
        document.getElementById('camera').classList.remove('on');
        this.textContent = 'Activer la caméra';
        document.getElementById('btnPause').disabled = true;
        return;
    }
    navigator.mediaDevices.getUserMedia({ video: { width: { ideal: 1280 }, height: { ideal: 720 } } })
        .then(function (s) {
            stream = s;
            video.srcObject = s;
            video.play();
            actif = true;
            document.getElementById('camera').classList.add('on');
            document.getElementById('btnPause').disabled = false;
            this.textContent = 'Désactiver la caméra';
        }.bind(this))
        .catch(function () {
            afficher('<div><div class="msg">Caméra inaccessible. Autorisez l\'accès caméra, ou utilisez le lecteur / la saisie manuelle.</div></div>', 'err');
        });
});

document.getElementById('btnPause').addEventListener('click', function () {
    actif = !actif;
    this.textContent = actif ? 'Pause' : 'Reprendre';
});

document.getElementById('btnValider').addEventListener('click', function () {
    var champ = document.getElementById('codeInput');
    if (champ.value.trim() === '') return;
    pointer(champ.value.trim());
    champ.value = '';
});

document.getElementById('codeInput').addEventListener('keydown', function (ev) {
    if (ev.key === 'Enter') {
        ev.preventDefault();
        document.getElementById('btnValider').click();
    }
});
</script>
</body>
</html>