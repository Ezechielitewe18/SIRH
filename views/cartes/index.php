<?php $pageTitle = 'Cartes QR Code'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-qrcode text-primary"></i> Cartes QR Code</h1>
        <a href="<?= APP_URL ?>/cartes/pointer" class="btn btn-success" target="_blank">
            <i class="fas fa-fingerprint"></i> Pointage par QR
        </a>
    </div>
</section>

<section class="content">
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Employé</th>
                        <th>Service</th>
                        <th>Code QR</th>
                        <th>Statut carte</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartes as $item): ?>
                    <?php $emp = $item['employe']; $carte = $item['carte']; ?>
                    <tr>
                        <td><span class="badge badge-info"><?= htmlspecialchars($emp['matricule']) ?></span></td>
                        <td><strong><?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?></strong></td>
                        <td><?= htmlspecialchars($emp['nom_service'] ?? 'N/A') ?></td>
                        <td>
                            <?php if ($carte): ?>
                            <code><?= htmlspecialchars($carte['code_qr']) ?></code>
                            <span class="badge badge-<?= $carte['actif'] ? 'success' : 'danger' ?>"><?= $carte['actif'] ? 'active' : 'inactive' ?></span>
                            <?php else: ?>
                            <span class="text-muted">Pas de carte</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($carte): ?>
                            <span class="badge badge-<?= $carte['actif'] ? 'success' : 'danger' ?>"><?= $carte['actif'] ? 'Active' : 'Inactive' ?></span>
                            <?php else: ?>
                            <span class="badge badge-warning">Non générée</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($carte): ?>
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#qrModal<?= $emp['id_employe'] ?>">
                                <i class="fas fa-eye"></i> Voir QR
                            </button>
                            <form method="POST" action="<?= APP_URL ?>/cartes/toggle/<?= $carte['id_carte'] ?>" style="display:inline;">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-<?= $carte['actif'] ? 'secondary' : 'success' ?>" title="<?= $carte['actif'] ? 'Désactiver' : 'Activer' ?>">
                                    <i class="fas fa-<?= $carte['actif'] ? 'ban' : 'check' ?>"></i>
                                </button>
                            </form>
                            <form method="POST" action="<?= APP_URL ?>/cartes/generer/<?= $emp['id_employe'] ?>" style="display:inline;"
                                  onsubmit="return confirm('Régénérer (cela crée un nouveau code) ?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-primary"><i class="fas fa-sync-alt"></i></button>
                            </form>

                            <div class="modal fade" id="qrModal<?= $emp['id_employe'] ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content text-center">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Carte QR - <?= htmlspecialchars($emp['prenom']) ?></h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <?php $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode(APP_URL . "/cartes/pointer?code=" . $carte['code_qr']); ?>
                                            <img src="<?= $qrUrl ?>" alt="QR Code" class="img-fluid" style="max-width:200px;">
                                            <p class="mt-2">Code : <code><?= htmlspecialchars($carte['code_qr']) ?></code></p>
                                            <a href="<?= $qrUrl ?>" download="carte_<?= $emp['matricule'] ?>.png" class="btn btn-success">
                                                <i class="fas fa-download"></i> Télécharger
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php else: ?>
                            <form method="POST" action="<?= APP_URL ?>/cartes/generer/<?= $emp['id_employe'] ?>" style="display:inline;">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-primary"><i class="fas fa-qrcode"></i> Générer</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($cartes)): ?>
                    <tr><td colspan="6" class="text-center text-muted">Aucun employé</td></tr>
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
