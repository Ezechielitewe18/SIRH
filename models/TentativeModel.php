<?php
require_once __DIR__ . '/../core/Model.php';

/**
 * GLOBIT SAS - SIRH : suivi des tentatives de connexion (anti force brute).
 * Seuils configures dans config/config.php (LOGIN_MAX_ECHECS, LOGIN_MAX_ECHECS_IP,
 * LOGIN_FENETRE_MINUTES).
 */
class TentativeModel extends Model {
    protected $table = 'tentatives_connexion';
    protected $primaryKey = 'id';

    public function ipClient() {
        return substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45);
    }

    public function enregistrer($email) {
        $stmt = $this->db->prepare('INSERT INTO tentatives_connexion (email, ip_adresse) VALUES (:e, :i)');
        $stmt->execute(['e' => substr($email, 0, 100), 'i' => $this->ipClient()]);
        $this->db->exec('DELETE FROM tentatives_connexion WHERE ajoutee_le < (NOW() - INTERVAL 1 HOUR)');
    }

    public function estBloque($email) {
        $seuil = date('Y-m-d H:i:s', time() - (int) LOGIN_FENETRE_MINUTES * 60);
        $stmt = $this->db->prepare('
            SELECT
                COALESCE(SUM(email  = :e), 0) AS nb_email,
                COALESCE(SUM(ip_adresse = :i), 0) AS nb_ip
            FROM tentatives_connexion
            WHERE ajoutee_le >= :seuil
        ');
        $stmt->execute(['e' => substr($email, 0, 100), 'i' => $this->ipClient(), 'seuil' => $seuil]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }
        return (int) $row['nb_email'] >= (int) LOGIN_MAX_ECHECS
            || (int) $row['nb_ip'] >= (int) LOGIN_MAX_ECHECS_IP;
    }

    public function reussite($email) {
        $stmt = $this->db->prepare('DELETE FROM tentatives_connexion WHERE email = :e');
        $stmt->execute(['e' => substr($email, 0, 100)]);
    }
}