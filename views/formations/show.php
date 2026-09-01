<?php $pageTitle = 'Détail de la formation'; ?>
<?php ob_start();

$employeeModel = new EmployeeModel();
$employesNonInscrits = [];
$inscritIds = array_column($inscrits ?? [], 'id_employe');

// Récupérer tous les employés actifs pour le formulaire d'inscription
$req = (Database::getInstance()->getConnection())->query(
    "SELECT id_employe, nom, prenom, matricule FROM employes WHERE statut = 'actif' ORDER BY nom"
);
$tousEmployes = $req->fetchAll();
foreach ($tousEmployes as $e) {
    if (!in_array($e['id_employe'], $inscritIds)) {
        $employesNonInscrits[] = $e;
    }
}
?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= htmlspecialchars($formation['titre']) ?></h1>
        <a href="<?= APP_URL ?>/formations" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Informations</h3></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Type</th><td><?= htmlspecialchars($formation['type_formation'] ?? 'N/A') ?></td></tr>
                        <tr><th>Durée</th><td><?= htmlspecialchars($formation['duree'] ?? 'N/A') ?></td></tr>
                        <tr><th>Date début</th><td><?= $formation['date_debut'] ? date('d/m/Y', strtotime($formation['date_debut'])) : 'N/A' ?></td></tr>
                        <tr><th>Date fin</th><td><?= $formation['date_fin'] ? date('d/m/Y', strtotime($formation['date_fin'])) : 'N/A' ?></td></tr>
                        <tr><th>Statut</th>
                            <td>
                                <span class="badge badge-<?= $formation['statut'] === 'terminee' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($formation['statut']) ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                    <hr>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($formation['description'] ?? 'Aucune description')) ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Inscrire un employé</h3></div>
                <form method="POST" action="<?= APP_URL ?>/formations/inscrire/<?= $formation['id_formation'] ?>">
                    <div class="card-body">
                        <div class="form-group">
                            <select name="id_employe" class="form-control" required>
                                <option value="">-- Choisir un employé --</option>
                                <?php foreach ($employesNonInscrits as $e): ?>
                                <option value="<?= $e['id_employe'] ?>"><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?> (<?= $e['matricule'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-user-plus"></i> Inscrire</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Employés inscrits (<?= count($inscrits) ?>)</h3></div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Employé</th>
                                <th>Service</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inscrits as $i): ?>
                            <tr>
                                <td><span class="badge badge-info"><?= htmlspecialchars($i['matricule']) ?></span></td>
                                <td><?= htmlspecialchars($i['prenom'] . ' ' . $i['nom']) ?></td>
                                <td><?= htmlspecialchars($i['nom_service'] ?? 'N/A') ?></td>
                                <td>
                                    <form method="POST" action="<?= APP_URL ?>/formations/statut/<?= $formation['id_formation'] ?>/<?= $i['id_employe'] ?>" class="form-inline" style="display:inline;">
                                        <select name="statut" class="form-control form-control-sm" onchange="this.form.submit()">
                                            <option value="inscrit" <?= $i['statut'] === 'inscrit' ? 'selected' : '' ?>>Inscrit</option>
                                            <option value="present" <?= $i['statut'] === 'present' ? 'selected' : '' ?>>Présent</option>
                                            <option value="absent" <?= $i['statut'] === 'absent' ? 'selected' : '' ?>>Absent</option>
                                            <option value="termine" <?= $i['statut'] === 'termine' ? 'selected' : '' ?>>Terminé</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="<?= APP_URL ?>/formations/desinscrire/<?= $formation['id_formation'] ?>/<?= $i['id_employe'] ?>" style="display:inline;" onsubmit="return confirm('Retirer cet employé ?')">
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-user-minus"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($inscrits)): ?>
                            <tr><td colspan="5" class="text-center text-muted">Aucun employé inscrit</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
