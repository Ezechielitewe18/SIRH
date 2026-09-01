<?php
/**
 * Point d'entrée principal du SIRH
 * Routeur MVC - Apache rewrite vers ce fichier
 */

// Définir le chemin racine
define('ROOT_PATH', __DIR__);

// Charger la configuration
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';

// Charger le routeur
require_once ROOT_PATH . '/core/Router.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';

// Charger les controlleurs
require_once ROOT_PATH . '/controllers/AuthController.php';
require_once ROOT_PATH . '/controllers/DashboardController.php';
require_once ROOT_PATH . '/controllers/EmployeeController.php';
require_once ROOT_PATH . '/controllers/ServiceController.php';
require_once ROOT_PATH . '/controllers/PresenceController.php';
require_once ROOT_PATH . '/controllers/CongeController.php';
require_once ROOT_PATH . '/controllers/PaieController.php';
require_once ROOT_PATH . '/controllers/FormationController.php';
require_once ROOT_PATH . '/controllers/NotificationController.php';
require_once ROOT_PATH . '/controllers/ExportController.php';
require_once ROOT_PATH . '/controllers/UtilisateurController.php';
require_once ROOT_PATH . '/controllers/LogController.php';
require_once ROOT_PATH . '/controllers/CarteController.php';
require_once ROOT_PATH . '/controllers/RapportController.php';

// Charger les modèles (nécessaires pour le layout)
require_once ROOT_PATH . '/models/NotificationModel.php';
require_once ROOT_PATH . '/models/PaieModel.php';
require_once ROOT_PATH . '/models/FormationModel.php';
require_once ROOT_PATH . '/models/UserModel.php';
require_once ROOT_PATH . '/models/EmployeeModel.php';
require_once ROOT_PATH . '/models/ServiceModel.php';
require_once ROOT_PATH . '/models/PresenceModel.php';
require_once ROOT_PATH . '/models/CongeModel.php';
require_once ROOT_PATH . '/models/LogModel.php';
require_once ROOT_PATH . '/models/CarteModel.php';

// Initialiser le routeur
$router = new Router();
$auth = new AuthController();

// ============================================
// Routes d'authentification (pas d'auth requise)
// ============================================
$router->get('/login', function() use ($auth) { $auth->login(); });
$router->post('/login', function() use ($auth) { $auth->login(); });
$router->get('/register', function() use ($auth) { $auth->register(); });
$router->post('/register', function() use ($auth) { $auth->register(); });
$router->get('/logout', function() use ($auth) { $auth->logout(); });

// ============================================
// Routes protégées
// ============================================

// Dashboard
$router->get('/dashboard', function() use ($auth) {
    $auth->checkAuth();
    $controller = new DashboardController();
    $controller->index();
});

// À propos / Droits d'auteur
$router->get('/about', function() use ($auth) {
    $auth->checkAuth();
    $pageTitle = 'À propos';
    require ROOT_PATH . '/views/about.php';
});

// Employés
$router->get('/employees', function() use ($auth) {
    $auth->checkAuth();
    $controller = new EmployeeController();
    $controller->index();
});

$router->get('/employees/create', function() use ($auth) {
    $auth->checkAuth();
    $controller = new EmployeeController();
    $controller->create();
});

$router->post('/employees/create', function() use ($auth) {
    $auth->checkAuth();
    $controller = new EmployeeController();
    $controller->create();
});

$router->get('/employees/show/{id}', function($id) use ($auth) {
    $auth->checkAuth();
    $controller = new EmployeeController();
    $controller->show($id);
});

$router->get('/employees/edit/{id}', function($id) use ($auth) {
    $auth->checkAuth();
    $controller = new EmployeeController();
    $controller->edit($id);
});

$router->post('/employees/edit/{id}', function($id) use ($auth) {
    $auth->checkAuth();
    $controller = new EmployeeController();
    $controller->edit($id);
});

$router->post('/employees/delete/{id}', function($id) use ($auth) {
    $auth->checkAuth();
    $controller = new EmployeeController();
    $controller->delete($id);
});

// Services
$router->get('/services', function() use ($auth) {
    $auth->checkAuth();
    $controller = new ServiceController();
    $controller->index();
});

$router->get('/services/create', function() use ($auth) {
    $auth->checkAuth();
    $controller = new ServiceController();
    $controller->create();
});

$router->post('/services/create', function() use ($auth) {
    $auth->checkAuth();
    $controller = new ServiceController();
    $controller->create();
});

$router->get('/services/edit/{id}', function($id) use ($auth) {
    $auth->checkAuth();
    $controller = new ServiceController();
    $controller->edit($id);
});

$router->post('/services/edit/{id}', function($id) use ($auth) {
    $auth->checkAuth();
    $controller = new ServiceController();
    $controller->edit($id);
});

$router->post('/services/delete/{id}', function($id) use ($auth) {
    $auth->checkAuth();
    $controller = new ServiceController();
    $controller->delete($id);
});

// Présences
$router->get('/presences', function() use ($auth) {
    $auth->checkAuth();
    $controller = new PresenceController();
    $controller->index();
});

$router->post('/presences/checkin', function() use ($auth) {
    $auth->checkAuth();
    $controller = new PresenceController();
    $controller->checkin();
});

$router->post('/presences/checkout', function() use ($auth) {
    $auth->checkAuth();
    $controller = new PresenceController();
    $controller->checkout();
});

// Congés
$router->get('/conges', function() use ($auth) {
    $auth->checkAuth();
    $controller = new CongeController();
    $controller->index();
});

$router->get('/conges/create', function() use ($auth) {
    $auth->checkAuth();
    $controller = new CongeController();
    $controller->create();
});

$router->post('/conges/create', function() use ($auth) {
    $auth->checkAuth();
    $controller = new CongeController();
    $controller->create();
});

$router->post('/conges/approve/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new CongeController();
    $controller->approve($id);
});

$router->post('/conges/reject/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new CongeController();
    $controller->reject($id);
});

// ============================================
// Paie (admin + rh)
// ============================================
$router->get('/paie', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new PaieController();
    $controller->index();
});

$router->get('/paie/generer', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new PaieController();
    $controller->generer();
});

$router->get('/paie/generer/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new PaieController();
    $controller->genererUnEmploye($id);
});

$router->get('/paie/detail/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new PaieController();
    $controller->detail($id);
});

$router->post('/paie/valider/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new PaieController();
    $controller->valider($id);
});

$router->post('/paie/payer/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new PaieController();
    $controller->payer($id);
});

$router->get('/paie/parametres', function() use ($auth) {
    $auth->requireRole(['admin']);
    $controller = new PaieController();
    $controller->parametres();
});

$router->get('/paie/archives', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new PaieController();
    $controller->archives();
});

$router->post('/paie/parametres', function() use ($auth) {
    $auth->requireRole(['admin']);
    $controller = new PaieController();
    $controller->parametres();
});

// ============================================
// Formations (admin + rh)
// ============================================
$router->get('/formations', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new FormationController();
    $controller->index();
});

$router->get('/formations/create', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new FormationController();
    $controller->create();
});

$router->post('/formations/create', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new FormationController();
    $controller->create();
});

$router->get('/formations/show/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new FormationController();
    $controller->show($id);
});

$router->post('/formations/inscrire/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new FormationController();
    $controller->inscrire($id);
});

$router->post('/formations/desinscrire/{id}/{idEmploye}', function($id, $idEmploye) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new FormationController();
    $controller->desinscrire($id, $idEmploye);
});

$router->post('/formations/statut/{id}/{idEmploye}', function($id, $idEmploye) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new FormationController();
    $controller->majStatut($id, $idEmploye);
});

$router->post('/formations/delete/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new FormationController();
    $controller->delete($id);
});

// ============================================
// Notifications
// ============================================
$router->get('/notifications', function() use ($auth) {
    $auth->checkAuth();
    $controller = new NotificationController();
    $controller->index();
});

$router->get('/notifications/count', function() use ($auth) {
    $auth->checkAuth();
    $controller = new NotificationController();
    $controller->unreadCount();
});

$router->get('/notifications/read/{id}', function($id) use ($auth) {
    $auth->checkAuth();
    $controller = new NotificationController();
    $controller->markRead($id);
});

// ============================================
// Exports (Excel/PDF) - admin + rh
// ============================================
$router->get('/export/employees/excel', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new ExportController();
    $controller->employeesExcel();
});

$router->get('/export/employees/pdf', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new ExportController();
    $controller->employeesPdf();
});

$router->get('/export/presences', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new ExportController();
    $controller->presencesExcel();
});

$router->get('/export/conges', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new ExportController();
    $controller->congesExcel();
});

$router->get('/export/paie', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new ExportController();
    $controller->paieExcel();
});

// ============================================
// Gestion des utilisateurs (admin)
// ============================================
$router->get('/utilisateurs', function() use ($auth) {
    $auth->requireRole(['admin']);
    $controller = new UtilisateurController();
    $controller->index();
});

$router->get('/utilisateurs/create', function() use ($auth) {
    $auth->requireRole(['admin']);
    $controller = new UtilisateurController();
    $controller->create();
});

$router->post('/utilisateurs/create', function() use ($auth) {
    $auth->requireRole(['admin']);
    $controller = new UtilisateurController();
    $controller->create();
});

$router->get('/utilisateurs/edit/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin']);
    $controller = new UtilisateurController();
    $controller->edit($id);
});

$router->post('/utilisateurs/edit/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin']);
    $controller = new UtilisateurController();
    $controller->edit($id);
});

$router->post('/utilisateurs/toggle/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin']);
    $controller = new UtilisateurController();
    $controller->toggle($id);
});

$router->post('/utilisateurs/resetPassword/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin']);
    $controller = new UtilisateurController();
    $controller->resetPassword($id);
});

// ============================================
// Journal d'activité (admin)
// ============================================
$router->get('/journal', function() use ($auth) {
    $auth->requireRole(['admin']);
    $controller = new LogController();
    $controller->index();
});

$router->post('/journal/clear', function() use ($auth) {
    $auth->requireRole(['admin']);
    $controller = new LogController();
    $controller->clear();
});

// ============================================
// Cartes QR (admin + rh)
// ============================================
$router->get('/cartes', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new CarteController();
    $controller->index();
});

$router->post('/cartes/generer/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new CarteController();
    $controller->generer($id);
});

$router->post('/cartes/toggle/{id}', function($id) use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new CarteController();
    $controller->toggle($id);
});

// Page publique de pointage par QR (accessible à tous)
$router->get('/cartes/pointer', function() use ($auth) {
    $controller = new CarteController();
    $controller->pointer();
});

$router->post('/cartes/checkin', function() use ($auth) {
    $controller = new CarteController();
    $controller->checkin();
});

// ============================================
// Rapports (admin + rh)
// ============================================
$router->get('/rapports', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new RapportController();
    $controller->index();
});

$router->get('/rapports/absentisme', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new RapportController();
    $controller->absentisme();
});

$router->get('/rapports/conges', function() use ($auth) {
    $auth->requireRole(['admin', 'rh']);
    $controller = new RapportController();
    $controller->congesRapport();
});

// Route par défaut
$router->get('/', function() use ($auth) {
    $auth->checkAuth();
    header('Location: ' . APP_URL . '/dashboard');
    exit;
});
// Route 404
$router->notFound(function() {
    http_response_code(404);
    echo "<div style='text-align:center;padding:50px;'><h1>404</h1><p>Page non trouvée</p><a href='" . APP_URL . "/dashboard'>Retour au tableau de bord</a></div>";
});

// Dispatcher la requête
$router->dispatch();
