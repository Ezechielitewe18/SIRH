<?php
require_once __DIR__ . '/../core/Model.php';

class ServiceModel extends Model {
    protected $table = 'services';
    protected $primaryKey = 'id_service';

    public function findAllOrdered() {
        return $this->findAll('nom_service ASC');
    }

    public function getEmployeeCount() {
        $sql = "SELECT s.*, COUNT(e.id_employe) as nombre_employes
                FROM {$this->table} s
                LEFT JOIN employes e ON s.id_service = e.id_service AND e.statut = 'actif'
                GROUP BY s.id_service
                ORDER BY s.nom_service ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getTotalServices() {
        return $this->count();
    }
}
