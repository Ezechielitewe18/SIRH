<?php
require_once __DIR__ . '/../models/EmployeeModel.php';
require_once __DIR__ . '/../models/PresenceModel.php';
require_once __DIR__ . '/../models/CongeModel.php';
require_once __DIR__ . '/../models/PaieModel.php';
require_once __DIR__ . '/../models/ServiceModel.php';
require_once __DIR__ . '/../models/FormationModel.php';

class RapportController {
    public function index() {
        $mois = $_GET['mois'] ?? date('m');
        $annee = $_GET['annee'] ?? date('Y');

        $em = new EmployeeModel();
        $pm = new PresenceModel();
        $cm = new CongeModel();
        $sm = new ServiceModel();
        $fm = new FormationModel();

        $totalEmployes = $em->getActiveCount();

        
        $monthlyPresence = $pm->getMonthlyStats($mois, $annee);
        $totalJours = count($monthlyPresence);
        $totalPoints = array_sum(array_column($monthlyPresence, 'total'));
        $totalPresents = array_sum(array_column($monthlyPresence, 'presents'));
        $totalRetards = array_sum(array_column($monthlyPresence, 'en_retard'));

        
        $tauxPresence = $totalPoints > 0 ? round(($totalPresents / $totalPoints) * 100, 1) : 0;

        
        $conges = $cm->findAllWithEmployee();
        $congesApprouves = 0;
        $congesRefuses = 0;
        $congesEnAttente = 0;
        $totalJoursConges = 0;
        foreach ($conges as $c) {
            switch ($c['statut']) {
                case 'approuve': $congesApprouves++; $totalJoursConges += $c['nombre_jours']; break;
                case 'refuse': $congesRefuses++; break;
                default: $congesEnAttente++; break;
            }
        }

        
        $employeesByService = $em->countByService();

        
        $employeesByGender = $em->countByGender();

        
        $pmodel = new PaieModel();
        $bulletins = $pmodel->findAllWithEmployee($mois, $annee);
        $masseSalariale = array_sum(array_column($bulletins, 'total_brut'));
        $totalNet = array_sum(array_column($bulletins, 'total_net'));

        
        $totalFormations = $fm->getTotalFormations();

        require __DIR__ . '/../views/rapports/index.php';
    }

    public function absentisme() {
        $mois = $_GET['mois'] ?? date('m');
        $annee = $_GET['annee'] ?? date('Y');

        $em = new EmployeeModel();
        $pm = new PresenceModel();

        $employes = $em->findAllWithService();
        $stats = [];

        foreach ($employes as $e) {
            $presences = $pm->findByEmployee($e['id_employe']);
            $joursPresentes = 0;
            $joursAbsents = 0;
            foreach ($presences as $p) {
                if (substr($p['date_presence'], 5, 2) == $mois && substr($p['date_presence'], 0, 4) == $annee) {
                    
                    if (!in_array($p['validation'] ?? '', ['auto', 'validee'])) {
                        continue;
                    }
                    if ($p['statut'] === 'absent') {
                        $joursAbsents++;
                    } elseif ($p['statut'] === 'present' || $p['statut'] === 'retard') {
                        $joursPresentes++;
                    }
                }
            }
            $total = $joursPresentes + $joursAbsents;
            $tauxAbsent = $total > 0 ? round(($joursAbsents / $total) * 100, 1) : 0;
            $stats[] = [
                'matricule' => $e['matricule'],
                'nom' => $e['prenom'] . ' ' . $e['nom'],
                'service' => $e['nom_service'],
                'jours_presents' => $joursPresentes,
                'jours_absents' => $joursAbsents,
                'taux_absence' => $tauxAbsent
            ];
        }

        usort($stats, function($a, $b) { return $b['taux_absence'] <=> $a['taux_absence']; });

        require __DIR__ . '/../views/rapports/absentisme.php';
    }

    public function congesRapport() {
        $mois = $_GET['mois'] ?? date('m');
        $annee = $_GET['annee'] ?? date('Y');

        $cm = new CongeModel();
        $conges = $cm->findAllWithEmployee();

        $approuves = array_filter($conges, function($c){ return $c['statut'] === 'approuve'; });
        $refuses = array_filter($conges, function($c){ return $c['statut'] === 'refuse'; });
        $congesApprouves = count($approuves);
        $congesRefuses = count($refuses);
        $totalJoursApprouves = array_sum(array_column($approuves, 'nombre_jours'));

        
        $types = [];
        foreach ($approuves as $c) {
            $types[$c['type_conge']] = ($types[$c['type_conge']] ?? 0) + 1;
        }

        require __DIR__ . '/../views/rapports/conges.php';
    }
}
