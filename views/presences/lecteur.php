<?php $pageTitle = 'Poste de réception'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>Poste de réception</h1>
            <p class="text-muted mb-0">Pointage des badges RFID au lecteur USB. L'opérateur doit être connecté (admin ou RH).</p>
        </div>
        <a href="<?= APP_URL ?>/cartes" class="btn btn-default mt-2 mt-sm-0">
            <i class="fas fa-id-card"></i> Gérer les cartes
        </a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-lg-5">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-wifi"></i> Lire une carte</h3>
                </div>
                <form id="form-lecteur" method="POST" action="<?= APP_URL ?>/presences/lecteur" autocomplete="off">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="form-group mb-2">
                            <label for="uid_carte">Numéro de carte</label>
                            <input type="text" name="uid_carte" id="uid_carte" class="form-control form-control-lg"
                                   placeholder="Passez la carte sur le lecteur…" required autofocus
                                   inputmode="text" autocapitalize="characters">
                            <small class="form-text text-muted">
                                Le lecteur USB tape le numéro puis valide. Sinon, saisissez-le et appuyez sur Entrée.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-check"></i> Enregistrer le pointage
                        </button>
                    </div>
                </form>
            </div>

            <div id="resultat" class="alert d-none" role="status" aria-live="polite"></div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-list text-secondary"></i> Événements du <?= htmlspecialchars($date) ?></h3>
                    <form method="GET" action="<?= APP_URL ?>/presences/lecteur" class="form-inline">
                        <input type="date" name="date" class="form-control form-control-sm" value="<?= htmlspecialchars($date) ?>">
                        <button class="btn btn-sm btn-default ml-1"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Heure</th>
                                <th>Employé</th>
                                <th>Carte</th>
                                <th>Action</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($evenements)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Aucun événement pour cette date.</td></tr>
                        <?php else: ?>
                            <?php
                            $badges = [
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
                                <td><?= htmlspecialchars(substr($evt['horodatage'], 11, 5)) ?></td>
                                <td>
                                    <?php if (!empty($evt['nom'])): ?>
                                        <strong><?= htmlspecialchars($evt['prenom'] . ' ' . $evt['nom']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($evt['matricule']) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Inconnu</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($evt['uid_carte'])): ?>
                                        <code><?= htmlspecialchars($evt['uid_carte']) ?></code>
                                    <?php else: ?>
                                        <small class="text-muted"><?= htmlspecialchars(mb_substr((string) ($evt['identifiant_lu'] ?? '—'), 0, 24)) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($evt['type_evenement']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $badges[$evt['statut_traitement']] ?? 'secondary' ?>">
                                        <?= htmlspecialchars($evt['statut_traitement']) ?>
                                    </span>
                                    <?php if (!empty($evt['message'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($evt['message']) ?></small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    var form = document.getElementById('form-lecteur');
    var champ = document.getElementById('uid_carte');
    var zone = document.getElementById('resultat');

    function afficher(type, message) {
        zone.className = 'alert alert-' + type;
        zone.textContent = message;
        zone.classList.remove('d-none');
    }

    form.addEventListener('submit', function (evenement) {
        evenement.preventDefault();
        var uid = champ.value.trim();
        if (uid === '') { return; }

        var bouton = form.querySelector('button[type="submit"]');
        bouton.disabled = true;

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(function (reponse) { return reponse.json(); })
        .then(function (data) {
            var nom = data.employe
                ? ' ' + (data.employe.prenom || '') + ' ' + (data.employe.nom || '')
                : '';
            if (data.ok) {
                afficher('success', '✔ ' + (data.message || 'Pointage enregistré.') + nom);
            } else {
                var type = (data.statut === 'rejete') ? 'danger' : 'warning';
                afficher(type, '✖ ' + (data.message || 'Pointage refusé.') + nom);
            }
            champ.value = '';
            champ.focus();
            if (data.ok) {
                setTimeout(function () { window.location.reload(); }, 1200);
            }
        })
        .catch(function () {
            afficher('danger', 'Erreur de communication avec le serveur. Réessayez.');
        })
        .finally(function () {
            bouton.disabled = false;
        });
    });
})();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
