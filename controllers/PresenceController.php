<?php
require_once __DIR__ . '/../models/PresenceModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';

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
        require __DIR__ . '/../views/presences/index.php';
    }

    public function checkin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employeeId = $_SESSION['employee_id'] ?? null;
            if (!$employeeId) {
                $_SESSION['error'] = 'Aucun employé associé à ce compte';
                header('Location: ' . APP_URL . '/presences');
                exit;
            }

            $result = $this->presenceModel->checkIn($employeeId);
            $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
        }
        header('Location: ' . APP_URL . '/presences');
        exit;
    }

    public function checkout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employeeId = $_SESSION['employee_id'] ?? null;
            if (!$employeeId) {
                $_SESSION['error'] = 'Aucun employé associé à ce compte';
                header('Location: ' . APP_URL . '/presences');
                exit;
            }

            $result = $this->presenceModel->checkOut($employeeId);
            $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
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
}
