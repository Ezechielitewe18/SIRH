<?php $pageTitle = 'Dispositifs de pointage'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>Dispositifs de pointage</h1>
            <p class="text-muted mb-0">Appareils autorisés à envoyer des pointages. Un appareil non déclaré est refusé.</p>
        </div>
        <a href="<?= APP_URL ?>/dispositifs/create" class="btn btn-primary mt-2 mt-sm-0">
            <i class="fas fa-plus"></i> Déclarer un appareil
        </a>
    </div>
</section>

<section class="content">
    <?php if (empty($dispositifs)): ?>
        <div class="alert alert-info">
            <i class="fas fa-broadcast-tower"></i>
            Aucun appareil déclaré. Le QR fonctionne sans appareil ; un lecteur RFID réseau ou un terminal biométrique devra être déclaré ici avant de pouvoir envoyer des pointages.
        </div>
    <?php else: ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-broadcast-tower text-primary"></i> <?= count($dispositifs) ?> appareil(s)</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Libellé</th>
                        <th>Type</th>
                        <th>Emplacement</th>
                        <th>Statut</th>
                        <th>Dernier contact</th>
                        <th>Testé le</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $badges = [
                    'actif' => 'success',
                    'maintenance' => 'warning',
                    'hors_service' => 'danger',
                ];
                foreach ($dispositifs as $appareil):
                ?>
                    <tr>
                        <td><code><?= htmlspecialchars($appareil['code']) ?></code></td>
                        <td><?= htmlspecialchars($appareil['libelle']) ?></td>
                        <td><span class="badge badge-light"><?= htmlspecialchars($appareil['type']) ?></span></td>
                        <td><?= htmlspecialchars($appareil['emplacement'] ?? '—') ?></td>
                        <td>
                            <span class="badge badge-<?= $badges[$appareil['statut']] ?? 'secondary' ?>">
                                <?= htmlspecialchars($appareil['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <?= !empty($appareil['dernier_contact'])
                                ? htmlspecialchars(substr($appareil['dernier_contact'], 0, 16))
                                : '<span class="text-muted">Jamais</span>' ?>
                        </td>
                        <td>
                            <?= !empty($appareil['teste_le'])
                                ? '<i class="fas fa-check-circle text-success"></i> ' . htmlspecialchars(substr($appareil['teste_le'], 0, 10))
                                : '<span class="text-muted">Non testé</span>' ?>
                        </td>
                        <td class="text-right">
                            <a href="<?= APP_URL ?>/dispositifs/show/<?= (int) $appareil['id_dispositif'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-cog"></i> Gérer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
