<?php $pageTitle = 'Gestion des présences'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1>Présences</h1>
</section>

<section class="content">
    <?php if (isset($_SESSION['employee_id'])): ?>
    <div class="row mb-3">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-clock"></i> Ma présence aujourd'hui</h3>
                </div>
                <div class="card-body">
                    <?php if ($maPresence): ?>
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Arrivée</small>
                                <div class="font-weight-bold"><?= $maPresence['heure_arrivee'] ? date('H:i', strtotime($maPresence['heure_arrivee'])) : '-' ?></div>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Départ</small>
                                <div class="font-weight-bold"><?= $maPresence['heure_depart'] ? date('H:i', strtotime($maPresence['heure_depart'])) : '-' ?></div>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Statut</small>
                                <div>
                                    <span class="badge badge-<?= $maPresence['statut'] === 'present' ? 'success' : ($maPresence['statut'] === 'retard' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($maPresence['statut']) ?><?= $maPresence['retard'] > 0 ? ' (+' . $maPresence['retard'] . ' min)' : '' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Validation</small>
                                <div>
                                    <?php
                                    $vclass = ['auto' => 'success', 'validee' => 'success', 'en_attente' => 'warning', 'rejetee' => 'danger'];
                                    $vlabel = ['auto' => 'Automatique', 'validee' => 'Validée', 'en_attente' => 'En attente', 'rejetee' => 'Rejetée'];
                                    $v = $maPresence['validation'];
                                    ?>
                                    <span class="badge badge-<?= $vclass[$v] ?? 'secondary' ?>"><?= $vlabel[$v] ?? $v ?></span>
                                    <?php if ($v === 'rejetee' && !empty($maPresence['justification'])): ?>
                                        <div class="text-danger mt-1"><i class="fas fa-times-circle"></i> <?= htmlspecialchars($maPresence['justification']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php if (empty($maPresence['heure_depart']) && $maPresence['statut'] !== 'absent' && $maPresence['validation'] !== 'rejetee'): ?>
                        <hr>
                        <form method="POST" action="<?= APP_URL ?>/presences/checkout" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-warning"><i class="fas fa-sign-out-alt"></i> Départ</button>
                        </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">Vous n'avez pas encore déclaré votre arrivée aujourd'hui.</p>
                        <form method="POST" action="<?= APP_URL ?>/presences/declarer">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-sign-in-alt"></i> Déclarer mon arrivée</button>
                        </form>
                        <p class="text-muted mt-2 mb-0"><small>Votre déclaration sera vérifiée par la RH. Le pointage par QR code est réservé à la Direction Générale.</small></p>
                    <?php endif; ?>
                    <div id="currentTime" class="text-muted mt-2"><small></small></div>
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
                    $sclass = ['qrcode' => 'primary', 'declaration' => 'info', 'manuel' => 'secondary'];
                    $slabel = ['qrcode' => 'QR', 'declaration' => 'Déclaration', 'manuel' => 'Manuel'];
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

<?php $extraScripts = '<script>
function updateClock() {
    const now = new Date();
    const el = document.getElementById("currentTime");
    if (el) el.textContent = "Heure actuelle: " + now.toLocaleTimeString("fr-FR");
}
setInterval(updateClock, 1000);
updateClock();

$("#modalRejet").on("show.bs.modal", function (e) {
    const btn = $(e.relatedTarget);
    $("#rejetId").val(btn.data("id"));
    $("#rejetNom").text(btn.data("nom"));
});
</script>';

$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
