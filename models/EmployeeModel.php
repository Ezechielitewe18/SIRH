<?php
require_once __DIR__ . '/../core/Model.php';

class EmployeeModel extends Model {
    protected $table = 'employes';
    protected $primaryKey = 'id_employe';

    public function findAllWithService($orderBy = 'e.id_employe DESC') {
        if (!preg_match('/^[a-zA-Z_.][a-zA-Z0-9_.]*(?:\s+(?:ASC|DESC))?$/i', $orderBy)) {
            $orderBy = 'e.id_employe DESC';
        }
        $sql = "SELECT e.*, s.nom_service
                FROM {$this->table} e
                LEFT JOIN services s ON e.id_service = s.id_service
                ORDER BY {$orderBy}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findByIdWithService($id) {
        $sql = "SELECT e.*, s.nom_service
                FROM {$this->table} e
                LEFT JOIN services s ON e.id_service = s.id_service
                WHERE e.{$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findByUserId($userId) {
        $sql = "SELECT e.*, s.nom_service
                FROM {$this->table} e
                LEFT JOIN services s ON e.id_service = s.id_service
                WHERE e.id_utilisateur = :id_utilisateur";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_utilisateur' => $userId]);
        return $stmt->fetch();
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function generateMatricule() {
        $year = date('Y');
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE matricule LIKE :prefix";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['prefix' => "EMP-{$year}-%"]);
        $result = $stmt->fetch();
        $num = $result['total'] + 1;
        return "EMP-{$year}-" . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function countByService() {
        $sql = "SELECT s.nom_service, COUNT(e.id_employe) as nombre
                FROM services s
                LEFT JOIN {$this->table} e ON s.id_service = e.id_service AND e.statut = 'actif'
                GROUP BY s.id_service, s.nom_service";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function countByGender() {
        $sql = "SELECT sexe, COUNT(*) as total FROM {$this->table} WHERE statut = 'actif' GROUP BY sexe";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function searchEmployees($term) {
        $sql = "SELECT e.*, s.nom_service
                FROM {$this->table} e
                LEFT JOIN services s ON e.id_service = s.id_service
                WHERE e.nom LIKE :term1
                OR e.prenom LIKE :term2
                OR e.matricule LIKE :term3
                OR e.email LIKE :term4
                ORDER BY e.nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'term1' => "%{$term}%",
            'term2' => "%{$term}%",
            'term3' => "%{$term}%",
            'term4' => "%{$term}%"
        ]);
        return $stmt->fetchAll();
    }

    public function getActiveCount() {
        return $this->count(['statut' => 'actif']);
    }
}
