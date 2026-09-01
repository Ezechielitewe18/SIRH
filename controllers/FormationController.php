<?php
require_once __DIR__ . '/../models/FormationModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';

class FormationController {
    private $formationModel;
    private $employeeModel;

    public function __construct() {
        $this->formationModel = new FormationModel();
        $this->employeeModel = new EmployeeModel();
    }

    public function index() {
        $formations = $this->formationModel->findAllWithCount();
        require __DIR__ . '/../views/formations/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            if (empty($titre)) {
                $errors[] = 'Le titre de la formation est requis';
                require __DIR__ . '/../views/formations/create.php';
                return;
            }

            $this->formationModel->create([
                'titre' => $titre,
                'description' => trim($_POST['description'] ?? ''),
                'duree' => trim($_POST['duree'] ?? ''),
                'type_formation' => trim($_POST['type_formation'] ?? ''),
                'statut' => $_POST['statut'] ?? 'planifiee',
                'date_debut' => $_POST['date_debut'] ?: null,
                'date_fin' => $_POST['date_fin'] ?: null
            ]);

            $_SESSION['success'] = 'Formation créée avec succès';
            header('Location: ' . APP_URL . '/formations');
            exit;
        }

        require __DIR__ . '/../views/formations/create.php';
    }

    public function show($id) {
        $formation = $this->formationModel->findById($id);
        if (!$formation) {
            header('Location: ' . APP_URL . '/formations');
            exit;
        }
        $inscrits = $this->formationModel->getInscrits($id);
        require __DIR__ . '/../views/formations/show.php';
    }

    public function inscrire($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idEmploye = $_POST['id_employe'] ?? null;
            if ($idEmploye) {
                $result = $this->formationModel->inscrireEmploye($id, $idEmploye);
                $_SESSION[ $result['success'] ? 'success' : 'error' ] = $result['message'];
            }
        }
        header('Location: ' . APP_URL . '/formations/show/' . $id);
        exit;
    }

    public function desinscrire($idFormation, $idEmploye) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->formationModel->desinscrireEmploye($idFormation, $idEmploye);
            $_SESSION['success'] = 'Employé retiré de la formation';
        }
        header('Location: ' . APP_URL . '/formations/show/' . $idFormation);
        exit;
    }

    public function majStatut($idFormation, $idEmploye) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $statut = $_POST['statut'] ?? '';
            $this->formationModel->updateInscriptionStatut($idFormation, $idEmploye, $statut);
            $_SESSION['success'] = 'Statut d\'inscription mis à jour';
        }
        header('Location: ' . APP_URL . '/formations/show/' . $idFormation);
        exit;
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->formationModel->delete($id);
            $_SESSION['success'] = 'Formation supprimée';
        }
        header('Location: ' . APP_URL . '/formations');
        exit;
    }
}
