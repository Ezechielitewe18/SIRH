<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Présentation GLOBIT - SIRH</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#0b0f1a;
  --panel:#121a2b;
  --panel2:#0f1625;
  --accent:#3b82f6;
  --accent2:#6366f1;
  --cyan:#22d3ee;
  --text:#e2e8f0;
  --muted:#8aa0bd;
  --gold:#f59e0b;
}
*{margin:0;padding:0;box-sizing:border-box}
body{
  background:var(--bg);
  color:var(--text);
  font-family:'Inter',sans-serif;
  overflow:hidden;
  height:100vh;
}
/* Fond décoratif */
.bg-gradient{
  position:fixed;inset:0;z-index:0;
  background:
    radial-gradient(900px circle at 15% 10%, rgba(99,102,241,.15), transparent 45%),
    radial-gradient(800px circle at 85% 90%, rgba(34,211,238,.12), transparent 45%),
    radial-gradient(600px circle at 80% 15%, rgba(59,130,246,.10), transparent 50%),
    var(--bg);
}
.grid-lines{
  position:fixed;inset:0;z-index:0;opacity:.05;
  background-image:linear-gradient(rgba(255,255,255,.5) 1px,transparent 1px),
                   linear-gradient(90deg,rgba(255,255,255,.5) 1px,transparent 1px);
  background-size:60px 60px;
}
/* Barre logo fixe */
.topbar{
  position:fixed;top:0;left:0;right:0;z-index:20;
  display:flex;align-items:center;justify-content:space-between;
  padding:18px 40px;
}
.brand{
  display:flex;align-items:center;gap:12px;
  font-family:'Poppins',sans-serif;font-weight:800;font-size:24px;letter-spacing:1px;
}
.brand .logo{
  width:42px;height:42px;border-radius:10px;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 6px 20px rgba(99,102,241,.4);
}
.brand .logo span{color:#fff;font-size:20px;font-weight:800}
.brand .dot{color:var(--cyan)}
.brand small{display:block;font-family:'Inter';font-weight:500;font-size:11px;letter-spacing:3px;color:var(--muted)}
/* Compteur / barre */
.slide-meta{
  display:flex;align-items:center;gap:18px;color:var(--muted);font-size:14px;
}
.progress{width:0;height:4px;background:linear-gradient(90deg,var(--accent),var(--cyan));transition:width .1s linear;border-radius:4px}
.progress-wrap{position:fixed;top:0;left:0;right:0;z-index:21;height:4px;background:rgba(255,255,255,.06)}
/* Zone slide */
.stage{
  position:relative;z-index:5;
  height:100vh;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:90px 60px 40px;
}
.slide{display:none;width:100%;max-width:1300px;animation:fadeIn .5s ease}
.slide.active{display:block}
@keyframes fadeIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

/* Titre du slide */
.slide-head{text-align:center;margin-bottom:30px}
.slide-kicker{
  display:inline-flex;align-items:center;gap:8px;
  color:var(--cyan);font-size:13px;font-weight:700;letter-spacing:3px;text-transform:uppercase;
  margin-bottom:10px;
}
.slide-kicker .line{width:34px;height:2px;background:linear-gradient(90deg,transparent,var(--cyan))}
.slide-kicker .line.r{background:linear-gradient(90deg,var(--cyan),transparent)}
h2.slide-title{
  font-family:'Poppins',sans-serif;font-weight:800;font-size:44px;line-height:1.1;
  background:linear-gradient(90deg,#fff,#b8c2d6);
  -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;
}
.slide-desc{color:var(--muted);font-size:17px;margin-top:12px;max-width:760px;margin-left:auto;margin-right:auto}
/* Contenu slide */
.slide-body{
  display:flex;gap:32px;align-items:stretch;
}
.shot{
  flex:1.2;position:relative;border-radius:16px;overflow:hidden;
  border:1px solid rgba(255,255,255,.08);
  background:var(--panel2);
  box-shadow:0 24px 60px rgba(0,0,0,.5);
}
.shot img{display:block;width:100%;height:100%;object-fit:contain;background:#0a0f1c}
.shot .frame-top{height:26px;background:linear-gradient(180deg,#1a2334,#151d2c);display:flex;align-items:center;gap:6px;padding:0 12px;border-bottom:1px solid rgba(255,255,255,.06)}
.shot .frame-top i{width:10px;height:10px;border-radius:50%;display:inline-block}
.shot .frame-top .r1{background:#ff5f57}.shot .frame-top .r2{background:#febc2e}.shot .frame-top .r3{background:#28c840}
.shot .frame-top span{margin-left:8px;font-size:11px;color:#64748b}
.shot-body{height:calc(100% - 26px)}
.features{
  flex:1;display:flex;flex-direction:column;gap:14px;justify-content:center;
}
.feature{
  display:flex;gap:14px;align-items:flex-start;
  background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);
  border-radius:12px;padding:16px 18px;
  transition:transform .2s,border-color .2s;
}
.feature:hover{transform:translateX(4px);border-color:rgba(59,130,246,.4)}
.feature .icon{
  flex-shrink:0;width:42px;height:42px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;font-size:18px;
  background:linear-gradient(135deg,rgba(59,130,246,.2),rgba(99,102,241,.2));
  color:var(--cyan);
}
.feature h4{font-size:16px;font-weight:700;color:#fff;margin-bottom:2px}
.feature p{font-size:13.5px;color:var(--muted);line-height:1.45}

/* Slide hero (intro) */
.slide.hero{text-align:center}
.hero .big-logo{
  width:120px;height:120px;border-radius:28px;margin:0 auto 26px;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 20px 50px rgba(99,102,241,.5);
}
.hero .big-logo span{font-size:56px;color:#fff;font-weight:800;font-family:'Poppins'}
.hero h1{
  font-family:'Poppins';font-weight:800;font-size:72px;line-height:1;
  background:linear-gradient(90deg,#fff 20%,var(--cyan));
  -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;
}
.hero .tagline{font-size:22px;color:var(--muted);margin-top:18px;font-weight:500}
.hero .chips{display:flex;gap:12px;justify-content:center;margin-top:34px;flex-wrap:wrap}
.hero .chip{
  padding:10px 20px;border-radius:30px;font-size:14px;font-weight:600;
  background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:var(--text);
}
.hero .chip i{margin-right:8px;color:var(--cyan)}

/* Slide final (merci) */
.slide.merci{text-align:center}
.merci h2{font-family:'Poppins';font-weight:800;font-size:58px;background:linear-gradient(90deg,#fff,var(--cyan));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.merci .sub{font-size:20px;color:var(--muted);margin-top:16px}
.merci .btn-contact{
  display:inline-block;margin-top:30px;padding:14px 34px;border-radius:12px;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  color:#fff;font-weight:700;font-size:15px;text-decoration:none;
  box-shadow:0 12px 30px rgba(99,102,241,.4);
}

/* Navigation */
.nav-arrow{
  position:fixed;top:50%;transform:translateY(-50%);z-index:30;
  width:56px;height:56px;border-radius:50%;
  background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);
  color:#fff;font-size:22px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  transition:background .2s,transform .2s;
  backdrop-filter:blur(6px);
}
.nav-arrow:hover{background:rgba(59,130,246,.3)}
.nav-arrow.prev{left:26px}
.nav-arrow.next{right:26px}
/* Points */
.dots{
  position:fixed;bottom:22px;left:50%;transform:translateX(-50%);z-index:30;
  display:flex;gap:10px;
}
.dot{
  width:30px;height:7px;border-radius:4px;background:rgba(255,255,255,.18);cursor:pointer;transition:all .25s;
}
.dot.active{background:linear-gradient(90deg,var(--accent),var(--cyan));width:44px}
/* Hint clavier */
.hint{
  position:fixed;bottom:22px;right:40px;z-index:30;
  color:var(--muted);font-size:12px;letter-spacing:1px;display:flex;align-items:center;gap:8px;
}
.hint kbd{
  background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);
  border-radius:5px;padding:3px 8px;font-size:11px;color:#fff;
}
.timer-btn{
  position:fixed;bottom:20px;left:40px;z-index:30;
  display:flex;align-items:center;gap:8px;
  background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.12);color:var(--muted);
  padding:8px 16px;border-radius:8px;cursor:pointer;font-size:13px;transition:all .2s;
}
.timer-btn:hover{color:#fff;border-color:rgba(59,130,246,.4)}
.timer-btn .pause{display:none}
body.auto .timer-btn .play{display:none}
body.auto .timer-btn .pause{display:inline}

/* Responsive */
@media(max-width:900px){
  .slide-body{flex-direction:column}
  .features{flex-direction:row;flex-wrap:wrap}
  .feature{flex:1 1 40%}
  h2.slide-title{font-size:30px}
  .hero h1{font-size:46px}
  .nav-arrow{width:44px;height:44px;font-size:18px}
}
</style>
</head>
<body class="auto">

<div class="bg-gradient"></div>
<div class="grid-lines"></div>
<div class="progress-wrap"><div class="progress" id="progress"></div></div>

<header class="topbar">
  <div class="brand">
    <div class="logo"><span>G</span></div>
    <div>
      GLOBIT<span class="dot">.</span>
      <small>SIRH &middot; SYSTEME D&rsquo;INFORMATION</small>
    </div>
  </div>
  <div class="slide-meta">
    <span id="counter">1 / 9</span>
  </div>
</header>

<div class="stage" id="stage">

  <!-- SLIDE 1 : Intro -->
  <section class="slide hero active" data-kicker="Bienvenue">
    <div class="big-logo"><span>G</span></div>
    <h1>GLOBIT</h1>
    <div class="tagline">La solution moderne de gestion des ressources humaines</div>
    <div class="chips">
      <div class="chip"><i class="fa-solid fa-users"></i>Employés</div>
      <div class="chip"><i class="fa-solid fa-clipboard-check"></i>Présences</div>
      <div class="chip"><i class="fa-solid fa-money-bill-wave"></i>Paie</div>
      <div class="chip"><i class="fa-solid fa-mobile-screen-button"></i>Mobile</div>
    </div>
  </section>

  <!-- SLIDE 2 : Dashboard -->
  <section class="slide" data-kicker="Vue d'ensemble">
    <div class="slide-head">
      <div class="slide-kicker"><span class="line"></span>Vue d'ensemble<span class="line r"></span></div>
      <h2 class="slide-title">Tableau de bord intelligent</h2>
      <p class="slide-desc">Tous les indicateurs clés de l'entreprise en un coup d'œil.</p>
    </div>
    <div class="slide-body">
      <div class="shot">
        <div class="frame-top"><i class="r1"></i><i class="r2"></i><i class="r3"></i><span>GLOBIT - Tableau de bord</span></div>
        <div class="shot-body"><img src="assets/demo/dashboard.png" alt="Tableau de bord"></div>
      </div>
      <div class="features">
        <div class="feature"><div class="icon"><i class="fa-solid fa-users"></i></div><div><h4>Effectifs</h4><p>Nombre total d'employés actifs et répartition par service.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-chart-line"></i></div><div><h4>Indicateurs</h4><p>Taux de présence, absences et demandes en attente en temps réel.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-arrow-trend-up"></i></div><div><h4>Évolution</h4><p>Graphique de la masse salariale sur les douze derniers mois.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-bolt"></i></div><div><h4>Réactif</h4><p>Accès rapide aux dernières activités et alertes.</p></div></div>
      </div>
    </div>
  </section>

  <!-- SLIDE 3 : Employés -->
  <section class="slide" data-kicker="Gestion">
    <div class="slide-head">
      <div class="slide-kicker"><span class="line"></span>Gestion<span class="line r"></span></div>
      <h2 class="slide-title">Fiches employés complètes</h2>
      <p class="slide-desc">Centralisez toutes les informations de chaque collaborateur.</p>
    </div>
    <div class="slide-body">
      <div class="shot">
        <div class="frame-top"><i class="r1"></i><i class="r2"></i><i class="r3"></i><span>GLOBIT - Employés</span></div>
        <div class="shot-body"><img src="assets/demo/employees.png" alt="Employés"></div>
      </div>
      <div class="features">
        <div class="feature"><div class="icon"><i class="fa-solid fa-address-card"></i></div><div><h4>Création rapide</h4><p>Ajoutez un collaborateur avec son matricule, service et fonction.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-pen-to-square"></i></div><div><h4>Modification</h4><p>Mettez à jour les informations en quelques clics.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-magnifying-glass"></i></div><div><h4>Recherche</h4><p>Trouvez un employé instantanément par nom ou matricule.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-folder-tree"></i></div><div><h4>Filtres</h4><p>Filtrez par service, statut ou date d'embauche.</p></div></div>
      </div>
    </div>
  </section>

  <!-- SLIDE 4 : Présences -->
  <section class="slide" data-kicker="Pointage">
    <div class="slide-head">
      <div class="slide-kicker"><span class="line"></span>Pointage<span class="line r"></span></div>
      <h2 class="slide-title">Présences & validation</h2>
      <p class="slide-desc">Suivi du temps de travail avec contrôle par la RH.</p>
    </div>
    <div class="slide-body">
      <div class="shot">
        <div class="frame-top"><i class="r1"></i><i class="r2"></i><i class="r3"></i><span>GLOBIT - Présences</span></div>
        <div class="shot-body"><img src="assets/demo/presences.png" alt="Présences"></div>
      </div>
      <div class="features">
        <div class="feature"><div class="icon"><i class="fa-solid fa-circle-check"></i></div><div><h4>Déclaration</h4><p>L'employé déclare son arrivée et son départ en un clic.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-magnifying-glass-chart"></i></div><div><h4>Validation RH</h4><p>La RH valide ou rejette chaque présence avec justification.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-mobile-screen-button"></i></div><div><h4>Pointage mobile</h4><p>Depuis le téléphone, où que vous soyez.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-clock"></i></div><div><h4>Heures réelles</h4><p>Calcul automatique des heures et des retards.</p></div></div>
      </div>
    </div>
  </section>

  <!-- SLIDE 5 : Congés -->
  <section class="slide" data-kicker="Absences">
    <div class="slide-head">
      <div class="slide-kicker"><span class="line"></span>Absences<span class="line r"></span></div>
      <h2 class="slide-title">Gestion des congés</h2>
      <p class="slide-desc">Des demandes de congé simples et entièrement traçables.</p>
    </div>
    <div class="slide-body">
      <div class="shot">
        <div class="frame-top"><i class="r1"></i><i class="r2"></i><i class="r3"></i><span>GLOBIT - Congés</span></div>
        <div class="shot-body"><img src="assets/demo/conges.png" alt="Congés"></div>
      </div>
      <div class="features">
        <div class="feature"><div class="icon"><i class="fa-solid fa-calendar-days"></i></div><div><h4>Demande en ligne</h4><p>L'employé soumet sa demande avec dates et motif.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-check-double"></i></div><div><h4>Validation</h4><p>Approuvez ou rejetez avec notification automatique.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-hashtag"></i></div><div><h4>Solde</h4><p>Suivi du solde de congés de chaque employé.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-bell"></i></div><div><h4>Notification</h4><p>L'employé est notifié en temps réel de la décision.</p></div></div>
      </div>
    </div>
  </section>

  <!-- SLIDE 6 : Paie -->
  <section class="slide" data-kicker="Finances">
    <div class="slide-head">
      <div class="slide-kicker"><span class="line"></span>Finances<span class="line r"></span></div>
      <h2 class="slide-title">Bulletins de paie</h2>
      <p class="slide-desc">Une génération de paie automatisée et fiable.</p>
    </div>
    <div class="slide-body">
      <div class="shot">
        <div class="frame-top"><i class="r1"></i><i class="r2"></i><i class="r3"></i><span>GLOBIT - Paie</span></div>
        <div class="shot-body"><img src="assets/demo/paie.png" alt="Paie"></div>
      </div>
      <div class="features">
        <div class="feature"><div class="icon"><i class="fa-solid fa-briefcase"></i></div><div><h4>Calcul auto</h4><p>Salaire de base, heures sup' et primes calculés automatiquement.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-file-lines"></i></div><div><h4>Bulletin complet</h4><p>Les charges et les cotisations détaillées pour chaque employé.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-folder-tree"></i></div><div><h4>Archives</h4><p>Tous les bulletins par mois, historisés et consultables.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-print"></i></div><div><h4>Export</h4><p>Export PDF ou Excel à tout moment.</p></div></div>
      </div>
    </div>
  </section>

  <!-- SLIDE 7 : Cartes QR -->
  <section class="slide" data-kicker="Sécurité">
    <div class="slide-head">
      <div class="slide-kicker"><span class="line"></span>Sécurité<span class="line r"></span></div>
      <h2 class="slide-title">Pointage par QR Code</h2>
      <p class="slide-desc">Des badges électroniques sécurisés réservés à la Direction.</p>
    </div>
    <div class="slide-body">
      <div class="shot">
        <div class="frame-top"><i class="r1"></i><i class="r2"></i><i class="r3"></i><span>GLOBIT - Cartes QR</span></div>
        <div class="shot-body"><img src="assets/demo/cartes.png" alt="Cartes QR"></div>
      </div>
      <div class="features">
        <div class="feature"><div class="icon"><i class="fa-solid fa-qrcode"></i></div><div><h4>Génération</h4><p>Créez un badge QR unique pour chaque employé.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-camera"></i></div><div><h4>Scannage</h4><p>Pointer en scannant le QR à l'entrée.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-shield-halved"></i></div><div><h4>Réservé DG</h4><p>Le pointage QR est uniquement réservé à la Direction Générale.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-receipt"></i></div><div><h4>Traçabilité</h4><p>Chaque scan est enregistré dans le journal d'activité.</p></div></div>
      </div>
    </div>
  </section>

  <!-- SLIDE 8 : Rapports -->
  <section class="slide" data-kicker="Analyse">
    <div class="slide-head">
      <div class="slide-kicker"><span class="line"></span>Analyse<span class="line r"></span></div>
      <h2 class="slide-title">Rapports & journaux</h2>
      <p class="slide-desc">Des rapports décisionnels et un audit complet des actions.</p>
    </div>
    <div class="slide-body">
      <div class="shot">
        <div class="frame-top"><i class="r1"></i><i class="r2"></i><i class="r3"></i><span>GLOBIT - Rapports</span></div>
        <div class="shot-body"><img src="assets/demo/rapports.png" alt="Rapports"></div>
      </div>
      <div class="features">
        <div class="feature"><div class="icon"><i class="fa-solid fa-chart-line"></i></div><div><h4>Absentéisme</h4><p>Taux d'absence par employé et par mois.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-chart-line"></i></div><div><h4>Personnel</h4><p>Répartition des effectifs par service et par genre.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-receipt"></i></div><div><h4>Congés</h4><p>Synthèse des absences et des demandes.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-book-open"></i></div><div><h4>Journal</h4><p>Audit de toutes les actions pour la sécurité.</p></div></div>
      </div>
    </div>
  </section>

  <!-- SLIDE 9 : Merci -->
  <section class="slide merci" data-kicker="Conclusion">
    <h2>Merci !</h2>
    <div class="sub">GLOBIT — le référentiel RH de votre entreprise.</div>
    <p style="margin-top:28px;color:var(--muted);font-size:15px">Présences &middot; Congés &middot; Paie &middot; Formations &middot; Rapports</p>
  </section>

</div>

<!-- Navigation -->
<button class="nav-arrow prev" id="prev"><span>&#8592;</span></button>
<button class="nav-arrow next" id="next"><span>&#8594;</span></button>

<div class="dots" id="dots"></div>

<button class="timer-btn" id="timerBtn"><span class="play">⏸ Pause auto</span><span class="pause">▶ Auto (10s)</span></button>
<div class="hint"><kbd>&larr;</kbd><kbd>&rarr;</kbd> naviguer &middot; <kbd>Espace</kbd> suivant</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
(function(){
  var slides = Array.prototype.slice.call(document.querySelectorAll('.slide'));
  var dotsWrap = document.getElementById('dots');
  var counter = document.getElementById('counter');
  var progress = document.getElementById('progress');
  var N = slides.length;
  var current = 0;
  var auto = true;
  var DURATION = 10000; // 10s
  var timer = null;
  var startTime = 0;

  // Build dots
  slides.forEach(function(s, i){
    var d = document.createElement('div');
    d.className = 'dot' + (i===0 ? ' active' : '');
    d.dataset.i = i;
    d.addEventListener('click', function(){ goTo(i); resetAuto(); });
    dotsWrap.appendChild(d);
  });
  var dots = dotsWrap.children;

  function goTo(i){
    if(i<0) i = N-1;
    if(i>=N) i = 0;
    slides[current].classList.remove('active');
    dots[current].classList.remove('active');
    current = i;
    slides[current].classList.add('active');
    dots[current].classList.add('active');
    counter.textContent = (current+1) + ' / ' + N;
    progress.style.width = '0%';
  }

  function tick(){
    var elapsed = Date.now() - startTime;
    var pct = Math.min(100, elapsed / DURATION * 100);
    progress.style.width = pct + '%';
    if(elapsed >= DURATION){ goTo(current+1); startTime = Date.now(); }
  }

  function startAuto(){
    if(!auto) return;
    startTime = Date.now();
    clearInterval(timer);
    timer = setInterval(tick, 50);
    progress.style.width = '0%';
    document.body.classList.add('auto');
  }
  function stopAuto(){
    auto = false;
    clearInterval(timer);
    timer = null;
    progress.style.width = '0%';
    document.body.classList.remove('auto');
  }
  function resetAuto(){
    if(!auto) return;
    clearInterval(timer);
    startAuto();
  }

  function next(){ goTo(current+1); resetAuto(); }
  function prev(){ goTo(current-1); resetAuto(); }

  document.getElementById('next').addEventListener('click', next);
  document.getElementById('prev').addEventListener('click', prev);
  document.getElementById('timerBtn').addEventListener('click', function(){
    if(auto){ stopAuto(); } else { auto = true; startAuto(); }
  });

  document.addEventListener('keydown', function(e){
    if(e.key === 'ArrowRight' || e.key === ' ' || e.key === 'PageDown'){ e.preventDefault(); next(); }
    else if(e.key === 'ArrowLeft' || e.key === 'PageUp'){ e.preventDefault(); prev(); }
    else if(e.key === 'Home'){ goTo(0); resetAuto(); }
    else if(e.key === 'End'){ goTo(N-1); resetAuto(); }
    else if(e.key === 'p' || e.key === 'P'){ if(auto){stopAuto()}else{auto=true;startAuto()} }
  });

  // Toucher swipe
  var touchX = 0;
  document.addEventListener('touchstart', function(e){ touchX = e.touches[0].clientX; });
  document.addEventListener('touchend', function(e){
    var dx = e.changedTouches[0].clientX - touchX;
    if(Math.abs(dx) > 60){ dx < 0 ? next() : prev(); }
  });

  goTo(0);
  startAuto();
})();
</script>
</body>
</html>
