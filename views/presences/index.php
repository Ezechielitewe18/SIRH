<?php
$pageTitle = 'Gestion des présences';
$scansJour = isset($scans) && is_array($scans) ? $scans : [];
$nbArrivees = count(array_filter($scansJour, fn($s) => !empty($s['heure_arrivee'])));
$nbDeparts = count(array_filter($scansJour, fn($s) => !empty($s['heure_depart'])));
$dateAffichee = !empty($date) ? date('d/m/Y', strtotime($date)) : date('d/m/Y');
$vclass = ['auto' => 'success', 'validee' => 'success', 'en_attente' => 'warning', 'rejetee' => 'danger'];
$vlabel = ['auto' => 'Automatique', 'validee' => 'Validée', 'en_attente' => 'En attente', 'rejetee' => 'Rejetée'];
$sclass = ['declaration' => 'info', 'manuel' => 'neutral', 'qr' => 'info'];
$slabel = ['declaration' => 'Déclaration', 'manuel' => 'Manuel', 'qr' => 'QR'];
ob_start();
?>

<section class="content-header">
    <div class="sirh-page-header">
        <div class="sirh-page-heading">
            <h1 class="sirh-page-title"><i class="fas fa-fingerprint" aria-hidden="true"></i> Présences</h1>
            <p class="sirh-page-subtitle">Suivi des pointages et des déclarations pour le <?= $dateAffichee ?>.</p>
        </div>
        <div class="sirh-page-actions">
            <?php if ($canScan): ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalScanner">
                <i class="fas fa-camera" aria-hidden="true"></i> Ouvrir le scanner
            </button>
            <?php endif; ?>
            <a href="<?= APP_URL ?>/presences" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-rotate" aria-hidden="true"></i> Aujourd'hui
            </a>
        </div>
    </div>
</section>

<section class="content">

    <?php if (isset($_SESSION['employee_id'])): ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
            <h3 class="card-title"><i class="fas fa-user-clock" aria-hidden="true"></i> Ma présence aujourd'hui</h3>
            <?php if ($maPresence): ?>
            <span class="sirh-status sirh-status--<?= $vclass[$maPresence['validation']] ?? 'neutral' ?>">
                <?= $vlabel[$maPresence['validation']] ?? htmlspecialchars($maPresence['validation']) ?>
            </span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if ($maPresence): ?>
            <div class="sirh-mini-grid mb-3">
                <div class="sirh-mini sirh-mini--success">
                    <b><?= $maPresence['heure_arrivee'] ? date('H:i', strtotime($maPresence['heure_arrivee'])) : '—' ?></b>
                    <span>Arrivée</span>
                </div>
                <div class="sirh-mini sirh-mini--info">
                    <b><?= $maPresence['heure_depart'] ? date('H:i', strtotime($maPresence['heure_depart'])) : '—' ?></b>
                    <span>Départ</span>
                </div>
                <div class="sirh-mini <?= $maPresence['retard'] > 0 ? 'sirh-mini--warning' : '' ?>">
                    <b><?= $maPresence['retard'] > 0 ? '+' . (int) $maPresence['retard'] . ' min' : '0 min' ?></b>
                    <span>Retard</span>
                </div>
                <div class="sirh-mini">
                    <b style="font-size:.9rem;"><?= ucfirst(htmlspecialchars($maPresence['statut'])) ?></b>
                    <span>Statut</span>
                </div>
            </div>
            <?php else: ?>
            <p class="sirh-muted mb-3">Aucun pointage enregistré aujourd'hui. Présentez votre QR à la réception pour enregistrer votre arrivée.</p>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                <div style="max-width:560px;">
                    <p class="mb-1" style="color:var(--sirh-text);font-weight:600;">Mon QR de pointage</p>
                    <p class="sirh-muted mb-0" style="font-size:.85rem;">
                        <?php if ($maPresence && empty($maPresence['heure_arrivee'])): ?>
                            Un scan enregistrera votre <strong>arrivée</strong>.
                        <?php elseif ($maPresence && empty($maPresence['heure_depart'])): ?>
                            Votre arrivée est faite : ce QR pointera votre <strong>départ</strong>.
                        <?php else: ?>
                            Le code change toutes les <?= (int) QR_PERIODE ?> secondes.
                        <?php endif; ?>
                    </p>
                </div>
                <a href="<?= APP_URL ?>/presences/qr" class="btn btn-primary" id="btnMonQr">
                    <i class="fas fa-qrcode" aria-hidden="true"></i>
                    <?php
                    $qrLibelle = ($maPresence && !empty($maPresence['heure_arrivee']))
                        ? (empty($maPresence['heure_depart']) ? 'Mon QR de départ' : 'Mon QR du jour')
                        : 'Afficher mon QR';
                    echo $qrLibelle;
                    ?>
                </a>
            </div>

            <?php if (!$maPresence || $maPresence['statut'] === 'absent'): ?>
            <div id="compteARebours"></div>
            <?php endif; ?>

            <?php if ($maPresence && $maPresence['statut'] === 'absent'): ?>
            <div class="alert alert-danger py-2 px-3 mt-3 mb-2" style="font-size:.86rem;">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                Marqué <strong>absent</strong> (aucun pointage avant <?= LIMITE_DECLARATION ?>).
                Présentez quand même votre QR : la réception enregistrera votre <strong>arrivée tardive</strong>.
                <?php if (!empty($maPresence['justification'])): ?>
                    <br>Justificatif : <?= htmlspecialchars($maPresence['justification']) ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($maPresence && $maPresence['statut'] === 'justifie'): ?>
            <div class="alert alert-info py-2 px-3 mt-3 mb-0" style="font-size:.86rem;">
                <i class="fas fa-clipboard-check" aria-hidden="true"></i> Absence <strong>régularisée</strong> par le RH : présentez votre QR de départ avant de quitter.
            </div>
            <?php endif; ?>

            <div id="currentTime" class="sirh-muted mt-3" style="font-size:.8rem;"></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($canScan): ?>
    <div class="card" style="border-left:4px solid var(--sirh-blue);">
        <div class="card-body sirh-reception">
            <div class="sirh-reception-main">
                <span class="sirh-reception-icon" aria-hidden="true"><i class="fas fa-qrcode"></i></span>
                <div>
                    <h3>Poste de réception</h3>
                    <p>Présentez le QR de l'employé dans le cadre. Le premier scan enregistre l'arrivée, le second le départ.</p>
                    <p class="sirh-reception-hint"><i class="fas fa-circle-info" aria-hidden="true"></i> Un scan après <?= LIMITE_DECLARATION ?> reste accepté et est compté comme retard.</p>
                </div>
            </div>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalScanner">
                <i class="fas fa-camera" aria-hidden="true"></i> Ouvrir le scanner
            </button>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($isManager): ?>
    <h2 class="sirh-section-title"><i class="fas fa-chart-simple" aria-hidden="true"></i> Suivi du jour</h2>
    <div class="sirh-stat-grid">
        <article class="sirh-stat sirh-stat--success">
            <div class="sirh-stat-top">
                <div>
                    <p class="sirh-stat-label">Arrivées QR</p>
                    <p class="sirh-stat-value" id="nbArrivees"><?= $nbArrivees ?></p>
                </div>
                <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-right-to-bracket"></i></span>
            </div>
        </article>
        <article class="sirh-stat sirh-stat--info">
            <div class="sirh-stat-top">
                <div>
                    <p class="sirh-stat-label">Départs QR</p>
                    <p class="sirh-stat-value" id="nbDeparts"><?= $nbDeparts ?></p>
                </div>
                <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-right-from-bracket"></i></span>
            </div>
        </article>
        <article class="sirh-stat sirh-stat--warning">
            <div class="sirh-stat-top">
                <div>
                    <p class="sirh-stat-label">Déclarations à valider</p>
                    <p class="sirh-stat-value"><?= count($enAttente) ?></p>
                </div>
                <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-clipboard-check"></i></span>
            </div>
            <a href="#declarations" class="sirh-stat-footer">Traiter les déclarations <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </article>
        <article class="sirh-stat sirh-stat--danger">
            <div class="sirh-stat-top">
                <div>
                    <p class="sirh-stat-label">Absents non pointés</p>
                    <p class="sirh-stat-value"><?= count($absents) ?></p>
                </div>
                <span class="sirh-stat-icon" aria-hidden="true"><i class="fas fa-user-times"></i></span>
            </div>
        </article>
    </div>

    <div class="card" id="declarations">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-clipboard-check" aria-hidden="true"></i> Déclarations à valider (<?= count($enAttente) ?>)</h3>
        </div>
        <?php if (!empty($enAttente)): ?>
        <div class="table-responsive">
            <table class="table table-hover sirh-table-medium">
                <thead>
                    <tr>
                        <th scope="col">Matricule</th>
                        <th scope="col">Nom complet</th>
                        <th scope="col">Service</th>
                        <th scope="col">Date</th>
                        <th scope="col">Arrivée</th>
                        <th scope="col">Statut</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enAttente as $p): ?>
                    <tr>
                        <td><span class="sirh-matricule"><?= htmlspecialchars($p['matricule']) ?></span></td>
                        <td><span class="sirh-cell-strong"><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></span></td>
                        <td><?= htmlspecialchars($p['nom_service'] ?? 'N/A') ?></td>
                        <td><?= date('d/m/Y', strtotime($p['date_presence'])) ?></td>
                        <td><?= $p['heure_arrivee'] ? date('H:i', strtotime($p['heure_arrivee'])) : '—' ?></td>
                        <td>
                            <span class="sirh-status sirh-status--<?= $p['statut'] === 'present' ? 'success' : 'warning' ?>"><?= ucfirst($p['statut']) ?></span>
                            <?= $p['est_direction'] ? '<span class="badge badge-primary ml-1">DG</span>' : '' ?>
                        </td>
                        <td class="text-right">
                            <div class="d-inline-flex align-items-center" style="gap:5px;">
                                <form method="POST" action="<?= APP_URL ?>/presences/valider/<?= $p['id_presence'] ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check" aria-hidden="true"></i> Valider</button>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalRejet"
                                        data-id="<?= $p['id_presence'] ?>" data-nom="<?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>">
                                    <i class="fas fa-times" aria-hidden="true"></i> Rejeter
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="sirh-empty">
            <span class="sirh-empty-icon" aria-hidden="true"><i class="fas fa-clipboard-check"></i></span>
            <p class="sirh-empty-title">Aucune déclaration en attente</p>
            <p class="sirh-empty-text">Toutes les déclarations de présence ont été traitées.</p>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($absents)): ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-times" aria-hidden="true"></i> Absents non pointés avant <?= LIMITE_DECLARATION ?> (<?= count($absents) ?>)</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover sirh-table-medium">
                <thead>
                    <tr>
                        <th scope="col">Matricule</th>
                        <th scope="col">Nom complet</th>
                        <th scope="col">Service</th>
                        <th scope="col">Date</th>
                        <th scope="col" class="text-right">Régularisation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($absents as $a): ?>
                    <tr>
                        <td><span class="sirh-matricule"><?= htmlspecialchars($a['matricule']) ?></span></td>
                        <td><span class="sirh-cell-strong"><?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?></span></td>
                        <td><?= htmlspecialchars($a['nom_service'] ?? 'N/A') ?></td>
                        <td><?= date('d/m/Y', strtotime($a['date_presence'])) ?></td>
                        <td class="text-right">
                            <form method="POST" action="<?= APP_URL ?>/presences/regulariser" class="d-inline" onsubmit="return confirm('Régulariser cette absence ?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $a['id_presence'] ?>">
                                <input type="hidden" name="statut" value="justifie">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check" aria-hidden="true"></i> Marquer justifié</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <h2 class="sirh-section-title" id="registre"><i class="fas fa-table-list" aria-hidden="true"></i> Registre des présences</h2>
    <div class="sirh-toolbar">
        <form method="GET" action="<?= APP_URL ?>/presences" class="sirh-toolbar-group">
            <label class="sirh-toolbar-label" for="pres-date">Date</label>
            <input type="date" class="form-control form-control-sm" id="pres-date" name="date" value="<?= htmlspecialchars($date ?? date('Y-m-d')) ?>" style="width:auto;">
            <button class="btn btn-secondary btn-sm" type="submit"><i class="fas fa-filter" aria-hidden="true"></i> Filtrer</button>
            <?php if (($date ?? date('Y-m-d')) !== date('Y-m-d')): ?>
            <a href="<?= APP_URL ?>/presences" class="btn btn-outline-secondary btn-sm">Aujourd'hui</a>
            <?php endif; ?>
        </form>
        <?php if ($canScan): ?>
        <div class="sirh-toolbar-group">
            <a href="<?= APP_URL ?>/export/presences?date=<?= urlencode($date ?? date('Y-m-d')) ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-file-excel" aria-hidden="true"></i> Exporter Excel
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <?php if (!empty($presences)): ?>
        <div class="table-responsive">
            <table class="table table-hover sirh-table-wide">
                <thead>
                    <tr>
                        <th scope="col">Matricule</th>
                        <th scope="col">Nom complet</th>
                        <th scope="col">Service</th>
                        <th scope="col">Date</th>
                        <th scope="col">Arrivée</th>
                        <th scope="col">Départ</th>
                        <th scope="col">Retard</th>
                        <th scope="col">Statut</th>
                        <th scope="col">Source</th>
                        <th scope="col">Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($presences as $p): ?>
                    <tr>
                        <td><span class="sirh-matricule"><?= htmlspecialchars($p['matricule']) ?></span></td>
                        <td><span class="sirh-cell-strong"><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></span></td>
                        <td><?= htmlspecialchars($p['nom_service'] ?? 'N/A') ?></td>
                        <td><?= date('d/m/Y', strtotime($p['date_presence'])) ?></td>
                        <td><?= $p['heure_arrivee'] ? date('H:i', strtotime($p['heure_arrivee'])) : '—' ?></td>
                        <td><?= $p['heure_depart'] ? date('H:i', strtotime($p['heure_depart'])) : '—' ?></td>
                        <td><?= !empty($p['retard']) && $p['retard'] > 0 ? (int) $p['retard'] . ' min' : '—' ?></td>
                        <td>
                            <span class="sirh-status sirh-status--<?= $p['statut'] === 'present' ? 'success' : ($p['statut'] === 'retard' ? 'warning' : ($p['statut'] === 'justifie' ? 'info' : 'danger')) ?>">
                                <?= ucfirst($p['statut']) ?>
                            </span>
                        </td>
                        <td><span class="sirh-status sirh-status--<?= $sclass[$p['source']] ?? 'neutral' ?>"><?= $slabel[$p['source']] ?? htmlspecialchars($p['source']) ?></span></td>
                        <td>
                            <span class="sirh-status sirh-status--<?= $vclass[$p['validation']] ?? 'neutral' ?>"><?= $vlabel[$p['validation']] ?? htmlspecialchars($p['validation']) ?></span>
                            <?php if ($p['validation'] === 'rejetee' && !empty($p['justification'])): ?>
                                <small class="d-block text-danger mt-1" title="Justification"><?= htmlspecialchars($p['justification']) ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="sirh-empty">
            <span class="sirh-empty-icon" aria-hidden="true"><i class="fas fa-calendar-xmark"></i></span>
            <p class="sirh-empty-title">Aucune présence enregistrée</p>
            <p class="sirh-empty-text">Aucun pointage n'a été enregistré pour le <?= $dateAffichee ?>. Modifiez la date ou lancez un scan depuis le poste de réception.</p>
            <?php if ($canScan): ?>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalScanner">
                <i class="fas fa-camera" aria-hidden="true"></i> Ouvrir le scanner
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($isManager): ?>
<div class="modal fade" id="modalRejet" tabindex="-1" role="dialog" aria-labelledby="modalRejetTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="<?= APP_URL ?>/presences/rejeter">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRejetTitle">Rejeter la déclaration de <span id="rejetNom"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejetId">
                    <div class="form-group mb-0">
                        <label for="rejetJustification">Justification (obligatoire)</label>
                        <textarea name="justification" id="rejetJustification" class="form-control" rows="3" required placeholder="Motif du rejet…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-times" aria-hidden="true"></i> Rejeter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($canScan): ?>
<div class="modal fade scanner-modal" id="modalScanner" tabindex="-1" role="dialog" aria-labelledby="modalScannerTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalScannerTitle"><i class="fas fa-camera" aria-hidden="true"></i> Scanner un QR de présence</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="scanner-state scanner-state--info" id="scanState" role="status" aria-live="polite">
                    <i class="fas fa-circle-info" aria-hidden="true"></i>
                    <span><strong>Prêt à scanner.</strong> La caméra s'active à l'ouverture.</span>
                </div>

                <div class="scanner-stage mt-3" id="scanStage">
                    <video id="scanVideo" playsinline muted></video>
                    <canvas id="scanCanvas"></canvas>
                    <div class="scanner-reticle" aria-hidden="true"></div>
                    <div class="scanner-placeholder" id="scanPlaceholder">
                        <i class="fas fa-camera" aria-hidden="true"></i>
                        <span>En attente de la caméra.<br>Si l'accès est refusé, utilisez la saisie manuelle ci-dessous.</span>
                    </div>
                </div>

                <div class="scanner-toolbar">
                    <button type="button" class="btn btn-primary" id="scanToggle">
                        <i class="fas fa-video" aria-hidden="true"></i> Activer la caméra
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="scanPause" disabled>
                        <i class="fas fa-pause" aria-hidden="true"></i> Pause
                    </button>
                </div>

                <div class="scanner-result" id="scanResult" role="status" aria-live="polite"></div>

                <div class="scanner-manual">
                    <label for="scanCode">Lecteur code-barres ou saisie manuelle</label>
                    <div class="scanner-manual-row">
                        <input type="text" id="scanCode" class="form-control" placeholder="Saisir ou scanner le code…" autocomplete="off">
                        <button type="button" class="btn btn-secondary" id="scanSubmit">Pointer</button>
                    </div>
                    <p class="scanner-help">Le lecteur USB se comporte comme un clavier. Le code n'est jamais affiché sur le téléphone de l'employé.</p>
                </div>

                <div class="scanner-journal">
                    <h6><i class="fas fa-clock-rotate-left" aria-hidden="true"></i> Pointages QR du jour</h6>
                    <div class="scanner-journal-list" id="scanJournal">
                        <?php if (empty($scansJour)): ?>
                        <div class="sirh-empty" style="padding:18px 12px;">
                            <span class="sirh-empty-icon" style="width:36px;height:36px;font-size:.85rem;" aria-hidden="true"><i class="fas fa-qrcode"></i></span>
                            <p class="sirh-empty-title" style="font-size:.86rem;">Aucun scan aujourd'hui</p>
                            <p class="sirh-empty-text" style="font-size:.8rem;">Les pointages apparaîtront ici en temps réel.</p>
                        </div>
                        <?php else: foreach ($scansJour as $s): ?>
                        <div class="scanner-journal-row">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="scanGoRegister" data-dismiss="modal">Voir le registre</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
ob_start();
?>
<script>
var SERVER_NOW_MS = <?= (int) round(microtime(true) * 1000) ?>;
var LIMITE_DECLARATION_MS = <?= strtotime(date('Y-m-d') . ' ' . LIMITE_DECLARATION . ':00') * 1000 ?>;
var CLOCK_OFFSET = SERVER_NOW_MS - Date.now();

function maintenantMs() { return Date.now() + CLOCK_OFFSET; }
function heureTzFmt(ms, avecSecondes) {
    try {
        return new Intl.DateTimeFormat("fr-FR", { timeZone: "Africa/Kinshasa", hour: "2-digit", minute: "2-digit", second: avecSecondes ? "2-digit" : undefined }).format(new Date(ms));
    } catch (e) {
        return new Date(ms).toLocaleTimeString("fr-FR");
    }
}
function updateClock() {
    var el = document.getElementById("currentTime");
    if (el) { el.textContent = "Heure actuelle : " + heureTzFmt(maintenantMs(), true); }
}
setInterval(updateClock, 1000);
updateClock();

function updateCompteARebours() {
    var el = document.getElementById("compteARebours");
    if (!el) { return; }
    var diff = LIMITE_DECLARATION_MS - maintenantMs();
    if (diff <= 0) {
        el.innerHTML = '<div class="alert alert-warning mb-2" style="font-size:.86rem;padding:10px 12px"><i class="fas fa-hourglass-end" aria-hidden="true"></i> Heure limite atteinte (<?= LIMITE_DECLARATION ?>). Votre pointage reste possible : il sera enregistré comme <strong>arrivée tardive</strong> au scan de votre QR.</div>';
        el.className = "mt-3";
        return;
    }
    var h = Math.floor(diff / 3600000);
    var m = Math.floor((diff % 3600000) / 60000);
    var s = Math.floor((diff % 60000) / 1000);
    el.innerHTML = '<div class="alert alert-warning mb-2" style="font-size:.86rem;padding:10px 12px"><i class="fas fa-hourglass-half" aria-hidden="true"></i> Il vous reste <strong>' + h + 'h ' + m + 'min ' + s + 's</strong> pour pointer sans retard.<br><small style="font-size:.76rem">Avant <?= HEURE_DEBUT ?> = présent · à partir de <?= HEURE_DEBUT ?> = retard · le code change toutes les <?= QR_PERIODE ?> s.</small></div>';
    el.className = "mt-3";
}
setInterval(updateCompteARebours, 1000);
updateCompteARebours();

$("#modalRejet").on("show.bs.modal", function (e) {
    var btn = $(e.relatedTarget);
    $("#rejetId").val(btn.data("id"));
    $("#rejetNom").text(btn.data("nom"));
});

<?php if ($canScan): ?>
(function () {
    "use strict";

    var modalEl = document.getElementById("modalScanner");
    if (!modalEl) { return; }

    var SCAN_URL = <?= json_encode(APP_URL . '/presences/scan') ?>;
    var CSRF = <?= json_encode(csrf_token()) ?>;

    var video = document.getElementById("scanVideo");
    var canvas = document.getElementById("scanCanvas");
    var ctx = canvas.getContext("2d", { willReadFrequently: true });
    var stage = document.getElementById("scanStage");
    var toggleBtn = document.getElementById("scanToggle");
    var pauseBtn = document.getElementById("scanPause");
    var codeInput = document.getElementById("scanCode");
    var submitBtn = document.getElementById("scanSubmit");
    var resultBox = document.getElementById("scanResult");
    var stateBox = document.getElementById("scanState");
    var journal = document.getElementById("scanJournal");

    var stream = null;
    var actif = false;
    var enCours = false;
    var verrouille = false;
    var rafId = null;
    var dernierFrame = 0;
    var dernierCode = "";
    var dernierCodeAt = 0;

    function esc(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
    }

    function setState(kind, icon, html) {
        stateBox.className = "scanner-state scanner-state--" + kind;
        stateBox.innerHTML = '<i class="fas ' + icon + '" aria-hidden="true"></i><span>' + html + '</span>';
    }

    function bip(ok) {
        try {
            var AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) { return; }
            var audio = new AudioCtx();
            var osc = audio.createOscillator();
            var gain = audio.createGain();
            osc.connect(gain);
            gain.connect(audio.destination);
            osc.frequency.value = ok ? 880 : 220;
            gain.gain.setValueAtTime(0.07, audio.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.0001, audio.currentTime + 0.25);
            osc.start();
            osc.stop(audio.currentTime + 0.25);
            osc.onended = function () { try { audio.close(); } catch (e) {} };
        } catch (e) {}
    }

    function showResult(ok, data) {
        var employe = (data && data.employe) || {};
        var message = (data && data.message) || (ok ? "Opération enregistrée." : "Le pointage a échoué.");
        var action = data && data.action;
        var badge = "";
        if (ok && action === "arrivee") { badge = '<span class="badge badge-success ml-2">Arrivée</span>'; }
        if (ok && action === "depart") { badge = '<span class="badge badge-info ml-2">Départ</span>'; }
        resultBox.className = "scanner-result is-visible " + (ok ? "is-ok" : "is-err");
        resultBox.innerHTML =
            '<div class="scanner-result-name">' + esc(((employe.prenom || "") + " " + (employe.nom || "")).trim() || (ok ? "Pointage enregistré" : "Pointage refusé")) + badge + '</div>' +
            (employe.matricule ? '<div class="scanner-result-meta">Matricule ' + esc(employe.matricule) + '</div>' : '') +
            '<div class="scanner-result-message">' + esc(message) + '</div>';
    }

    function addJournal(data) {
        var employe = (data && data.employe) || {};
        var now = new Date();
        var time = heureTzFmt(now.getTime(), false);
        var empty = journal.querySelector(".sirh-empty");
        if (empty) { empty.remove(); }
        var row = document.createElement("div");
        row.className = "scanner-journal-row";
        row.innerHTML = '<span class="who">' + esc(((employe.prenom || "") + " " + (employe.nom || "")).trim()) + '</span>' +
            '<span class="when">' + esc(employe.matricule || "") + " · " + esc(time) + '</span>';
        journal.insertBefore(row, journal.firstChild);
    }

    function updateCounters(action) {
        if (action === "arrivee") {
            var arr = document.getElementById("nbArrivees");
            if (arr) { arr.textContent = String((parseInt(arr.textContent, 10) || 0) + 1); }
        }
        if (action === "depart") {
            var dep = document.getElementById("nbDeparts");
            if (dep) { dep.textContent = String((parseInt(dep.textContent, 10) || 0) + 1); }
        }
    }

    function pointer(code) {
        code = (code || "").trim();
        if (code === "") {
            setState("warning", "fa-triangle-exclamation", "<strong>Code vide.</strong> Saisissez le code du QR ou scannez-le.");
            return;
        }
        if (enCours) { return; }
        if (code === dernierCode && Date.now() - dernierCodeAt < 5000) {
            setState("warning", "fa-triangle-exclamation", "<strong>Scan ignoré.</strong> Ce code vient déjà d'être traité.");
            return;
        }

        enCours = true;
        verrouille = true;
        submitBtn.disabled = true;
        setState("info", "fa-spinner fa-spin", "<strong>Validation en cours…</strong> Vérification auprès du serveur.");

        var body = new URLSearchParams();
        body.append("code", code);
        body.append("csrf_token", CSRF);

        fetch(SCAN_URL, {
            method: "POST",
            credentials: "same-origin",
            headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
            body: body.toString()
        })
            .then(function (response) {
                var type = response.headers.get("content-type") || "";
                if (type.indexOf("application/json") === -1) {
                    throw new Error("Réponse inattendue du serveur.");
                }
                return response.json();
            })
            .then(function (json) {
                dernierCode = code;
                dernierCodeAt = Date.now();
                if (json && json.success) {
                    bip(true);
                    showResult(true, json);
                    addJournal(json);
                    updateCounters(json.action);
                    setState("success", "fa-circle-check", "<strong>Présence enregistrée.</strong> " + esc(json.message || ""));
                } else {
                    bip(false);
                    var message = (json && json.message) ? json.message : "Le pointage a échoué.";
                    showResult(false, json || {});
                    setState("danger", "fa-circle-xmark", "<strong>Pointage refusé.</strong> " + esc(message));
                }
            })
            .catch(function (error) {
                bip(false);
                var message = (error && error.message) ? error.message : "Erreur réseau. Réessayez.";
                showResult(false, { message: message });
                setState("danger", "fa-triangle-exclamation", "<strong>Erreur de communication.</strong> " + esc(message));
            })
            .then(function () {
                enCours = false;
                submitBtn.disabled = false;
                window.setTimeout(function () {
                    verrouille = false;
                    if (stream && actif) {
                        setState("success", "fa-camera", "<strong>Caméra active.</strong> Présentez le QR suivant.");
                    } else {
                        setState("info", "fa-circle-info", "<strong>Prêt à scanner.</strong> Ouvrez la caméra ou saisissez le code.");
                    }
                }, 2400);
            });
    }

    function stopCamera(notify) {
        actif = false;
        verrouille = false;
        if (rafId) { window.cancelAnimationFrame(rafId); rafId = null; }
        if (stream) {
            stream.getTracks().forEach(function (track) { track.stop(); });
            stream = null;
        }
        video.srcObject = null;
        stage.classList.remove("is-on");
        toggleBtn.innerHTML = '<i class="fas fa-video" aria-hidden="true"></i> Activer la caméra';
        pauseBtn.disabled = true;
        pauseBtn.innerHTML = '<i class="fas fa-pause" aria-hidden="true"></i> Pause';
        if (notify !== false) {
            setState("info", "fa-circle-info", "<strong>Caméra arrêtée.</strong> Vous pouvez la réactiver ou saisir le code manuellement.");
        }
    }

    function cameraError(error) {
        var name = error && error.name;
        var message;
        if (name === "NotAllowedError" || name === "SecurityError") {
            message = "Accès caméra refusé. Autorisez la caméra dans le navigateur, puis réessayez.";
        } else if (name === "NotFoundError" || name === "DevicesNotFoundError") {
            message = "Aucune caméra détectée. Utilisez le lecteur ou la saisie manuelle.";
        } else if (name === "NotReadableError" || name === "TrackStartError") {
            message = "La caméra est déjà utilisée par une autre application.";
        } else if (window.location.protocol !== "https:" && window.location.hostname !== "localhost" && window.location.hostname !== "127.0.0.1") {
            message = "La caméra nécessite HTTPS ou localhost. Utilisez la saisie manuelle.";
        } else {
            message = "Caméra indisponible. Utilisez le lecteur ou la saisie manuelle.";
        }
        setState("danger", "fa-video-slash", "<strong>Caméra indisponible.</strong> " + esc(message));
    }

    function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            cameraError({ name: "NotSupportedError" });
            return;
        }
        setState("info", "fa-spinner fa-spin", "<strong>Demande d'autorisation…</strong> Autorisez l'accès à la caméra.");
        navigator.mediaDevices.getUserMedia({
            audio: false,
            video: { facingMode: { ideal: "environment" }, width: { ideal: 1280 }, height: { ideal: 720 } }
        }).then(function (mediaStream) {
            stream = mediaStream;
            video.srcObject = mediaStream;
            return video.play().catch(function () {});
        }).then(function () {
            stage.classList.add("is-on");
            toggleBtn.innerHTML = '<i class="fas fa-video-slash" aria-hidden="true"></i> Arrêter la caméra';
            pauseBtn.disabled = false;
            pauseBtn.innerHTML = '<i class="fas fa-pause" aria-hidden="true"></i> Pause';
            actif = true;
            verrouille = false;
            setState("success", "fa-camera", "<strong>Caméra active.</strong> Présentez le QR dans le cadre.");
            startLoop();
        }).catch(function (error) {
            stopCamera(false);
            cameraError(error);
        });
    }

    function loop(timestamp) {
        if (!actif || !stream) { rafId = null; return; }
        rafId = window.requestAnimationFrame(loop);
        if (verrouille || enCours || document.hidden) { return; }
        if (timestamp - dernierFrame < 120) { return; }
        dernierFrame = timestamp;
        if (!video.videoWidth || video.readyState < 2) { return; }

        var scale = Math.min(1, 720 / video.videoWidth);
        canvas.width = Math.max(1, Math.round(video.videoWidth * scale));
        canvas.height = Math.max(1, Math.round(video.videoHeight * scale));
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        var image = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var result = window.jsQR ? window.jsQR(image.data, image.width, image.height, { inversionAttempts: "attemptBoth" }) : null;
        if (result && result.data) {
            verrouille = true;
            pointer(result.data);
        }
    }

    function startLoop() {
        if (rafId) { window.cancelAnimationFrame(rafId); }
        rafId = window.requestAnimationFrame(loop);
    }

    toggleBtn.addEventListener("click", function () {
        if (stream) {
            stopCamera(true);
        } else {
            startCamera();
        }
    });

    pauseBtn.addEventListener("click", function () {
        if (!stream) { return; }
        actif = !actif;
        if (actif) {
            pauseBtn.innerHTML = '<i class="fas fa-pause" aria-hidden="true"></i> Pause';
            setState("success", "fa-camera", "<strong>Lecture reprise.</strong> Présentez le QR.");
            startLoop();
        } else {
            pauseBtn.innerHTML = '<i class="fas fa-play" aria-hidden="true"></i> Reprendre';
            setState("warning", "fa-pause", "<strong>Lecture en pause.</strong> La caméra reste active.");
        }
    });

    submitBtn.addEventListener("click", function () {
        var value = codeInput.value;
        codeInput.value = "";
        pointer(value);
    });

    codeInput.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            submitBtn.click();
        }
    });

    $("#modalScanner").on("show.bs.modal", function () {
        resultBox.className = "scanner-result";
        resultBox.innerHTML = "";
        codeInput.value = "";
        setState("info", "fa-spinner fa-spin", "<strong>Ouverture de la caméra…</strong> Autorisez l'accès si le navigateur le demande.");
        window.setTimeout(startCamera, 180);
    });

    $("#modalScanner").on("hidden.bs.modal", function () {
        stopCamera(false);
        enCours = false;
        submitBtn.disabled = false;
        resultBox.className = "scanner-result";
        resultBox.innerHTML = "";
    });

    document.getElementById("scanGoRegister").addEventListener("click", function () {
        window.setTimeout(function () {
            var target = document.getElementById("registre");
            if (!target) { return; }
            var reduce = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
            target.scrollIntoView({ behavior: reduce ? "auto" : "smooth", block: "start" });
        }, 320);
    });

    document.addEventListener("visibilitychange", function () {
        if (document.hidden && stream) {
            stopCamera(true);
        }
    });
})();
<?php endif; ?>
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
