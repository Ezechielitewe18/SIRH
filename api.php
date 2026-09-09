<?php

define('ROOT_PATH', __DIR__);

error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/models/UserModel.php';
require_once ROOT_PATH . '/models/EmployeeModel.php';
require_once ROOT_PATH . '/models/PresenceModel.php';
require_once ROOT_PATH . '/models/CongeModel.php';
require_once ROOT_PATH . '/models/NotificationModel.php';
require_once ROOT_PATH . '/models/PaieModel.php';

function api_json($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function api_error($message, $code = 400) {
    api_json(['success' => false, 'message' => $message], $code);
}

$allowedOrigin = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
header('Access-Control-Allow-Origin: ' . $allowedOrigin);
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? '';

function createToken($userId) {
    global $db;
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
    $stmt = $db->prepare("INSERT INTO api_tokens (id_utilisateur, token, expires_at) VALUES (:u, :t, :e)");
    $stmt->execute(['u' => $userId, 't' => $token, 'e' => $expires]);
    return ['token' => $token, 'expires_at' => $expires];
}

function authUser() {
    global $db;
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? '';
    if (preg_match('/Bearer\s+(.+)/i', $auth, $m)) {
        $token = trim($m[1]);
    } else {
        return null;
    }

    $stmt = $db->prepare("SELECT t.id_utilisateur, t.expires_at, u.role, u.statut, u.nom_complet, u.email
                          FROM api_tokens t
                          INNER JOIN utilisateurs u ON t.id_utilisateur = u.id_utilisateur
                          WHERE t.token = :t LIMIT 1");
    $stmt->execute(['t' => $token]);
    $row = $stmt->fetch();

    if (!$row) return null;
    if ($row['statut'] !== 'actif') return null;
    if (strtotime($row['expires_at']) < time()) {
        $del = $db->prepare("DELETE FROM api_tokens WHERE token = :t");
        $del->execute(['t' => $token]);
        return null;
    }
    return $row;
}

function requireAuth() {
    $user = authUser();
    if (!$user) {
        api_error('Non authentifié. Token invalide ou expiré.', 401);
    }
    return $user;
}

function requireRole($user, $roles) {
    if (!in_array($user['role'], (array)$roles)) {
        api_error('Accès refusé : rôle insuffisant.', 403);
    }
}

if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') api_error('Méthode non autorisée', 405);
    $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $email = trim($body['email'] ?? '');
    $password = $body['password'] ?? '';

    if (!$email || !$password) api_error('Email et mot de passe requis.');

    $userModel = new UserModel();
    $user = $userModel->authenticate($email, $password);
    if (!$user) api_error('Email ou mot de passe incorrect.', 401);

    $employeeModel = new EmployeeModel();
    $employee = $employeeModel->findByUserId($user['id_utilisateur']);

    $tokenData = createToken($user['id_utilisateur']);

    api_json([
        'success' => true,
        'token' => $tokenData['token'],
        'expires_at' => $tokenData['expires_at'],
        'user' => [
            'id_utilisateur' => $user['id_utilisateur'],
            'nom_complet' => $user['nom_complet'],
            'email' => $user['email'],
            'role' => $user['role'],
            'id_employe' => $employee['id_employe'] ?? null,
            'matricule' => $employee['matricule'] ?? null,
        ]
    ]);
}

if ($action === 'logout') {
    $user = requireAuth();
    global $db;
    $db->prepare("DELETE FROM api_tokens WHERE id_utilisateur = :u")->execute(['u' => $user['id_utilisateur']]);
    api_json(['success' => true, 'message' => 'Déconnecté']);
}

if ($action === 'profil') {
    $user = requireAuth();
    $employeeModel = new EmployeeModel();
    $employee = $employeeModel->findByUserId($user['id_utilisateur']);

    $profil = [
        'id_utilisateur' => $user['id_utilisateur'],
        'nom_complet' => $user['nom_complet'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
    if ($employee) {
        $profil['employe'] = [
            'id_employe' => $employee['id_employe'],
            'matricule' => $employee['matricule'],
            'nom' => $employee['nom'],
            'postnom' => $employee['postnom'],
            'prenom' => $employee['prenom'],
            'sexe' => $employee['sexe'],
            'telephone' => $employee['telephone'],
            'email' => $employee['email'],
            'poste' => $employee['poste'],
            'service' => $employee['nom_service'],
            'date_embauche' => $employee['date_embauche'],
            'salaire' => $employee['salaire'],
            'est_direction' => (bool)$employee['est_direction'],
        ];
    }
    api_json(['success' => true, 'data' => $profil]);
}

if ($action === 'presence_aujourdhui') {
    $user = requireAuth();
    $employeeModel = new EmployeeModel();
    $employee = $employeeModel->findByUserId($user['id_utilisateur']);
    if (!$employee) api_error('Aucun employé associé à ce compte.', 404);

    $pm = new PresenceModel();
    $presence = $pm->findTodayByEmployee($employee['id_employe']);

    api_json([
        'success' => true,
        'data' => [
            'id_employe' => $employee['id_employe'],
            'matricule' => $employee['matricule'],
            'presence' => !empty($presence) ? $presence[0] : null,
        ]
    ]);
}

if ($action === 'presence_declarer') {
    $user = requireAuth();
    $employeeModel = new EmployeeModel();
    $employee = $employeeModel->findByUserId($user['id_utilisateur']);
    if (!$employee) api_error('Aucun employé associé à ce compte.', 404);

    $pm = new PresenceModel();
    $result = $pm->declarer($employee['id_employe']);

    if ($result['success']) {
        $nm = new NotificationModel();
        $nm->notifyAllByRole(['admin', 'rh'], 'Nouvelle déclaration de présence',
            $employee['prenom'] . ' ' . $employee['nom'] . ' a déclaré son arrivée.',
            'systeme', APP_URL . '/presences');
    }
    api_json($result['success'] ? ['success' => true, 'message' => $result['message'], 'data' => $result['presence']] : api_error($result['message'], 409));
}

if ($action === 'presence_depart') {
    $user = requireAuth();
    $employeeModel = new EmployeeModel();
    $employee = $employeeModel->findByUserId($user['id_utilisateur']);
    if (!$employee) api_error('Aucun employé associé à ce compte.', 404);

    $pm = new PresenceModel();
    $result = $pm->checkOut($employee['id_employe']);
    api_json($result['success'] ? ['success' => true, 'message' => $result['message']] : api_error($result['message'], 409));
}

if ($action === 'presences') {
    $user = requireAuth();
    $employeeModel = new EmployeeModel();
    $employee = $employeeModel->findByUserId($user['id_utilisateur']);
    if (!$employee) api_error('Aucun employé associé à ce compte.', 404);

    $pm = new PresenceModel();
    $liste = $pm->findByEmployee($employee['id_employe']);
    $mois = $_GET['mois'] ?? null;
    $annee = $_GET['annee'] ?? null;
    if ($mois && $annee) {
        $liste = array_values(array_filter($liste, function($p) use ($mois, $annee) {
            return date('m', strtotime($p['date_presence'])) == $mois
                && date('Y', strtotime($p['date_presence'])) == $annee;
        }));
    }
    api_json(['success' => true, 'data' => $liste]);
}

if ($action === 'presences_validation') {
    $user = requireAuth();
    requireRole($user, ['admin', 'rh']);
    $pm = new PresenceModel();
    $enAttente = $pm->trouverEnAttente();
    api_json(['success' => true, 'data' => $enAttente]);
}

if ($action === 'presence_valider') {
    $user = requireAuth();
    requireRole($user, ['admin', 'rh']);
    $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $id = $body['id_presence'] ?? $_GET['id'] ?? null;
    if (!$id) api_error('id_presence requis.');

    $pm = new PresenceModel();
    $ok = $pm->valider($id, $user['id_utilisateur']);
    api_json($ok ? ['success' => true, 'message' => 'Présence validée'] : api_error('Présence introuvable', 404));
}

if ($action === 'conges') {
    $user = requireAuth();
    $employeeModel = new EmployeeModel();
    $employee = $employeeModel->findByUserId($user['id_utilisateur']);
    if (!$employee) api_error('Aucun employé associé à ce compte.', 404);

    $cm = new CongeModel();
    $conges = $cm->findByEmployee($employee['id_employe']);
    api_json(['success' => true, 'data' => $conges]);
}

if ($action === 'conge_demander') {
    $user = requireAuth();
    $employeeModel = new EmployeeModel();
    $employee = $employeeModel->findByUserId($user['id_utilisateur']);
    if (!$employee) api_error('Aucun employé associé à ce compte.', 404);

    $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $type = $body['type_conge'] ?? '';
    $dateDebut = $body['date_debut'] ?? '';
    $dateFin = $body['date_fin'] ?? '';
    $motif = $body['motif'] ?? '';

    $typesValides = ['annuel', 'maladie', 'maternite', 'paternite', 'exceptionnel', 'autre'];
    if (!in_array($type, $typesValides)) api_error('Type de congé invalide.');
    if (!$dateDebut || !$dateFin) api_error('Dates de début et fin requises.');
    if ($dateFin < $dateDebut) api_error('La date de fin doit être postérieure à la date de début.');

    $cm = new CongeModel();
    $nbJours = $cm->calculateDays($dateDebut, $dateFin);

    $id = $cm->create([
        'id_employe' => $employee['id_employe'],
        'type_conge' => $type,
        'date_debut' => $dateDebut,
        'date_fin' => $dateFin,
        'nombre_jours' => $nbJours,
        'motif' => $motif,
        'statut' => 'en_attente',
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $nm = new NotificationModel();
    $nm->notifyAllByRole(['admin', 'rh'], 'Nouvelle demande de congé',
        $employee['prenom'] . ' ' . $employee['nom'] . ' demande un congé ' . $type . ' (' . $nbJours . ' jours).',
        'systeme', APP_URL . '/conges');

    api_json(['success' => true, 'message' => 'Demande de congé soumise', 'data' => ['id_conge' => $id, 'nombre_jours' => $nbJours]]);
}

if ($action === 'conge_approuver') {
    $user = requireAuth();
    requireRole($user, ['admin', 'rh']);
    $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $id = $body['id_conge'] ?? $_GET['id'] ?? null;
    if (!$id) api_error('id_conge requis.');

    $cm = new CongeModel();
    $conge = $cm->findById($id);
    if (!$conge) api_error('Congé introuvable', 404);

    $cm->approve($id, $user['id_utilisateur']);

    $employeeModel = new EmployeeModel();
    $employee = $employeeModel->findById($conge['id_employe']);
    if ($employee && $employee['id_utilisateur']) {
        $nm = new NotificationModel();
        $nm->add($employee['id_utilisateur'], 'Congé approuvé',
            'Votre demande de congé du ' . date('d/m/Y', strtotime($conge['date_debut'])) . ' a été approuvée.',
            'conges', APP_URL . '/conges');
    }
    api_json(['success' => true, 'message' => 'Congé approuvé']);
}

if ($action === 'conge_refuser') {
    $user = requireAuth();
    requireRole($user, ['admin', 'rh']);
    $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $id = $body['id_conge'] ?? $_GET['id'] ?? null;
    $motif = $body['motif_refus'] ?? '';
    if (!$id) api_error('id_conge requis.');
    if (!$motif) api_error('Motif de refus requis.');

    $cm = new CongeModel();
    $conge = $cm->findById($id);
    if (!$conge) api_error('Congé introuvable', 404);

    $cm->reject($id, $user['id_utilisateur'], $motif);

    $employeeModel = new EmployeeModel();
    $employee = $employeeModel->findById($conge['id_employe']);
    if ($employee && $employee['id_utilisateur']) {
        $nm = new NotificationModel();
        $nm->add($employee['id_utilisateur'], 'Congé refusé',
            'Votre demande de congé a été refusée. Motif : ' . $motif,
            'conges', APP_URL . '/conges');
    }
    api_json(['success' => true, 'message' => 'Congé refusé']);
}

if ($action === 'notifications') {
    $user = requireAuth();
    $nm = new NotificationModel();
    $notifs = $nm->getByUser($user['id_utilisateur'], 50);
    $nonLues = $nm->getUnreadCount($user['id_utilisateur']);
    api_json(['success' => true, 'data' => ['liste' => $notifs, 'non_lues' => $nonLues]]);
}

if ($action === 'notifications_lues') {
    $user = requireAuth();
    $nm = new NotificationModel();
    $nm->markAllRead($user['id_utilisateur']);
    api_json(['success' => true, 'message' => 'Notifications marquées comme lues']);
}

if ($action === 'bulletins') {
    $user = requireAuth();
    $employeeModel = new EmployeeModel();
    $employee = $employeeModel->findByUserId($user['id_utilisateur']);
    if (!$employee) api_error('Aucun employé associé à ce compte.', 404);

    $pm = new PaieModel();
    $mesBulletins = $pm->findByEmployee($employee['id_employe']);
    api_json(['success' => true, 'data' => $mesBulletins]);
}

api_error('Action inconnue : ' . $action, 404);
