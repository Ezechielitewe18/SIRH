<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>GLOBIT - SIRH</title>
<link rel="manifest" href="manifest.webmanifest">
<meta name="theme-color" content="#3b82f6">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<link rel="apple-touch-icon" href="public/img/icon-192.png">
<link rel="icon" type="image/png" href="public/img/icon-192.png">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#0b0f1a; --panel:#141c2e; --panel2:#0f1625;
  --accent:#3b82f6; --accent2:#6366f1; --cyan:#22d3ee;
  --text:#e2e8f0; --muted:#8aa0bd; --green:#22c55e; --red:#ef4444; --gold:#f59e0b;
}
*{margin:0;padding:0;box-sizing:border-box;-webkit-tap-highlight-color:transparent}
body{
  background:var(--bg);color:var(--text);
  font-family:'Inter',sans-serif;
  max-width:430px;margin:0 auto;min-height:100vh;
  position:relative;padding-bottom:64px;
}
.bg{
  position:fixed;inset:0;z-index:0;pointer-events:none;
  background:
    radial-gradient(600px circle at 20% 0%, rgba(99,102,241,.15), transparent 50%),
    radial-gradient(500px circle at 90% 90%, rgba(34,211,238,.10), transparent 50%),
    var(--bg);
}
.app{position:relative;z-index:1}

/* Topbar */
.topbar{
  display:flex;align-items:center;gap:12px;padding:16px 18px 10px;
}
.topbar .logo{
  width:38px;height:38px;border-radius:10px;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  display:flex;align-items:center;justify-content:center;font-weight:800;font-size:20px;color:#fff;
}
.topbar .t{font-weight:800;font-size:19px;letter-spacing:.5px}
.topbar .t small{display:block;font-size:10.5px;font-weight:500;color:var(--muted);letter-spacing:2px}
.topbar .spacer{flex:1}
.topbar .bell{
  position:relative;width:38px;height:38px;border-radius:50%;
  background:var(--panel);display:flex;align-items:center;justify-content:center;
  border:1px solid rgba(255,255,255,.08);cursor:pointer;
}
.topbar .bell .badge{
  position:absolute;top:-3px;right:-3px;min-width:18px;height:18px;border-radius:9px;
  background:var(--red);color:#fff;font-size:10.5px;font-weight:700;
  display:flex;align-items:center;justify-content:center;padding:0 4px;display:none;
}

/* Sections */
.section{display:none;padding:8px 16px;animation:fade .3s ease}
.section.active{display:block}
@keyframes fade{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}

/* Cartes */
.card{
  background:var(--panel);border:1px solid rgba(255,255,255,.06);
  border-radius:14px;padding:16px;margin-bottom:14px;
}
.card h3{font-size:15px;font-weight:700;margin-bottom:4px}
.card .sub{font-size:13px;color:var(--muted)}
.card .big{font-size:30px;font-weight:800;margin-top:6px}
.green{color:var(--green)}.red{color:var(--red)}.gold{color:var(--gold)}.cyan{color:var(--cyan)}

/* Stats row */
.stat-row{display:flex;gap:12px;margin-bottom:14px}
.stat{
  flex:1;background:var(--panel);border:1px solid rgba(255,255,255,.06);
  border-radius:14px;padding:14px;text-align:center;
}
.stat .n{font-size:22px;font-weight:800}
.stat .l{font-size:11px;color:var(--muted);margin-top:2px}

/* Boutons */
.btn{
  display:block;width:100%;padding:14px;border:none;border-radius:12px;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  color:#fff;font-size:15px;font-weight:700;cursor:pointer;font-family:'Inter';
}
.btn:active{opacity:.85}
.btn.outline{
  background:transparent;border:1.5px solid rgba(255,255,255,.15);color:var(--text);
}
.btn.green{background:linear-gradient(135deg,#16a34a,#22c55e)}
.btn.red{background:linear-gradient(135deg,#dc2626,#ef4444)}

/* Listes */
.list-item{
  display:flex;align-items:center;gap:12px;padding:12px 0;
  border-bottom:1px solid rgba(255,255,255,.05);
}
.list-item:last-child{border-bottom:none}
.list-item .ic{
  width:36px;height:36px;border-radius:10px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  background:rgba(59,130,246,.15);color:var(--cyan);font-size:16px;
}
.list-item .ct{flex:1}
.list-item .tt{font-size:14px;font-weight:600}
.list-item .dd{font-size:12px;color:var(--muted);margin-top:2px}

/* Badge statut */
.st{
  font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;
}
.st.en_attente,.st.en_attente{background:rgba(245,158,11,.2);color:var(--gold)}
.st.approuve,.st.validee,.st.auto{background:rgba(34,197,94,.15);color:var(--green)}
.st.refuse,.st.rejetee{background:rgba(239,68,68,.15);color:var(--red)}

/* Formulaire */
label{display:block;font-size:12.5px;color:var(--muted);margin:12px 0 6px;font-weight:600}
input,select,textarea{
  width:100%;padding:13px;border-radius:10px;border:1.5px solid rgba(255,255,255,.1);
  background:var(--panel2);color:var(--text);font-size:15px;font-family:'Inter';outline:none;
}
input:focus,select:focus,textarea:focus{border-color:var(--accent)}
.row{display:flex;gap:10px}
.row>div{flex:1}

/* Modal */
.modal-bg{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:50;display:none;align-items:flex-end}
.modal-bg.show{display:flex}
.modal{
  width:100%;max-width:430px;margin:0 auto;background:var(--panel);
  border-radius:18px 18px 0 0;padding:22px 18px 28px;
  animation:up .25s ease;
}
@keyframes up{from{transform:translateY(100%)}to{transform:none}}
.modal h3{font-size:17px;font-weight:800;margin-bottom:14px}

/* Login */
.login-screen{position:fixed;inset:0;z-index:60;background:var(--bg);display:flex;align-items:center;justify-content:center;padding:24px;flex-direction:column}
.login-card{width:100%;max-width:360px}
.login-logo{
  width:72px;height:72px;border-radius:18px;margin:0 auto 16px;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  display:flex;align-items:center;justify-content:center;
  font-size:36px;font-weight:800;color:#fff;
  box-shadow:0 16px 40px rgba(99,102,241,.4);
}
.login-card h1{text-align:center;font-size:26px;font-weight:800;margin-bottom:4px}
.login-card .tag{text-align:center;color:var(--muted);font-size:13px;margin-bottom:22px}
.login-err{color:var(--red);font-size:13px;text-align:center;margin-top:12px;display:none}

/* Champ mot de passe visible */
.pw-wrap{position:relative}
.pw-wrap .eye{position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--muted);font-size:15px}

/* Bottom nav */
.bottom-nav{
  position:fixed;bottom:0;left:0;right:0;z-index:40;
  max-width:430px;margin:0 auto;
  background:rgba(11,15,26,.92);backdrop-filter:blur(10px);
  border-top:1px solid rgba(255,255,255,.07);
  display:flex;padding-bottom:env(safe-area-inset-bottom);
}
.bottom-nav .nb{
  flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;
  padding:10px 0 8px;color:var(--muted);font-size:10px;cursor:pointer;font-weight:600;
}
.bottom-nav .nb i{font-size:19px}
.bottom-nav .nb.active{color:var(--cyan)}

/* Install banner */
.install-banner{
  position:fixed;bottom:70px;left:16px;right:16px;z-index:45;
  max-width:398px;margin:0 auto;
  background:var(--panel);border:1px solid rgba(255,255,255,.12);border-radius:14px;
  display:none;align-items:center;gap:12px;padding:12px;
  box-shadow:0 12px 40px rgba(0,0,0,.5);
}
.install-banner.show{display:flex}
.install-banner img{width:42px;height:42px;border-radius:10px}
.install-banner .txt{flex:1;font-size:12.5px}
.install-banner .txt b{display:block;font-size:14px}
.install-banner .cl{color:var(--cyan);cursor:pointer;font-weight:700;font-size:14px}

.empty{text-align:center;color:var(--muted);padding:30px 0;font-size:14px}
.empty i{font-size:38px;opacity:.4;display:block;margin-bottom:10px}
.spinner{
  width:20px;height:20px;border:2px solid rgba(255,255,255,.2);
  border-top-color:#fff;border-radius:50%;display:inline-block;animation:spin .7s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}
</style>
</head>
<body>

<div class="bg"></div>

<!-- ============ LOGIN ============ -->
<div class="login-screen" id="loginScreen">
  <div class="login-card">
    <div class="login-logo">G</div>
    <h1>GLOBIT</h1>
    <div class="tag">Connexion à votre espace RH</div>
    <div id="loginForm">
      <label>Email</label>
      <input type="email" id="loginEmail" placeholder="email@entreprise.com" autocomplete="username">
      <label>Mot de passe</label>
      <div class="pw-wrap">
        <input type="password" id="loginPass" placeholder="••••••••" autocomplete="current-password">
        <span class="eye" onclick="togglePw(this)">👁</span>
      </div>
      <div style="margin-top:20px">
        <button class="btn" id="loginBtn" onclick="doLogin()">Se connecter</button>
      </div>
      <div class="login-err" id="loginErr"></div>
    </div>
  </div>
</div>

<!-- ============ APP ============ -->
<div class="app" id="app" style="display:none">

  <div class="topbar">
    <div class="logo" id="navAvatar">G</div>
    <div class="t" id="navName">Chargement...<small>GLOBIT SIRH</small></div>
    <div class="spacer"></div>
    <div class="bell" onclick="go('notifs')"><span id="bellCnt">🔔</span><span class="badge" id="bellBadge">0</span></div>
  </div>

  <!-- ACCUEIL -->
  <div class="section active" id="sec-home">
    <div class="card">
      <h3 id="welcome">Bonjour 👋</h3>
      <div class="sub" id="homeSub">Chargement de vos indicateurs...</div>
    </div>
    <div class="stat-row">
      <div class="stat"><div class="n cyan" id="statPres">-</div><div class="l">Présences (mois)</div></div>
      <div class="stat"><div class="n gold" id="statConge">-</div><div class="l">Congés</div></div>
      <div class="stat"><div class="n green" id="statPaie">-</div><div class="l">Dernier net</div></div>
    </div>
    <div class="card" id="todayCard">
      <h3>Ma présence aujourd'hui</h3>
      <div class="sub" id="todayDetail">Aucun pointage</div>
      <div style="margin-top:12px">
        <button class="btn" id="btnCheckIn" onclick="declarerPresence()">✅ Déclarer mon arrivée</button>
      </div>
    </div>
  </div>

  <!-- PRESENCE -->
  <div class="section" id="sec-pres">
    <h2 style="font-size:22px;font-weight:800;margin-bottom:14px">Présence</h2>
    <div class="card">
      <h3>Pointage du jour</h3>
      <div id="presTodayBox">
        <div class="sub">Chargement...</div>
        <div style="margin-top:12px">
          <button class="btn" onclick="declarerPresence()">✅ Déclarer arrivée</button>
        </div>
        <div style="margin-top:8px">
          <button class="btn outline" onclick="pointerSortie()">⏱ Pointer sortie</button>
        </div>
      </div>
    </div>
    <div class="card">
      <h3>Historique</h3>
      <div id="presHistory"><div class="empty"><i>📋</i>Aucune présence</div></div>
    </div>
  </div>

  <!-- CONGES -->
  <div class="section" id="sec-conge">
    <h2 style="font-size:22px;font-weight:800;margin-bottom:14px">Mes congés</h2>
    <button class="btn" onclick="openModal('congeModal')">＋ Nouvelle demande</button>
    <div style="height:14px"></div>
    <div id="congeList"><div class="empty"><i>🗓</i>Aucun congé</div></div>
  </div>

  <!-- NOTIFS -->
  <div class="section" id="sec-notifs">
    <h2 style="font-size:22px;font-weight:800;margin-bottom:14px">Notifications</h2>
    <div id="notifList"><div class="empty"><i>🔔</i>Aucune notification</div></div>
  </div>

  <!-- PROFIL -->
  <div class="section" id="sec-profil">
    <div class="card" style="text-align:center">
      <div style="width:72px;height:72px;border-radius:50%;margin:6px auto 10px;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:30px;font-weight:800;color:#fff">G</div>
      <h3 id="profName">-</h3>
      <div class="sub" id="profRole">-</div>
      <div style="background:rgba(255,255,255,.05);border-radius:12px;margin-top:14px;padding:12px" id="profInfo"></div>
    </div>
    <button class="btn outline" onclick="logout()">↪ Se déconnecter</button>
  </div>

</div>

<!-- Bottom nav -->
<div class="bottom-nav" id="bottomNav" style="display:none">
  <div class="nb active" data-sec="home"><i>🏠</i>Accueil</div>
  <div class="nb" data-sec="pres"><i>⏱</i>Présence</div>
  <div class="nb" data-sec="conge"><i>🗓</i>Congés</div>
  <div class="nb" data-sec="notifs"><i>🔔</i>Notifs</div>
  <div class="nb" data-sec="profil"><i>👤</i>Profil</div>
</div>

<!-- Install banner -->
<div class="install-banner" id="installBanner">
  <img src="public/img/icon-192.png" alt="GLOBIT">
  <div class="txt"><b>Installer GLOBIT</b>Ajoutez l'app à votre écran d'accueil.</div>
  <div class="cl" onclick="installApp()">Installer</div>
</div>

<!-- Modal conge -->
<div class="modal-bg" id="congeModal">
  <div class="modal">
    <h3>Nouvelle demande de congé</h3>
    <label>Type de congé</label>
    <select id="congeType">
      <option value="annuel">Annuel</option>
      <option value="maladie">Maladie</option>
      <option value="maternite">Maternité</option>
      <option value="paternite">Paternité</option>
      <option value="exceptionnel">Exceptionnel</option>
      <option value="autre">Autre</option>
    </select>
    <div class="row">
      <div><label>Début</label><input type="date" id="congeDebut"></div>
      <div><label>Fin</label><input type="date" id="congeFin"></div>
    </div>
    <label>Motif</label>
    <textarea id="congeMotif" rows="3" placeholder="Motif de la demande..."></textarea>
    <div style="margin-top:16px;display:flex;gap:10px">
      <button class="btn outline" onclick="closeModal('congeModal')" style="flex:1">Annuler</button>
      <button class="btn" onclick="submitConge()" style="flex:2">Envoyer</button>
    </div>
  </div>
</div>

<script>
var API_BASE = 'api.php';
var TOKEN_KEY = 'globit_token';
var USER_KEY = 'globit_user';
var token = localStorage.getItem(TOKEN_KEY) || '';
var user = JSON.parse(localStorage.getItem(USER_KEY) || 'null');
var deferredPrompt = null;
var SW_SCOPE = ''; // détecté

// ---------- Utilitaires ----------
function api(action, method, data) {
  var opts = {
    method: method || 'GET',
    headers: { 'Content-Type': 'application/json' }
  };
  if (token) opts.headers['Authorization'] = 'Bearer ' + token;
  if (data) opts.body = JSON.stringify(data);
  return fetch(API_BASE + '?action=' + action, opts).then(function(r) {
    // Parser le JSON meme en cas d'erreur HTTP (400/401/403/404/409)
    var ct = r.headers.get('Content-Type') || '';
    if (ct.indexOf('application/json') === -1) {
      // Reponse non-JSON (erreur serveur HTML) => creer une erreur propre
      if (!r.ok) throw { http: r.status, msg: 'Erreur serveur (HTTP ' + r.status + ')' };
      throw { http: 0, msg: 'Reponse inattendue du serveur' };
    }
    return r.json().then(function(j) {
      if (!r.ok) {
        // Token expire ou invalide : deconnexion automatique
        if (r.status === 401 && action !== 'login') {
          token = ''; user = null;
          localStorage.removeItem(TOKEN_KEY);
          localStorage.removeItem(USER_KEY);
          location.reload();
        }
        throw { http: r.status, msg: j.message || 'Erreur ' + r.status };
      }
      return j;
    });
  }).catch(function(e) {
    // Erreur reseau (telephone hors-ligne, timeout, etc.)
    if (e && e.http !== undefined) throw e;
    throw { http: 0, msg: 'Erreur reseau. Verifiez votre connexion.' };
  });
}

// ---------- Interface ----------
var sections = ['home','pres','conge','notifs','profil'];
function go(sec) {
  document.querySelectorAll('.section').forEach(function(s){ s.classList.remove('active'); });
  document.getElementById('sec-' + sec).classList.add('active');
  document.querySelectorAll('.bottom-nav .nb').forEach(function(n){
    n.classList.toggle('active', n.dataset.sec === sec);
  });
  if (sec === 'home') loadHome();
  if (sec === 'pres') loadPresence();
  if (sec === 'conge') loadConges();
  if (sec === 'notifs') loadNotifs();
  if (sec === 'profil') loadProfil();
}
document.querySelectorAll('.bottom-nav .nb').forEach(function(n){
  n.addEventListener('click', function(){ go(n.dataset.sec); });
});

function togglePw(eye) {
  var input = eye.previousElementSibling;
  var show = input.type === 'password';
  input.type = show ? 'text' : 'password';
  eye.textContent = show ? '🙈' : '👁';
}

function openModal(id){ document.getElementById(id).classList.add('show'); }
function closeModal(id){ document.getElementById(id).classList.remove('show'); }
document.querySelectorAll('.modal-bg').forEach(function(m){
  m.addEventListener('click', function(e){ if(e.target===m) m.classList.remove('show'); });
});

// ---------- Login ----------
function doLogin() {
  var email = document.getElementById('loginEmail').value.trim();
  var pw = document.getElementById('loginPass').value;
  var btn = document.getElementById('loginBtn');
  var err = document.getElementById('loginErr');
  err.style.display = 'none';
  if (!email || !pw) { err.textContent = 'Veuillez remplir tous les champs.'; err.style.display='block'; return; }
  btn.innerHTML = '<span class="spinner"></span>';
  api('login','POST',{ email: email, password: pw }).then(function(j){
    token = j.token;
    user = j.user;
    localStorage.setItem(TOKEN_KEY, token);
    localStorage.setItem(USER_KEY, JSON.stringify(user));
    btn.textContent = 'Se connecter';
    enterApp();
  }).catch(function(e){
    btn.textContent = 'Se connecter';
    err.textContent = e.msg || 'Erreur de connexion';
    err.style.display = 'block';
  });
}

function enterApp() {
  document.getElementById('loginScreen').style.display = 'none';
  document.getElementById('app').style.display = 'block';
  document.getElementById('bottomNav').style.display = 'flex';
  document.getElementById('navName').childNodes[0].textContent = (user.nom_complet || '').split(' ')[0];
  document.getElementById('navAvatar').textContent = (user.nom_complet || 'G')[0].toUpperCase();
  registerSW();
  go('home');
}

function logout() {
  api('logout','POST').catch(function(){});
  token=''; user=null;
  localStorage.removeItem(TOKEN_KEY);
  localStorage.removeItem(USER_KEY);
  location.reload();
}

// ---------- Accueil ----------
function loadHome() {
  var now = new Date();
  var mois = ('0'+(now.getMonth()+1)).slice(-2);
  var annee = now.getFullYear();
  document.getElementById('welcome').textContent = 'Bonjour, ' + (user.nom_complet||'').split(' ')[0] + ' 👋';

  Promise.all([
    api('presences_validation','GET').catch(function(){return null;}),
    api('conges','GET').catch(function(){ return {data: []}; }),
    api('bulletins','GET').catch(function(){ return {data: []}; }),
    api('presence_aujourdhui','GET').catch(function(){ return {data:{presence:null}}; })
  ]).then(function(r){
    var presStat = document.getElementById('statPres');
    var congeStat = document.getElementById('statConge');
    var paieStat = document.getElementById('statPaie');

    // Présences du mois (admin voit toutes, employé son historique) - fallback
    presStat.textContent = '—';
    congeStat.textContent = (r[1].data || []).length;
    var bl = r[2].data || [];
    if (bl.length) {
      var dern = bl[0];
      paieStat.textContent = formatMoney(dern.total_net);
    } else paieStat.textContent = '—';

    // Présence du jour
    var p = (r[3].data || {}).presence;
    var box = document.getElementById('todayDetail');
    var btn = document.getElementById('btnCheckIn');
    if (p) {
      var st = p.validation;
      var label = st==='auto'?'Auto-validée':st==='validee'?'Validée':st==='en_attente'?'En attente':'Rejetée';
      box.innerHTML = '<span class="st '+st+'">'+label+'</span> Arrivée ' + (p.heure_arrivee||'') + (p.heure_depart? ' · Départ '+p.heure_depart : '');
      btn.style.display = (st==='rejetee') ? 'block' : 'none';
      if (st==='rejetee') btn.textContent = '↻ Redéclarer mon arrivée';
    } else {
      box.textContent = 'Aucun pointage aujourd\'hui.';
      btn.style.display = 'block';
      btn.textContent = '✅ Déclarer mon arrivée';
    }
  });
}

// ---------- Présence ----------
function loadPresence() {
  api('presence_aujourdhui','GET').then(function(j){
    var p = (j.data||{}).presence;
    var box = document.getElementById('presTodayBox');
    if (p) {
      var st = p.validation;
      var label = st==='auto'?'Auto-validée':st==='validee'?'Validée':st==='en_attente'?'En attente':'Rejetée';
      box.innerHTML = '<div class="list-item"><div class="ic">⏱</div><div class="ct"><div class="tt">' + label + '</div><div class="dd">Arrivée ' + (p.heure_arrivee||'—') + (p.heure_depart? ' · Départ '+p.heure_depart : '') + '</div></div><span class="st '+st+'">'+(p.statut==='retard'?'Retard':p.statut)+'</span></div>';
      box.innerHTML += '<div style="margin-top:10px;display:flex;gap:8px"><button class="btn red" style="flex:2" onclick="declarerPresence(true)">'+(st==='rejetee'?'↻ Redéclarer':'Déclarer arrivée')+'</button><button class="btn outline" style="flex:2" onclick="pointerSortie()">⏱ Sortie</button></div>';
    } else {
      box.innerHTML = '<div class="sub">Aucun pointage aujourd\'hui</div><div style="margin-top:12px;display:flex;gap:8px"><button class="btn" style="flex:2" onclick="declarerPresence(true)">✅ Déclarer arrivée</button><button class="btn outline" style="flex:2" onclick="pointerSortie()">⏱ Sortie</button></div>';
    }
    loadPresHistory();
  }).catch(function(){ document.getElementById('presTodayBox').innerHTML='<div class="sub">Erreur de chargement</div>'; });
}

function loadPresHistory() {
  api('presences','GET').then(function(j){
    var list = j.data || [];
    var html = '';
    if (!list.length) { html = '<div class="empty"><i>📋</i>Aucune présence</div>'; }
    list.forEach(function(p){
      var st = p.validation;
      var stl = st==='auto'?'auto':st==='validee'?'validee':st==='en_attente'?'en_attente':'rejetee';
      html += '<div class="list-item"><div class="ic">📅</div><div class="ct"><div class="tt">'+p.date_presence+'</div><div class="dd">Arrivée '+(p.heure_arrivee||'—')+' · Départ '+(p.heure_depart||'—')+'</div></div><span class="st '+stl+'">'+(st==='auto'?'Validée':st)+'</span></div>';
    });
    document.getElementById('presHistory').innerHTML = html;
  }).catch(function(){});
}

function declarerPresence(reload) {
  api('presence_declarer','POST',{}).then(function(){
    toast('Arrivée déclarée, en attente de validation');
    loadPresence(); if(reload!==true) loadHome();
  }).catch(function(e){ toast(e.msg||'Erreur'); });
}
function pointerSortie() {
  api('presence_depart','POST',{}).then(function(){
    toast('Sortie enregistrée');
    loadPresence();
  }).catch(function(e){ toast(e.msg||'Erreur'); });
}

// ---------- Congés ----------
function loadConges() {
  api('conges','GET').then(function(j){
    var list = j.data || [];
    var html = '';
    if (!list.length) html = '<div class="empty"><i>🗓</i>Aucun congé</div>';
    list.forEach(function(c){
      html += '<div class="list-item"><div class="ic">🗓</div><div class="ct"><div class="tt">'+cap(c.type_conge)+' · '+c.nombre_jours+' j</div><div class="dd">'+formatDate(c.date_debut)+' → '+formatDate(c.date_fin)+'</div></div><span class="st '+c.statut+'">'+c.statut+'</span></div>';
    });
    document.getElementById('congeList').innerHTML = html;
  }).catch(function(){ document.getElementById('congeList').innerHTML='<div class="empty"><i>⚠</i>Erreur</div>'; });
}

function submitConge() {
  var type = document.getElementById('congeType').value;
  var deb = document.getElementById('congeDebut').value;
  var fin = document.getElementById('congeFin').value;
  var motif = document.getElementById('congeMotif').value;
  if (!deb || !fin) { toast('Choisissez les dates'); return; }
  api('conge_demander','POST',{ type_conge:type, date_debut:deb, date_fin:fin, motif:motif }).then(function(){
    toast('Demande envoyée');
    closeModal('congeModal');
    loadConges();
  }).catch(function(e){ toast(e.msg||'Erreur'); });
}

// ---------- Notifications ----------
function loadNotifs() {
  api('notifications','GET').then(function(j){
    var list = (j.data||{}).liste || [];
    var nonlues = (j.data||{}).non_lues || 0;
    document.getElementById('bellBadge').textContent = nonlues;
    document.getElementById('bellBadge').style.display = nonlues ? 'flex' : 'none';
    var html = '';
    if (!list.length) html = '<div class="empty"><i>🔔</i>Aucune notification</div>';
    list.forEach(function(n){
      var unread = n.est_lu==0 ? ' style="background:rgba(59,130,246,.06)"' : '';
      html += '<div class="card"'+unread+'><div style="display:flex;gap:10px"><div class="ic">'+(n.type_notif==='conges'?'🗓':'🔔')+'</div><div><h3 style="font-size:14px">'+n.titre+'</h3><div class="sub">'+n.message+'</div><div style="font-size:11px;color:var(--muted);margin-top:4px">'+formatDate(n.created_at,'datetime')+'</div></div></div></div>';
    });
    document.getElementById('notifList').innerHTML = html;
    if (nonlues) api('notifications_lues','POST',{}).catch(function(){});
  }).catch(function(){});
}

// ---------- Profil ----------
function loadProfil() {
  api('profil','GET').then(function(j){
    var d = j.data || {};
    document.getElementById('profName').textContent = d.nom_complet || '';
    document.getElementById('profRole').textContent = (d.role||'').toUpperCase();
    var e = d.employe;
    var info = '';
    if (e) {
      info += '<div style="text-align:left;font-size:13px;color:var(--muted)">';
      info += '<div>Matricule : <b style="color:var(--text)">'+e.matricule+'</b></div>';
      info += '<div>Poste : <b style="color:var(--text)">'+e.poste+'</b></div>';
      info += '<div>Service : <b style="color:var(--text)">'+e.service+'</b></div>';
      info += '<div>Téléphone : <b style="color:var(--text)">'+(e.telephone||'—')+'</b></div>';
      info += '</div>';
    } else info = '<div class="sub">Compte administrateur</div>';
    document.getElementById('profInfo').innerHTML = info;
  }).catch(function(){});
}

// ---------- Helpers ----------
function formatMoney(n){ return Number(n||0).toLocaleString('fr-FR',{style:'currency',currency:'USD',maximumFractionDigits:0}); }
function formatDate(s, mode){
  if(!s) return '—';
  var d = new Date(s.indexOf('T')>=0 ? s : s.replace(' ','T'));
  if(isNaN(d)) return s;
  var j=d.getDate(),m=d.getMonth()+1,a=d.getFullYear();
  if(mode==='datetime'){ return ('0'+j).slice(-2)+'/'+('0'+m).slice(-2)+'/'+a+' '+('0'+d.getHours()).slice(-2)+':'+('0'+d.getMinutes()).slice(-2); }
  return ('0'+j).slice(-2)+'/'+('0'+m).slice(-2)+'/'+a;
}
function cap(s){ if(!s) return ''; return s.charAt(0).toUpperCase()+s.slice(1); }
function toast(msg){
  var t=document.createElement('div');
  t.style.cssText='position:fixed;bottom:90px;left:50%;transform:translateX(-50%);background:var(--panel);color:var(--text);padding:11px 18px;border-radius:10px;z-index:100;font-size:13.5px;border:1px solid rgba(255,255,255,.1);box-shadow:0 8px 24px rgba(0,0,0,.4);transition:opacity .3s';
  t.textContent=msg;
  document.body.appendChild(t);
  setTimeout(function(){t.style.opacity='0';},2500);
  setTimeout(function(){t.remove();},2900);
}

// ---------- Service Worker + Install ----------
function registerSW(){
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js').catch(function(){});
  }
  // Install prompt
  window.addEventListener('beforeinstallprompt', function(e){
    e.preventDefault();
    deferredPrompt = e;
    setTimeout(function(){ document.getElementById('installBanner').classList.add('show'); }, 800);
  });
}
function installApp(){
  if (!deferredPrompt) { toast('Utilisez le menu navigateur → Ajouter à l\'écran d\'accueil'); return; }
  deferredPrompt.prompt();
  deferredPrompt.userChoice.then(function(){
    document.getElementById('installBanner').classList.remove('show');
    deferredPrompt=null;
  });
}

// ---------- Init ----------
if (token && user) {
  enterApp();
} else {
  document.getElementById('loginScreen').style.display='flex';
}
</script>
</body>
</html>
