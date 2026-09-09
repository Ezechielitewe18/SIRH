<?php
require_once __DIR__ . '/../models/PresenceModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class PresenceController {
    private $presenceModel;
    private $employeeModel;

    public function __construct() {
        $this->presenceModel = new PresenceModel();
        $this->employeeModel = new EmployeeModel();
    }

    public function index() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $presences = $this->presenceModel->findAllWithEmployee($date);

        $role = $_SESSION['user_role'] ?? '';
        $isManager = in_array($role, ['admin', 'rh', 'directeur']);
        $enAttente = $isManager ? $this->presenceModel->trouverEnAttente() : [];

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

    private function requireManager() {
        if (!in_array($_SESSION['user_role'] ?? '', ['admin', 'rh', 'directeur'])) {
            $_SESSION['error'] = 'Action réservée aux gestionnaires (RH / Direction)';
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
    }
}