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

        $date = $_GET['date'] ?? date('Y-m-d');
        $presences = $this->presenceModel->findAllWithEmployee($date);

        $role = $_SESSION['user_role'] ?? '';
        $isManager = in_array($role, ['admin', 'rh', 'directeur']);
        $canScan = in_array($role, ['admin', 'rh']);
        $enAttente = $isManager ? $this->presenceModel->trouverEnAttente() : [];
        $absents = $isManager ? $this->presenceModel->findAbsentsDuJour($date) : [];

        $maPresence = null;
        if (!empty($_SESSION['employee_id'])) {
            $aujourdhui = $this->presenceModel->findTodayByEmployee($_SESSION['employee_id']);
            $maPresence = $aujourdhui[0] ?? null;
        }
require __DIR__ . '/../views/presences/index.php';
    }

    public function declarer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employeeId = $_SESSION['employee_id'] ?? null;
            if (!$employeeId) {
                $_SESSION['error'] = 'Aucun employé associé à ce compte';
            } else {
                $result = $this->presenceModel->declarer($employeeId);
                if (isset($result['success']) && $result['success']) {
                    
                    $employeeModel = new EmployeeModel();
                    $employee = $employeeModel->findById($employeeId);
                    $nom = $employee ? ($employee['prenom'] . ' ' . $employee['nom']) : 'Un employé';
                    $notificationModel = new NotificationModel();
                    $notificationModel->notifyAllByRole(
                        ['admin', 'rh', 'directeur'],
                        'Déclaration de présence à valider',
                        "$nom a déclaré son arrivée. Une validation est requise.",
                        'presence',
                        APP_URL . '/presences'
                    );
                }
                $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
            }
        }
        header('Location: ' . APP_URL . '/presences');
        exit;
    }

    
    public function checkin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employeeId = $_SESSION['employee_id'] ?? null;
            if (!$employeeId) {
                $_SESSION['error'] = 'Aucun employé associé à ce compte';
            } else {
                $result = $this->presenceModel->declarer($employeeId);
                $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
            }
        }
        header('Location: ' . APP_URL . '/presences');
        exit;
    }

    public function checkout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employeeId = $_SESSION['employee_id'] ?? null;
            if (!$employeeId) {
                $_SESSION['error'] = 'Aucun employé associé à ce compte';
            } else {
                $result = $this->presenceModel->checkOut($employeeId);
                $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
            }
        }
        header('Location: ' . APP_URL . '/presences');
        exit;
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
        echo json_encode($jeton);
    }

    /** Ecran reception : scan des QR a la webcam ou au lecteur. */
public function scan() {
    $this->presenceModel->marquerAbsentsAvantLimite();
    $date = $_GET['date'] ?? date('Y-m-d');
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

    $code = $_POST['code'] ?? '';
    if (trim($code) === '') {
        echo json_encode(['success' => false, 'message' => 'Aucun code reçu.']);
        return;
    }

    $validation = $this->qrcodeModel->validerJeton($code);
    if (!$validation['success']) {
        echo json_encode($validation);
        return;
    }

    $employe = $validation['employe'];
    $resultat = $this->presenceModel->scanPointage($employe['id_employe'], $_SESSION['user_id']);

    if ($resultat['success']) {
        $log = new LogModel();
        $log->log(
            $resultat['action'] === 'depart' ? 'Scan QR depart' : 'Scan QR arrivee',
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
    echo json_encode($resultat);
}

private function requireManager() {
        if (!in_array($_SESSION['user_role'] ?? '', ['admin', 'rh', 'directeur'])) {
            $_SESSION['error'] = 'Action réservée aux gestionnaires (RH / Direction)';
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
    }
}