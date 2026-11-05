<?php $pageTitle = 'Attribuer une carte'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Attribuer une carte RFID</h1>
        <a href="<?= APP_URL ?>/cartes" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-id-card text-primary"></i> Nouveau badge</h3>
                </div>
                <form method="POST" action="<?= APP_URL ?>/cartes/create">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="id_employe">Employé <span class="text-danger">*</span></label>
                            <select name="id_employe" id="id_employe" class="form-control" required>
                                <option value="">— Choisir un employé —</option>
                                <?php foreach ($employes ?? [] as $employe): ?>
                                    <option value="<?= (int) $employe['id_employe'] ?>"
                                        <?= (int) ($employePreselectionne ?? 0) === (int) $employe['id_employe'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($employe['matricule'] . ' — ' . $employe['prenom'] . ' ' . $employe['nom']) ?>
                                        <?= !empty($employe['nom_service']) ? ' (' . htmlspecialchars($employe['nom_service']) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="uid_carte">Numéro lu par le lecteur <span class="text-danger">*</span></label>
                            <input type="text" name="uid_carte" id="uid_carte" class="form-control"
                                   placeholder="Ex. 04A2B3C4D5" required autocomplete="off"
                                   value="<?= htmlspecialchars($_POST['uid_carte'] ?? '') ?>">
                            <small class="form-text text-muted">
                                Passez la carte sur le lecteur : le numéro s'inscrit ici. Les espaces et séparateurs sont ignorés.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="technologie">Technologie de la carte</label>
                            <input type="text" name="technologie" id="technologie" class="form-control"
                                   list="technologies" placeholder="Ex. Mifare Classic 1K"
                                   value="<?= htmlspecialchars($_POST['technologie'] ?? '') ?>">
                            <datalist id="technologies">
                                <option value="Mifare Classic 1K">
                                <option value="Mifare Classic 4K">
                                <option value="Mifare DESFire">
                                <option value="NFC NTAG213">
                                <option value="NFC NTAG216">
                            </datalist>
                            <small class="form-text text-muted">
                                À renseigner d'après la fiche du fournisseur. Une carte 125 kHz et une carte 13,56 MHz ne sont pas interchangeables.
                            </small>
                        </div>

                        <div class="form-group mb-0">
                            <label for="libelle">Libellé</label>
                            <input type="text" name="libelle" id="libelle" class="form-control"
                                   placeholder="Ex. Badge entrée principale"
                                   value="<?= htmlspecialchars($_POST['libelle'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Attribuer la carte
                        </button>
                        <a href="<?= APP_URL ?>/cartes" class="btn btn-default">Annuler</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="callout callout-info">
                <h5><i class="fas fa-info-circle"></i> Bonnes pratiques</h5>
                <ul class="mb-0 pl-3">
                    <li>Une carte ne peut être active que pour un seul employé.</li>
                    <li>Carte perdue : déclarez-la « perdue » immédiatement, puis remplacez-la.</li>
                    <li>Une carte désactivée reste dans l'historique : elle n'est jamais effacée.</li>
                    <li>Le numéro affiché dépend du lecteur : notez toujours la technologie avec la carte.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
