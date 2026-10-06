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
        $mois = $_POST['mois'] ?? date('m');
        $annee = $_POST['annee'] ?? date('Y');

        
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
        $mois = $_POST['mois'] ?? date('m');
        $annee = $_POST['annee'] ?? date('Y');

        $result = $this->paieModel->genererBulletin($id, $mois, $annee);
        $_SESSION[ $result['success'] ? 'success' : 'error' ] = $result['message'];
        header('Location: ' . APP_URL . '/paie?mois=' . $mois . '&annee=' . $annee);
        exit;
    }

    public function valider($id) {
        $this->paieModel->validerBulletin($id);
        $this->informerEmploye($id, 'valide');
        $_SESSION['success'] = 'Bulletin validé';
        header('Location: ' . APP_URL . '/paie');
        exit;
    }

    public function payer($id) {
        $this->paieModel->payerBulletin($id);
        $this->informerEmploye($id, 'paye');
        $_SESSION['success'] = 'Bulletin marqué comme payé';
        header('Location: ' . APP_URL . '/paie');
        exit;
    }

    /**
     * Previent le salarie que son bulletin est disponible ou paye.
     * Notification dans l'application (visible sur son telephone) + e-mail.
     */
    private function informerEmploye($idBulletin, $statut) {
        $bulletin = $this->paieModel->findById($idBulletin);
        if (!$bulletin) {
            return;
        }
        $employe = $this->employeeModel->findById($bulletin['id_employe']);
        if (!$employe || empty($employe['id_utilisateur'])) {
            return;
        }

        $periode = str_pad((string)$bulletin['mois'], 2, '0', STR_PAD_LEFT) . '/' . $bulletin['annee'];
        $net = number_format((float)$bulletin['total_net'], 2, ',', ' ') . ' FC';

        if ($statut === 'paye') {
            $titre = 'Votre salaire de ' . $periode . ' a été payé';
            $message = "Votre bulletin de paie de $periode est marque comme paye.\n"
                . "Net a payer : $net\n"
                . "Vous pouvez consulter le detail de votre bulletin depuis votre espace.";
        } else {
            $titre = 'Votre bulletin de paie ' . $periode . ' est disponible';
            $message = "Votre bulletin de paie de $periode a ete valide par le service RH.\n"
                . "Net a payer : $net\n"
                . "Vous pouvez consulter le detail depuis votre espace.";
        }

        $notif = new NotificationModel();
        $notif->add(
            $employe['id_utilisateur'],
            $titre,
            $message,
            'paie',
            APP_URL . '/paie/mes-bulletins'
        );

        if (!empty($employe['email'])) {
            $notif->sendEmail(
                $employe['email'],
                $titre . ' - GLOBIT SAS',
                $notif->emailTemplate($titre, $message, APP_URL . '/paie/mes-bulletins')
            );
        }
    }

    /** Bulletins du salarie connecte (jamais ceux des autres). */
    public function mesBulletins() {
        $employe = $this->employeDuCompte();
        if (!$employe) {
            return;
        }
        $mesBulletins = $this->paieModel->findByEmployee($employe['id_employe']);
        require __DIR__ . '/../views/paie/mes_bulletins.php';
    }

    public function monBulletin($id) {
        $employe = $this->employeDuCompte();
        if (!$employe) {
            return;
        }
        $bulletin = $this->paieModel->findById($id);
        if (!$bulletin || (int)$bulletin['id_employe'] !== (int)$employe['id_employe']) {
            $_SESSION['error'] = 'Bulletin introuvable';
            header('Location: ' . APP_URL . '/paie/mes-bulletins');
            exit;
        }
        $employeFiche = $this->employeeModel->findByIdWithService($bulletin['id_employe']);
        require __DIR__ . '/../views/paie/ma_fiche.php';
    }

    private function employeDuCompte() {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        $employe = $this->employeeModel->findByUserId($_SESSION['user_id']);
        if (!$employe) {
            $_SESSION['error'] = "Aucun profil employe n'est associe a ce compte.";
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
        return $employe;
    }

    public function archives() {
        $archives = $this->paieModel->getArchiveMois();
        require __DIR__ . '/../views/paie/archives.php';
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
