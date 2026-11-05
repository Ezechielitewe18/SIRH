<?php $pageTitle = 'Appareil — ' . ($appareil['libelle'] ?? ''); ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1><?= htmlspecialchars($appareil['libelle']) ?></h1>
            <p class="text-muted mb-0"><code><?= htmlspecialchars($appareil['code']) ?></code> — <?= htmlspecialchars($appareil['type']) ?></p>
        </div>
        <a href="<?= APP_URL ?>/dispositifs" class="btn btn-default mt-2 mt-sm-0">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Fiche de l'appareil</h3>
                </div>
                <div class="card-body">
                    <?php
                    $badges = ['actif' => 'success', 'maintenance' => 'warning', 'hors_service' => 'danger'];
                    ?>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Statut</dt>
                        <dd class="col-sm-7">
                            <span class="badge badge-<?= $badges[$appareil['statut']] ?? 'secondary' ?>">
                                <?= htmlspecialchars($appareil['statut']) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-5">Marque / modèle</dt>
                        <dd class="col-sm-7">
                            <?= htmlspecialchars(trim(($appareil['marque'] ?? '') . ' ' . ($appareil['modele'] ?? '')) ?: '—') ?>
                        </dd>

                        <dt class="col-sm-5">Numéro de série</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($appareil['numero_serie'] ?? '—') ?></dd>

                        <dt class="col-sm-5">Emplacement</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($appareil['emplacement'] ?? '—') ?></dd>

                        <dt class="col-sm-5">Dernier contact</dt>
                        <dd class="col-sm-7">
                            <?= !empty($appareil['dernier_contact'])
                                ? htmlspecialchars(substr($appareil['dernier_contact'], 0, 16))
                                : 'Jamais' ?>
                        </dd>

                        <dt class="col-sm-5">Test de réception</dt>
                        <dd class="col-sm-7">
                            <?= !empty($appareil['teste_le'])
                                ? htmlspecialchars(substr($appareil['teste_le'], 0, 16))
                                : 'Non testé' ?>
                        </dd>

                        <dt class="col-sm-5">Créé le</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars(substr($appareil['created_at'] ?? '', 0, 16)) ?></dd>
                    </dl>

                    <?php if (!empty($appareil['notes'])): ?>
                        <hr>
                        <p class="mb-0 text-muted" style="white-space: pre-line;"><?= htmlspecialchars($appareil['notes']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tools"></i> Actions</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Le jeton n'est jamais réaffiché. En cas de perte, régénérez-le : l'ancien devient inutilisable immédiatement.
                    </p>

                    <div class="d-flex flex-wrap">
                        <form method="POST" action="<?= APP_URL ?>/dispositifs/tester" class="mr-2 mb-2">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id_dispositif" value="<?= (int) $appareil['id_dispositif'] ?>">
                            <button class="btn btn-outline-success">
                                <i class="fas fa-check"></i> Marquer comme testé
                            </button>
                        </form>

                        <form method="POST" action="<?= APP_URL ?>/dispositifs/regenerer" class="mr-2 mb-2"
                              onsubmit="return confirm('Régénérer le jeton ? L\'appareil devra être reconfiguré avec le nouveau jeton.');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id_dispositif" value="<?= (int) $appareil['id_dispositif'] ?>">
                            <button class="btn btn-outline-warning">
                                <i class="fas fa-key"></i> Régénérer le jeton
                            </button>
                        </form>

                        <?php if ($appareil['statut'] === 'actif'): ?>
                            <button type="button" class="btn btn-outline-danger mb-2" data-toggle="collapse" data-target="#bloc-revoquer">
                                <i class="fas fa-ban"></i> Révoquer
                            </button>
                        <?php else: ?>
                            <form method="POST" action="<?= APP_URL ?>/dispositifs/reactiver" class="mb-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id_dispositif" value="<?= (int) $appareil['id_dispositif'] ?>">
                                <button class="btn btn-outline-primary">
                                    <i class="fas fa-play"></i> Remettre en service
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <?php if ($appareil['statut'] === 'actif'): ?>
                        <div class="collapse mt-2" id="bloc-revoquer">
                            <div class="border rounded p-3 bg-light">
                                <form method="POST" action="<?= APP_URL ?>/dispositifs/revoquer">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id_dispositif" value="<?= (int) $appareil['id_dispositif'] ?>">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="statut">Nouveau statut</label>
                                            <select name="statut" id="statut" class="form-control form-control-sm">
                                                <option value="hors_service">Hors service</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-8">
                                            <label for="motif">Motif</label>
                                            <input type="text" name="motif" id="motif" class="form-control form-control-sm"
                                                   placeholder="Ex. appareil perdu, remplacement">
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-ban"></i> Confirmer la révocation
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history text-secondary"></i> 30 derniers événements reçus</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th>Reçu le</th>
                        <th>Employé</th>
                        <th>Action</th>
                        <th>Statut</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($evenements)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Aucun événement reçu de cet appareil.</td></tr>
                <?php else: ?>
                    <?php
                    $badgesEvt = [
                        'traite' => 'success',
                        'en_attente' => 'warning',
                        'a_valider' => 'info',
                        'doublon' => 'secondary',
                        'rejete' => 'danger',
                        'recu' => 'light',
                    ];
                    foreach ($evenements as $evt):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars(substr($evt['horodatage'], 0, 16)) ?></td>
                        <td>
                            <?php if (!empty($evt['nom'])): ?>
                                <?= htmlspecialchars($evt['prenom'] . ' ' . $evt['nom']) ?>
                                <br><small class="text-muted"><?= htmlspecialchars($evt['matricule']) ?></small>
                            <?php else: ?>
                                <span class="text-muted">Inconnu</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($evt['type_evenement']) ?></td>
                        <td>
                            <span class="badge badge-<?= $badgesEvt[$evt['statut_traitement']] ?? 'secondary' ?>">
                                <?= htmlspecialchars($evt['statut_traitement']) ?>
                            </span>
                        </td>
                        <td><small class="text-muted"><?= htmlspecialchars($evt['message'] ?? '') ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
