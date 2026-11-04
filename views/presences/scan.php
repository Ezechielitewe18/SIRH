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
                radial-gradient(650px circle at 15% 5%, rgba(23, 105, 170, .30), transparent 60%),
                radial-gradient(550px circle at 90% 90%, rgba(14, 143, 168, .22), transparent 60%),
                linear-gradient(160deg, #071b2f 0%, #0a2540 60%, #0d3350 100%);
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
            background: linear-gradient(120deg, #f4f9fd, #cfe6f3 50%, #7fd6e6);
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
            color: #9fd3e8;
        }
        .pill a { color: #9fd3e8; text-decoration: none; }
        .pill a:hover { text-decoration: underline; }

        .grid { display: grid; grid-template-columns: 1.35fr 1fr; gap: 18px; align-items: start; }

        .card {
            background: rgba(255, 255, 255, .045);
            -webkit-backdrop-filter: blur(18px);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 0 50px rgba(23, 105, 170, .20), inset 0 1px 0 rgba(255, 255, 255, .06);
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
            border: 2px dashed rgba(127, 214, 230, .65);
            border-radius: 18px;
            pointer-events: none;
            opacity: 0;
            transition: opacity .3s ease;
        }
        .camera.on .reticle { opacity: 1; }

        .actions { display: flex; gap: 10px; margin-top: 14px; }
        .scan-statut { margin-top: 10px; font-size: 12px; color: #98a2c4; min-height: 18px; line-height: 1.4; }
        .scan-statut.ok { color: #86efac; font-weight: 600; }
        .scan-statut.err { color: #fca5a5; }
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
            background: linear-gradient(135deg, #1769aa, #0e8fa8);
            border-color: rgba(255, 255, 255, .18);
            color: #fff;
        }

        /* Lecteur / saisie manuelle */
        .saisie { margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(255, 255, 255, .08); }
        .saisie label { display: block; font-size: 12px; color: #98a2c4; margin-bottom: 7px; }
        .saisie-row { display: flex; gap: 10px; }
        .saisie input {
            flex: 1;
            min-width: 0;
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
        .saisie input:focus { border-color: rgba(23, 105, 170, .85); box-shadow: 0 0 0 4px rgba(23, 105, 170, .18); }
        .saisie input.code-court { flex: 0 0 110px; text-align: center; letter-spacing: .18em; }
        @media (max-width: 520px) {
            .saisie-row { flex-wrap: wrap; }
            .saisie-row input, .saisie-row .btn { flex: 1 1 100% !important; }
        }
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
            <div class="brand">GLOBIT SAS</div>
            <h1>Scan de présence — Réception</h1>
            <div class="meta">Présentez le QR de l'employé dans le cadre. 1<sup>er</sup> scan = <strong>ARRIVÉE</strong> — 2<sup>e</sup> scan = <strong>DÉPART</strong>.</div>
        </div>
        <div class="pill">
            Session <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?> ·
            <a href="<?= APP_URL ?>/presences">Présences</a> ·
            <a href="<?= APP_URL ?>/dashboard">Tableau de bord</a> ·
            <a href="#" id="btnPleinEcranScan">Plein écran</a>
        </div>
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
            <div class="scan-statut" id="scanStatut" aria-live="polite"></div>

            <div class="saisie">
                <label>Lecteur code-barres / saisie manuelle du code</label>
                <div class="saisie-row">
                    <input type="text" id="codeInput" placeholder="Le code s'inscrit ici via le lecteur…" autocomplete="off">
                    <button type="button" class="btn" id="btnValider">Pointer</button>
                </div>
                <div class="aide">Le lecteur USB se comporte comme un clavier : scannez le QR, puis « Pointer ». Le jeton du QR n'est jamais affiché en clair.</div>
            </div>

            <div class="saisie">
                <label>Code de secours — matricule + code à 6 chiffres (sans caméra)</label>
                <div class="saisie-row">
                    <input type="text" id="matriculeInput" placeholder="Ex. EMP-2026-0001" autocomplete="off">
                    <input type="text" id="codeCourtInput" class="code-court" placeholder="6 chiffres" inputmode="numeric" maxlength="6" autocomplete="off">
                    <button type="button" class="btn" id="btnValiderCode">Pointer</button>
                </div>
                <div class="aide">Le code change toutes les 30 s : il s'affiche sous le QR, sur le téléphone de l'employé.</div>
            </div>

            <div class="resultat" id="resultat" role="status" aria-live="polite" aria-atomic="true">
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
var LOGIN_URL = <?= json_encode(APP_URL . '/login') ?>;
var CSRF = <?= json_encode(csrf_token()) ?>;

/* Les noms et messages viennent de la base : on les echappe avant innerHTML. */
function esc(v) {
    return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
}

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
function envoyerPointage(body) {
    body.append('csrf_token', CSRF);

    afficher('<div class="attente">Validation…</div>', '');

    fetch(SCAN_URL, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: body.toString()
    })
        .then(function (r) {
            if (r.redirected || r.status === 401 || r.status === 403) throw new Error('session');
            return r.json();
        })
        .then(function (j) {
            bip(j.success);
            if (j.success) {
                var e = j.employe || {};
                var action = j.action === 'depart' ? 'depart' : 'arrivee';
                afficher(
                    '<div><div class="nom">' + esc(e.prenom) + ' ' + esc(e.nom) + '</div>' +
                    '<div class="info">' + esc(e.matricule) + '</div>' +
                    '<div class="msg">' + esc(j.message) + '</div>' +
                    '<div class="badge ' + action + '">' + (action === 'depart' ? 'Départ' : 'Arrivée') + '</div></div>',
                    'ok'
                );
                rechargerJournal();
            } else {
                afficher('<div><div class="msg">' + esc(j.message) + '</div></div>', 'err');
            }
            setTimeout(function () {
                afficher('<div class="attente">En attente d\'un QR…</div>', '');
                if (actif) scanner(true);
            }, 2500);
        })
        .catch(function (err) {
            bip(false);
            if (err && err.message === 'session') {
                afficher('<div><div class="msg">Session expirée. <a href="' + LOGIN_URL + '" style="color:#fca5a5;text-decoration:underline">Se reconnecter</a>.</div></div>', 'err');
                return;
            }
            afficher('<div><div class="msg">Erreur réseau. Réessayez.</div></div>', 'err');
        });
}

function pointer(code) {
    var body = new URLSearchParams();
    body.append('code', code);
    envoyerPointage(body);
}

/* Pointage sans camera : matricule + code de secours a 6 chiffres. */
function pointerCode() {
    var mat = document.getElementById('matriculeInput').value.trim().toUpperCase();
    var cc = document.getElementById('codeCourtInput').value.replace(/\D/g, '');

    if (mat === '') {
        afficher('<div><div class="msg">Indiquez le matricule de l\'employé.</div></div>', 'err');
        return;
    }
    if (cc.length !== 6) {
        afficher('<div><div class="msg">Le code de secours doit comporter 6 chiffres.</div></div>', 'err');
        return;
    }

    var body = new URLSearchParams();
    body.append('matricule', mat);
    body.append('code_court', cc);
    document.getElementById('codeCourtInput').value = '';
    envoyerPointage(body);
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
var statutEl = document.getElementById('scanStatut');
var actif = false;
var stream = null;
var dernierScan = 0;
var imagesAnalysees = 0;

function etatScan(texte, classe) {
    if (!statutEl) return;
    statutEl.textContent = texte;
    statutEl.className = 'scan-statut' + (classe ? ' ' + classe : '');
}

function scanner(respecterPause) {
    if (!actif) return;
    requestAnimationFrame(function () { scanner(true); });
    if (respecterPause && Date.now() - dernierScan < 900) return;
    if (!video.videoWidth) return;

    /* Redimensionner a 800 px max : jsQR decode beaucoup plus vite sur mobile. */
    var largeurCible = Math.min(800, video.videoWidth);
    var ratio = largeurCible / video.videoWidth;
    canvas.width = Math.round(video.videoWidth * ratio);
    canvas.height = Math.round(video.videoHeight * ratio);
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    var img = ctx.getImageData(0, 0, canvas.width, canvas.height);
    var res = null;
    try {
        res = window.jsQR(img.data, img.width, img.height, { inversionAttempts: 'attemptBoth' });
    } catch (e) {
        etatScan('Erreur de décodage : ' + (e && e.message ? e.message : 'inconnue'), 'err');
        return;
    }
    imagesAnalysees++;
    if (imagesAnalysees % 25 === 0 && !res) {
        etatScan('Analyse… ' + imagesAnalysees + ' images (' + canvas.width + '×' + canvas.height + ')');
    }
    if (res && res.data) {
        etatScan('QR détecté — validation…', 'ok');
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
        etatScan('');
        return;
    }
    /* Les navigateurs n'autorisent la camera qu'en HTTPS (ou sur localhost).
       Sans ce garde-fou, l'acces direct a navigator.mediaDevices echoue en
       silence sur une adresse http:// du reseau local. */
    if (!window.isSecureContext || !navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
        etatScan('Caméra bloquée : page non sécurisée. Ouvrez-la en https://', 'err');
        afficher('<div><div class="msg">Caméra bloquée : cette page doit être ouverte en HTTPS.</div>' +
            '<div class="info" style="margin-top:6px;">Remplacez « http:// » par « https:// » dans l\'adresse, acceptez le certificat, puis réessayez. Sinon utilisez le lecteur / la saisie manuelle.</div></div>', 'err');
        return;
    }
    etatScan('Demande d\'accès à la caméra…');
    navigator.mediaDevices.getUserMedia({ video: { width: { ideal: 1280 }, height: { ideal: 720 } } })
        .then(function (s) {
            stream = s;
            video.srcObject = s;
            var lecture = video.play();
            if (lecture && lecture.catch) {
                lecture.catch(function () {
                    etatScan('Flux caméra chargé mais lecture refusée. Touchez à nouveau « Activer la caméra ».', 'err');
                });
            }
            actif = true;
            imagesAnalysees = 0;
            document.getElementById('camera').classList.add('on');
            document.getElementById('btnPause').disabled = false;
            this.textContent = 'Désactiver la caméra';
            etatScan('Caméra active — présentez le QR dans le cadre.');
        }.bind(this))
        .catch(function (err) {
            var motif = err && err.name ? err.name : 'inconnue';
            etatScan('Caméra inaccessible (' + motif + ').', 'err');
            afficher('<div><div class="msg">Caméra inaccessible (' + esc(motif) + ').</div>' +
                '<div class="info" style="margin-top:6px;">Autorisez l\'accès caméra dans le navigateur, ou utilisez le lecteur / la saisie manuelle.</div></div>', 'err');
        });
});

/* Message preventif : en HTTP, la camera restera bloquee par le navigateur. */
if (!window.isSecureContext) {
    var phScan = document.getElementById('placeholder');
    if (phScan) {
        phScan.innerHTML = 'Caméra indisponible en HTTP.<br>Ouvrez cette page en <strong>https://</strong> pour scanner, ou utilisez la saisie manuelle ci-dessous.';
    }
}

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

document.getElementById('btnValiderCode').addEventListener('click', pointerCode);

['matriculeInput', 'codeCourtInput'].forEach(function (id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('keydown', function (ev) {
        if (ev.key === 'Enter') {
            ev.preventDefault();
            pointerCode();
        }
    });
});

/* Confort de la borne : plein ecran, ecran allume, journal actualise. */
var btnPlein = document.getElementById('btnPleinEcranScan');
if (btnPlein) {
    btnPlein.addEventListener('click', function (ev) {
        ev.preventDefault();
        var el = document.documentElement;
        if (document.fullscreenElement && document.exitFullscreen) {
            document.exitFullscreen();
        } else if (el.requestFullscreen) {
            el.requestFullscreen().catch(function () {});
        }
    });
}

var wakeLock = null;
function garderEcranAllume() {
    if (!('wakeLock' in navigator)) return;
    navigator.wakeLock.request('screen').then(function (l) {
        wakeLock = l;
        l.addEventListener('release', function () { wakeLock = null; });
    }).catch(function () {});
}

document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'visible') {
        garderEcranAllume();
        rechargerJournal();
    }
});
garderEcranAllume();
setInterval(function () {
    if (document.visibilityState === 'visible') rechargerJournal();
}, 15000);

document.getElementById('codeInput').focus();
</script>
</body>
</html>