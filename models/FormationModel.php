<?php
require_once __DIR__ . '/../core/Model.php';

class FormationModel extends Model {
    protected $table = 'formations';
    protected $primaryKey = 'id_formation';

    public function findAllWithCount() {
        $sql = "SELECT f.*,
                       (SELECT COUNT(*) FROM formations_employes fe WHERE fe.id_formation = f.id_formation) as nb_inscrits
                FROM formations f
                ORDER BY f.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getInscrits($idFormation) {
        $sql = "SELECT fe.*, e.nom, e.prenom, e.matricule, s.nom_service
                FROM formations_employes fe
                INNER JOIN employes e ON fe.id_employe = e.id_employe
                LEFT JOIN services s ON e.id_service = s.id_service
                WHERE fe.id_formation = :id
                ORDER BY e.nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $idFormation]);
        return $stmt->fetchAll();
    }

    public function inscrireEmploye($idFormation, $idEmploye) {
        $stmt = $this->db->prepare(
            "SELECT id FROM formations_employes WHERE id_formation = :f AND id_employe = :e"
        );
        $stmt->execute(['f' => $idFormation, 'e' => $idEmploye]);
        $existant = $stmt->fetch();
        if ($existant) {
            return ['success' => false, 'message' => 'Cet employé est déjà inscrit'];
        }

        $id = $this->db->prepare(
            "INSERT INTO formations_employes (id_formation, id_employe) VALUES (:f, :e)"
        );
        $id->execute(['f' => $idFormation, 'e' => $idEmploye]);
        return ['success' => true, 'message' => 'Employé inscrit à la formation'];
    }

    public function desinscrireEmploye($idFormation, $idEmploye) {
        $stmt = $this->db->prepare(
            "DELETE FROM formations_employes WHERE id_formation = :f AND id_employe = :e"
        );
        return $stmt->execute(['f' => $idFormation, 'e' => $idEmploye]);
    }

    public function getFormationsByEmployee($idEmploye) {
        $sql = "SELECT f.*, fe.statut as inscription_statut, fe.note
                FROM formations f
                INNER JOIN formations_employes fe ON f.id_formation = fe.id_formation
                WHERE fe.id_employe = :id
                ORDER BY f.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $idEmploye]);
        return $stmt->fetchAll();
    }

    public function updateInscriptionStatut($idFormation, $idEmploye, $statut) {
        $stmt = $this->db->prepare(
            "UPDATE formations_employes SET statut = :s WHERE id_formation = :f AND id_employe = :e"
        );
        return $stmt->execute(['s' => $statut, 'f' => $idFormation, 'e' => $idEmploye]);
    }

    public function getTotalFormations() {
        return $this->count();
    }
}
