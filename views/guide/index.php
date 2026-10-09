<?php $pageTitle = 'Guide d\'utilisation'; ?>
<?php ob_start(); ?>

<style>
html { scroll-behavior: smooth; }

.guide-hero {
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 45%, #0ea5e9 100%);
    border: none;
    border-radius: 18px;
    color: #fff;
    padding: 34px 36px;
    box-shadow: 0 18px 40px -18px rgba(37, 99, 235, .75);
    position: relative;
    overflow: hidden;
    animation: guideFade .7s cubic-bezier(.16,1,.3,1) both;
}
.guide-hero::after {
    content: "";
    position: absolute;
    right: -60px; top: -60px;
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(255,255,255,.25) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
}
.guide-hero h1 { font-size: 1.9rem; font-weight: 800; margin: 0 0 6px; letter-spacing: -.5px; }
.guide-hero p { margin: 0; opacity: .92; font-size: 1.02rem; }
.guide-hero .chips { margin-top: 18px; display: flex; flex-wrap: wrap; gap: 8px; }
.guide-hero .chip {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.28);
    border-radius: 999px;
    padding: 5px 13px;
    font-size: .8rem;
    font-weight: 600;
    backdrop-filter: blur(4px);
}

/* Barre de progression de lecture */
.guide-progress {
    position: fixed; top: 0; left: 0; height: 3px; width: 0;
    background: linear-gradient(90deg, #2563eb, #22d3ee);
    z-index: 9999;
    transition: width .15s linear;
}

/* Sommaire */
.toc {
    position: sticky; top: 70px;
    border: none; border-radius: 16px;
    box-shadow: 0 10px 30px -18px rgba(15, 23, 42, .45);
    overflow: hidden;
}
.toc .toc-title {
    font-size: .78rem; letter-spacing: .12em; text-transform: uppercase;
    color: #64748b; font-weight: 700; padding: 16px 18px 8px;
}
.toc a {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 18px; font-size: .9rem; color: #475569;
    border-left: 3px solid transparent;
    transition: background .2s ease, color .2s ease, border-color .2s ease;
}
.toc a i { width: 16px; text-align: center; font-size: .82rem; opacity: .75; }
.toc a:hover { background: #f1f5f9; color: #1d4ed8; border-left-color: #93c5fd; }
.toc a.active {
    background: linear-gradient(90deg, #eff6ff, #fff);
    color: #1d4ed8; font-weight: 700;
    border-left-color: #2563eb;
}
.toc a.active i { opacity: 1; }

/* Cartes de section */
.g-sec {
    border: none; border-radius: 16px;
    box-shadow: 0 10px 30px -20px rgba(15, 23, 42, .55);
    margin-bottom: 26px;
    overflow: hidden;
    scroll-margin-top: 84px;
}
.g-sec .card-header {
    background: #fff;
    border-bottom: 1px solid #eef2f7;
    padding: 18px 24px;
    display: flex; align-items: center; gap: 14px;
}
.g-sec .ico {
    width: 42px; height: 42px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.05rem; color: #fff; flex-shrink: 0;
}
.g-sec .num {
    margin-left: auto;
    font-size: .72rem; font-weight: 800; letter-spacing: .1em;
    color: #94a3b8; text-transform: uppercase;
}
.g-sec .card-title { font-size: 1.08rem; font-weight: 700; color: #0f172a; margin: 0; }
.g-sec .card-body { padding: 26px 28px; font-size: .95rem; line-height: 1.75; color: #334155; }

.g-lead { font-size: 1rem; color: #475569; margin-bottom: 20px; }
.g-sub {
    font-size: .8rem; font-weight: 800; letter-spacing: .1em;
    text-transform: uppercase; color: #94a3b8; margin: 22px 0 12px;
}
.g-sec .card-body ul { margin-bottom: 0; }
.g-sec .card-body li { margin-bottom: 9px; }
.g-sec .card-body a:not(.btn) {
    color: #1d4ed8; font-weight: 600;
    border-bottom: 1px dashed rgba(37, 99, 235, .45);
    text-decoration: none;
    transition: color .2s ease, background .2s ease, border-color .2s ease;
}
.g-sec .card-body a:not(.btn):hover { color: #0ea5e9; border-bottom-style: solid; background: #eff6ff; }

/* Étapes numérotées (timeline) */
.steps { list-style: none; padding: 0; margin: 0; counter-reset: gstep; }
.steps > li {
    position: relative;
    display: flex; gap: 18px;
    padding-bottom: 22px;
}
.steps > li:last-child { padding-bottom: 0; }
.steps > li::before {
    content: "";
    position: absolute; left: 19px; top: 40px; bottom: -4px;
    width: 2px; background: linear-gradient(#dbeafe, #f1f5f9);
}
.steps > li:last-child::before { display: none; }
.step-n {
    counter-increment: gstep;
    width: 40px; height: 40px; flex-shrink: 0;
    border-radius: 50%;
    background: linear-gradient(135deg, #2563eb, #38bdf8);
    color: #fff; font-weight: 800; font-size: .95rem;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 6px 16px -6px rgba(37, 99, 235, .8);
    transition: transform .25s ease;
}
.steps > li:hover .step-n { transform: scale(1.12) rotate(-4deg); }
.step-b h6 { font-size: .98rem; font-weight: 700; color: #0f172a; margin: 6px 0 4px; }
.step-b p { margin: 0; color: #64748b; font-size: .9rem; }

/* Colonne détail (2 colonnes internes) */
.g-cols { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 26px; }
.g-col { background: #f8fafc; border: 1px solid #eef2f7; border-radius: 14px; padding: 20px 22px; }
.g-col h6 { font-weight: 800; color: #0f172a; margin-bottom: 14px; font-size: .95rem; }

/* Badges de rôle */
.role {
    display: inline-block; padding: 3px 10px; border-radius: 999px;
    font-size: .68rem; font-weight: 800; letter-spacing: .04em;
    text-transform: uppercase; vertical-align: middle;
}
.role-admin { background: #fef3c7; color: #92400e; }
.role-rh { background: #dbeafe; color: #1e40af; }
.role-all { background: #dcfce7; color: #166534; }

/* Tableaux */
.g-sec .table { border-radius: 12px; overflow: hidden; }
.g-sec .table th { background: #f8fafc; font-size: .78rem; text-transform: uppercase; letter-spacing: .06em; color: #64748b; border-top: none; }

/* Encadré info */
.g-note {
    background: #eff6ff; border: 1px solid #dbeafe;
    border-left: 4px solid #2563eb;
    border-radius: 10px; padding: 13px 16px;
    font-size: .9rem; color: #1e3a8a; margin-top: 20px;
}
.g-warn {
    background: #fffbeb; border: 1px solid #fde68a;
    border-left: 4px solid #f59e0b;
    border-radius: 10px; padding: 13px 16px;
    font-size: .9rem; color: #78350f; margin-top: 20px;
}

/* Carte état de la base */
.state-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px -20px rgba(15,23,42,.5); }
.state-num { font-size: 1.9rem; font-weight: 800; color: #1d4ed8; line-height: 1; }

/* Animations d'apparition */
.reveal { opacity: 0; transform: translateY(22px); transition: opacity .65s cubic-bezier(.16,1,.3,1), transform .65s cubic-bezier(.16,1,.3,1); }
.reveal.in { opacity: 1; transform: none; }
/* Survol des cartes : placé APRÈS .reveal (spécificité supérieure) pour ne pas
   hériter de la transition d'apparition ni du délai d'échelonnement. */
.card.g-sec.reveal.in { transition: opacity .65s cubic-bezier(.16,1,.3,1), transform .65s cubic-bezier(.16,1,.3,1), box-shadow .3s ease; }
.card.g-sec.reveal.in:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 38px -20px rgba(15, 23, 42, .5);
    transition: transform .25s ease, box-shadow .25s ease;
}
@keyframes guideFade { from { opacity: 0; transform: translateY(-14px); } to { opacity: 1; transform: none; } }

/* Accessibilité : aucun mouvement si l'utilisateur l'a désactivé */
@media (prefers-reduced-motion: reduce) {
    html { scroll-behavior: auto; }
    .reveal, .reveal.in { opacity: 1 !important; transform: none !important; transition: none !important; }
    .guide-hero { animation: none; }
    .guide-progress { transition: none; }
}

@media (max-width: 991px) {
    .toc { position: static; margin-bottom: 24px; }
    .g-sec .card-body { padding: 20px; }
    .guide-hero { padding: 26px 22px; }
}
</style>

<div class="guide-progress" id="guideProgress"></div>

<section class="content-header" style="border:none;padding:0;">
    <div class="guide-hero">
        <h1><i class="fas fa-book-open mr-2"></i>Guide d'utilisation</h1>
        <p>Prenez en main le SIRH en quelques minutes : 14 rubriques courtes, dans l'ordre, avec les écrans à ouvrir.</p>
        <div class="chips">
            <span class="chip"><i class="fas fa-clock mr-1"></i> 5 min de lecture</span>
            <span class="chip"><i class="fas fa-layer-group mr-1"></i> 14 rubriques</span>
            <span class="chip"><i class="fas fa-user-shield mr-1"></i> Rôles indiqués partout</span>
            <span class="chip"><i class="fas fa-redo mr-1"></i> Mise à jour : v<?= APP_VERSION ?></span>
        </div>
    </div>
</section>

<section class="content" style="padding-top: 24px;">
    <div class="row">

        <!-- Sommaire -->
        <div class="col-lg-3">
            <div class="card toc">
                <div class="toc-title"><i class="fas fa-list mr-1"></i> Sommaire</div>
                <nav class="flex-column nav flex-sm-column">
                    <a href="#premiers-pas" class="toc-link"><i class="fas fa-flag text-success"></i> 1. Premiers pas</a>
                    <a href="#tableau-de-bord" class="toc-link"><i class="fas fa-tachometer-alt text-primary"></i> 2. Tableau de bord</a>
                    <a href="#employes" class="toc-link"><i class="fas fa-users text-primary"></i> 3. Employés</a>
                    <a href="#services" class="toc-link"><i class="fas fa-sitemap text-primary"></i> 4. Services</a>
                    <a href="#presences" class="toc-link"><i class="fas fa-fingerprint text-success"></i> 5. Présences (QR)</a>
                    <a href="#conges" class="toc-link"><i class="fas fa-umbrella-beach text-info"></i> 6. Congés</a>
                    <a href="#paie" class="toc-link"><i class="fas fa-money-check-alt text-warning"></i> 7. Paie</a>
                    <a href="#formations" class="toc-link"><i class="fas fa-graduation-cap text-secondary"></i> 8. Formations</a>
                    <a href="#rapports" class="toc-link"><i class="fas fa-chart-bar text-success"></i> 9. Rapports</a>
                    <a href="#messagerie" class="toc-link"><i class="fas fa-comments text-info"></i> 10. Messages</a>
                    <a href="#comptes" class="toc-link"><i class="fas fa-user-shield text-danger"></i> 11. Comptes & rôles</a>
                    <a href="#mobile" class="toc-link"><i class="fas fa-mobile-alt text-primary"></i> 12. Mobile (PWA)</a>
                    <a href="#rythme" class="toc-link"><i class="fas fa-calendar-check text-success"></i> 13. Rythme à suivre</a>
                    <a href="#aide" class="toc-link"><i class="fas fa-life-ring text-danger"></i> 14. Aide & dépannage</a>
                </nav>
            </div>

            <div class="card state-card card-outline card-primary reveal">
                <div class="card-header" style="background:#fff;">
                    <h3 class="card-title" style="font-size:.95rem;"><i class="fas fa-tasks text-primary mr-2"></i>Votre base</h3>
                </div>
                <div class="card-body" style="padding:20px 22px;">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <div class="state-num"><?= (int) $stats['services'] ?></div>
                            <small class="text-muted">service(s)</small>
                        </div>
                        <div>
                            <div class="state-num"><?= (int) $stats['employes'] ?></div>
                            <small class="text-muted">employé(s) actif(s)</small>
                        </div>
                    </div>
                    <?php if ($stats['services'] === 0 || $stats['employes'] === 0): ?>
                        <div class="g-warn" style="margin-top:0;"><i class="fas fa-exclamation-triangle mr-1"></i> Commencez par l'étape 1.</div>
                    <?php else: ?>
                        <div class="g-note" style="margin-top:0;"><i class="fas fa-check-circle mr-1"></i> Base opérationnelle.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Contenu -->
        <div class="col-lg-9">

            <!-- 1 -->
            <div class="card g-sec reveal" id="premiers-pas">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#16a34a,#4ade80);"><i class="fas fa-flag"></i></span>
                    <span class="card-title">Premiers pas <small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">À faire une seule fois, à l'installation</small></span>
                    <span class="num">Étape 1 / 6</span>
                </div>
                <div class="card-body">
                    <ol class="steps">
                        <li>
                            <span class="step-n">1</span>
                            <div class="step-b">
                                <h6>Créez vos services <span class="role role-admin">Admin</span></h6>
                                <p>Direction, Comptabilité, Production… ces départements structurent tout le logiciel.</p>
                                <a href="<?= APP_URL ?>/services"><i class="fas fa-external-link-alt mr-1"></i>Ouvrir les services</a>
                            </div>
                        </li>
                        <li>
                            <span class="step-n">2</span>
                            <div class="step-b">
                                <h6>Ajoutez vos employés <span class="role role-admin">Admin</span> <span class="role role-rh">RH</span></h6>
                                <p>Nom, service, poste, salaire de base : le matricule est généré automatiquement.</p>
                                <a href="<?= APP_URL ?>/employees/create"><i class="fas fa-external-link-alt mr-1"></i>Créer un employé</a>
                            </div>
                        </li>
                        <li>
                            <span class="step-n">3</span>
                            <div class="step-b">
                                <h6>Créez leurs comptes de connexion <span class="role role-admin">Admin</span></h6>
                                <p>Chaque compte est rattaché à l'employé. Mot de passe : 8 caractères minimum.</p>
                                <a href="<?= APP_URL ?>/utilisateurs/create"><i class="fas fa-external-link-alt mr-1"></i>Créer un utilisateur</a>
                            </div>
                        </li>
                        <li>
                            <span class="step-n">4</span>
                            <div class="step-b">
                                <h6>Vérifiez les paramètres de paie <span class="role role-admin">Admin</span></h6>
                                <p>Impôts, cotisations, primes, heures mensuelles : tout est calculé à partir de ces valeurs.</p>
                                <a href="<?= APP_URL ?>/paie/parametres"><i class="fas fa-external-link-alt mr-1"></i>Paramètres de paie</a>
                            </div>
                        </li>
                        <li>
                            <span class="step-n">5</span>
                            <div class="step-b">
                                <h6>Montrez le pointage QR à vos équipes <span class="role role-all">Tout le monde</span></h6>
                                <p>Chacun affiche son QR à l'arrivée, la réception scanne. Deux scans par jour : arrivée puis départ.</p>
                                <a href="<?= APP_URL ?>/presences/qr"><i class="fas fa-external-link-alt mr-1"></i>Mon QR d'arrivée</a>
                            </div>
                        </li>
                        <li>
                            <span class="step-n">6</span>
                            <div class="step-b">
                                <h6>Installez l'application mobile <span class="role role-all">Tout le monde</span></h6>
                                <p>Ajoutez la page aux écrans d'accueil des téléphones pour un accès en un geste.</p>
                                <a href="<?= APP_URL ?>/mobile.php"><i class="fas fa-external-link-alt mr-1"></i>Ouvrir la version mobile</a>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- 2 -->
            <div class="card g-sec reveal" id="tableau-de-bord">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#2563eb,#38bdf8);"><i class="fas fa-tachometer-alt"></i></span>
                    <span class="card-title">Tableau de bord <small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Votre page d'accueil</small></span>
                    <span class="num">Rubrique 2</span>
                </div>
                <div class="card-body">
                    <p class="g-lead">Il répond à une seule question : <strong>où en est-on aujourd'hui ?</strong></p>
                    <div class="g-cols">
                        <div class="g-col">
                            <h6><i class="fas fa-th-large text-primary mr-2"></i>Cartes d'indicateurs</h6>
                            Effectif total, présences du jour, congés en attente.
                        </div>
                        <div class="g-col">
                            <h6><i class="fas fa-chart-pie text-success mr-2"></i>Graphiques</h6>
                            Répartition par service, par sexe, évolution des congés.
                        </div>
                        <div class="g-col">
                            <h6><i class="fas fa-bolt text-warning mr-2"></i>Raccourcis</h6>
                            Les actions les plus fréquentes, en un clic.
                        </div>
                    </div>
                    <div class="mt-3"><a href="<?= APP_URL ?>/dashboard" class="btn btn-primary btn-sm"><i class="fas fa-tachometer-alt mr-1"></i>Ouvrir le tableau de bord</a></div>
                </div>
            </div>

            <!-- 3 -->
            <div class="card g-sec reveal" id="employes">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#2563eb,#6366f1);"><i class="fas fa-users"></i></span>
                    <span class="card-title">Employés <span class="role role-admin ml-2">Admin</span><span class="role role-rh">RH</span><small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Le dossier numérique de chaque personne</small></span>
                    <span class="num">Rubrique 3</span>
                </div>
                <div class="card-body">
                    <ol class="steps">
                        <li><span class="step-n">1</span><div class="step-b"><h6>Créer</h6><p>Identité, service, poste, salaire de base et primes.</p><a href="<?= APP_URL ?>/employees/create">Nouvel employé</a></div></li>
                        <li><span class="step-n">2</span><div class="step-b"><h6>Consulter</h6><p>Cliquez sur un employé pour ouvrir sa fiche complète (historique, primes).</p></div></li>
                        <li><span class="step-n">3</span><div class="step-b"><h6>Modifier</h6><p>Changement de service, de poste, de statut : <span class="badge badge-success">actif</span> ou <span class="badge badge-warning">suspendu</span>.</p></div></li>
                        <li><span class="step-n">4</span><div class="step-b"><h6>Exporter</h6><p>La liste s'exporte en Excel ou PDF depuis la page des employés.</p><a href="<?= APP_URL ?>/export/employees/excel">Télécharger l'Excel</a></div></li>
                    </ol>
                    <div class="g-note"><i class="fas fa-info-circle mr-1"></i> Un employé sans compte utilisateur ne peut pas se connecter : créez les deux (étapes 2 et 3).</div>
                </div>
            </div>

            <!-- 4 -->
            <div class="card g-sec reveal" id="services">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8);"><i class="fas fa-sitemap"></i></span>
                    <span class="card-title">Services <span class="role role-admin ml-2">Admin</span><small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Les départements de l'entreprise</small></span>
                    <span class="num">Rubrique 4</span>
                </div>
                <div class="card-body">
                    <p class="g-lead">Chaque service regroupe les employés et alimente directement les statistiques, les congés et le tableau de bord.</p>
                    <ul>
                        <li><a href="<?= APP_URL ?>/services">Gérer les services</a> : créer, renommer, supprimer.</li>
                        <li>Le nombre d'employés de chaque service s'affiche automatiquement.</li>
                    </ul>
                </div>
            </div>

            <!-- 5 -->
            <div class="card g-sec reveal" id="presences">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#16a34a,#22c55e);"><i class="fas fa-fingerprint"></i></span>
                    <span class="card-title">Présences & pointage QR<small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Deux scans par jour : arrivée puis départ</small></span>
                    <span class="num">Rubrique 5</span>
                </div>
                <div class="card-body">
                    <div class="g-cols">
                        <div class="g-col">
                            <h6><i class="fas fa-user text-primary mr-2"></i>Côté employé <span class="role role-all">Tout le monde</span></h6>
                            <ol class="steps">
                                <li><span class="step-n">1</span><div class="step-b"><p>Ouvrez <a href="<?= APP_URL ?>/presences">Présences</a> puis « Afficher mon QR ».</p></div></li>
                                <li><span class="step-n">2</span><div class="step-b"><p>Présentez-le à la réception : le <strong>premier scan enregistre l'arrivée</strong>.</p></div></li>
                                <li><span class="step-n">3</span><div class="step-b"><p>Avant de partir, réaffichez le QR : le <strong>deuxième scan enregistre le départ</strong>.</p></div></li>
                            </ol>
                        </div>
                        <div class="g-col">
                            <h6><i class="fas fa-desktop text-success mr-2"></i>Côté réception / RH <span class="role role-admin">Admin</span> <span class="role role-rh">RH</span></h6>
                            <ol class="steps">
                                <li><span class="step-n">1</span><div class="step-b"><p>Ouvrez le <a href="<?= APP_URL ?>/presences/scan">poste de réception</a> et scannez le QR de l'employé.</p></div></li>
                                <li><span class="step-n">2</span><div class="step-b"><p>Validez, rejetez ou régularisez une absence depuis <a href="<?= APP_URL ?>/presences">Présences</a>.</p></div></li>
                                <li><span class="step-n">3</span><div class="step-b"><p>Suivez les statuts : <span class="badge badge-success">présent</span> <span class="badge badge-warning">retard</span> <span class="badge badge-danger">absent</span>.</p></div></li>
                            </ol>
                        </div>
                    </div>
                    <div class="g-note"><i class="fas fa-info-circle mr-1"></i> Le QR change automatiquement à chaque affichage : il ne peut pas être réutilisé. Sans scanner, le RH peut pointer manuellement.</div>
                </div>
            </div>

            <!-- 6 -->
            <div class="card g-sec reveal" id="conges">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#0ea5e9,#67e8f9);"><i class="fas fa-umbrella-beach"></i></span>
                    <span class="card-title">Congés <small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Demander, approuver, suivre</small></span>
                    <span class="num">Rubrique 6</span>
                </div>
                <div class="card-body">
                    <div class="g-cols">
                        <div class="g-col">
                            <h6><i class="fas fa-user text-primary mr-2"></i>L'employé demande</h6>
                            <ol class="steps">
                                <li><span class="step-n">1</span><div class="step-b"><p><a href="<?= APP_URL ?>/conges">Congés</a> → <a href="<?= APP_URL ?>/conges/create">nouvelle demande</a>.</p></div></li>
                                <li><span class="step-n">2</span><div class="step-b"><p>Type et dates : le nombre de jours est calculé automatiquement.</p></div></li>
                                <li><span class="step-n">3</span><div class="step-b"><p>Suivi : <span class="badge badge-warning">en attente</span> <span class="badge badge-success">approuvé</span> <span class="badge badge-danger">refusé</span>.</p></div></li>
                            </ol>
                        </div>
                        <div class="g-col">
                            <h6><i class="fas fa-user-tie text-success mr-2"></i>L'admin / RH tranche <span class="role role-admin">Admin</span> <span class="role role-rh">RH</span></h6>
                            <ol class="steps">
                                <li><span class="step-n">1</span><div class="step-b"><p>Les demandes en attente sont signalées dans <a href="<?= APP_URL ?>/conges">Congés</a>.</p></div></li>
                                <li><span class="step-n">2</span><div class="step-b"><p><strong>Approuver</strong> ou <strong>Refuser</strong> en un clic.</p></div></li>
                                <li><span class="step-n">3</span><div class="step-b"><p>Une notification part vers l'employé, les chiffres du tableau de bord se mettent à jour.</p></div></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7 -->
            <div class="card g-sec reveal" id="paie">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);"><i class="fas fa-money-check-alt"></i></span>
                    <span class="card-title">Paie & bulletins <span class="role role-admin ml-2">Admin</span><span class="role role-rh">RH</span><small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Paramètres une fois, puis chaque fin de mois</small></span>
                    <span class="num">Rubrique 7</span>
                </div>
                <div class="card-body">
                    <p class="g-sub">Une fois : les paramètres <span class="role role-admin">Admin</span></p>
                    <p>Dans <a href="<?= APP_URL ?>/paie/parametres">Paramètres de paie</a>, ces valeurs servent au calcul automatique :</p>
                    <div class="g-cols">
                        <div class="g-col"><h6><i class="fas fa-percentage text-warning mr-2"></i>Sur le brut</h6>Taxe professionnelle, prestation sociale, pension retraite.</div>
                        <div class="g-col"><h6><i class="fas fa-home text-warning mr-2"></i>Sur le salaire de base</h6>Indemnité logement, prime transport.</div>
                        <div class="g-col"><h6><i class="fas fa-clock text-warning mr-2"></i>Temps de travail</h6>Taux heure supplémentaire, heures travaillées par mois.</div>
                    </div>

                    <p class="g-sub">Chaque mois</p>
                    <ol class="steps">
                        <li><span class="step-n">1</span><div class="step-b"><h6>Choisir le mois</h6><p><a href="<?= APP_URL ?>/paie">Page Paie</a> → sélectionnez la période concernée.</p></div></li>
                        <li><span class="step-n">2</span><div class="step-b"><h6>Générer les bulletins</h6><p>Brut, retenues et net sont calculés automatiquement pour chaque employé.</p></div></li>
                        <li><span class="step-n">3</span><div class="step-b"><h6>Valider puis payer</h6><p>Le bulletin passe en statut payé et rejoint les <a href="<?= APP_URL ?>/paie/archives">archives</a>.</p></div></li>
                    </ol>

                    <div class="g-note"><i class="fas fa-user-lock mr-1"></i> Chaque employé retrouve <strong>ses seuls bulletins</strong> dans <a href="<?= APP_URL ?>/paie/mes-bulletins">Mes bulletins</a> — personne d'autre n'y a accès.</div>
                </div>
            </div>

            <!-- 8 -->
            <div class="card g-sec reveal" id="formations">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#64748b,#94a3b8);"><i class="fas fa-graduation-cap"></i></span>
                    <span class="card-title">Formations <span class="role role-admin ml-2">Admin</span><span class="role role-rh">RH</span><small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Planifier et suivre les montées en compétence</small></span>
                    <span class="num">Rubrique 8</span>
                </div>
                <div class="card-body">
                    <ol class="steps">
                        <li><span class="step-n">1</span><div class="step-b"><h6>Créer une formation</h6><p>Titre, durée, dates, type interne ou externe. <a href="<?= APP_URL ?>/formations/create">Nouvelle formation</a></p></div></li>
                        <li><span class="step-n">2</span><div class="step-b"><h6>Inscrire les participants</h6><p>Statuts : <span class="badge badge-info">en cours</span> <span class="badge badge-success">terminé</span> <span class="badge badge-danger">annulé</span>, avec observation.</p></div></li>
                        <li><span class="step-n">3</span><div class="step-b"><h6>Suivre</h6><p>Notes et avancement visibles sur la liste des formations.</p></div></li>
                    </ol>
                </div>
            </div>

            <!-- 9 -->
            <div class="card g-sec reveal" id="rapports">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#16a34a,#4ade80);"><i class="fas fa-chart-bar"></i></span>
                    <span class="card-title">Rapports & statistiques <span class="role role-admin ml-2">Admin</span><span class="role role-rh">RH</span><small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Les chiffres pour décider</small></span>
                    <span class="num">Rubrique 9</span>
                </div>
                <div class="card-body">
                    <div class="g-cols">
                        <div class="g-col"><h6><i class="fas fa-chart-line text-success mr-2"></i>Vue d'ensemble</h6><a href="<?= APP_URL ?>/rapports">Indicateurs consolidés</a> de l'entreprise.</div>
                        <div class="g-col"><h6><i class="fas fa-user-slash text-danger mr-2"></i>Absentéisme</h6><a href="<?= APP_URL ?>/rapports/absentisme">Qui manque, combien de fois, sur quelles périodes.</a></div>
                        <div class="g-col"><h6><i class="fas fa-umbrella-beach text-info mr-2"></i>Congés</h6><a href="<?= APP_URL ?>/rapports/conges">Soldes et historique par employé.</a></div>
                    </div>
                    <div class="g-note"><i class="fas fa-file-excel mr-1"></i> Exports Excel/PDF disponibles sur les pages employés, présences, congés et paie.</div>
                </div>
            </div>

            <!-- 10 -->
            <div class="card g-sec reveal" id="messagerie">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8);"><i class="fas fa-comments"></i></span>
                    <span class="card-title">Messages & notifications <span class="role role-all ml-2">Tout le monde</span><small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Communiquer dans l'outil</small></span>
                    <span class="num">Rubrique 10</span>
                </div>
                <div class="card-body">
                    <div class="g-cols">
                        <div class="g-col">
                            <h6><i class="fas fa-comment-dots text-info mr-2"></i>Messages</h6>
                            <ul>
                                <li><a href="<?= APP_URL ?>/messages/nouveau">Écrire</a> à un collègue (confidentiel).</li>
                                <li><a href="<?= APP_URL ?>/messages/annonces">Annonces</a> : publication visible par tous.</li>
                                <li>Les conversations non lues portent un badge rouge.</li>
                            </ul>
                        </div>
                        <div class="g-col">
                            <h6><i class="fas fa-bell text-warning mr-2"></i>Notifications</h6>
                            <ul class="mb-0">
                                <li>Demandes de congé, bulletins, validations : tout arrive dans la cloche.</li>
                                <li>Un clic ouvre directement l'écran concerné.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 11 -->
            <div class="card g-sec reveal" id="comptes">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#dc2626,#f87171);"><i class="fas fa-user-shield"></i></span>
                    <span class="card-title">Comptes, rôles & sécurité <span class="role role-admin ml-2">Admin</span><small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Qui a le droit de faire quoi</small></span>
                    <span class="num">Rubrique 11</span>
                </div>
                <div class="card-body">
                    <p class="g-lead">Dans <a href="<?= APP_URL ?>/utilisateurs">Gérer les utilisateurs</a> : créer un compte, réinitialiser un mot de passe, désactiver un compte le jour du départ.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead><tr><th style="width:160px;">Rôle</th><th>Ce qu'il peut faire</th></tr></thead>
                            <tbody>
                                <tr><td><span class="role role-admin">Admin</span></td><td>Tout : services, employés, paie, rapports, utilisateurs, paramètres, journal d'activité.</td></tr>
                                <tr><td><span class="role role-rh">RH</span></td><td>Employés, validation des présences, approbation des congés, paie, formations, rapports, exports.</td></tr>
                                <tr><td><span class="role role-all">Employé</span></td><td>Son tableau de bord, son pointage QR, ses demandes de congé, ses bulletins, les messages.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="g-note" style="margin-top:22px;"><i class="fas fa-history mr-1"></i> <a href="<?= APP_URL ?>/journal">Journal d'activité</a> : trace de chaque action (qui, quoi, quand) — indispensable en cas de litige.</div>
                </div>
            </div>

            <!-- 12 -->
            <div class="card g-sec reveal" id="mobile">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#2563eb,#6366f1);"><i class="fas fa-mobile-alt"></i></span>
                    <span class="card-title">Application mobile (PWA) <span class="role role-all ml-2">Tout le monde</span><small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Le SIRH dans la poche</small></span>
                    <span class="num">Rubrique 12</span>
                </div>
                <div class="card-body">
                    <ol class="steps">
                        <li><span class="step-n">1</span><div class="step-b"><p>Ouvrez <a href="<?= APP_URL ?>/mobile.php">l'interface mobile</a> depuis le téléphone.</p></div></li>
                        <li><span class="step-n">2</span><div class="step-b"><p>Dans le menu du navigateur, choisissez <strong>« Ajouter à l'écran d'accueil »</strong>.</p></div></li>
                        <li><span class="step-n">3</span><div class="step-b"><p>L'application s'ouvre en plein écran : accueil, <strong>QR personnel</strong>, congés, messages, profil.</p></div></li>
                    </ol>
                </div>
            </div>

            <!-- 13 -->
            <div class="card g-sec reveal" id="rythme">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#16a34a,#22c55e);"><i class="fas fa-calendar-check"></i></span>
                    <span class="card-title">Le bon rythme de travail<small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Ce qu'il faut faire, et quand</small></span>
                    <span class="num">Rubrique 13</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead><tr><th style="width:170px;">Fréquence</th><th>Que faire</th></tr></thead>
                            <tbody>
                                <tr><td><span class="badge badge-success p-2">Chaque jour</span></td><td>Contrôler les pointages, valider les arrivées, répondre aux demandes de congé.</td></tr>
                                <tr><td><span class="badge badge-info p-2">Chaque semaine</span></td><td>Consulter l'absentéisme, vérifier les formations en cours, archiver les messages traités.</td></tr>
                                <tr><td><span class="badge badge-warning p-2">Chaque mois</span></td><td>Compléter les présences, <strong>générer et payer les bulletins</strong>, exporter les rapports.</td></tr>
                                <tr><td><span class="badge badge-danger p-2">Au départ</span></td><td>Passer l'employé en <em>suspendu</em>, puis désactiver son compte utilisateur.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 14 -->
            <div class="card g-sec reveal" id="aide">
                <div class="card-header">
                    <span class="ico" style="background:linear-gradient(135deg,#dc2626,#f87171);"><i class="fas fa-life-ring"></i></span>
                    <span class="card-title">Aide & dépannage<small class="d-block text-muted font-weight-normal" style="font-size:.82rem;">Les cas les plus fréquents</small></span>
                    <span class="num">Rubrique 14</span>
                </div>
                <div class="card-body">
                    <div class="g-cols">
                        <div class="g-col">
                            <h6><i class="fas fa-key text-warning mr-2"></i>Mot de passe oublié</h6>
                            Utilisez <a href="<?= APP_URL ?>/forgot-password">Mot de passe oublié ?</a> sur la page de connexion, ou demandez à l'administrateur de le réinitialiser depuis <a href="<?= APP_URL ?>/utilisateurs">Utilisateurs</a>.
                        </div>
                        <div class="g-col">
                            <h6><i class="fas fa-lock text-danger mr-2"></i>« Trop de tentatives »</h6>
                            Le logiciel bloque 5 échecs consécutifs pendant 15 minutes : c'est une protection, patientez puis réessayez.
                        </div>
                        <div class="g-col">
                            <h6><i class="fas fa-qrcode text-success mr-2"></i>Le QR ne s'affiche pas</h6>
                            Vérifiez d'être connecté et que l'heure de l'appareil est correcte : le QR rafraîchit tout seul.
                        </div>
                        <div class="g-col">
                            <h6><i class="fas fa-desktop text-primary mr-2"></i>Page vide ou 403</h6>
                            Votre rôle n'a pas accès à cette page. Revenez au <a href="<?= APP_URL ?>/dashboard">tableau de bord</a> : seuls les menus visibles vous sont ouverts.
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <a href="<?= APP_URL ?>/dashboard" class="btn btn-primary"><i class="fas fa-tachometer-alt mr-1"></i>Retour au tableau de bord</a>
                        <a href="<?= APP_URL ?>/about" class="btn btn-outline-secondary ml-2"><i class="fas fa-info-circle mr-1"></i>À propos</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
(function () {
    var sections = Array.prototype.slice.call(document.querySelectorAll('.g-sec'));
    var links = Array.prototype.slice.call(document.querySelectorAll('.toc-link'));
    var bar = document.getElementById('guideProgress');

    function revealNow(el) {
        if (!el || el.classList.contains('in')) { return; }
        el.classList.add('in');
        el.style.transitionDelay = '';
        if (ioRef) { ioRef.unobserve(el); }
    }

    var ioRef = null;

    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (!e.isIntersecting) { return; }
                var el = e.target;
                el.classList.add('in');
                io.unobserve(el);
                // Le délai d'échelonnement ne doit plus s'appliquer ensuite
                // (sinon le survol de la carte serait décalé d'autant).
                var cleared = false;
                var clearDelay = function (ev) {
                    if (cleared) { return; }
                    if (ev && ev.target !== el) { return; } // ignorer les transitions des enfants
                    cleared = true;
                    el.style.transitionDelay = '';
                    el.removeEventListener('transitionend', clearDelay);
                };
                el.addEventListener('transitionend', clearDelay);
                setTimeout(clearDelay, 1200);
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -5% 0px' });
        ioRef = io;
        document.querySelectorAll('.reveal').forEach(function (el, i) {
            el.style.transitionDelay = (i % 4) * 70 + 'ms';
            io.observe(el);
        });
    } else {
        document.querySelectorAll('.reveal').forEach(function (el) { el.classList.add('in'); });
    }

    // Filet de sécurité : si l'observateur n'a pas marché (navigateur,
    // extension, onglet en arrière-plan), on affiche ce qui est déjà à l'écran.
    setTimeout(function () {
        document.querySelectorAll('.reveal:not(.in)').forEach(function (el) {
            var r = el.getBoundingClientRect();
            if (r.top < window.innerHeight && r.bottom > 0) { revealNow(el); }
        });
    }, 2500);

    // Saut depuis le sommaire : la section visée doit être affichée immédiatement,
    // sans attendre l'observateur pendant le défilement animé.
    links.forEach(function (l) {
        l.addEventListener('click', function () {
            var id = (l.getAttribute('href') || '').slice(1);
            if (!id) { return; }
            var target = document.getElementById(id);
            if (!target) { return; }
            revealNow(target);
            var idx = sections.indexOf(target);
            for (var i = 0; i <= idx && i > -1; i++) { revealNow(sections[i]); }
        });
    });

    function onScroll() {
        var y = window.scrollY || window.pageYOffset;
        var h = document.documentElement.scrollHeight - window.innerHeight;
        if (bar) { bar.style.width = (h > 0 ? Math.min(100, (y / h) * 100) : 0) + '%'; }

        var current = sections[0];
        for (var i = 0; i < sections.length; i++) {
            if (sections[i].getBoundingClientRect().top <= 140) { current = sections[i]; }
        }
        links.forEach(function (l) {
            l.classList.toggle('active', current && l.getAttribute('href') === '#' + current.id);
        });
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
