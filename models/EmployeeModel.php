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

    /**
     * Primes fixes d'un employe. Elles sont facultatives : un employe
     * sans prime renvoie simplement une liste vide.
     */
    public function getPrimes($idEmploye, $seulementActives = true) {
        $sql = "SELECT * FROM primes WHERE id_employe = :id";
        if ($seulementActives) {
            $sql .= " AND actif = 1";
        }
        $sql .= " ORDER BY id_prime ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $idEmploye]);
        return $stmt->fetchAll();
    }

    public function totalPrimes($idEmploye) {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(montant), 0) AS total FROM primes WHERE id_employe = :id AND actif = 1"
        );
        $stmt->execute(['id' => $idEmploye]);
        return (float)$stmt->fetch()['total'];
    }

    /**
     * Enregistre la liste des primes fixes remplacee integralement
     * (tableau : [['libelle' => ..., 'montant' => ...], ...]).
     * Une liste vide supprime toutes les primes : le champ reste facultatif.
     */
    public function syncPrimes($idEmploye, array $primes) {
        $this->db->prepare("DELETE FROM primes WHERE id_employe = :id")->execute(['id' => $idEmploye]);

        $total = 0;
        foreach ($primes as $prime) {
            $libelle = trim($prime['libelle'] ?? '');
            $montant = (float)($prime['montant'] ?? 0);
            if ($libelle === '' || $montant <= 0) {
                continue;
            }
            $stmt = $this->db->prepare(
                "INSERT INTO primes (id_employe, libelle, montant, actif) VALUES (:id, :libelle, :montant, 1)"
            );
            $stmt->execute(['id' => $idEmploye, 'libelle' => $libelle, 'montant' => $montant]);
            $total += $montant;
        }
        return $total;
    }
}
