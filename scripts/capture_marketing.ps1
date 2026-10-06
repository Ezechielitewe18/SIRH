<#
.SYNOPSIS
    Regenere les captures d'ecran de marketing/captures/ (11 images) et les
    vignettes utilisees par la page de presentation demo.php (assets/demo, 7 images).

.DESCRIPTION
    Lance une instance PHP locale pointant sur la base FICTIVE sirh_demo
    (jeu marketing, jamais la base reelle), ouvre une session de depart via
    scripts/capture_router.php, puis photographie chaque ecran avec Edge.

    Aucun fichier de l'application n'est modifie : config/database.php reste
    intact (la bascule de base est faite par variable d'environnement).

.EXAMPLE
    powershell -NoProfile -ExecutionPolicy Bypass -File scripts\capture_marketing.ps1
#>

$root  = 'C:\xampp\htdocs\SIRH'
$php   = 'C:\xampp\php\php.exe'
$edge  = 'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe'
$port  = 8123
$base  = "http://127.0.0.1:$port"
$dirMk = Join-Path $root 'marketing\captures'
$dirDm = Join-Path $root 'assets\demo'
$prof  = Join-Path $env:TEMP 'opencode\edgeCaptureProfile'

# nom / chemin / role / largeur / hauteur / dossier de sortie
# (dimensions reprises des images actuelles)
$captures = @(
    @{ n = 'login';             p = '/login';                role = 'admin';   w = 1440; h = 900;  dir = $dirMk },
    @{ n = 'dashboard';         p = '/dashboard';            role = 'admin';   w = 1440; h = 900;  dir = $dirMk },
    @{ n = 'employees';         p = '/employees';            role = 'admin';   w = 1440; h = 1000; dir = $dirMk },
    @{ n = 'presences';         p = '/presences';            role = 'admin';   w = 1440; h = 1000; dir = $dirMk },
    @{ n = 'conges';            p = '/conges';               role = 'admin';   w = 1440; h = 860;  dir = $dirMk },
    @{ n = 'paie';              p = '/paie';                 role = 'admin';   w = 1440; h = 860;  dir = $dirMk },
    @{ n = 'absentisme';        p = '/rapports/absentisme';  role = 'admin';   w = 1440; h = 860;  dir = $dirMk },
    @{ n = 'scan';              p = '/presences/scan';       role = 'admin';   w = 1440; h = 900;  dir = $dirMk },
    @{ n = 'mobile';            p = '/mobile.php';           role = 'admin';   w = 430;  h = 880;  dir = $dirMk },
    @{ n = 'demo';              p = '/demo.php';             role = 'admin';   w = 1440; h = 900;  dir = $dirMk },
    @{ n = 'qr_employe';        p = '/presences/qr';         role = 'employe'; w = 430;  h = 880;  dir = $dirMk },

    # vignettes de la page de presentation demo.php (1920x1080)
    @{ n = 'login';             p = '/login';                role = 'admin';   w = 1920; h = 1080; dir = $dirDm },
    @{ n = 'dashboard';         p = '/dashboard';            role = 'admin';   w = 1920; h = 1080; dir = $dirDm },
    @{ n = 'employees';         p = '/employees';            role = 'admin';   w = 1920; h = 1080; dir = $dirDm },
    @{ n = 'presences';         p = '/presences';            role = 'admin';   w = 1920; h = 1080; dir = $dirDm },
    @{ n = 'conges';            p = '/conges';               role = 'admin';   w = 1920; h = 1080; dir = $dirDm },
    @{ n = 'paie';              p = '/paie';                 role = 'admin';   w = 1920; h = 1080; dir = $dirDm },
    @{ n = 'journal';           p = '/journal';              role = 'admin';   w = 1920; h = 1080; dir = $dirDm }
)

$env:SIRH_DB         = 'sirh_demo'
$env:CAPTURE_LOGIN   = '1'

function Wait-Server($url) {
    for ($i = 0; $i -lt 40; $i++) {
        try {
            $r = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 5
            if ($r.StatusCode -eq 200) { return $true }
        } catch { }
        Start-Sleep -Milliseconds 400
    }
    return $false
}

$roles = @($captures | ForEach-Object { $_.role } | Sort-Object -Unique)
$done = 0
$failed = @()

foreach ($role in $roles) {
    $env:CAPTURE_USER_ROLE = $role

    $srv = Start-Process -FilePath $php `
        -ArgumentList @('-S', "127.0.0.1:$port", '-t', $root, 'scripts\capture_router.php') `
        -WorkingDirectory $root -PassThru -WindowStyle Hidden

    if (-not (Wait-Server "$base/login")) {
        Write-Output "ERREUR : serveur PHP indisponible (role=$role)"
        $failed += 'serveur'
        Stop-Process -Id $srv.Id -Force -ErrorAction SilentlyContinue
        continue
    }
    Write-Output "serveur pret (role=$role, base=sirh_demo)"

    foreach ($c in ($captures | Where-Object { $_.role -eq $role })) {
        $dest = Join-Path $c.dir ($c.n + '.png')
        $args = @(
            '--headless=new', '--disable-gpu', '--hide-scrollbars',
            "--user-data-dir=$prof", '--virtual-time-budget=9000',
            "--window-size=$($c.w),$($c.h)",
            "--screenshot=$dest",
            ($base + $c.p)
        )
        $p = Start-Process -FilePath $edge -ArgumentList $args -Wait -PassThru -NoNewWindow
        $ok = (Test-Path $dest) -and ((Get-Item $dest).LastWriteTime -gt (Get-Date).AddMinutes(-2))
        $destTxt = Split-Path $c.dir -Leaf
        if ($ok) { $done++; Write-Output ("  [{0}] {1}.png  {2}x{3}  OK" -f $destTxt, $c.n, $c.w, $c.h) }
        else { $failed += "$($c.n)@$($c.dir)"; Write-Output ("  [{0}] {1}.png  ECHEC" -f $destTxt, $c.n) }
    }

    Stop-Process -Id $srv.Id -Force -ErrorAction SilentlyContinue
    Start-Sleep -Milliseconds 800
}

Write-Output ("captures regenerees : " + $done + "/" + $captures.Count)
if ($failed.Count -gt 0) { Write-Output ("echecs : " + ($failed -join ', ')) }
