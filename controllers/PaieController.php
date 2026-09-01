<?php
require_once __DIR__ . '/../models/PaieModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';

class PaieController {
    private $paieModel;
    private $employeeModel;

    public function __construct() {
        $this->paieModel = new PaieModel();
        $this->employeeModel = new EmployeeModel();
    }

    public function index() {
        $mois = $_GET['mois'] ?? date('m');
        $annee = $_GET['annee'] ?? date('Y');
        $bulletins = $this->paieModel->findAllWithEmployee($mois, $annee);
        $parametres = $this->paieModel->getAllParameters();

        $totalBrut = array_sum(array_column($bulletins, 'total_brut'));
        $totalNet = array_sum(array_column($bulletins, 'total_net'));

        require __DIR__ . '/../views/paie/index.php';
    }

    public function generer() {
        $mois = $_GET['mois'] ?? date('m');
        $annee = $_GET['annee'] ?? date('Y');

        // Générer pour tous les employés actifs
        $employes = $this->employeeModel->find(['statut' => 'actif']);
        $count = 0;
        foreach ($employes as $emp) {
            $result = $this->paieModel->genererBulletin($emp['id_employe'], $mois, $annee);
            if ($result['success']) $count++;
        }

        $_SESSION['success'] = "$count bulletin(s) de paie généré(s) pour $mois/$annee";
        header('Location: ' . APP_URL . '/paie?mois=' . $mois . '&annee=' . $annee);
        exit;
    }

    public function genererUnEmploye($id) {
        $mois = $_GET['mois'] ?? date('m');
        $annee = $_GET['annee'] ?? date('Y');

        $result = $this->paieModel->genererBulletin($id, $mois, $annee);
        $_SESSION[ $result['success'] ? 'success' : 'error' ] = $result['message'];
        header('Location: ' . APP_URL . '/paie?mois=' . $mois . '&annee=' . $annee);
        exit;
    }

    public function valider($id) {
        $this->paieModel->validerBulletin($id);
        $_SESSION['success'] = 'Bulletin validé';
        header('Location: ' . APP_URL . '/paie');
        exit;
    }

    public function payer($id) {
        $this->paieModel->payerBulletin($id);
        $_SESSION['success'] = 'Bulletin marqué comme payé';
        header('Location: ' . APP_URL . '/paie');
        exit;
    }

    public function detail($id) {
        $bulletin = $this->paieModel->findById($id);
        if (!$bulletin) {
            header('Location: ' . APP_URL . '/paie');
            exit;
        }
        $employe = $this->employeeModel->findByIdWithService($bulletin['id_employe']);
        require __DIR__ . '/../views/paie/detail.php';
    }

    public function parametres() {
        $parametres = $this->paieModel->getAllParameters();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST['valeur'] ?? [] as $id => $valeur) {
                $sql = "UPDATE parametres_paie SET valeur = :valeur WHERE id_parametre = :id";
                $stmt = (Database::getInstance()->getConnection())->prepare($sql);
                $stmt->execute(['valeur' => $valeur, 'id' => $id]);
            }
            $_SESSION['success'] = 'Paramètres de paie mis à jour';
            header('Location: ' . APP_URL . '/paie/parametres');
            exit;
        }

        require __DIR__ . '/../views/paie/parametres.php';
    }
}
