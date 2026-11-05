<?php
require_once __DIR__ . '/../models/CarteModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';
require_once __DIR__ . '/../models/LogModel.php';

/**
 * Gestion des badges RFID/NFC (phase 2).
 *
 * Regles appliquees ici :
 *  - une carte est attribuee a un seul employe ;
 *  - une carte n'est jamais supprimee : elle change de statut ;
 *  - remplacer une carte conserve l'ancienne avec le statut « remplacee ».
 */
class CarteController {
    private $carteModel;
    private $employeeModel;
    private $logModel;

    public function __construct() {
        $this->carteModel = new CarteModel();
        $this->employeeModel = new EmployeeModel();
        $this->logModel = new LogModel();
    }

    public function index() {
        $cartes = $this->carteModel->listerAvecEmploye();
        $employes = $this->employeeModel->findAllWithService('e.nom ASC');

        require __DIR__ . '/../views/cartes/index.php';
    }

    public function create() {
        $employes = $this->employeeModel->findAllWithService('e.nom ASC');
        $employePreselectionne = isset($_GET['employe']) ? (int) $_GET['employe'] : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultat = $this->carteModel->attribuer(
                (int) ($_POST['id_employe'] ?? 0),
                $_POST['uid_carte'] ?? '',
                $_POST['technologie'] ?? null,
                $_POST['libelle'] ?? null
            );

            if (!$resultat['ok']) {
                $_SESSION['error'] = $resultat['message'];
                require __DIR__ . '/../views/cartes/create.php';
                return;
            }

            $this->logModel->log('Attribution carte', $resultat['message'] . ' (carte #' . $resultat['id_carte'] . ')', 'cartes');
            $_SESSION['success'] = $resultat['message'];
            header('Location: ' . APP_URL . '/cartes');
            exit;
        }

        require __DIR__ . '/../views/cartes/create.php';
    }

    public function desactiver() {
        $id = (int) ($_POST['id_carte'] ?? 0);
        $statut = $_POST['statut'] ?? 'desactivee';
        $notes = trim((string) ($_POST['notes'] ?? ''));

        $resultat = $this->carteModel->desactiver($id, $statut, $notes !== '' ? $notes : null);

        if ($resultat['ok']) {
            $this->logModel->log('Carte ' . $statut, 'Carte #' . $id . ($notes !== '' ? ' : ' . $notes : ''), 'cartes');
            $_SESSION['success'] = $resultat['message'];
        } else {
            $_SESSION['error'] = $resultat['message'];
        }

        header('Location: ' . APP_URL . '/cartes');
        exit;
    }

    public function remplacer() {
        $id = (int) ($_POST['id_carte'] ?? 0);
        $resultat = $this->carteModel->remplacer(
            $id,
            $_POST['uid_nouvelle'] ?? '',
            $_POST['technologie'] ?? null,
            $_POST['libelle'] ?? null
        );

        if ($resultat['ok']) {
            $this->logModel->log('Remplacement carte', 'Carte #' . $id . ' remplacee par #' . $resultat['id_carte'], 'cartes');
            $_SESSION['success'] = $resultat['message'];
        } else {
            $_SESSION['error'] = $resultat['message'];
        }

        header('Location: ' . APP_URL . '/cartes');
        exit;
    }
}
