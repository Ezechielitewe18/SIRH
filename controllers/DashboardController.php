<?php
require_once __DIR__ . '/../models/EmployeeModel.php';
require_once __DIR__ . '/../models/ServiceModel.php';
require_once __DIR__ . '/../models/PresenceModel.php';
require_once __DIR__ . '/../models/CongeModel.php';
require_once __DIR__ . '/../models/PaieModel.php';
require_once __DIR__ . '/../models/FormationModel.php';

class DashboardController {
    private $employeeModel;
    private $serviceModel;
    private $presenceModel;
    private $congeModel;
    private $paieModel;
    private $formationModel;

    public function __construct() {
        $this->employeeModel = new EmployeeModel();
        $this->serviceModel = new ServiceModel();
        $this->presenceModel = new PresenceModel();
        $this->congeModel = new CongeModel();
        $this->paieModel = new PaieModel();
        $this->formationModel = new FormationModel();
    }

    public function index() {
        $mois = date('m');
        $annee = date('Y');
        $bulletins = $this->paieModel->findAllWithEmployee($mois, $annee);

        $data = [
            'totalEmployees' => $this->employeeModel->getActiveCount(),
            'totalServices' => $this->serviceModel->getTotalServices(),
            'todayStats' => $this->presenceModel->getTodayStats(),
            'pendingLeaves' => $this->congeModel->getPendingCount(),
            'approvedLeaves' => $this->congeModel->getApprovedCount(),
            'employeesByService' => $this->employeeModel->countByService(),
            'employeesByGender' => $this->employeeModel->countByGender(),
            'monthlyPresence' => $this->presenceModel->getMonthlyStats(),
            'recentLeaves' => $this->congeModel->findAllWithEmployee(),
            'totalFormations' => $this->formationModel->getTotalFormations(),
            'totalBulletins' => count($bulletins),
            'totalPaieNet' => array_sum(array_column($bulletins, 'total_net')),
        ];

        require __DIR__ . '/../views/dashboard/index.php';
    }
}
