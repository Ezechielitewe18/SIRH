<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pointage - GLOBIT</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="hold-transition" style="background:#f4f6f9;">
<div class="d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="card" style="width:100%;max-width:500px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.1);">
        <div class="card-header text-center bg-primary text-white" style="border-radius:12px 12px 0 0;">
            <h4><i class="fas fa-fingerprint"></i> Pointage par QR Code</h4>
        </div>
        <div class="card-body text-center">
            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); endif; ?>

            <div class="mb-4">
                <img src="<?= APP_URL ?>/public/img/qr-placeholder.svg" onerror="this.style.display='none'" alt="" style="display:none;">
                <i class="fas fa-user-check fa-5x text-success"></i>
            </div>

            <?php if ($employee): ?>
            <h4><?= htmlspecialchars($employee['prenom'] . ' ' . $employee['nom']) ?></h4>
            <p class="text-muted">Matricule: <?= htmlspecialchars($employee['matricule']) ?> | <?= htmlspecialchars($employee['nom_service']) ?></p>
            <div class="mt-3">
                <form method="POST" action="<?= APP_URL ?>/cartes/checkin" class="d-inline">
                    <input type="hidden" name="code" value="<?= htmlspecialchars($_GET['code'] ?? '') ?>">
                    <button class="btn btn-success btn-lg"><i class="fas fa-sign-in-alt"></i> Pointer l'arrivée</button>
                </form>
            </div>
            <p class="mt-3 text-muted small">Heure actuelle: <span id="clock"></span></p>
            <?php else: ?>
            <p class="text-muted">Code QR non reconnu.</p>
            <a href="<?= APP_URL ?>/cartes/pointer" class="btn btn-primary">Scanner à nouveau</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
function updateClock() { document.getElementById('clock').textContent = new Date().toLocaleTimeString('fr-FR'); }
setInterval(updateClock, 1000);
</script>
</body>
</html>
