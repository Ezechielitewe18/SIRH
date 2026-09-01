<?php
require_once __DIR__ . '/../models/ServiceModel.php';

class ServiceController {
    private $serviceModel;

    public function __construct() {
        $this->serviceModel = new ServiceModel();
    }

    public function index() {
        $services = $this->serviceModel->getEmployeeCount();
        require __DIR__ . '/../views/services/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom_service'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($nom)) {
                $errors[] = 'Le nom du service est requis';
                require __DIR__ . '/../views/services/create.php';
                return;
            }

            $this->serviceModel->create([
                'nom_service' => $nom,
                'description' => $description
            ]);

            $_SESSION['success'] = 'Service créé avec succès';
            header('Location: ' . APP_URL . '/services');
            exit;
        }

        require __DIR__ . '/../views/services/create.php';
    }

    public function edit($id) {
        $service = $this->serviceModel->findById($id);
        if (!$service) {
            header('Location: ' . APP_URL . '/services');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom_service'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($nom)) {
                $errors[] = 'Le nom du service est requis';
                require __DIR__ . '/../views/services/edit.php';
                return;
            }

            $this->serviceModel->update($id, [
                'nom_service' => $nom,
                'description' => $description
            ]);

            $_SESSION['success'] = 'Service modifié avec succès';
            header('Location: ' . APP_URL . '/services');
            exit;
        }

        require __DIR__ . '/../views/services/edit.php';
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->serviceModel->delete($id);
            $_SESSION['success'] = 'Service supprimé avec succès';
        }
        header('Location: ' . APP_URL . '/services');
        exit;
    }
}
