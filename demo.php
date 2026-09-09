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
.bg-gradient{
  position:fixed;inset:0;z-index:0;
  background:
    radial-gradient(900px circle at 15% 10%, rgba(99,102,241,.18), transparent 45%),
    radial-gradient(800px circle at 85% 90%, rgba(34,211,238,.15), transparent 45%),
    radial-gradient(600px circle at 80% 15%, rgba(59,130,246,.12), transparent 50%),
    var(--bg);
  transition:background .8s ease;
}
body.slide-dark .bg-gradient{
  background:
    radial-gradient(900px circle at 15% 10%, rgba(99,102,241,.12), transparent 45%),
    radial-gradient(800px circle at 85% 90%, rgba(34,211,238,.08), transparent 45%),
    var(--bg);
}
.orb{
  position:fixed;border-radius:50%;z-index:1;filter:blur(70px);opacity:.5;
  pointer-events:none;
  animation:orbFloat 18s ease-in-out infinite;
}
.orb.o1{width:340px;height:340px;left:-80px;top:-60px;background:radial-gradient(circle,rgba(99,102,241,.55),transparent 70%);transition:left .9s ease,top .9s ease,width .9s ease}
.orb.o2{width:420px;height:420px;right:-100px;bottom:-80px;background:radial-gradient(circle,rgba(34,211,238,.45),transparent 70%);animation-delay:-6s;transition:right .9s ease,bottom .9s ease,width .9s ease}
.orb.o3{width:260px;height:260px;left:55%;top:-40px;background:radial-gradient(circle,rgba(59,130,246,.5),transparent 70%);animation-delay:-12s}
body.slide-dark .orb.o1{left:-140px;top:-130px}
body.slide-dark .orb.o2{right:-180px;bottom:-160px;width:300px}
@keyframes orbFloat{
  0%,100%{transform:translate(0,0) scale(1)}
  33%{transform:translate(40px,-30px) scale(1.1)}
  66%{transform:translate(-30px,40px) scale(.95)}
}
.grid-lines{
  position:fixed;inset:0;z-index:0;opacity:.04;
  background-image:linear-gradient(rgba(255,255,255,.5) 1px,transparent 1px),
                   linear-gradient(90deg,rgba(255,255,255,.5) 1px,transparent 1px);
  background-size:60px 60px;
  mask-image:radial-gradient(ellipse at center, transparent 20%, #000 75%);
  -webkit-mask-image:radial-gradient(ellipse at center, transparent 20%, #000 75%);
}
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
  width:46px;height:46px;border-radius:12px;position:relative;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 6px 24px rgba(99,102,241,.55), inset 0 0 0 1px rgba(255,255,255,.15);
  animation:logoPulse 3.5s ease-in-out infinite;
}
@keyframes logoPulse{0%,100%{box-shadow:0 6px 24px rgba(99,102,241,.55),inset 0 0 0 1px rgba(255,255,255,.15)}50%{box-shadow:0 6px 34px rgba(34,211,238,.7),inset 0 0 0 1px rgba(255,255,255,.25)}}
.brand .logo::after{
  content:'';position:absolute;inset:0;border-radius:12px;
  background:linear-gradient(135deg,transparent 30%,rgba(255,255,255,.25) 50%,transparent 70%);
  background-size:200% 200%;animation:shine 3s linear infinite;
}
@keyframes shine{0%{background-position:100% 100%}100%{background-position:-100% -100%}}
.brand .logo span{color:#fff;font-size:22px;font-weight:800}
.brand .dot{color:var(--cyan)}
.brand small{display:block;font-family:'Inter';font-weight:500;font-size:11px;letter-spacing:3px;color:var(--muted)}
.slide-meta{
  display:flex;align-items:center;gap:18px;color:var(--muted);font-size:14px;
}
.brand-name{font-size:15px;font-weight:600;color:#fff;display:flex;align-items:center;gap:8px}
.brand-name i{color:var(--cyan)}
.progress{width:0;height:4px;background:linear-gradient(90deg,var(--accent),var(--cyan));transition:width .1s linear;border-radius:4px}
.progress-wrap{position:fixed;top:0;left:0;right:0;z-index:21;height:4px;background:rgba(255,255,255,.06)}
.stage{
  position:relative;z-index:5;
  height:100vh;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:90px 60px 40px;
}
.slide{display:none;width:100%;max-width:1300px}
.slide.active{display:block;animation:slideIn .65s cubic-bezier(.22,1,.36,1)}
@keyframes slideIn{from{opacity:0;transform:translateY(36px) scale(.96)}to{opacity:1;transform:translateY(0) scale(1)}}

.slide-head{text-align:center;margin-bottom:34px}
.slide-kicker{
  display:inline-flex;align-items:center;gap:8px;
  color:var(--cyan);font-size:13px;font-weight:700;letter-spacing:3px;text-transform:uppercase;
  margin-bottom:10px;
  opacity:0;transform:translateY(10px);transition:opacity .5s ease .1s,transform .5s ease .1s;
}
.slide.active .slide-kicker{opacity:1;transform:translateY(0)}
.slide-kicker .line{width:34px;height:2px;background:linear-gradient(90deg,transparent,var(--cyan))}
.slide-kicker .line.r{background:linear-gradient(90deg,var(--cyan),transparent)}
h2.slide-title{
  font-family:'Poppins',sans-serif;font-weight:800;font-size:44px;line-height:1.1;
  background:linear-gradient(90deg,#fff,#b8c2d6);
  -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;
  opacity:0;transform:translateY(14px);transition:opacity .5s ease .18s,transform .5s ease .18s;
}
.slide.active h2.slide-title{opacity:1;transform:translateY(0)}
.slide-desc{color:var(--muted);font-size:17px;margin-top:12px;max-width:760px;margin-left:auto;margin-right:auto;
  opacity:0;transform:translateY(10px);transition:opacity .5s ease .26s,transform .5s ease .26s;}
.slide.active .slide-desc{opacity:1;transform:translateY(0)}
.slide-body{
  display:flex;gap:32px;align-items:stretch;
  opacity:0;transform:translateY(20px);transition:opacity .6s ease .34s,transform .6s ease .34s;
}
.slide.active .slide-body{opacity:1;transform:translateY(0)}
.shot{
  flex:1.2;position:relative;border-radius:16px;overflow:hidden;
  border:1px solid rgba(255,255,255,.09);
  background:var(--panel2);
  box-shadow:0 24px 60px rgba(0,0,0,.5), 0 0 0 1px rgba(34,211,238,.06);
  transform:perspective(1200px) rotateY(0deg) rotateX(0deg);
  transition:transform .5s cubic-bezier(.22,1,.36,1);
}
.shot:hover{transform:perspective(1200px) rotateY(-4deg) rotateX(2deg)}
.shot::before{
  content:'';position:absolute;inset:-2px;z-index:-1;border-radius:18px;
  background:linear-gradient(120deg,rgba(99,102,241,.4),transparent 40%,rgba(34,211,238,.4));
  filter:blur(24px);opacity:.5;transition:opacity .4s;
}
.shot:hover::before{opacity:.85}
.shot img{display:block;width:100%;height:100%;object-fit:contain;background:radial-gradient(120% 120% at 50% 0%,#111a2c,#0a0f1c);transition:transform .8s cubic-bezier(.22,1,.36,1)}
.shot:hover img{transform:scale(1.03)}
.shot::after{
  content:'';position:absolute;inset:0;pointer-events:none;
  background:linear-gradient(140deg,rgba(255,255,255,.06),transparent 40%),
             radial-gradient(120% 60% at 50% 110%,rgba(34,211,238,.1),transparent 60%);
}
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
  background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.07);
  border-radius:12px;padding:16px 18px;
  backdrop-filter:blur(8px);
  transition:transform .3s cubic-bezier(.22,1,.36,1),border-color .3s,box-shadow .3s,background .3s;
  opacity:0;transform:translateY(24px);
}
.feature.revealed{opacity:1;transform:translateY(0)}
.feature:hover{
  transform:translateX(6px);border-color:rgba(59,130,246,.55);
  background:rgba(255,255,255,.06);
  box-shadow:0 10px 30px rgba(0,0,0,.35), 0 0 20px rgba(99,102,241,.15);
}
.feature .icon{
  flex-shrink:0;width:44px;height:44px;border-radius:11px;
  display:flex;align-items:center;justify-content:center;font-size:18px;
  background:linear-gradient(135deg,rgba(59,130,246,.25),rgba(99,102,241,.25));
  color:var(--cyan);
  box-shadow:inset 0 0 0 1px rgba(255,255,255,.08);
  transition:transform .3s,color .3s;
}
.feature:hover .icon{transform:scale(1.12) rotate(-4deg);color:#fff}
.feature h4{font-size:16px;font-weight:700;color:#fff;margin-bottom:2px}
.feature p{font-size:13.5px;color:var(--muted);line-height:1.45}
.feature:hover p{color:#b9c6db}

.slide.hero{text-align:center}
.hero .big-logo{
  width:130px;height:130px;border-radius:30px;margin:0 auto 26px;position:relative;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 20px 60px rgba(99,102,241,.6), inset 0 0 0 1px rgba(255,255,255,.18);
  animation:dashPulse 3.5s ease-in-out infinite;
}
@keyframes dashPulse{0%,100%{box-shadow:0 20px 60px rgba(99,102,241,.6),inset 0 0 0 1px rgba(255,255,255,.18)}50%{box-shadow:0 20px 80px rgba(34,211,238,.75),inset 0 0 0 1px rgba(255,255,255,.3)}}
.hero .big-logo::after{
  content:'';position:absolute;inset:0;border-radius:30px;
  background:linear-gradient(135deg,transparent 30%,rgba(255,255,255,.3) 50%,transparent 70%);
  background-size:200% 200%;animation:shine 3s linear infinite;
}
.hero .big-logo span{font-size:60px;color:#fff;font-weight:800;font-family:'Poppins'}
.hero h1{
  font-family:'Poppins';font-weight:800;font-size:78px;line-height:1;
  background:linear-gradient(90deg,#fff 20%,var(--cyan));
  -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;
  filter:drop-shadow(0 6px 30px rgba(34,211,238,.25));
}
.hero .tagline{font-size:22px;color:var(--muted);margin-top:18px;font-weight:500}
.hero .chips{display:flex;gap:12px;justify-content:center;margin-top:34px;flex-wrap:wrap}
.hero .chip{
  padding:11px 22px;border-radius:30px;font-size:14px;font-weight:600;
  background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);color:var(--text);
  backdrop-filter:blur(6px);
  transition:transform .25s,box-shadow .25s,border-color .25s;
}
.hero .chip:hover{transform:translateY(-4px);box-shadow:0 10px 24px rgba(0,0,0,.3);border-color:rgba(34,211,238,.5)}
.hero .chip i{margin-right:8px;color:var(--cyan)}

.slide.merci{text-align:center}
.merci .big-check{
  width:110px;height:110px;border-radius:50%;margin:0 auto 26px;position:relative;
  background:linear-gradient(135deg,#10b981,#22d3ee);
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 20px 60px rgba(16,185,129,.5), inset 0 0 0 1px rgba(255,255,255,.18);
  animation:dashPulse 3.5s ease-in-out infinite;
  font-size:58px;color:#fff;
}
.merci h2{font-family:'Poppins';font-weight:800;font-size:64px;background:linear-gradient(90deg,#fff,var(--cyan));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;filter:drop-shadow(0 6px 30px rgba(34,211,238,.3))}
.merci .sub{font-size:20px;color:var(--muted);margin-top:16px}
.merci .btn-contact{
  display:inline-flex;align-items:center;gap:10px;margin-top:30px;padding:15px 38px;border-radius:12px;position:relative;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  color:#fff;font-weight:700;font-size:15px;text-decoration:none;
  box-shadow:0 12px 30px rgba(99,102,241,.45);
  transition:transform .25s,box-shadow .25s;
}
.merci .btn-contact:hover{transform:translateY(-3px);box-shadow:0 18px 44px rgba(99,102,241,.6)}
.merci .btn-contact::after{
  content:'';position:absolute;inset:0;border-radius:12px;
  background:linear-gradient(135deg,transparent 30%,rgba(255,255,255,.3) 50%,transparent 70%);
  background-size:200% 200%;animation:shine 3s linear infinite;
}
.merci .modules{margin-top:36px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.merci .module{
  padding:10px 20px;border-radius:12px;font-size:13px;font-weight:600;color:var(--text);
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);
  transition:transform .25s,border-color .25s;
}
.merci .module i{color:var(--cyan);margin-right:6px}
.merci .module:hover{transform:translateY(-3px);border-color:rgba(34,211,238,.4)}

.nav-arrow{
  position:fixed;top:50%;transform:translateY(-50%);z-index:30;
  width:56px;height:56px;border-radius:50%;
  background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);
  color:#fff;font-size:22px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  transition:background .2s,transform .2s;
  backdrop-filter:blur(6px);
}
.nav-arrow:hover{background:rgba(59,130,246,.35);transform:translateY(-50%) scale(1.06);box-shadow:0 0 24px rgba(99,102,241,.4)}
.nav-arrow.prev{left:26px}
.nav-arrow.next{right:26px}
.dots{
  position:fixed;bottom:22px;left:50%;transform:translateX(-50%);z-index:30;
  display:flex;gap:10px;
}
.dot{
  width:30px;height:7px;border-radius:4px;background:rgba(255,255,255,.18);cursor:pointer;transition:all .25s;
}
.dot.active{background:linear-gradient(90deg,var(--accent),var(--cyan));width:44px}
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

@media(max-width:900px){
  .slide-body{flex-direction:column}
  .features{flex-direction:row;flex-wrap:wrap}
  .feature{flex:1 1 40%}
  h2.slide-title{font-size:30px}
  .hero h1{font-size:46px}
  .nav-arrow{width:44px;height:44px;font-size:18px}
}
@media(max-width:600px){
  .feature{flex:1 1 100%}
  .merci .modules{flex-direction:column}
}
</style>
</head>
<body class="auto">

<div class="bg-gradient"></div>
<div class="orb o1"></div>
<div class="orb o2"></div>
<div class="orb o3"></div>
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
    <span id="counter">1 / 11</span>
  </div>
</header>

<div class="stage" id="stage">

  <section class="slide hero active" data-kicker="Bienvenue">
    <div class="big-logo"><span>G</span></div>
    <h1>GLOBIT</h1>
    <div class="tagline">La solution moderne de gestion des ressources humaines</div>
    <div class="chips">
      <div class="chip"><i class="fa-solid fa-users"></i>Employés</div>
      <div class="chip"><i class="fa-solid fa-clipboard-check"></i>Présences</div>
      <div class="chip"><i class="fa-solid fa-money-bill-wave"></i>Paie</div>
      <div class="chip"><i class="fa-solid fa-mobile-screen-button"></i>Mobile</div>
      <div class="chip"><i class="fa-solid fa-qrcode"></i>QR Code</div>
    </div>
  </section>

  <section class="slide" data-kicker="Connexion">
    <div class="slide-head">
      <div class="slide-kicker"><span class="line"></span>Connexion<span class="line r"></span></div>
      <h2 class="slide-title">Un espace de connexion moderne</h2>
      <p class="slide-desc">Une interface sombre, épurée et sécurisée pour accéder à la plateforme.</p>
    </div>
    <div class="slide-body">
      <div class="shot">
        <div class="frame-top"><i class="r1"></i><i class="r2"></i><i class="r3"></i><span>GLOBIT - Connexion</span></div>
        <div class="shot-body"><img src="assets/demo/login.png" alt="Connexion"></div>
      </div>
      <div class="features">
        <div class="feature"><div class="icon"><i class="fa-solid fa-lock"></i></div><div><h4>Authentification sécurisée</h4><p>Connexion protégée par sessions PHP et anti-CSRF.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-shield-halved"></i></div><div><h4>Mots de passe chiffrés</h4><p>Hachage bcrypt et protection contre l'injection SQL.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-right-to-bracket"></i></div><div><h4>Accès par rôle</h4><p>Admin, RH et employé : chacun son espace et ses droits.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-mobile-screen-button"></i></div><div><h4>Connexion mobile</h4><p>Accédez aussi via l'API JSON depuis le téléphone.</p></div></div>
      </div>
    </div>
  </section>

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
        <div class="feature"><div class="icon"><i class="fa-solid fa-envelope"></i></div><div><h4>Email professionnel</h4><p>Un email @globit.com généré automatiquement pour chaque employé.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-pen-to-square"></i></div><div><h4>Modification</h4><p>Mettez à jour les informations en quelques clics.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-magnifying-glass"></i></div><div><h4>Recherche</h4><p>Trouvez un employé instantanément par nom ou matricule.</p></div></div>
      </div>
    </div>
  </section>

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

  <section class="slide" data-kicker="Analyse">
    <div class="slide-head">
      <div class="slide-kicker"><span class="line"></span>Analyse<span class="line r"></span></div>
      <h2 class="slide-title">Rapports & journaux</h2>
      <p class="slide-desc">Des rapports décisionnels et un audit complet des actions.</p>
    </div>
    <div class="slide-body">
      <div class="shot">
        <div class="frame-top"><i class="r1"></i><i class="r2"></i><i class="r3"></i><span>GLOBIT - Rapports</span></div>
        <div class="shot-body"><img src="assets/demo/journal.png" alt="Rapports"></div>
      </div>
      <div class="features">
        <div class="feature"><div class="icon"><i class="fa-solid fa-chart-line"></i></div><div><h4>Absentéisme</h4><p>Taux d'absence par employé et par mois.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-chart-pie"></i></div><div><h4>Personnel</h4><p>Répartition des effectifs par service et par genre.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-calendar-check"></i></div><div><h4>Congés</h4><p>Synthèse des absences et des demandes.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-book-open"></i></div><div><h4>Journal</h4><p>Audit de toutes les actions pour la sécurité.</p></div></div>
      </div>
    </div>
  </section>

  <section class="slide" data-kicker="Applications">
    <div class="slide-head">
      <div class="slide-kicker"><span class="line"></span>Applications<span class="line r"></span></div>
      <h2 class="slide-title">L'écosystème GLOBIT</h2>
      <p class="slide-desc">Une plateforme web complète avec une application mobile intégrée.</p>
    </div>
    <div class="slide-body">
      <div class="shot">
        <div class="frame-top"><i class="r1"></i><i class="r2"></i><i class="r3"></i><span>GLOBIT - Mobile</span></div>
        <div class="shot-body"><img src="assets/demo/dashboard.png" alt="Applications"></div>
      </div>
      <div class="features">
        <div class="feature"><div class="icon"><i class="fa-solid fa-globe"></i></div><div><h4>Plateforme web</h4><p>Administration complète avec interface AdminLTE et Bootstrap.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-mobile-screen-button"></i></div><div><h4>App mobile PWA</h4><p>Installable sur téléphone : pointage, congés et notifications.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-plug"></i></div><div><h4>API JSON</h4><p>API sécurisée par token pour connecter l'application mobile.</p></div></div>
        <div class="feature"><div class="icon"><i class="fa-solid fa-bell"></i></div><div><h4>Notifications</h4><p>Alertes en temps réel pour chaque décision RH.</p></div></div>
      </div>
    </div>
  </section>

  <section class="slide merci" data-kicker="Conclusion">
    <div class="big-check"><i class="fa-solid fa-check"></i></div>
    <h2>Merci !</h2>
    <div class="sub">GLOBIT — le référentiel RH de votre entreprise.</div>
    <div class="modules">
      <div class="module"><i class="fa-solid fa-users"></i>Employés</div>
      <div class="module"><i class="fa-solid fa-clipboard-check"></i>Présences</div>
      <div class="module"><i class="fa-solid fa-calendar-days"></i>Congés</div>
      <div class="module"><i class="fa-solid fa-money-bill-wave"></i>Paie</div>
      <div class="module"><i class="fa-solid fa-qrcode"></i>QR Code</div>
      <div class="module"><i class="fa-solid fa-chart-line"></i>Rapports</div>
    </div>
  </section>

</div>

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
  var DURATION = 10000;
  var timer = null;
  var startTime = 0;

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
    document.body.classList.toggle('slide-dark', slides[current].classList.contains('merci'));
    progress.style.width = '0%';
    var feats = slides[current].querySelectorAll('.feature');
    feats.forEach(function(f){ f.classList.remove('revealed'); });
    feats.forEach(function(f, idx){
      setTimeout(function(){ f.classList.add('revealed'); }, 130 * idx + 250);
    });
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