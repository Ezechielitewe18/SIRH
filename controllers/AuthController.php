<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->authenticate($email, $password);

            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id_utilisateur'];
                $_SESSION['user_name'] = $user['nom_complet'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                
                $employeeModel = new EmployeeModel();
                $employee = $employeeModel->findByUserId($user['id_utilisateur']);
                if ($employee) {
                    $_SESSION['employee_id'] = $employee['id_employe'];
                }

                header('Location: ' . APP_URL . '/dashboard');
                exit;
            } else {
                $error = 'Email ou mot de passe incorrect';
                require __DIR__ . '/../views/auth/login.php';
            }
        } else {
            require __DIR__ . '/../views/auth/login.php';
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom_complet'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $role = in_array($_POST['role'] ?? 'employe', ['employe', 'rh']) ? $_POST['role'] : 'employe';

            $errors = [];

            if (empty($nom)) $errors[] = 'Le nom complet est requis';
            if (empty($email)) $errors[] = 'L\'email est requis';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
            if (empty($password)) $errors[] = 'Le mot de passe est requis';
            if (strlen($password) < 6) $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
            if ($password !== $password_confirm) $errors[] = 'Les mots de passe ne correspondent pas';

            
            if (empty($errors)) {
                $existing = $this->userModel->findByEmail($email);
                if ($existing) {
                    $errors[] = 'Cet email est déjà utilisé';
                }
            }

            if (empty($errors)) {
                $userId = $this->userModel->register([
                    'nom_complet' => $nom,
                    'email' => $email,
                    'mot_de_passe' => $password,
                    'role' => $role
                ]);

                session_regenerate_id(true);
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $nom;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $role;

                header('Location: ' . APP_URL . '/dashboard');
                exit;
            }

            require __DIR__ . '/../views/auth/register.php';
        } else {
            require __DIR__ . '/../views/auth/register.php';
        }
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]);
        }
        session_destroy();
        header('Location: ' . APP_URL . '/login');
        exit;
    }

    public function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public function requireRole($roles) {
        $this->checkAuth();
        if (!in_array($_SESSION['user_role'], (array) $roles)) {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
    }
}
