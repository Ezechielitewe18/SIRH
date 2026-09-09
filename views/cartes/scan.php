<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner QR Code - GLOBIT</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="hold-transition" style="background:#f4f6f9;">
<div class="d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="card" style="width:100%;max-width:500px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.1);">
        <div class="card-header text-center bg-primary text-white" style="border-radius:12px 12px 0 0;">
            <h4><i class="fas fa-camera"></i> Scanner un QR Code</h4>
        </div>
        <div class="card-body text-center">
            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); endif; ?>

            <div id="qr-reader" style="width:100%;"></div>

            <hr>
            <h5>Ou saisissez le code manuellement :</h5>
            <form method="GET" action="<?= APP_URL ?>/cartes/pointer" class="mt-2">
                <div class="input-group">
                    <input type="text" class="form-control" name="code" placeholder="Ex: GLOBIT-XXXX" required>
                    <div class="input-group-append">
                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>

            <p class="mt-4 text-center">
                <a href="<?= APP_URL ?>/login" class="btn btn-outline-secondary btn-sm">Retour à l'accueil</a>
            </p>
        </div>
    </div>
</div>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
</script>
</body>
</html>
