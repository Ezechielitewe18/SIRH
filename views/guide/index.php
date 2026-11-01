<?php $pageTitle = 'Guide d\'utilisation'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="sirh-page-header">
        <div class="sirh-page-heading">
            <h1 class="sirh-page-title"><i class="fas fa-book-open" aria-hidden="true"></i> Guide d'utilisation</h1>
            <p class="sirh-page-subtitle">Procédures de référence pour utiliser GLOBIT SIRH au quotidien.</p>
        </div>
        <div class="sirh-page-actions">
            <a href="<?= APP_URL ?>/dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-tachometer-alt" aria-hidden="true"></i> Tableau de bord
            </a>
        </div>
    </div>
</section>

<section class="content">
    <div class="guide-layout">

        <aside class="guide-toc" aria-label="Sommaire du guide">
            <p class="guide-toc-title">Sommaire</p>
            <nav>
                <a href="#premiers-pas" class="is-active"><i class="fas fa-flag" aria-hidden="true"></i> 1. Premiers pas</a>
                <a href="#tableau-de-bord"><i class="fas fa-tachometer-alt" aria-hidden="true"></i> 2. Tableau de bord</a>
                <a href="#employes"><i class="fas fa-user-tie" aria-hidden="true"></i> 3. Employés</a>
                <a href="#services"><i class="fas fa-building" aria-hidden="true"></i> 4. Services</a>
                <a href="#presences"><i class="fas fa-fingerprint" aria-hidden="true"></i> 5. Présences</a>
                <a href="#conges"><i class="fas fa-calendar-alt" aria-hidden="true"></i> 6. Congés</a>
                <a href="#paie"><i class="fas fa-money-bill-wave" aria-hidden="true"></i> 7. Paie</a>
                <a href="#formations"><i class="fas fa-graduation-cap" aria-hidden="true"></i> 8. Formations</a>
                <a href="#rapports"><i class="fas fa-chart-bar" aria-hidden="true"></i> 9. Rapports</a>
                <a href="#messagerie"><i class="fas fa-comments" aria-hidden="true"></i> 10. Messages</a>
                <a href="#comptes"><i class="fas fa-user-shield" aria-hidden="true"></i> 11. Comptes et rôles</a>
                <a href="#mobile"><i class="fas fa-mobile-alt" aria-hidden="true"></i> 12. Version mobile</a>
                <a href="#rythme"><i class="fas fa-calendar-check" aria-hidden="true"></i> 13. Rythme de travail</a>
                <a href="#aide"><i class="fas fa-life-ring" aria-hidden="true"></i> 14. Aide et dépannage</a>
            </nav>
        </aside>

        <div>
            <div class="guide-intro">
                <h2 class="h4" style="margin:0 0 6px;color:var(--sirh-navy);font-weight:700;">Mode d'emploi</h2>
                <p>Ce guide décrit les écrans, les boutons et l'ordre des opérations. Les mentions de rôle indiquent qui peut effectuer chaque action.</p>
                <div class="guide-intro-meta">
                    <span class="guide-meta-item"><i class="fas fa-layer-group" aria-hidden="true"></i> 14 rubriques</span>
                    <span class="guide-meta-item"><i class="fas fa-clock" aria-hidden="true"></i> Lecture : environ 10 minutes</span>
                    <span class="guide-meta-item"><i class="fas fa-code-branch" aria-hidden="true"></i> Version <?= htmlspecialchars(APP_VERSION) ?></span>
                    <span class="guide-meta-item"><i class="fas fa-database" aria-hidden="true"></i> <?= (int) $stats['services'] ?> service(s) · <?= (int) $stats['employes'] ?> employé(s) actif(s)</span>
                </div>
            </div>

            <?php if ($stats['services'] === 0 || $stats['employes'] === 0): ?>
            <div class="guide-warn" style="margin-top:0;margin-bottom:18px;">
                <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                <span>La base ne contient pas encore de service ou d'employé. Commencez par la rubrique 1 avant d'utiliser les autres modules.</span>
            </div>
            <?php endif; ?>

            <section class="guide-section" id="premiers-pas">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-flag"></i></span>
                    <div>
                        <h2 class="guide-section-title">1. Premiers pas</h2>
                        <span class="guide-section-sub">À réaliser une seule fois, à la mise en service.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 1</span>
                </header>
                <div class="guide-section-body">
                    <ol class="guide-steps">
                        <li>
                            <span class="guide-step-n">1</span>
                            <div class="guide-step-body">
                                <h6>Créer les services <span class="guide-role guide-role--admin">Admin</span></h6>
                                <p>Menu <strong>Services</strong>, bouton <strong>Ajouter un service</strong>. Les services structurent les employés, les rapports et le tableau de bord.</p>
                            </div>
                        </li>
                        <li>
                            <span class="guide-step-n">2</span>
                            <div class="guide-step-body">
                                <h6>Enregistrer les employés <span class="guide-role guide-role--admin">Admin</span> <span class="guide-role guide-role--rh">RH</span></h6>
                                <p>Menu <strong>Employés</strong>, bouton <strong>Ajouter un employé</strong>. Le matricule est généré automatiquement.</p>
                            </div>
                        </li>
                        <li>
                            <span class="guide-step-n">3</span>
                            <div class="guide-step-body">
                                <h6>Créer les comptes de connexion <span class="guide-role guide-role--admin">Admin</span></h6>
                                <p>Menu <strong>Utilisateurs</strong>, bouton <strong>Ajouter un utilisateur</strong>. Rattachez chaque compte à l'employé correspondant. Mot de passe : 8 caractères minimum.</p>
                            </div>
                        </li>
                        <li>
                            <span class="guide-step-n">4</span>
                            <div class="guide-step-body">
                                <h6>Vérifier les paramètres de paie <span class="guide-role guide-role--admin">Admin</span></h6>
                                <p>Menu <strong>Paie</strong>, bouton <strong>Paramètres</strong>. Ces valeurs servent au calcul des bulletins.</p>
                            </div>
                        </li>
                        <li>
                            <span class="guide-step-n">5</span>
                            <div class="guide-step-body">
                                <h6>Présenter le pointage QR aux équipes <span class="guide-role guide-role--all">Tous</span></h6>
                                <p>Menu <strong>Présences</strong>, bouton <strong>Afficher mon QR</strong>. Le premier scan enregistre l'arrivée, le second le départ.</p>
                            </div>
                        </li>
                        <li>
                            <span class="guide-step-n">6</span>
                            <div class="guide-step-body">
                                <h6>Installer la version mobile <span class="guide-role guide-role--all">Tous</span></h6>
                                <p>Ouvrez <a href="<?= APP_URL ?>/mobile.php">l'interface mobile</a> sur le téléphone, puis choisissez <strong>Ajouter à l'écran d'accueil</strong> dans le navigateur.</p>
                            </div>
                        </li>
                    </ol>
                </div>
            </section>

            <section class="guide-section" id="tableau-de-bord">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-tachometer-alt"></i></span>
                    <div>
                        <h2 class="guide-section-title">2. Tableau de bord</h2>
                        <span class="guide-section-sub">Vue d'ensemble de l'activité RH.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 2</span>
                </header>
                <div class="guide-section-body">
                    <p>Le tableau de bord présente les indicateurs du mois sélectionné. Pour changer de période, utilisez les listes <strong>Période</strong> puis le bouton <strong>Appliquer</strong>.</p>
                    <div class="guide-columns">
                        <div class="guide-column">
                            <h6>Indicateurs</h6>
                            <ul>
                                <li>Employés actifs, présents et retards du jour.</li>
                                <li>Congés en attente, approuvés et en cours.</li>
                                <li>Masse nette de la paie du mois.</li>
                            </ul>
                        </div>
                        <div class="guide-column">
                            <h6>Graphiques</h6>
                            <ul>
                                <li>Employés par service et répartition par sexe.</li>
                                <li>Présences du mois et évolution de la paie sur douze mois.</li>
                                <li>Congés en cours, par type et dernières demandes.</li>
                            </ul>
                        </div>
                        <div class="guide-column">
                            <h6>Accès rapides</h6>
                            <ul>
                                <li>Chaque carte renvoie vers le module concerné.</li>
                                <li>Le lien <strong>Voir les employés</strong> n'apparaît que pour les profils autorisés.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="guide-section" id="employes">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-user-tie"></i></span>
                    <div>
                        <h2 class="guide-section-title">3. Employés <span class="guide-role guide-role--admin">Admin</span> <span class="guide-role guide-role--rh">RH</span></h2>
                        <span class="guide-section-sub">Dossier administratif de chaque personne.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 3</span>
                </header>
                <div class="guide-section-body">
                    <ol class="guide-steps">
                        <li><span class="guide-step-n">1</span><div class="guide-step-body"><h6>Rechercher</h6><p>Saisissez un nom, un matricule ou un service dans le champ <strong>Rechercher un employé…</strong>, puis appuyez sur <strong>Rechercher</strong>. Le bouton <strong>×</strong> efface la recherche.</p></div></li>
                        <li><span class="guide-step-n">2</span><div class="guide-step-body"><h6>Consulter</h6><p>Cliquez sur le nom ou sur l'icône en forme d'œil pour ouvrir la fiche complète.</p></div></li>
                        <li><span class="guide-step-n">3</span><div class="guide-step-body"><h6>Modifier</h6><p>Utilisez l'icône crayon. Le statut <span class="sirh-status sirh-status--success">Actif</span> ou <span class="sirh-status sirh-status--warning">Suspendu</span> conditionne l'accès aux modules.</p></div></li>
                        <li><span class="guide-step-n">4</span><div class="guide-step-body"><h6>Exporter</h6><p>Les boutons <strong>Excel</strong> et <strong>PDF</strong> génèrent la liste complète des employés.</p></div></li>
                    </ol>
                    <div class="guide-note">
                        <i class="fas fa-circle-info" aria-hidden="true"></i>
                        <span>Un employé sans compte utilisateur ne peut pas se connecter. Créez le compte depuis le menu <strong>Utilisateurs</strong>.</span>
                    </div>
                </div>
            </section>

            <section class="guide-section" id="services">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-building"></i></span>
                    <div>
                        <h2 class="guide-section-title">4. Services <span class="guide-role guide-role--admin">Admin</span></h2>
                        <span class="guide-section-sub">Départements de l'entreprise.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 4</span>
                </header>
                <div class="guide-section-body">
                    <p>Le menu <strong>Services</strong> permet de créer, renommer et supprimer un département. Le nombre d'employés rattachés est affiché pour chaque service.</p>
                    <ul>
                        <li>Un service ne peut être supprimé que s'il n'est plus utilisé par un employé.</li>
                        <li>Les services alimentent les filtres, les rapports et le tableau de bord.</li>
                    </ul>
                </div>
            </section>

            <section class="guide-section" id="presences">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-fingerprint"></i></span>
                    <div>
                        <h2 class="guide-section-title">5. Présences et pointage QR</h2>
                        <span class="guide-section-sub">Deux scans par jour : arrivée puis départ.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 5</span>
                </header>
                <div class="guide-section-body">
                    <div class="guide-columns">
                        <div class="guide-column">
                            <h6>Côté employé <span class="guide-role guide-role--all">Tous</span></h6>
                            <ol>
                                <li>Ouvrez le menu <strong>Présences</strong>.</li>
                                <li>Cliquez sur <strong>Afficher mon QR</strong>.</li>
                                <li>Présentez le QR à la réception : le premier scan enregistre l'arrivée.</li>
                                <li>Avant de partir, réaffichez le QR : le second scan enregistre le départ.</li>
                            </ol>
                        </div>
                        <div class="guide-column">
                            <h6>Côté réception <span class="guide-role guide-role--admin">Admin</span> <span class="guide-role guide-role--rh">RH</span></h6>
                            <ol>
                                <li>Ouvrez <strong>Présences</strong>, puis <strong>Ouvrir le scanner</strong>.</li>
                                <li>Autorisez la caméra si le navigateur le demande.</li>
                                <li>Présentez le QR dans le cadre. Le résultat est confirmé par le serveur.</li>
                                <li>Si la caméra est indisponible, utilisez le champ de saisie manuelle ou un lecteur USB.</li>
                            </ol>
                        </div>
                    </div>
                    <p class="mt-2 mb-0">Le registre du jour affiche les pointages, les retards et les statuts. Les boutons <strong>Valider</strong>, <strong>Rejeter</strong> et <strong>Marquer justifié</strong> traitent les déclarations et les absences.</p>
                    <div class="guide-note">
                        <i class="fas fa-circle-info" aria-hidden="true"></i>
                        <span>Un scan après <?= htmlspecialchars(LIMITE_DECLARATION) ?> reste accepté et est enregistré comme retard. Le QR change toutes les <?= (int) QR_PERIODE ?> secondes et ne peut pas être réutilisé.</span>
                    </div>
                </div>
            </section>

            <section class="guide-section" id="conges">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-calendar-alt"></i></span>
                    <div>
                        <h2 class="guide-section-title">6. Congés</h2>
                        <span class="guide-section-sub">Demander, approuver et suivre les absences.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 6</span>
                </header>
                <div class="guide-section-body">
                    <div class="guide-columns">
                        <div class="guide-column">
                            <h6>Demander un congé <span class="guide-role guide-role--all">Tous</span></h6>
                            <ol>
                                <li>Ouvrez le menu <strong>Congés</strong>.</li>
                                <li>Cliquez sur <strong>Nouvelle demande</strong>.</li>
                                <li>Choisissez le type et les dates : le nombre de jours est calculé automatiquement.</li>
                                <li>Suivez le statut dans la liste.</li>
                            </ol>
                        </div>
                        <div class="guide-column">
                            <h6>Traiter une demande <span class="guide-role guide-role--admin">Admin</span> <span class="guide-role guide-role--rh">RH</span></h6>
                            <ol>
                                <li>Filtrez avec <strong>Tous</strong>, <strong>En attente</strong>, <strong>Approuvés</strong> ou <strong>Refusés</strong>.</li>
                                <li>Cliquez sur l'icône de validation pour approuver.</li>
                                <li>Cliquez sur l'icône de refus, saisissez le motif, puis validez <strong>Refuser la demande</strong>.</li>
                                <li>L'employé reçoit une notification et le tableau de bord est mis à jour.</li>
                            </ol>
                        </div>
                    </div>
                    <p class="mt-2 mb-0">Statuts affichés : <span class="sirh-status sirh-status--warning">En attente</span> <span class="sirh-status sirh-status--success">Approuvé</span> <span class="sirh-status sirh-status--danger">Refusé</span>.</p>
                </div>
            </section>

            <section class="guide-section" id="paie">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-money-bill-wave"></i></span>
                    <div>
                        <h2 class="guide-section-title">7. Paie et bulletins <span class="guide-role guide-role--admin">Admin</span> <span class="guide-role guide-role--rh">RH</span></h2>
                        <span class="guide-section-sub">Paramétrer une fois, générer chaque mois.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 7</span>
                </header>
                <div class="guide-section-body">
                    <p><strong>Paramétrage initial.</strong> Dans <strong>Paie</strong>, bouton <strong>Paramètres</strong>, renseignez les taux et les primes appliqués aux calculs :</p>
                    <div class="guide-columns">
                        <div class="guide-column"><h6>Sur le brut</h6><p>Taxe professionnelle, prestation sociale, pension retraite.</p></div>
                        <div class="guide-column"><h6>Sur le salaire de base</h6><p>Indemnité de logement, prime de transport.</p></div>
                        <div class="guide-column"><h6>Temps de travail</h6><p>Taux des heures supplémentaires, heures travaillées par mois.</p></div>
                    </div>
                    <p class="mt-3"><strong>Chaque mois :</strong></p>
                    <ol class="guide-steps">
                        <li><span class="guide-step-n">1</span><div class="guide-step-body"><h6>Choisir la période</h6><p>Menu <strong>Paie</strong>, sélectionnez le mois et l'année, puis cliquez sur <strong>Filtrer</strong>.</p></div></li>
                        <li><span class="guide-step-n">2</span><div class="guide-step-body"><h6>Générer les bulletins</h6><p>Cliquez sur <strong>Générer les bulletins</strong> et confirmez. Le brut, les retenues et le net sont calculés pour chaque employé.</p></div></li>
                        <li><span class="guide-step-n">3</span><div class="guide-step-body"><h6>Valider puis payer</h6><p>Utilisez l'icône de validation, puis l'icône de paiement. Les bulletins payés rejoignent <strong>Archives</strong>.</p></div></li>
                    </ol>
                    <div class="guide-note">
                        <i class="fas fa-user-lock" aria-hidden="true"></i>
                        <span>Un employé ne voit que ses propres bulletins dans <strong>Mes bulletins</strong>. Aucun autre profil n'y a accès.</span>
                    </div>
                </div>
            </section>

            <section class="guide-section" id="formations">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-graduation-cap"></i></span>
                    <div>
                        <h2 class="guide-section-title">8. Formations <span class="guide-role guide-role--admin">Admin</span> <span class="guide-role guide-role--rh">RH</span></h2>
                        <span class="guide-section-sub">Planifier et suivre les montées en compétence.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 8</span>
                </header>
                <div class="guide-section-body">
                    <ol class="guide-steps">
                        <li><span class="guide-step-n">1</span><div class="guide-step-body"><h6>Créer une formation</h6><p>Menu <strong>Formations</strong>, bouton <strong>Nouvelle formation</strong> : titre, durée, dates et type interne ou externe.</p></div></li>
                        <li><span class="guide-step-n">2</span><div class="guide-step-body"><h6>Inscrire les participants</h6><p>Ouvrez la formation et ajoutez les employés concernés.</p></div></li>
                        <li><span class="guide-step-n">3</span><div class="guide-step-body"><h6>Suivre l'avancement</h6><p>Les statuts <span class="sirh-status sirh-status--info">En cours</span>, <span class="sirh-status sirh-status--success">Terminé</span> et <span class="sirh-status sirh-status--danger">Annulé</span> sont visibles dans la liste.</p></div></li>
                    </ol>
                </div>
            </section>

            <section class="guide-section" id="rapports">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-chart-bar"></i></span>
                    <div>
                        <h2 class="guide-section-title">9. Rapports et statistiques <span class="guide-role guide-role--admin">Admin</span> <span class="guide-role guide-role--rh">RH</span></h2>
                        <span class="guide-section-sub">Indicateurs consolidés pour la décision.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 9</span>
                </header>
                <div class="guide-section-body">
                    <div class="guide-columns">
                        <div class="guide-column"><h6>Vue d'ensemble</h6><p><a href="<?= APP_URL ?>/rapports">Indicateurs consolidés</a> de l'entreprise.</p></div>
                        <div class="guide-column"><h6>Absentéisme</h6><p><a href="<?= APP_URL ?>/rapports/absentisme">Absences par employé, fréquence et période.</a></p></div>
                        <div class="guide-column"><h6>Congés</h6><p><a href="<?= APP_URL ?>/rapports/conges">Historique et soldes par employé.</a></p></div>
                    </div>
                    <div class="guide-note">
                        <i class="fas fa-file-excel" aria-hidden="true"></i>
                        <span>Les exports Excel et PDF sont disponibles sur les pages Employés, Présences, Congés et Paie.</span>
                    </div>
                </div>
            </section>

            <section class="guide-section" id="messagerie">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-comments"></i></span>
                    <div>
                        <h2 class="guide-section-title">10. Messages et notifications <span class="guide-role guide-role--all">Tous</span></h2>
                        <span class="guide-section-sub">Communication interne.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 10</span>
                </header>
                <div class="guide-section-body">
                    <div class="guide-columns">
                        <div class="guide-column">
                            <h6>Messages</h6>
                            <ul>
                                <li>Menu <strong>Messages</strong>, bouton <strong>Nouveau message</strong> pour écrire à un collègue.</li>
                                <li><strong>Annonces</strong> publie une information visible par tous.</li>
                                <li>Un badge rouge signale les conversations non lues.</li>
                            </ul>
                        </div>
                        <div class="guide-column">
                            <h6>Notifications</h6>
                            <ul>
                                <li>Demandes de congé, bulletins et validations arrivent dans la cloche.</li>
                                <li>Un clic ouvre directement l'écran concerné.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="guide-section" id="comptes">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-user-shield"></i></span>
                    <div>
                        <h2 class="guide-section-title">11. Comptes, rôles et sécurité <span class="guide-role guide-role--admin">Admin</span></h2>
                        <span class="guide-section-sub">Droits d'accès et traçabilité.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 11</span>
                </header>
                <div class="guide-section-body">
                    <p>Le menu <strong>Utilisateurs</strong> permet de créer un compte, de réinitialiser un mot de passe et de désactiver un accès.</p>
                    <div class="table-responsive">
                        <table class="guide-table">
                            <thead>
                                <tr><th scope="col" style="width:150px;">Rôle</th><th scope="col">Permissions</th></tr>
                            </thead>
                            <tbody>
                                <tr><td><span class="guide-role guide-role--admin">Admin</span></td><td>Accès complet : services, employés, présences, congés, paie, formations, rapports, utilisateurs et journal d'activité.</td></tr>
                                <tr><td><span class="guide-role guide-role--rh">RH</span></td><td>Employés, présences, congés, paie, formations, rapports et exports. Pas d'accès à la gestion des utilisateurs ni au journal.</td></tr>
                                <tr><td><span class="guide-role guide-role--all">Employé</span></td><td>Tableau de bord, pointage QR, demandes de congé, bulletins personnels, messages et notifications.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="guide-note">
                        <i class="fas fa-history" aria-hidden="true"></i>
                        <span>Le <a href="<?= APP_URL ?>/journal">journal d'activité</a> enregistre chaque action (auteur, action, date) et sert de référence en cas de litige.</span>
                    </div>
                </div>
            </section>

            <section class="guide-section" id="mobile">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-mobile-alt"></i></span>
                    <div>
                        <h2 class="guide-section-title">12. Version mobile <span class="guide-role guide-role--all">Tous</span></h2>
                        <span class="guide-section-sub">Application installable sur téléphone.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 12</span>
                </header>
                <div class="guide-section-body">
                    <ol class="guide-steps">
                        <li><span class="guide-step-n">1</span><div class="guide-step-body"><h6>Ouvrir l'interface mobile</h6><p>Depuis le téléphone, ouvrez <a href="<?= APP_URL ?>/mobile.php">la version mobile</a> et connectez-vous.</p></div></li>
                        <li><span class="guide-step-n">2</span><div class="guide-step-body"><h6>Installer l'application</h6><p>Dans le menu du navigateur, choisissez <strong>Ajouter à l'écran d'accueil</strong>.</p></div></li>
                        <li><span class="guide-step-n">3</span><div class="guide-step-body"><h6>Utiliser au quotidien</h6><p>Accueil, QR personnel, congés, messages et profil sont accessibles en plein écran.</p></div></li>
                    </ol>
                </div>
            </section>

            <section class="guide-section" id="rythme">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-calendar-check"></i></span>
                    <div>
                        <h2 class="guide-section-title">13. Rythme de travail</h2>
                        <span class="guide-section-sub">Opérations récurrentes.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 13</span>
                </header>
                <div class="guide-section-body">
                    <div class="table-responsive">
                        <table class="guide-table">
                            <thead>
                                <tr><th scope="col" style="width:160px;">Fréquence</th><th scope="col">Opérations</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Chaque jour</td><td>Contrôler les pointages, valider les déclarations, traiter les demandes de congé.</td></tr>
                                <tr><td>Chaque semaine</td><td>Consulter l'absentéisme, suivre les formations en cours, traiter les messages.</td></tr>
                                <tr><td>Chaque mois</td><td>Compléter les présences, générer et payer les bulletins, exporter les rapports.</td></tr>
                                <tr><td>Au départ d'un employé</td><td>Passer l'employé en statut <strong>Suspendu</strong>, puis désactiver son compte utilisateur.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="guide-section" id="aide">
                <header>
                    <span class="guide-section-icon" aria-hidden="true"><i class="fas fa-life-ring"></i></span>
                    <div>
                        <h2 class="guide-section-title">14. Aide et dépannage</h2>
                        <span class="guide-section-sub">Cas les plus fréquents.</span>
                    </div>
                    <span class="guide-section-num">Rubrique 14</span>
                </header>
                <div class="guide-section-body">
                    <div class="guide-columns">
                        <div class="guide-column">
                            <h6>Mot de passe oublié</h6>
                            <p>Utilisez <a href="<?= APP_URL ?>/forgot-password">Mot de passe oublié</a> sur la page de connexion, ou demandez à l'administrateur de le réinitialiser depuis <strong>Utilisateurs</strong>.</p>
                        </div>
                        <div class="guide-column">
                            <h6>Message « Trop de tentatives »</h6>
                            <p>Après cinq échecs consécutifs, la connexion est bloquée pendant quinze minutes. Patientez puis réessayez.</p>
                        </div>
                        <div class="guide-column">
                            <h6>Caméra indisponible</h6>
                            <p>Autorisez la caméra dans le navigateur, vérifiez qu'aucune autre application ne l'utilise, ou saisissez le code manuellement. La caméra exige HTTPS ou localhost.</p>
                        </div>
                        <div class="guide-column">
                            <h6>Données absentes</h6>
                            <p>Vérifiez la période sélectionnée et les filtres actifs. Un filtre de statut peut masquer des lignes existantes.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
ob_start();
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var links = Array.prototype.slice.call(document.querySelectorAll('.guide-toc a'));
    var sections = links.map(function (link) {
        return document.getElementById((link.getAttribute('href') || '').slice(1));
    });
    if (!links.length || sections.some(function (s) { return !s; })) { return; }

    function setActive(id) {
        links.forEach(function (link) {
            link.classList.toggle('is-active', link.getAttribute('href') === '#' + id);
        });
    }

    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) { setActive(entry.target.id); }
            });
        }, { rootMargin: '-80px 0px -70% 0px', threshold: 0 });
        sections.forEach(function (section) { observer.observe(section); });
    }

    links.forEach(function (link) {
        link.addEventListener('click', function () {
            setActive((link.getAttribute('href') || '').slice(1));
        });
    });
});
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
