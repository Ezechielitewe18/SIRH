<?php
require_once __DIR__ . '/../models/CarteModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';
require_once __DIR__ . '/../models/PresenceModel.php';
require_once __DIR__ . '/../models/LogModel.php';

class CarteController {
    private $carteModel;
    private $employeeModel;
    private $presenceModel;
    private $logModel;

    public function __construct() {
        $this->carteModel = new CarteModel();
        $this->employeeModel = new EmployeeModel();
        $this->presenceModel = new PresenceModel();
        $this->logModel = new LogModel();
    }

    public function index() {
        $employees = $this->employeeModel->findAllWithService();
        // Générer les cartes pour les employés qui n'en ont pas
        $cartes = [];
        foreach ($employees as $e) {
            $carte = $this->carteModel->getByEmploye($e['id_employe']);
            $cartes[$e['id_employe']] = [
                'employe' => $e,
                'carte' => $carte
            ];
        }
        require __DIR__ . '/../views/cartes/index.php';
    }

    public function generer($idEmploye) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $carte = $this->carteModel->genererPourEmploye($idEmploye);
            $this->logModel->log('Génération carte', "Carte générée pour l'employé #$idEmploye", 'cartes');
            $_SESSION['success'] = 'Carte QR générée';
        }
        header('Location: ' . APP_URL . '/cartes');
        exit;
    }

    public function toggle($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->carteModel->toggleActif($id);
            $_SESSION['success'] = 'Statut de la carte modifié';
        }
        header('Location: ' . APP_URL . '/cartes');
        exit;
    }

    /**
     * Page de pointage par QR code (ouverture publique à partir d'un lien scanner)
     */
    public function pointer() {
        $code = $_GET['code'] ?? null;
        $employee = null;

        if ($code) {
            $employee = $this->carteModel->findByCode($code);
            if ($employee && $employee['actif']) {
                require __DIR__ . '/../views/cartes/pointer.php';
                return;
            }
        }

        // Si pas de code, afficher la page de scan
        require __DIR__ . '/../views/cartes/scan.php';
    }

    public function checkin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = $_POST['code'] ?? '';
            $employee = $this->carteModel->findByCode($code);
            if ($employee && $employee['actif']) {
                $result = $this->presenceModel->checkIn($employee['id_employe']);
                $this->logModel->log('Pointage QR', "Arrivée par QR - {$employee['matricule']}", 'cartes');
                $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
                header('Location: ' . APP_URL . '/cartes/pointer?code=' . urlencode($code));
                exit;
            }
            $_SESSION['error'] = 'Carte invalide ou désactivée';
            header('Location: ' . APP_URL . '/cartes/pointer');
            exit;
        }
        header('Location: ' . APP_URL . '/cartes/pointer');
        exit;
    }
}
