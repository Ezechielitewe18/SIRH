<?php $pageTitle = 'Modifier l\'employé'; ?>
<?php ob_start(); ?>

<section class="content-header">
    <h1>Modifier l'employé</h1>
</section>

<section class="content">
    <div class="card">
        <form method="POST" action="<?= APP_URL ?>/employees/edit/<?= $employee['id_employe'] ?>">
            <?= csrf_field() ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Matricule</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($employee['matricule']) ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($employee['nom']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Post-nom</label>
                            <input type="text" class="form-control" name="postnom" value="<?= htmlspecialchars($employee['postnom'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Prénom *</label>
                            <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($employee['prenom']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sexe *</label>
                            <select class="form-control" name="sexe" required>
                                <option value="M" <?= $employee['sexe'] === 'M' ? 'selected' : '' ?>>Masculin</option>
                                <option value="F" <?= $employee['sexe'] === 'F' ? 'selected' : '' ?>>Féminin</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date de naissance</label>
                            <input type="date" class="form-control" name="date_naissance" value="<?= $employee['date_naissance'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Lieu de naissance</label>
                            <input type="text" class="form-control" name="lieu_naissance" value="<?= htmlspecialchars($employee['lieu_naissance'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Adresse</label>
                            <input type="text" class="form-control" name="adresse" value="<?= htmlspecialchars($employee['adresse'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" class="form-control" name="telephone" value="<?= htmlspecialchars($employee['telephone'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email professionnel</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="<?= htmlspecialchars($employee['email'] ?? '') ?>" readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Email professionnel lié au compte</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Service</label>
                            <select class="form-control" name="id_service">
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($services ?? [] as $service): ?>
                                <option value="<?= $service['id_service'] ?>" <?= ($employee['id_service'] ?? '') == $service['id_service'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service['nom_service']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Poste</label>
                            <input type="text" class="form-control" name="poste" value="<?= htmlspecialchars($employee['poste'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date d'embauche *</label>
                            <input type="date" class="form-control" name="date_embauche" value="<?= $employee['date_embauche'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Salaire</label>
                            <input type="number" class="form-control" name="salaire" step="0.01" value="<?= $employee['salaire'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Statut</label>
                            <select class="form-control" name="statut">
                                <option value="actif" <?= $employee['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                                <option value="inactif" <?= $employee['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                                <option value="suspendu" <?= $employee['statut'] === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Primes : facultatives, propres a chaque employe -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-star text-warning"></i> Primes fixes mensuelles (facultatif)</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted" style="font-size:13px">
                        Laissez vide si l'employé ne touche pas de prime.
                        Les primes saisies ici s'ajoutent automatiquement à son bulletin.
                    </p>
                    <table class="table table-sm mb-2" style="margin-bottom:0">
                        <thead>
                            <tr>
                                <th style="width:60%">Nom de la prime</th>
                                <th style="width:30%">Montant (FC)</th>
                                <th style="width:10%"></th>
                            </tr>
                        </thead>
                        <tbody id="primesBody"></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAjouterPrime">
                        <i class="fas fa-plus"></i> Ajouter une prime
                    </button>
                    <span class="text-muted" id="totalPrimes" style="margin-left:10px;font-size:13px"></span>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                <a href="<?= APP_URL ?>/employees" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</section>

<script>
var primesBody = document.getElementById('primesBody');

function ajouterPrimeLigne(libelle, montant) {
    var tr = document.createElement('tr');

    var inLibelle = document.createElement('input');
    inLibelle.type = 'text';
    inLibelle.className = 'form-control form-control-sm prime-libelle';
    inLibelle.name = 'prime_libelle[]';
    inLibelle.placeholder = 'Ex: Prime de fonction';
    inLibelle.value = libelle || '';

    var inMontant = document.createElement('input');
    inMontant.type = 'number';
    inMontant.className = 'form-control form-control-sm prime-montant';
    inMontant.name = 'prime_montant[]';
    inMontant.step = '0.01';
    inMontant.min = '0';
    inMontant.placeholder = '0.00';
    inMontant.value = (montant === '' || montant === null || montant === undefined) ? '' : montant;

    var tdLibelle = document.createElement('td');
    tdLibelle.appendChild(inLibelle);
    var tdMontant = document.createElement('td');
    tdMontant.appendChild(inMontant);

    var btnRetirer = document.createElement('button');
    btnRetirer.type = 'button';
    btnRetirer.className = 'btn btn-sm btn-outline-danger btn-retirer-prime';
    btnRetirer.title = 'Retirer';
    btnRetirer.innerHTML = '<i class="fas fa-times"></i>';
    var tdAction = document.createElement('td');
    tdAction.className = 'text-center';
    tdAction.appendChild(btnRetirer);

    tr.appendChild(tdLibelle);
    tr.appendChild(tdMontant);
    tr.appendChild(tdAction);
    primesBody.appendChild(tr);
    majTotalPrimes();
}

function majTotalPrimes() {
    var total = 0;
    document.querySelectorAll('.prime-montant').forEach(function(i) { total += parseFloat(i.value) || 0; });
    document.getElementById('totalPrimes').innerHTML = total > 0
        ? 'Total des primes : <strong>' + total.toLocaleString('fr-FR') + ' FC</strong>'
        : '';
}

document.getElementById('btnAjouterPrime').addEventListener('click', function() { ajouterPrimeLigne('', ''); });
primesBody.addEventListener('click', function(e) {
    if (e.target.closest('.btn-retirer-prime')) {
        e.target.closest('tr').remove();
        majTotalPrimes();
    }
});
primesBody.addEventListener('input', majTotalPrimes);

<?php foreach ($primes ?? [] as $p): ?>
ajouterPrimeLigne(<?= json_encode($p['libelle']) ?>, <?= (float)$p['montant'] ?>);
<?php endforeach; ?>
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
