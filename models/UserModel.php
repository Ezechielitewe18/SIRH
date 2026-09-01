<?php
require_once __DIR__ . '/../core/Model.php';

class UserModel extends Model {
    protected $table = 'utilisateurs';
    protected $primaryKey = 'id_utilisateur';

    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            if ($user['statut'] === 'actif') {
                $this->update($user['id_utilisateur'], [
                    'derniere_connexion' => date('Y-m-d H:i:s')
                ]);
                return $user;
            }
        }
        return false;
    }

    public function register($data) {
        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        return $this->create($data);
    }

    public function updatePassword($id, $password) {
        return $this->update($id, [
            'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public function countByRole($role) {
        return $this->count(['role' => $role, 'statut' => 'actif']);
    }

    public function getTotalUsers() {
        return $this->count(['statut' => 'actif']);
    }

    public function findAllWithEmployee() {
        $sql = "SELECT u.*, e.id_employe, e.nom as emp_nom, e.prenom as emp_prenom, e.matricule as emp_matricule
                FROM utilisateurs u
                LEFT JOIN employes e ON u.id_utilisateur = e.id_utilisateur
                ORDER BY u.nom_complet ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function updateRole($id, $role) {
        return $this->update($id, ['role' => $role]);
    }

    public function toggleStatut($id) {
        $user = $this->findById($id);
        if ($user) {
            $newStatut = ($user['statut'] === 'actif') ? 'inactif' : 'actif';
            return $this->update($id, ['statut' => $newStatut]);
        }
        return false;
    }

    public function existsEmail($email, $exceptId = null) {
        $sql = "SELECT id_utilisateur FROM utilisateurs WHERE email = :email";
        $params = ['email' => $email];
        if ($exceptId) {
            $sql .= " AND id_utilisateur != :exceptId";
            $params['exceptId'] = $exceptId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    public function getUnlinkedUsers() {
        $sql = "SELECT u.* FROM utilisateurs u
                LEFT JOIN employes e ON u.id_utilisateur = e.id_utilisateur
                WHERE e.id_employe IS NULL
                ORDER BY u.nom_complet ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
