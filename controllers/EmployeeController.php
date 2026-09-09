<?php
require_once __DIR__ . '/../models/EmployeeModel.php';
require_once __DIR__ . '/../models/ServiceModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class EmployeeController {
    private $employeeModel;
    private $serviceModel;
    private $userModel;

    public function __construct() {
        $this->employeeModel = new EmployeeModel();
        $this->serviceModel = new ServiceModel();
        $this->userModel = new UserModel();
    }

    public function index() {
        $search = $_GET['search'] ?? '';

        if (!empty($search)) {
            $employees = $this->employeeModel->searchEmployees($search);
        } else {
            $employees = $this->employeeModel->findAllWithService();
        }

        require __DIR__ . '/../views/employees/index.php';
    }

    public function create() {
        $services = $this->serviceModel->findAllOrdered();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateEmployee($_POST);

            if (empty($errors)) {
                $matricule = $this->employeeModel->generateMatricule();
                $prenom = trim($_POST['prenom']);
                $nom = trim($_POST['nom']);
                $email = $this->generateWorkEmail($prenom, $nom);

                $data = [
                    'matricule' => $matricule,
                    'nom' => $nom,
                    'postnom' => trim($_POST['postnom']),
                    'prenom' => $prenom,
                    'sexe' => $_POST['sexe'],
                    'date_naissance' => $_POST['date_naissance'] ?: null,
                    'lieu_naissance' => trim($_POST['lieu_naissance']),
                    'adresse' => trim($_POST['adresse']),
                    'telephone' => trim($_POST['telephone']),
                    'email' => $email,
                    'poste' => trim($_POST['poste']),
                    'date_embauche' => $_POST['date_embauche'],
                    'salaire' => $_POST['salaire'] ?: null,
                    'id_service' => $_POST['id_service'] ?: null,
                    'statut' => 'actif'
                ];

                $employeeId = $this->employeeModel->create($data);

                $defaultPassword = 'Globit@' . date('Y');
                $userId = $this->userModel->register([
                    'nom_complet' => $prenom . ' ' . $nom,
                    'email' => $email,
                    'mot_de_passe' => $defaultPassword,
                    'role' => 'employe'
                ]);

                if ($userId) {
                    $this->employeeModel->update($employeeId, ['id_utilisateur' => $userId]);
                }

                $_SESSION['success'] = 'Employé ajouté avec succès. Matricule: ' . $matricule . ' | Email: ' . $email . ' | Mot de passe: ' . $defaultPassword;
                header('Location: ' . APP_URL . '/employees');
                exit;
            }
        }

        require __DIR__ . '/../views/employees/create.php';
    }

    public function show($id) {
        $employee = $this->employeeModel->findByIdWithService($id);
        if (!$employee) {
            header('Location: ' . APP_URL . '/employees');
            exit;
        }

        require __DIR__ . '/../views/employees/show.php';
    }

    public function edit($id) {
        $employee = $this->employeeModel->findByIdWithService($id);
        if (!$employee) {
            header('Location: ' . APP_URL . '/employees');
            exit;
        }

        $services = $this->serviceModel->findAllOrdered();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateEmployee($_POST, $id);

            if (empty($errors)) {
                $data = [
                    'nom' => trim($_POST['nom']),
                    'postnom' => trim($_POST['postnom']),
                    'prenom' => trim($_POST['prenom']),
                    'sexe' => $_POST['sexe'],
                    'date_naissance' => $_POST['date_naissance'] ?: null,
                    'lieu_naissance' => trim($_POST['lieu_naissance']),
                    'adresse' => trim($_POST['adresse']),
                    'telephone' => trim($_POST['telephone']),
                    'email' => trim($_POST['email']),
                    'poste' => trim($_POST['poste']),
                    'date_embauche' => $_POST['date_embauche'],
                    'salaire' => $_POST['salaire'] ?: null,
                    'id_service' => $_POST['id_service'] ?: null,
                    'statut' => $_POST['statut']
                ];

                $this->employeeModel->update($id, $data);
                $_SESSION['success'] = 'Employé modifié avec succès';
                header('Location: ' . APP_URL . '/employees');
                exit;
            }

            $employee = array_merge($employee, $_POST);
        }

        require __DIR__ . '/../views/employees/edit.php';
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->employeeModel->delete($id);
            $_SESSION['success'] = 'Employé supprimé avec succès';
        }
        header('Location: ' . APP_URL . '/employees');
        exit;
    }

    private function generateWorkEmail($prenom, $nom) {
        $slug = $this->sanitizeSlug($prenom) . '.' . $this->sanitizeSlug($nom);
        $email = $slug . '@globit.com';
        $base = $email;
        $i = 2;
        while ($this->emailExists($email)) {
            $email = $slug . $i . '@globit.com';
            $i++;
        }
        return $email;
    }

    private function sanitizeSlug($str) {
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9]/', '', $str);
        return $str;
    }

    private function emailExists($email) {
        $employee = $this->employeeModel->findByEmail($email);
        $user = $this->userModel->findByEmail($email);
        return $employee || $user;
    }

    private function validateEmployee($data, $id = null) {
        $errors = [];
        if (empty(trim($data['nom'] ?? ''))) $errors[] = 'Le nom est requis';
        if (empty(trim($data['prenom'] ?? ''))) $errors[] = 'Le prénom est requis';
        if (empty($_POST['sexe'])) $errors[] = 'Le sexe est requis';
        if (empty($_POST['date_embauche'])) $errors[] = 'La date d\'embauche est requise';
        return $errors;
    }
}
