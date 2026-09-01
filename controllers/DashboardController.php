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
        $mois = $_GET['mois'] ?? date('m');
        $annee = $_GET['annee'] ?? date('Y');
        $bulletins = $this->paieModel->findAllWithEmployee($mois, $annee);

        $todayStats = $this->presenceModel->getTodayStats();
        $totalEmployes = $this->employeeModel->getActiveCount();
        $tauxPresence = ($todayStats['total'] ?? 0) > 0
            ? round((($todayStats['presents'] ?? 0) + ($todayStats['en_retard'] ?? 0)) / $todayStats['total'] * 100, 1)
            : 0;

        // Évolution de la paie sur 12 mois
        $evolution = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = new DateTime("$annee-$mois-01");
            $d->modify("-$i months");
            $m = $d->format('m');
            $y = $d->format('Y');
            $evolution[$y . '-' . $m] = ['mois_label' => $d->format('M y'), 'total_net' => 0, 'total_brut' => 0];
        }
        foreach ($this->paieModel->getPaieEvolution($annee) as $row) {
            $key = $annee . '-' . str_pad($row['mois'], 2, '0', STR_PAD_LEFT);
            if (isset($evolution[$key])) {
                $evolution[$key]['total_net'] = (float)$row['total_net'];
                $evolution[$key]['total_brut'] = (float)$row['total_brut'];
            }
        }

        // Congés en cours (approuvés chevauchant aujourd'hui)
        $congesEnCours = array_filter($this->congeModel->findAllWithEmployee('approuve'), function($c) {
            $today = date('Y-m-d');
            return $today >= $c['date_debut'] && $today <= $c['date_fin'];
        });

        // Répartition des congés par type (12 derniers mois)
        $congesStats = $this->congeModel->findAllWithEmployee();
        $congesParType = [];
        foreach ($congesStats as $c) {
            $congesParType[$c['type_conge']] = ($congesParType[$c['type_conge']] ?? 0) + 1;
        }

        $data = [
            'mois' => $mois,
            'annee' => $annee,
            'totalEmployees' => $totalEmployes,
            'totalServices' => $this->serviceModel->getTotalServices(),
            'todayStats' => $todayStats,
            'tauxPresence' => $tauxPresence,
            'pendingLeaves' => $this->congeModel->getPendingCount(),
            'approvedLeaves' => $this->congeModel->getApprovedCount(),
            'congesEnCours' => array_slice($congesEnCours, 0, 5),
            'employeesByService' => $this->employeeModel->countByService(),
            'employeesByGender' => $this->employeeModel->countByGender(),
            'monthlyPresence' => $this->presenceModel->getMonthlyStats($mois, $annee),
            'recentLeaves' => $this->congeModel->findAllWithEmployee(),
            'totalFormations' => $this->formationModel->getTotalFormations(),
            'totalBulletins' => count($bulletins),
            'totalPaieNet' => array_sum(array_column($bulletins, 'total_net')),
            'totalPaieBrut' => array_sum(array_column($bulletins, 'total_brut')),
            'paieEvolution' => array_values($evolution),
            'congesParType' => $congesParType,
        ];

        require __DIR__ . '/../views/dashboard/index.php';
    }
}