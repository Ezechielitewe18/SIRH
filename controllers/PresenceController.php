<?php
require_once __DIR__ . '/../models/PresenceModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../models/QrcodeModel.php';
require_once __DIR__ . '/../models/LogModel.php';

class PresenceController {
    private $presenceModel;
    private $employeeModel;
    private $qrcodeModel;

    public function __construct() {
        $this->presenceModel = new PresenceModel();
        $this->employeeModel = new EmployeeModel();
        $this->qrcodeModel = new QrcodeModel();
    }

    public function index() {
        $this->presenceModel->marquerAbsentsAvantLimite();

        $role = $_SESSION['user_role'] ?? '';
        $date = $_GET['date'] ?? date('Y-m-d');

        // Espace salarie : uniquement son propre historique. Le registre
        // collectif des presences reste reserve aux managers.
        if ($role === 'employe') {
            $employeeId = $_SESSION['employee_id'] ?? null;
            $mois = (int) ($_GET['mois'] ?? date('m'));
            $annee = (int) ($_GET['annee'] ?? date('Y'));

            $mesPresences = $employeeId
                ? $this->presenceModel->findByMonthEmploye($employeeId, $mois, $annee)
                : [];
            $stats = $employeeId
                ? $this->presenceModel->statsMoisPourEmploye($employeeId, $mois, $annee)
                : ['jours_pointes' => 0, 'presents' => 0, 'retards' => 0, 'absents' => 0,
                   'justifies' => 0, 'minutes_retard' => 0, 'heures_travail' => 0];
            $maPresence = $employeeId ? $this->presenceModel->etatJour($employeeId) : null;
            $employe = $employeeId ? $this->employeeModel->findByIdWithService($employeeId) : null;

            require __DIR__ . '/../views/presences/mes_presences.php';
            return;
        }

        $presences = $this->presenceModel->findAllWithEmployee($date);

        $isManager = in_array($role, ['admin', 'rh', 'directeur']);
        $canScan = in_array($role, ['admin', 'rh']);
        $enAttente = $isManager ? $this->presenceModel->trouverEnAttente() : [];
        $absents = $isManager ? $this->presenceModel->findAbsentsDuJour($date) : [];
        $scans = $canScan ? $this->presenceModel->findScansDuJour(date('Y-m-d')) : [];

        $maPresence = null;
        if (!empty($_SESSION['employee_id'])) {
            $maPresence = $this->presenceModel->etatJour($_SESSION['employee_id']);
        }

        require __DIR__ . '/../views/presences/index.php';
    }

    public function valider($id) {
        $this->requireManager();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->presenceModel->valider($id, $_SESSION['user_id'])) {
                $_SESSION['success'] = 'Déclaration validée';
            } else {
                $_SESSION['error'] = 'Échec de la validation';
            }
        }
        header('Location: ' . APP_URL . '/presences');
        exit;
    }

    public function rejeter() {
        $this->requireManager();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $justification = trim($_POST['justification'] ?? '');
            if ($this->presenceModel->rejeter($id, $_SESSION['user_id'], $justification)) {
                $_SESSION['success'] = 'Déclaration rejetée';
            } else {
                $_SESSION['error'] = 'Échec du rejet';
            }
        }
        header('Location: ' . APP_URL . '/presences');
        exit;
    }

    public function regulariser() {
        $this->requireManager();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $statut = in_array($_POST['statut'] ?? '', ['justifie', 'present', 'retard']) ? $_POST['statut'] : 'justifie';
            if ($this->presenceModel->regulariser($id, $_SESSION['user_id'], $statut)) {
                $_SESSION['success'] = 'Absence régularisée';
            } else {
                $_SESSION['error'] = 'Échec de la régularisation';
            }
        }
        header('Location: ' . APP_URL . '/presences');
        exit;
    }

    public function todayStatus() {
        $employeeId = $_SESSION['employee_id'] ?? null;
        if (!$employeeId) {
            echo json_encode(['checked_in' => false]);
            return;
        }

        $presence = $this->presenceModel->findTodayByEmployee($employeeId);
        $checkedIn = !empty($presence);
        $checkedOut = $checkedIn && !empty($presence[0]['heure_depart']);

        echo json_encode([
            'checked_in' => $checkedIn,
            'checked_out' => $checkedOut,
            'presence' => $presence[0] ?? null
        ]);
    }

    /**
     * Page "Mon QR de presence" : le QR tourne toutes les QR_PERIODE secondes.
     * L'arrivee ne peut etre pointee que par scan a la reception.
     */
    public function qr() {
        $employeeId = $_SESSION['employee_id'] ?? null;
        if (!$employeeId) {
            $_SESSION['error'] = 'Aucun employé associé à ce compte : impossible d\'afficher un QR de présence.';
            header('Location: ' . APP_URL . '/presences');
            exit;
        }

        $employe = $this->employeeModel->findById($employeeId);
        if (!$employe) {
            $_SESSION['error'] = 'Employé introuvable.';
            header('Location: ' . APP_URL . '/presences');
            exit;
        }

        $this->presenceModel->marquerAbsentsAvantLimite();

        $today = $this->presenceModel->findTodayByEmployee($employeeId);
        $maPresence = $today[0] ?? null;

        require __DIR__ . '/../views/presences/qr.php';
    }

    /** Jeton QR dynamique au format JSON (rafraichi par l'ecran employe). */
    public function jetonQr() {
        header('Content-Type: application/json; charset=utf-8');

        $employeeId = $_SESSION['employee_id'] ?? null;
        if (!$employeeId) {
            echo json_encode(['success' => false, 'message' => 'Aucun employé associé à ce compte.']);
            return;
        }

        $jeton = $this->qrcodeModel->genererJeton($employeeId);
        $jeton['success'] = true;
        $jeton['periode_duree'] = QR_PERIODE;
        $jeton['code_court'] = $this->qrcodeModel->codeCourt($employeeId, $jeton['periode']);
        $employe = $this->employeeModel->findById($employeeId);
        $jeton['matricule'] = $employe['matricule'] ?? '';
        echo json_encode($jeton);
    }

    /**
     * Etat du jour de l'employe connecte, en JSON. Permet a l'ecran QR de
     * confirmer en direct que le scan de la reception a bien ete pris en compte.
     */
    public function qrStatut() {
        header('Content-Type: application/json; charset=utf-8');

        $employeeId = $_SESSION['employee_id'] ?? null;
        if (!$employeeId) {
            echo json_encode(['success' => false, 'message' => 'Session terminee.']);
            return;
        }

        $presence = $this->presenceModel->etatJour($employeeId);
        $etat = 'attente';
        if ($presence) {
            if ($presence['statut'] === 'absent') {
                $etat = 'absent';
            } elseif (empty($presence['heure_arrivee'])) {
                $etat = 'absent';
            } elseif (empty($presence['heure_depart'])) {
                $etat = 'arrivee';
            } else {
                $etat = 'termine';
            }
        }

        echo json_encode([
            'success' => true,
            'serveur_now' => (int) round(microtime(true) * 1000),
            'etat' => $etat,
            'statut' => $presence['statut'] ?? null,
            'validation' => $presence['validation'] ?? null,
            'heure_arrivee' => !empty($presence['heure_arrivee']) ? substr($presence['heure_arrivee'], 0, 5) : null,
            'heure_depart' => !empty($presence['heure_depart']) ? substr($presence['heure_depart'], 0, 5) : null,
            'retard' => (int) ($presence['retard'] ?? 0),
            'action' => ($presence && !empty($presence['heure_depart'])) ? 'depart' : 'arrivee',
        ]);
    }

    /** Ecran reception : scan des QR a la webcam ou au lecteur. */
    public function scan() {
        $this->presenceModel->marquerAbsentsAvantLimite();
        $date = $_GET['date'] ?? date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }
        $scans = $this->presenceModel->findScansDuJour($date);

        require __DIR__ . '/../views/presences/scan.php';
    }

/** Traitement d'un QR scanne (POST JSON ou formulaire). */
public function scanValider() {
    header('Content-Type: application/json; charset=utf-8');

    if (!csrf_verify()) {
        echo json_encode(['success' => false, 'message' => 'Session expirée, rechargez la page.']);
        return;
    }

    $code = trim((string) ($_POST['code'] ?? ''));
    $matricule = trim((string) ($_POST['matricule'] ?? ''));
    $codeCourt = trim((string) ($_POST['code_court'] ?? ''));
    $source = 'qr';

    if ($code !== '') {
        $validation = $this->qrcodeModel->validerJeton($code);
    } elseif ($matricule !== '' || $codeCourt !== '') {
        /* Pointage sans camera : matricule + code de secours a 6 chiffres. */
        $source = 'code';
        if ($this->tropDEssaisCode()) {
            echo json_encode(['success' => false, 'message' => 'Trop d\'essais de code. Patientez une minute avant de réessayer.']);
            return;
        }
        $validation = $this->qrcodeModel->validerCodeCourt($matricule, $codeCourt);
        if ($validation['success']) {
            $_SESSION['code_essais'] = [];
        } else {
            $this->enregistrerEssaiCode();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucun code reçu.']);
        return;
    }

    if (!$validation['success']) {
        echo json_encode($validation);
        return;
    }

    $employe = $validation['employe'];

    // Anti-double scan : la camera peut lire deux fois la meme image, ou le
    // lecteur USB envoyer deux fois le meme code. On refuse tout nouveau
    // pointage moins de 15 secondes apres le precedent pour ce meme employe.
    $recent = $this->presenceModel->pointageRecent($employe['id_employe'], 15);
    if ($recent) {
        echo json_encode([
            'success' => false,
            'action' => null,
            'message' => 'Pointage deja pris en compte il y a quelques secondes. Patientez avant un nouveau scan.',
            'employe' => [
                'id_employe' => $employe['id_employe'],
                'nom' => $employe['nom'],
                'prenom' => $employe['prenom'],
                'matricule' => $employe['matricule'],
            ],
        ]);
        return;
    }

    $resultat = $this->presenceModel->scanPointage($employe['id_employe'], $_SESSION['user_id']);

    if ($resultat['success']) {
        $log = new LogModel();
        $prefixe = $source === 'code' ? 'Pointage code' : 'Scan QR';
        $log->log(
            $prefixe . ($resultat['action'] === 'depart' ? ' depart' : ' arrivee'),
            $employe['prenom'] . ' ' . $employe['nom'] . ' (' . $employe['matricule'] . ')',
            'presences'
        );
    }

    $resultat['employe'] = [
        'id_employe' => $employe['id_employe'],
        'nom' => $employe['nom'],
        'prenom' => $employe['prenom'],
        'matricule' => $employe['matricule'],
    ];
    $resultat['source'] = $source;
    echo json_encode($resultat);
}

/**
 * Limite les essais de code de secours : 10 echecs par minute et par session.
 * Empeche de deviner un code a 6 chiffres en boucle.
 */
private function tropDEssaisCode() {
    $limite = time() - 60;
    $essais = $_SESSION['code_essais'] ?? [];
    $essais = array_values(array_filter($essais, function ($t) use ($limite) {
        return (int) $t > $limite;
    }));
    $_SESSION['code_essais'] = $essais;
    return count($essais) >= 10;
}

private function enregistrerEssaiCode() {
    $essais = $_SESSION['code_essais'] ?? [];
    $essais[] = time();
    $_SESSION['code_essais'] = $essais;
}

private function requireManager() {
        if (!in_array($_SESSION['user_role'] ?? '', ['admin', 'rh', 'directeur'])) {
            $_SESSION['error'] = 'Action réservée aux gestionnaires (RH / Direction)';
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
    }
}