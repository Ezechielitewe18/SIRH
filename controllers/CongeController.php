<?php
require_once __DIR__ . '/../models/CongeModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class CongeController {
    private $congeModel;
    private $employeeModel;
    private $notificationModel;

    public function __construct() {
        $this->congeModel = new CongeModel();
        $this->employeeModel = new EmployeeModel();
        $this->notificationModel = new NotificationModel();
    }

    public function index() {
        $role = $_SESSION['user_role'] ?? '';
        $statut = $_GET['statut'] ?? null;

        if ($role === 'employe') {
            $employeeId = $_SESSION['employee_id'] ?? null;
            $conges = $employeeId ? $this->congeModel->findByEmployee($employeeId) : [];
        } else {
            $conges = $this->congeModel->findAllWithEmployee($statut);
        }

        require __DIR__ . '/../views/conges/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employeeId = $_SESSION['employee_id'] ?? null;

            if (!$employeeId && $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'rh') {
                $_SESSION['error'] = 'Aucun employé associé';
                header('Location: ' . APP_URL . '/conges');
                exit;
            }

            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin = $_POST['date_fin'] ?? '';
            $typeConge = $_POST['type_conge'] ?? 'annuel';
            $motif = trim($_POST['motif'] ?? '');

            $errors = [];
            if (empty($dateDebut)) $errors[] = 'La date de début est requise';
            if (empty($dateFin)) $errors[] = 'La date de fin est requise';
            if ($dateDebut > $dateFin) $errors[] = 'La date de fin doit être postérieure à la date de début';
            if (empty($motif)) $errors[] = 'Le motif est requis';

            if (empty($errors)) {
                $nombreJours = $this->congeModel->calculateDays($dateDebut, $dateFin);

                $idConge = $this->congeModel->create([
                    'id_employe' => $employeeId,
                    'type_conge' => $typeConge,
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin,
                    'nombre_jours' => $nombreJours,
                    'motif' => $motif,
                    'statut' => 'en_attente'
                ]);

                // Notifier les RH et admins d'une nouvelle demande
                $employee = $employeeId ? $this->employeeModel->findById($employeeId) : null;
                $nomEmploye = $employee ? ($employee['prenom'] . ' ' . $employee['nom']) : 'Un employé';
                $lien = APP_URL . '/conges';
                $this->notificationModel->notifyAllByRole(
                    ['admin', 'rh'],
                    'Nouvelle demande de congé',
                    "$nomEmploye a soumis une demande de congé $typeConge du $dateDebut au $dateFin (" . $nombreJours . " jour(s)).",
                    'conge',
                    $lien
                );

                // Envoyer un email aux RH/admin
                foreach ($this->notificationModel->getEmailRecipients(['admin', 'rh']) as $rec) {
                    $this->notificationModel->sendEmail(
                        $rec['email'],
                        'GLOBIT - Nouvelle demande de congé',
                        $this->notificationModel->emailTemplate('Nouvelle demande de congé', "$nomEmploye a soumis une demande de congé.", $lien)
                    );
                }

                $_SESSION['success'] = 'Demande de congé soumise avec succès';
                header('Location: ' . APP_URL . '/conges');
                exit;
            }

            $_SESSION['errors'] = $errors;
        }

        require __DIR__ . '/../views/conges/create.php';
    }

    public function approve($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->congeModel->approve($id, $_SESSION['user_id']);

            // Notifier l'employé
            $conge = $this->congeModel->findById($id);
            if ($conge) {
                $emp = $this->employeeModel->findById($conge['id_employe']);
                if ($emp && $emp['id_utilisateur']) {
                    $this->notificationModel->add(
                        $emp['id_utilisateur'],
                        'Congé approuvé',
                        'Votre demande de congé du ' . $conge['date_debut'] . ' au ' . $conge['date_fin'] . ' a été approuvée.',
                        'conge',
                        APP_URL . '/conges'
                    );
                }
            }

            $_SESSION['success'] = 'Congé approuvé';
        }
        header('Location: ' . APP_URL . '/conges');
        exit;
    }

    public function reject($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $motifRefus = trim($_POST['motif_refus'] ?? '');
            $this->congeModel->reject($id, $_SESSION['user_id'], $motifRefus);

            // Notifier l'employé
            $conge = $this->congeModel->findById($id);
            if ($conge) {
                $emp = $this->employeeModel->findById($conge['id_employe']);
                if ($emp && $emp['id_utilisateur']) {
                    $this->notificationModel->add(
                        $emp['id_utilisateur'],
                        'Congé refusé',
                        'Votre demande de congé du ' . $conge['date_debut'] . ' au ' . $conge['date_fin'] . ' a été refusée. Motif : ' . ($motifRefus ?: 'non précisé'),
                        'conge',
                        APP_URL . '/conges'
                    );
                }
            }

            $_SESSION['success'] = 'Congé refusé';
        }
        header('Location: ' . APP_URL . '/conges');
        exit;
    }
}
