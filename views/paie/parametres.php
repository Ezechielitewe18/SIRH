<?php $pageTitle = 'Paramètres de la paie'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-cog text-secondary"></i> Paramètres de paie</h1>
        <a href="<?= APP_URL ?>/paie" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
</section>

<section class="content">
    <div class="card">
        <form method="POST" action="<?= APP_URL ?>/paie/parametres">
            <?= csrf_field() ?>
            <div class="card-body">
                <p class="text-muted">Modifiez les paramètres utilisés pour le calcul automatique des bulletins de paie.</p>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width:30%">Paramètre</th>
                                <th style="width:15%">Valeur</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($parametres as $p): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($p['nom_parametre']) ?></strong></td>
                                <td>
                                    <input type="number" step="0.01" class="form-control" name="valeur[<?= $p['id_parametre'] ?>]" value="<?= htmlspecialchars($p['valeur']) ?>">
                                </td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($p['type_parametre']) ?></span></td>
                                <td class="text-muted"><?= htmlspecialchars($p['description']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer les paramètres</button>
            </div>
        </form>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
