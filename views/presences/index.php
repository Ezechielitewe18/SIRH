<?php $pageTitle = 'Gestion des présences'; ?>
<?php
/* Regroupement des compteurs pour un affichage compact */
$scansJour = isset($scans) && is_array($scans) ? $scans : [];
$nbArrivees = count(array_filter($scansJour, fn($s) => !empty($s['heure_arrivee'])));
$nbDeparts = count(array_filter($scansJour, fn($s) => !empty($s['heure_depart'])));
?>
<?php ob_start(); ?>

<style>
    /* --- Cartes presences compactes --- */
    .carte-qr {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #f8f9fc;
        padding: 12px 14px;
        margin-bottom: 12px;
    }
    .carte-qr .titre {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: #6c757d;
        margin-bottom: 8px;
    }
    .pointage { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .pointage .lien-qr { flex: 1; font-size: 13px; color: #495057; margin: 0; }
    .mini-stat {
        display: inline-flex;
        flex-direction: column;
        line-height: 1.1;
        padding: 6px 12px;
        border-radius: 6px;
        background: #fff;
        border: 1px solid #e9ecef;
        margin: 0 6px 6px 0;
    }
    .mini-stat b { font-size: 16px; }
    .mini-stat span { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: #868e96; }
    .mini-stat.ok b { color: #28a745; }
    .mini-stat.dep b { color: #007bff; }
    .h-horaire { font-size: 20px; font-weight: 700; }
    .h-horaire.retard { color: #fd7e14; }
    .legende { font-size: 11px; color: #868e96; }
</style>

<section class="content-header">
    <h1>Présences</h1>
</section>

<section class="content">
    <?php if (isset($_SESSION['employee_id'])): ?>
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card card-primary">
                <div class="card-header py-2">
                    <h3 class="card-title" style="font-size:15px"><i class="fas fa-user-clock"></i> Ma présence aujourd'hui</h3>
                </div>
                <div class="card-body p-3">

                    <?php if ($maPresence): ?>
                    <div class="pointage mb-2">
                        <div class="mini-stat">
                            <b><?= $maPresence['heure_arrivee'] ? date('H:i', strtotime($maPresence['heure_arrivee'])) : '—' ?></b>
                            <span>Arrivée</span>
                        </div>
                        <div class="mini-stat">
                            <b><?= $maPresence['heure_depart'] ? date('H:i', strtotime($maPresence['heure_depart'])) : '—' ?></b>
                            <span>Départ</span>
                        </div>
                        <div class="mini-stat <?= $maPresence['retard'] > 0 ? 'dep' : 'ok' ?>">
                            <b><?= $maPresence['retard'] > 0 ? '+' . (int)$maPresence['retard'] . '′' : '0′' ?></b>
                            <span>Retard</span>
                        </div>
                        <div class="mini-stat">
                            <b style="font-size:13px">
                                <?php
                                $vclass = ['auto' => 'success', 'validee' => 'success', 'en_attente' => 'warning', 'rejetee' => 'danger'];
                                $vlabel = ['auto' => 'Auto', 'validee' => 'Validée', 'en_attente' => 'En attente', 'rejetee' => 'Rejetée'];
                                $v = $maPresence['validation'];
                                ?>
                                <span class="badge badge-<?= $vclass[$v] ?? 'secondary' ?>"><?= $vlabel[$v] ?? $v ?></span>
                            </b>
                            <span>Statut</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="carte-qr">
                        <div class="titre"><i class="fas fa-qrcode"></i> Mon pointage</div>
                        <div class="pointage">
                            <p class="lien-qr">
                                Présentez ce QR à la réception.
                                <?php if ($maPresence && empty($maPresence['heure_arrivee'])): ?>
                                    Un scan enregistrera votre <strong>arrivée</strong>.
                                <?php elseif ($maPresence && empty($maPresence['heure_depart'])): ?>
                                    Votre arrivée est faite : ce QR pointera votre <strong>départ</strong>.
                                <?php else: ?>
                                    Il change toutes les <?= (int) QR_PERIODE ?> s.
                                <?php endif; ?>
                            </p>
                            <a href="<?= APP_URL ?>/presences/qr" class="btn btn-primary btn-sm" id="btnMonQr">
                                <i class="fas fa-qrcode"></i>
                                <?php
                                $qrLibelle = ($maPresence && !empty($maPresence['heure_arrivee']))
                                    ? (empty($maPresence['heure_depart']) ? 'Mon QR de départ' : 'Mon QR du jour')
                                    : 'Afficher mon QR';
                                echo $qrLibelle;
                                ?>
                            </a>
                        </div>
                    </div>

                    <?php if (!$maPresence || $maPresence['statut'] === 'absent'): ?>
                    <div id="compteARebours"></div>
                    <?php endif; ?>

                    <?php if ($maPresence && $maPresence['statut'] === 'absent'): ?>
                    <div class="alert alert-danger py-2 px-3 mb-2" style="font-size:12px">
                        <i class="fas fa-exclamation-triangle"></i> Marqué <strong>absent</strong> (aucun pointage avant <?= LIMITE_DECLARATION ?>).
                        Présentez quand même votre QR : la réception enregistrera votre <strong>arrivée tardive</strong>.
                        <?php if (!empty($maPresence['justification'])): ?>
                            <br>Justificatif : <?= htmlspecialchars($maPresence['justification']) ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($maPresence && $maPresence['statut'] === 'justifie'): ?>
                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size:12px">
                        <i class="fas fa-clipboard-check"></i> Absence <strong>régularisée</strong> par le RH : présentez votre QR de départ avant de quitter.
                    </div>
                    <?php endif; ?>

                    <div id="currentTime" class="legende mt-2"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($canScan): ?>
    <div class="row">
        <div class="col-12">
            <div class="carte-qr" style="background:#eef4ff;border-color:#c7dbff">
                <div class="titre" style="color:#4263eb"><i class="fas fa-qrcode"></i> Poste de réception</div>
                <div class="pointage">
                    <p class="lien-qr">Réceptionniste : presented QR de l'employé.
                        <strong>1<sup>er</sup> scan = ARRIVÉE</strong> · <strong>2<sup>e</sup> scan = DÉPART</strong>.
                        Scan après <?= LIMITE_DECLARATION ?> accepté comme retard.</p>
                    <a href="<?= APP_URL ?>/presences/scan" class="btn btn-primary btn-sm">
                        <i class="fas fa-camera"></i> Ouvrir le scanner
                    </a>
                </div>
                <div class="mt-2">
                    <span class="mini-stat ok"><b><?= $nbArrivees ?></b><span>Arrivées</span></span>
                    <span class="mini-stat dep"><b><?= $nbDeparts ?></b><span>Départs</span></span>
                    <span class="legende ml-1">pointages QR du jour</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($isManager): ?>
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clipboard-check"></i> Déclarations à valider (<?= count($enAttente) ?>)</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom complet</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Arrivée</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enAttente as $p): ?>
                            <tr>
                                <td><span class="badge badge-info"><?= htmlspecialchars($p['matricule']) ?></span></td>
                                <td><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></td>
                                <td><?= htmlspecialchars($p['nom_service'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y', strtotime($p['date_presence'])) ?></td>
                                <td><?= $p['heure_arrivee'] ? date('H:i', strtotime($p['heure_arrivee'])) : '-' ?></td>
                                <td>
                                    <span class="badge badge-<?= $p['statut'] === 'present' ? 'success' : 'warning' ?>"><?= ucfirst($p['statut']) ?></span>
                                    <?= $p['est_direction'] ? '<span class="badge badge-primary">DG</span>' : '' ?>
                                </td>
                                <td>
                                    <form method="POST" action="<?= APP_URL ?>/presences/valider/<?= $p['id_presence'] ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Valider</button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalRejet"
                                            data-id="<?= $p['id_presence'] ?>" data-nom="<?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>">
                                        <i class="fas fa-times"></i> Rejeter
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($enAttente)): ?>
                            <tr><td colspan="7" class="text-center text-muted">Aucune déclaration en attente</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalRejet" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form method="POST" action="<?= APP_URL ?>/presences/rejeter">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Rejeter la déclaration de <span id="rejetNom"></span></h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="rejetId">
                        <div class="form-group">
                            <label>Justification (obligatoire)</label>
                            <textarea name="justification" class="form-control" rows="2" required placeholder="Motif du rejet..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Rejeter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($isManager && !empty($absents)): ?>
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-times"></i> Absents (non pointés avant <?= LIMITE_DECLARATION ?>) — <?= count($absents) ?></h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom complet</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Régularisation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absents as $a): ?>
                            <tr>
                                <td><span class="badge badge-info"><?= htmlspecialchars($a['matricule']) ?></span></td>
                                <td><?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?></td>
                                <td><?= htmlspecialchars($a['nom_service'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y', strtotime($a['date_presence'])) ?></td>
                                <td>
                                    <form method="POST" action="<?= APP_URL ?>/presences/regulariser" class="d-inline" onsubmit="return confirm('Régulariser cette absence ?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $a['id_presence'] ?>">
                                        <input type="hidden" name="statut" value="justifie">
                                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Marquer justifié</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Registre des présences</h3>
                <div class="d-flex align-items-center">
                    <a href="<?= APP_URL ?>/export/presences?date=<?= $date ?>" class="btn btn-info btn-sm mr-2">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <form method="GET" action="<?= APP_URL ?>/presences" class="form-inline">
                        <input type="date" class="form-control mr-2" name="date" value="<?= htmlspecialchars($date ?? date('Y-m-d')) ?>">
                        <button class="btn btn-secondary" type="submit"><i class="fas fa-filter"></i> Filtrer</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom complet</th>
                        <th>Service</th>
                        <th>Arrivée</th>
                        <th>Départ</th>
                        <th>Retard</th>
                        <th>Statut</th>
                        <th>Source</th>
                        <th>Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sclass = ['declaration' => 'info', 'manuel' => 'secondary'];
                    $slabel = ['declaration' => 'Déclaration', 'manuel' => 'Manuel'];
                    $vclass = ['auto' => 'success', 'validee' => 'success', 'en_attente' => 'warning', 'rejetee' => 'danger'];
                    $vlabel = ['auto' => 'Automatique', 'validee' => 'Validée', 'en_attente' => 'En attente', 'rejetee' => 'Rejetée'];
                    ?>
                    <?php foreach ($presences ?? [] as $p): ?>
                    <tr>
                        <td><span class="badge badge-info"><?= htmlspecialchars($p['matricule']) ?></span></td>
                        <td><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></td>
                        <td><?= htmlspecialchars($p['nom_service'] ?? 'N/A') ?></td>
                        <td><?= $p['heure_arrivee'] ? date('H:i', strtotime($p['heure_arrivee'])) : '-' ?></td>
                        <td><?= $p['heure_depart'] ? date('H:i', strtotime($p['heure_depart'])) : '-' ?></td>
                        <td><?= !empty($p['retard']) && $p['retard'] > 0 ? $p['retard'] . ' min' : '-' ?></td>
                        <td>
                            <span class="badge badge-<?= $p['statut'] === 'present' ? 'success' : ($p['statut'] === 'retard' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($p['statut']) ?>
                            </span>
                        </td>
                        <td><span class="badge badge-<?= $sclass[$p['source']] ?? 'secondary' ?>"><?= $slabel[$p['source']] ?? $p['source'] ?></span></td>
                        <td>
                            <span class="badge badge-<?= $vclass[$p['validation']] ?? 'secondary' ?>"><?= $vlabel[$p['validation']] ?? $p['validation'] ?></span>
                            <?php if ($p['validation'] === 'rejetee' && !empty($p['justification'])): ?>
                                <small class="d-block text-danger" title="Justification"><?= htmlspecialchars($p['justification']) ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($presences)): ?>
                    <tr><td colspan="9" class="text-center text-muted">Aucune présence enregistrée pour cette date</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php
$serveurNowMs = (int)round(microtime(true) * 1000);
$limiteMs = strtotime(date('Y-m-d') . ' ' . LIMITE_DECLARATION . ':00') * 1000;
$extraScripts = '<script>
var SERVER_NOW_MS = ' . $serveurNowMs . ';
var LIMITE_DECLARATION_MS = ' . $limiteMs . ';
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
    const el = document.getElementById("currentTime");
    if (el) el.textContent = "Heure actuelle: " + heureTzFmt(maintenantMs(), true);
}
setInterval(updateClock, 1000);
updateClock();

function updateCompteARebours() {
    const el = document.getElementById("compteARebours");
    if (!el) return;
    const diff = LIMITE_DECLARATION_MS - maintenantMs();
    if (diff <= 0) {
        el.innerHTML = "<div class=\"alert alert-warning mb-2\" style=\"font-size:13px;padding:10px 12px\"><i class=\"fas fa-hourglass-end\"></i> Heure limite atteinte (<?= LIMITE_DECLARATION ?>). Votre pointage reste possible : il sera enregistré comme <strong>arrivée tardive</strong> au scan de votre QR.</div>";
        el.className = "mb-3";
        return;
    }
    const h = Math.floor(diff / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    const txt = "Il vous reste <strong>" + h + "h " + m + "min " + s + "s</strong> pour pointer sans retard";
    el.innerHTML = "<div class=\"alert alert-warning mb-2\" style=\"font-size:13px;padding:10px 12px\"><i class=\"fas fa-hourglass-half\"></i> " + txt + "<br><small style=\"font-size:11px\">Présentez votre QR à l\'accueil : avant <?= HEURE_DEBUT ?> = présent · à partir de <?= HEURE_DEBUT ?> = retard · le code change toutes les <?= QR_PERIODE ?> s</small></div>";
    el.className = "mb-3";
}
setInterval(updateCompteARebours, 1000);
updateCompteARebours();

$("#modalRejet").on("show.bs.modal", function (e) {
    const btn = $(e.relatedTarget);
    $("#rejetId").val(btn.data("id"));
    $("#rejetNom").text(btn.data("nom"));
});
</script>';

$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
