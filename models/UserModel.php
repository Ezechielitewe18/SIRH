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
}
