<?php
require_once __DIR__ . '/../core/Model.php';

class CarteModel extends Model {
    protected $table = 'cartes_employes';
    protected $primaryKey = 'id_carte';


    public function genererPourEmploye($idEmploye) {

        $existant = $this->find(['id_employe' => $idEmploye], 'id_carte DESC');
        if (!empty($existant)) {
            return $existant[0];
        }

        $codeQr = $this->genererCodeUnique($idEmploye);

        $id = $this->create([
            'id_employe' => $idEmploye,
            'code_qr' => $codeQr,
            'actif' => 1
        ]);

        return $this->findById($id);
    }

    public function getByEmploye($idEmploye) {
        $existant = $this->find(['id_employe' => $idEmploye], 'id_carte DESC');
        return !empty($existant) ? $existant[0] : null;
    }

    public function findByCode($code) {
        $sql = "SELECT c.*, e.nom, e.prenom, e.matricule, e.photo, e.est_direction, s.nom_service
                FROM cartes_employes c
                INNER JOIN employes e ON c.id_employe = e.id_employe
                LEFT JOIN services s ON e.id_service = s.id_service
                WHERE c.code_qr = :code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['code' => $code]);
        return $stmt->fetch();
    }

    public function regenerate($idEmploye) {
        $this->delete($this->getByEmploye($idEmploye)['id_carte'] ?? 0);
        return $this->genererPourEmploye($idEmploye);
    }

    public function toggleActif($id) {
        $carte = $this->findById($id);
        if ($carte) {
            return $this->update($id, ['actif' => $carte['actif'] ? 0 : 1]);
        }
        return false;
    }

    private function genererCodeUnique($idEmploye) {
        $base = 'GLOBIT-' . strtoupper(substr(md5($idEmploye . time() . rand()), 0, 12));
        return $base;
    }
}
