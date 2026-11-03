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
        $role = $_SESSION['user_role'] ?? '';

        // Un employe ne voit que ses propres donnees : vue dediee, aucune
        // requete collective (effectif, paie totale, conges des collegues...).
        if ($role === 'employe') {
            $this->indexEmploye();
            return;
        }

        $mois = $_GET['mois'] ?? date('m');
        $annee = $_GET['annee'] ?? date('Y');
        $bulletins = $this->paieModel->findAllWithEmployee($mois, $annee);

        $todayStats = $this->presenceModel->getTodayStats();
        $totalEmployes = $this->employeeModel->getActiveCount();
        $tauxPresence = ($todayStats['total'] ?? 0) > 0
            ? round((($todayStats['presents'] ?? 0) + ($todayStats['en_retard'] ?? 0)) / $todayStats['total'] * 100, 1)
            : 0;

        
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

        
        $congesEnCours = array_filter($this->congeModel->findAllWithEmployee('approuve'), function($c) {
            $today = date('Y-m-d');
            return $today >= $c['date_debut'] && $today <= $c['date_fin'];
        });

        
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

    /**
     * Tableau de bord de l'espace salarie. Toutes les donnees sont filtrees
     * sur l'employe connecte : aucun acces aux chiffres de l'entreprise.
     */
    private function indexEmploye() {
        $employeeId = $_SESSION['employee_id'] ?? null;
        $mois = (int) ($_GET['mois'] ?? date('m'));
        $annee = (int) ($_GET['annee'] ?? date('Y'));

        $employe = null;
        $presenceJour = null;
        $stats = [
            'jours_pointes' => 0, 'presents' => 0, 'retards' => 0,
            'absents' => 0, 'justifies' => 0, 'minutes_retard' => 0,
            'heures_travail' => 0,
        ];
        $historique = [];
        $dernieres = [];
        $mesConges = [];
        $congesEnAttente = 0;
        $congesApprouves = 0;
        $congesEnCours = null;
        $bulletins = [];
        $dernierBulletin = null;

        if ($employeeId) {
            $employe = $this->employeeModel->findByIdWithService($employeeId);
            $presenceJour = $this->presenceModel->etatJour($employeeId);
            $stats = $this->presenceModel->statsMoisPourEmploye($employeeId, $mois, $annee);
            $historique = $this->presenceModel->findByMonthEmploye($employeeId, $mois, $annee);
            $dernieres = $this->presenceModel->dernieresPresences($employeeId, 6);

            $mesConges = $this->congeModel->findByEmployee($employeeId);
            foreach ($mesConges as $c) {
                if ($c['statut'] === 'en_attente') {
                    $congesEnAttente++;
                } elseif ($c['statut'] === 'approuve') {
                    $congesApprouves++;
                    $today = date('Y-m-d');
                    if ($congesEnCours === null && $today >= $c['date_debut'] && $today <= $c['date_fin']) {
                        $congesEnCours = $c;
                    }
                }
            }

            $bulletins = $this->paieModel->findByEmployee($employeeId);
            $dernierBulletin = $bulletins[0] ?? null;
        }

        $pageTitle = 'Mon espace';
        require __DIR__ . '/../views/dashboard/employe.php';
    }
}