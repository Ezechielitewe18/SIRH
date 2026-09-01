<?php
require_once __DIR__ . '/../core/Model.php';

class PresenceModel extends Model {
    protected $table = 'presences';
    protected $primaryKey = 'id_presence';

    public function findAllWithEmployee($date = null) {
        $sql = "SELECT p.*, e.nom, e.prenom, e.matricule, s.nom_service 
                FROM {$this->table} p 
                INNER JOIN employes e ON p.id_employe = e.id_employe 
                LEFT JOIN services s ON e.id_service = s.id_service";

        $params = [];
        if ($date) {
            $sql .= " WHERE p.date_presence = :date";
            $params['date'] = $date;
        }
        $sql .= " ORDER BY p.date_presence DESC, e.nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findByEmployee($id_employe, $date = null) {
        $sql = "SELECT * FROM {$this->table} WHERE id_employe = :id_employe";
        $params = ['id_employe' => $id_employe];

        if ($date) {
            $sql .= " AND date_presence = :date";
            $params['date'] = $date;
        }
        $sql .= " ORDER BY date_presence DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findTodayByEmployee($id_employe) {
        return $this->findByEmployee($id_employe, date('Y-m-d'));
    }

    public function checkIn($id_employe) {
        $today = date('Y-m-d');
        $heure = date('H:i:s');

        // Vérifier si déjà pointé aujourd'hui
        $existing = $this->findByEmployee($id_employe, $today);
        if (!empty($existing)) {
            return ['success' => false, 'message' => 'Vous avez déjà pointé aujourd\'hui'];
        }

        // Calculer le retard
        $retard = 0;
        $statut = 'present';
        $heureDebut = HEURE_DEBUT;
        if ($heure > $heureDebut) {
            $debut = new DateTime($heureDebut);
            $maintenant = new DateTime($heure);
            $diff = $debut->diff($maintenant);
            $retard = $diff->h * 60 + $diff->i;
            if ($retard > 0) {
                $statut = 'retard';
            }
        }

        $id = $this->create([
            'id_employe' => $id_employe,
            'date_presence' => $today,
            'heure_arrivee' => $heure,
            'statut' => $statut,
            'retard' => $retard
        ]);

        return ['success' => true, 'message' => 'Arrée enregistrée', 'presence_id' => $id];
    }

    public function checkOut($id_employe) {
        $today = date('Y-m-d');
        $heure = date('H:i:s');

        $existing = $this->findByEmployee($id_employe, $today);
        if (empty($existing)) {
            return ['success' => false, 'message' => 'Vous n\'avez pas pointé aujourd\'hui'];
        }

        if (!empty($existing[0]['heure_depart'])) {
            return ['success' => false, 'message' => 'Vous avez déjà pointé votre sortie'];
        }

        $this->update($existing[0]['id_presence'], [
            'heure_depart' => $heure
        ]);

        return ['success' => true, 'message' => 'Départ enregistré'];
    }

    public function getTodayStats() {
        $today = date('Y-m-d');
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'present' THEN 1 ELSE 0 END) as presents,
                    SUM(CASE WHEN statut = 'retard' THEN 1 ELSE 0 END) as en_retard,
                    SUM(CASE WHEN statut = 'absent' THEN 1 ELSE 0 END) as absents
                FROM {$this->table} WHERE date_presence = :date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['date' => $today]);
        return $stmt->fetch();
    }

    public function getMonthlyStats($month = null, $year = null) {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        $sql = "SELECT 
                    DATE_FORMAT(date_presence, '%Y-%m-%d') as jour,
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'present' THEN 1 ELSE 0 END) as presents,
                    SUM(CASE WHEN statut = 'retard' THEN 1 ELSE 0 END) as en_retard
                FROM {$this->table} 
                WHERE MONTH(date_presence) = :month AND YEAR(date_presence) = :year
                GROUP BY jour
                ORDER BY jour ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['month' => $month, 'year' => $year]);
        return $stmt->fetchAll();
    }

    public function markAbsent($id_employe, $date) {
        $existing = $this->findByEmployee($id_employe, $date);
        if (!empty($existing)) {
            return;
        }
        $this->create([
            'id_employe' => $id_employe,
            'date_presence' => $date,
            'statut' => 'absent'
        ]);
    }
}
