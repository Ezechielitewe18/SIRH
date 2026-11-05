<?php $pageTitle = isset($tokenAffiche) ? 'Jeton de l\'appareil' : 'Déclarer un appareil'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= isset($tokenAffiche) ? 'Jeton de l\'appareil' : 'Déclarer un appareil' ?></h1>
        <a href="<?= APP_URL ?>/dispositifs" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</section>

<section class="content">
    <?php if (isset($tokenAffiche)): ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-key"></i> Jeton d'authentification</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Ce jeton ne sera plus jamais affiché.</strong>
                            Copiez-le maintenant et remettez-le à l'appareil. Le serveur n'en conserve qu'une empreinte chiffrée.
                        </div>

                        <p class="mb-1">
                            Appareil :
                            <strong><?= htmlspecialchars($appareil['code'] ?? '') ?></strong>
                            — <?= htmlspecialchars($appareil['libelle'] ?? '') ?>
                            (<?= htmlspecialchars($appareil['type'] ?? '') ?>)
                        </p>

                        <div class="input-group mb-3">
                            <input type="text" id="jeton" class="form-control font-monospace" readonly
                                   value="<?= htmlspecialchars($tokenAffiche) ?>" onclick="this.select()">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary" id="btn-copier">
                                    <i class="fas fa-copy"></i> Copier
                                </button>
                            </div>
                        </div>

                        <p class="mb-1"><strong>En-tête attendu par l'appareil :</strong></p>
                        <pre class="bg-dark text-white p-2 rounded mb-3" id="entete">Authorization: Device <?= htmlspecialchars($tokenAffiche) ?></pre>

                        <p class="text-muted mb-0">
                            Test de disponibilité sans jeton :
                            <code><?= htmlspecialchars(APP_URL) ?>/pointage.php?action=sante</code>
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="<?= APP_URL ?>/dispositifs/show/<?= (int) ($appareil['id_dispositif'] ?? 0) ?>" class="btn btn-primary">
                            <i class="fas fa-check"></i> J'ai noté le jeton
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.getElementById('btn-copier').addEventListener('click', function () {
            var champ = document.getElementById('jeton');
            champ.select();
            champ.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(champ.value).then(function () {
                var b = document.getElementById('btn-copier');
                b.innerHTML = '<i class="fas fa-check"></i> Copié';
                setTimeout(function () { b.innerHTML = '<i class="fas fa-copy"></i> Copier'; }, 1500);
            });
        });
        </script>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-broadcast-tower text-primary"></i> Nouvel appareil</h3>
                    </div>
                    <form method="POST" action="<?= APP_URL ?>/dispositifs/create">
                        <?= csrf_field() ?>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="code">Code technique <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" class="form-control" required
                                       pattern="[A-Za-z0-9._-]{2,50}" placeholder="Ex. PORTIQUE-ENTREE"
                                       value="<?= htmlspecialchars($_POST['code'] ?? '') ?>">
                                <small class="form-text text-muted">Lettres, chiffres, point et tiret. Sert à identifier l'appareil dans les journaux.</small>
                            </div>

                            <div class="form-group">
                                <label for="libelle">Libellé <span class="text-danger">*</span></label>
                                <input type="text" name="libelle" id="libelle" class="form-control" required
                                       placeholder="Ex. Portique d'entrée principale"
                                       value="<?= htmlspecialchars($_POST['libelle'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label for="type">Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control" required>
                                    <?php foreach (['qr' => 'QR', 'rfid' => 'RFID / NFC', 'biometrie' => 'Biométrie', 'autre' => 'Autre'] as $valeur => $texte): ?>
                                        <option value="<?= $valeur ?>" <?= ($_POST['type'] ?? 'qr') === $valeur ? 'selected' : '' ?>>
                                            <?= $texte ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">
                                    Le type doit correspondre à la source envoyée : un appareil « RFID » ne peut pas envoyer un pointage « QR ».
                                </small>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="marque">Marque</label>
                                    <input type="text" name="marque" id="marque" class="form-control"
                                           value="<?= htmlspecialchars($_POST['marque'] ?? '') ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="modele">Modèle</label>
                                    <input type="text" name="modele" id="modele" class="form-control"
                                           value="<?= htmlspecialchars($_POST['modele'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="numero_serie">Numéro de série</label>
                                    <input type="text" name="numero_serie" id="numero_serie" class="form-control"
                                           value="<?= htmlspecialchars($_POST['numero_serie'] ?? '') ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="emplacement">Emplacement</label>
                                    <input type="text" name="emplacement" id="emplacement" class="form-control"
                                           placeholder="Ex. Hall, porte B"
                                           value="<?= htmlspecialchars($_POST['emplacement'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="2"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Déclarer l'appareil
                            </button>
                            <a href="<?= APP_URL ?>/dispositifs" class="btn btn-default">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info-circle"></i> À savoir</h5>
                    <ul class="mb-0 pl-3">
                        <li>Le jeton n'est affiché qu'à la création : le serveur ne garde qu'une empreinte.</li>
                        <li>Un appareil non déclaré est refusé par l'endpoint <code>pointage.php</code>.</li>
                        <li>Chaque appareil est révocable individuellement, sans toucher aux autres.</li>
                        <li>Ne déclarez pas un appareil avant de l'avoir reçu et testé : le jeton se perd vite.</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
