<?php
require_once __DIR__ . '/../core/Model.php';

class CongeModel extends Model {
    protected $table = 'conges';
    protected $primaryKey = 'id_conge';

    public function findAllWithEmployee($statut = null) {
        $sql = "SELECT c.*, e.nom, e.prenom, e.matricule, s.nom_service,
                       u.nom_complet as approbateur_nom
                FROM {$this->table} c
                INNER JOIN employes e ON c.id_employe = e.id_employe
                LEFT JOIN services s ON e.id_service = s.id_service
                LEFT JOIN utilisateurs u ON c.approuve_par = u.id_utilisateur";

        $params = [];
        if ($statut) {
            $sql .= " WHERE c.statut = :statut";
            $params['statut'] = $statut;
        }
        $sql .= " ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findByEmployee($id_employe) {
        $sql = "SELECT c.*, u.nom_complet as approbateur_nom
                FROM {$this->table} c
                LEFT JOIN utilisateurs u ON c.approuve_par = u.id_utilisateur
                WHERE c.id_employe = :id_employe
                ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $id_employe]);
        return $stmt->fetchAll();
    }

    public function approve($id, $approverId) {
        return $this->update($id, [
            'statut' => 'approuve',
            'approuve_par' => $approverId,
            'date_approbation' => date('Y-m-d H:i:s')
        ]);
    }

    public function reject($id, $approverId, $motifRefus) {
        return $this->update($id, [
            'statut' => 'refuse',
            'approuve_par' => $approverId,
            'motif_refus' => $motifRefus,
            'date_approbation' => date('Y-m-d H:i:s')
        ]);
    }

    public function getPendingCount() {
        return $this->count(['statut' => 'en_attente']);
    }

    public function getApprovedCount() {
        return $this->count(['statut' => 'approuve']);
    }

    public function getMonthlyStats($month = null, $year = null) {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        $sql = "SELECT type_conge, COUNT(*) as nombre, statut
                FROM {$this->table}
                WHERE MONTH(created_at) = :month AND YEAR(created_at) = :year
                GROUP BY type_conge, statut
                ORDER BY type_conge";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['month' => $month, 'year' => $year]);
        return $stmt->fetchAll();
    }

    public function calculateDays($dateDebut, $dateFin) {
        $debut = new DateTime($dateDebut);
        $fin = new DateTime($dateFin);
        $diff = $debut->diff($fin);
        return $diff->days + 1;
    }
}
