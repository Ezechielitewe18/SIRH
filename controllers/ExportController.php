<?php
require_once __DIR__ . '/../models/EmployeeModel.php';
require_once __DIR__ . '/../models/PresenceModel.php';
require_once __DIR__ . '/../models/CongeModel.php';
require_once __DIR__ . '/../models/PaieModel.php';
require_once __DIR__ . '/../models/ServiceModel.php';

class ExportController {

    public function employeesExcel() {
        $em = new EmployeeModel();
        $employees = $em->findAllWithService();

        $filename = 'employes_' . date('Y-m-d') . '.csv';
        $this->csvHeaders($filename);

        $out = fopen('php://output', 'w');
        
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Matricule', 'Nom', 'Postnom', 'Prenom', 'Sexe', 'Telephone', 'Email', 'Service', 'Poste', 'Date embauche', 'Salaire', 'Statut']);

        foreach ($employees as $e) {
            fputcsv($out, [
                $e['matricule'],
                $e['nom'],
                $e['postnom'],
                $e['prenom'],
                $e['sexe'],
                $e['telephone'],
                $e['email'],
                $e['nom_service'],
                $e['poste'],
                $e['date_embauche'],
                $e['salaire'],
                $e['statut']
            ]);
        }
        fclose($out);
        exit;
    }

    public function presencesExcel() {
        $date = $_GET['date'] ?? date('Y-m-d');
        
        $date = preg_replace('/[^0-9\-]/', '', $date);
        $pm = new PresenceModel();
        $presences = $pm->findAllWithEmployee($date);

        $filename = 'presences_' . $date . '.csv';
        $this->csvHeaders($filename);

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Matricule', 'Nom', 'Prenom', 'Service', 'Arrivee', 'Depart', 'Retard', 'Statut']);

        foreach ($presences as $p) {
            fputcsv($out, [
                $p['matricule'],
                $p['nom'],
                $p['prenom'],
                $p['nom_service'],
                $p['heure_arrivee'],
                $p['heure_depart'],
                $p['retard'],
                $p['statut']
            ]);
        }
        fclose($out);
        exit;
    }

    public function congesExcel() {
        $cm = new CongeModel();
        $conges = $cm->findAllWithEmployee();

        $filename = 'conges_' . date('Y-m-d') . '.csv';
        $this->csvHeaders($filename);

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Matricule', 'Nom', 'Prenom', 'Type', 'Debut', 'Fin', 'Jours', 'Motif', 'Statut']);

        foreach ($conges as $c) {
            fputcsv($out, [
                $c['matricule'],
                $c['nom'],
                $c['prenom'],
                $c['type_conge'],
                $c['date_debut'],
                $c['date_fin'],
                $c['nombre_jours'],
                $c['motif'],
                $c['statut']
            ]);
        }
        fclose($out);
        exit;
    }

    public function paieExcel() {
        $mois = $_GET['mois'] ?? date('m');
        $annee = $_GET['annee'] ?? date('Y');
        $pm = new PaieModel();
        $bulletins = $pm->findAllWithEmployee($mois, $annee);

        $filename = 'paie_' . $annee . '_' . $mois . '.csv';
        $this->csvHeaders($filename);

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Matricule', 'Nom', 'Salaire Base', 'Primes', 'Heures Sup', 'Brut', 'Retenues', 'Net', 'Statut']);

        foreach ($bulletins as $b) {
            fputcsv($out, [
                $b['matricule'],
                $b['nom'] . ' ' . $b['prenom'],
                $b['salaire_base'],
                $b['primes'],
                $b['montant_heures_sup'],
                $b['total_brut'],
                $b['total_retenues'],
                $b['total_net'],
                $b['statut']
            ]);
        }
        fclose($out);
        exit;
    }

    public function employeesPdf() {
        $em = new EmployeeModel();
        $employees = $em->findAllWithService();

        $html = $this->pdfHeader('Liste des employés');
        $html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse;font-size:11px;">';
        $html .= '<tr style="background:#007bff;color:#fff;font-weight:bold;">
                    <th>Matricule</th><th>Nom complet</th><th>Sexe</th><th>Service</th><th>Poste</th><th>Téléphone</th></tr>';
        foreach ($employees as $e) {
            $html .= '<tr>';
            $html .= '<td>' . $e['matricule'] . '</td>';
            $html .= '<td>' . $e['prenom'] . ' ' . $e['nom'] . '</td>';
            $html .= '<td>' . $e['sexe'] . '</td>';
            $html .= '<td>' . ($e['nom_service'] ?? 'N/A') . '</td>';
            $html .= '<td>' . ($e['poste'] ?? 'N/A') . '</td>';
            $html .= '<td>' . ($e['telephone'] ?? 'N/A') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        $html .= $this->pdfFooter();

        $this->renderPdf('employes.pdf', $html);
    }

    private function csvHeaders($filename) {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    private function pdfHeader($title) {
        $html = "<!DOCTYPE html><html><head><meta charset='UTF-8'><style>
            body{font-family:Arial,sans-serif;}
            .header{width:100%;border-bottom:3px solid #007bff;padding-bottom:10px;margin-bottom:20px;}
            .header h1{color:#007bff;margin:0;}
            .header p{color:#666;margin:2px 0;}
        </style></head><body>";
        $html .= "<div class='header'><h1>GLOBIT - " . $title . "</h1>";
        $html .= "<p>© " . date('Y') . " GLOBIT - Système d'Information des Ressources Humaines</p>";
        $html .= "<p>Généré le " . date('d/m/Y H:i') . "</p></div>";
        return $html;
    }

    private function pdfFooter() {
        return "<div style='margin-top:30px;border-top:1px solid #ccc;padding-top:10px;color:#888;font-size:10px;'>
            Tous droits réservés. Développé par " . (defined('AUTHOR_NAME') ? AUTHOR_NAME : 'Ezechiel Itewe Nzukumayi') . "</div></body></html>";
    }

    private function renderPdf($filename, $html) {
        
        
        
        $dompdfPath = __DIR__ . '/../lib/dompdf/autoload.inc.php';

        if (file_exists($dompdfPath)) {
            require_once $dompdfPath;
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;
        }

        
        header('Content-Type: text/html; charset=UTF-8');
        echo $html;
        echo "<script>window.print();</script>";
        exit;
    }
}
