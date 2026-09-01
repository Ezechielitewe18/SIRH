<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';
require_once __DIR__ . '/../models/LogModel.php';

class UtilisateurController {
    private $userModel;
    private $employeeModel;
    private $logModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->employeeModel = new EmployeeModel();
        $this->logModel = new LogModel();
    }

    public function index() {
        $users = $this->userModel->findAllWithEmployee();
        require __DIR__ . '/../views/utilisateurs/index.php';
    }

    public function create() {
        $employesSansCompte = $this->getEmployeesWithoutAccount();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom_complet'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'employe';
            $idEmploye = $_POST['id_employe'] ?? null;

            $errors = [];
            if (empty($nom)) $errors[] = 'Le nom complet est requis';
            if (empty($email)) $errors[] = 'L\'email est requis';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
            if (strlen($password) < 6) $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
            if ($this->userModel->existsEmail($email)) $errors[] = 'Cet email est déjà utilisé';

            if (empty($errors)) {
                $id = $this->userModel->register([
                    'nom_complet' => $nom,
                    'email' => $email,
                    'mot_de_passe' => $password,
                    'role' => $role
                ]);

                // Associer à l'employé si fourni
                if ($idEmploye) {
                    $sql = "UPDATE employes SET id_utilisateur = :uid WHERE id_employe = :eid";
                    $stmt = (Database::getInstance()->getConnection())->prepare($sql);
                    $stmt->execute(['uid' => $id, 'eid' => $idEmploye]);
                }

                $this->logModel->log('Création utilisateur', "Compte créé pour $nom ($email)", 'utilisateurs');
                $_SESSION['success'] = 'Utilisateur créé avec succès';
                header('Location: ' . APP_URL . '/utilisateurs');
                exit;
            }

            $_SESSION['errors'] = $errors;
        }

        require __DIR__ . '/../views/utilisateurs/create.php';
    }

    public function edit($id) {
        $user = $this->userModel->findById($id);
        if (!$user) {
            header('Location: ' . APP_URL . '/utilisateurs');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ne jamais désactiver son propre compte
            if ($id == $_SESSION['user_id'] && ($_POST['statut'] ?? '') === 'inactif') {
                $_SESSION['error'] = 'Vous ne pouvez pas désactiver votre propre compte';
                header('Location: ' . APP_URL . '/utilisateurs');
                exit;
            }

            $data = [
                'nom_complet' => trim($_POST['nom_complet']),
                'role' => $_POST['role'],
                'statut' => $_POST['statut']
            ];

            // Nouveau mot de passe si fourni
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    $_SESSION['errors'] = ['Le mot de passe doit contenir au moins 6 caractères'];
                    $employesSansCompte = $this->getEmployeesWithoutAccount();
                    require __DIR__ . '/../views/utilisateurs/edit.php';
                    return;
                }
                $this->userModel->updatePassword($id, $_POST['password']);
                $data['mot_de_passe'] = $this->userModel->findById($id)['mot_de_passe'];
            }

            $this->userModel->update($id, $data);
            $this->logModel->log('Modification utilisateur', "Compte mis à jour : {$user['email']}", 'utilisateurs');
            $_SESSION['success'] = 'Utilisateur mis à jour';
            header('Location: ' . APP_URL . '/utilisateurs');
            exit;
        }

        $employesSansCompte = $this->getEmployeesWithoutAccount();
        require __DIR__ . '/../views/utilisateurs/edit.php';
    }

    public function toggle($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($id == $_SESSION['user_id']) {
                $_SESSION['error'] = 'Vous ne pouvez pas désactiver votre propre compte';
                header('Location: ' . APP_URL . '/utilisateurs');
                exit;
            }
            $this->userModel->toggleStatut($id);
            $this->logModel->log('Changement statut utilisateur', "Statut modifié pour l'utilisateur #$id", 'utilisateurs');
            $_SESSION['success'] = 'Statut de l\'utilisateur modifié';
        }
        header('Location: ' . APP_URL . '/utilisateurs');
        exit;
    }

    public function resetPassword($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['new_password'] ?? '';
            if (strlen($newPassword) < 6) {
                $_SESSION['errors'] = ['Le mot de passe doit contenir au moins 6 caractères'];
                header('Location: ' . APP_URL . '/utilisateurs');
                exit;
            }
            $this->userModel->updatePassword($id, $newPassword);
            $this->logModel->log('Réinitialisation mot de passe', "Mot de passe réinitialisé pour l'utilisateur #$id", 'utilisateurs');
            $_SESSION['success'] = 'Mot de passe réinitialisé';
        }
        header('Location: ' . APP_URL . '/utilisateurs');
        exit;
    }

    private function getEmployeesWithoutAccount() {
        $sql = "SELECT e.id_employe, e.nom, e.prenom, e.matricule
                FROM employes e
                LEFT JOIN utilisateurs u ON e.id_utilisateur = u.id_utilisateur
                WHERE u.id_utilisateur IS NULL
                ORDER BY e.nom ASC";
        $stmt = (Database::getInstance()->getConnection())->query($sql);
        return $stmt->fetchAll();
    }
}
