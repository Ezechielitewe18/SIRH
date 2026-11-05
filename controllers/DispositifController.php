<?php
require_once __DIR__ . '/../models/DispositifModel.php';
require_once __DIR__ . '/../models/PointageModel.php';
require_once __DIR__ . '/../models/LogModel.php';

/**
 * Administration des appareils de pointage (phase 3).
 *
 * Un appareil declare recoit un jeton UNE SEULE FOIS, a la creation ou apres
 * regeneration. Le serveur ne conserve que l'empreinte SHA-256 du jeton :
 * il est donc impossible de le reafficher plus tard.
 */
class DispositifController {
    private $dispositifModel;
    private $logModel;

    public function __construct() {
        $this->dispositifModel = new DispositifModel();
        $this->logModel = new LogModel();
    }

    public function index() {
        $dispositifs = $this->dispositifModel->lister();
        require __DIR__ . '/../views/dispositifs/index.php';
    }

    public function show($id) {
        $appareil = $this->dispositifModel->findById((int) $id);
        if (!$appareil) {
            $_SESSION['error'] = 'Appareil introuvable.';
            header('Location: ' . APP_URL . '/dispositifs');
            exit;
        }

        $moteur = new PointageModel();
        $evenements = $moteur->evenementsDuDispositif($appareil['id_dispositif'], 30);
        require __DIR__ . '/../views/dispositifs/show.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultat = $this->dispositifModel->creer([
                'code' => $_POST['code'] ?? '',
                'libelle' => $_POST['libelle'] ?? '',
                'type' => $_POST['type'] ?? 'qr',
                'marque' => $_POST['marque'] ?? null,
                'modele' => $_POST['modele'] ?? null,
                'numero_serie' => $_POST['numero_serie'] ?? null,
                'emplacement' => $_POST['emplacement'] ?? null,
                'notes' => $_POST['notes'] ?? null,
            ]);

            if (!$resultat['ok']) {
                $_SESSION['error'] = $resultat['message'];
                require __DIR__ . '/../views/dispositifs/create.php';
                return;
            }

            $this->logModel->log('Creation appareil', $resultat['message'] . ' (appareil #' . $resultat['id_dispositif'] . ')', 'dispositifs');

            $appareil = $this->dispositifModel->findById($resultat['id_dispositif']);
            $tokenAffiche = $resultat['token'];
            $nouveau = true;
            require __DIR__ . '/../views/dispositifs/create.php';
            return;
        }

        require __DIR__ . '/../views/dispositifs/create.php';
    }

    public function revoquer() {
        $id = (int) ($_POST['id_dispositif'] ?? 0);
        $statut = $_POST['statut'] ?? 'hors_service';
        $motif = trim((string) ($_POST['motif'] ?? ''));

        $resultat = $this->dispositifModel->revoquer($id, $statut, $motif !== '' ? $motif : null);

        if ($resultat['ok']) {
            $this->logModel->log('Revocation appareil', 'Appareil #' . $id . ' : ' . $statut, 'dispositifs');
            $_SESSION['success'] = $resultat['message'];
        } else {
            $_SESSION['error'] = $resultat['message'];
        }

        header('Location: ' . APP_URL . '/dispositifs');
        exit;
    }

    public function reactiver() {
        $id = (int) ($_POST['id_dispositif'] ?? 0);
        $resultat = $this->dispositifModel->reactiver($id);

        if ($resultat['ok']) {
            $this->logModel->log('Reactivation appareil', 'Appareil #' . $id, 'dispositifs');
            $_SESSION['success'] = $resultat['message'];
        } else {
            $_SESSION['error'] = $resultat['message'];
        }

        header('Location: ' . APP_URL . '/dispositifs');
        exit;
    }

    public function regenerer() {
        $id = (int) ($_POST['id_dispositif'] ?? 0);
        $resultat = $this->dispositifModel->regenererJeton($id);

        if (!$resultat['ok']) {
            $_SESSION['error'] = $resultat['message'];
            header('Location: ' . APP_URL . '/dispositifs');
            exit;
        }

        $this->logModel->log('Regeneration jeton', 'Appareil #' . $id, 'dispositifs');
        $appareil = $this->dispositifModel->findById($id);
        $tokenAffiche = $resultat['token'];
        $nouveau = false;
        require __DIR__ . '/../views/dispositifs/create.php';
    }

    public function tester() {
        $id = (int) ($_POST['id_dispositif'] ?? 0);
        $appareil = $this->dispositifModel->findById($id);

        if (!$appareil) {
            $_SESSION['error'] = 'Appareil introuvable.';
        } else {
            $this->dispositifModel->marquerTeste($id);
            $this->logModel->log('Test appareil', 'Appareil #' . $id . ' marque comme teste', 'dispositifs');
            $_SESSION['success'] = 'Test de réception enregistré pour « ' . $appareil['libelle'] . ' ».';
        }

        header('Location: ' . APP_URL . '/dispositifs');
        exit;
    }
}
