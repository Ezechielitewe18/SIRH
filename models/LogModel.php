<?php
require_once __DIR__ . '/../core/Model.php';

class LogModel extends Model {
    protected $table = 'journal_activite';
    protected $primaryKey = 'id_log';

    /**
     * Enregistrer une action dans le journal d'audit
     */
    public function log($action, $details = null, $module = null) {
        $idUtilisateur = $_SESSION['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        return $this->create([
            'id_utilisateur' => $idUtilisateur,
            'action' => $action,
            'details' => $details,
            'module' => $module,
            'ip_adresse' => $ip
        ]);
    }

    public function findAllWithUser($limit = 200) {
        $sql = "SELECT l.*, u.nom_complet, u.email
                FROM journal_activite l
                LEFT JOIN utilisateurs u ON l.id_utilisateur = u.id_utilisateur
                ORDER BY l.created_at DESC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByModule($module, $limit = 100) {
        $sql = "SELECT l.*, u.nom_complet
                FROM journal_activite l
                LEFT JOIN utilisateurs u ON l.id_utilisateur = u.id_utilisateur
                WHERE l.module = :module
                ORDER BY l.created_at DESC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('module', $module);
        $stmt->bindValue('lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function search($term, $limit = 100) {
        $sql = "SELECT l.*, u.nom_complet
                FROM journal_activite l
                LEFT JOIN utilisateurs u ON l.id_utilisateur = u.id_utilisateur
                WHERE l.action LIKE :term OR l.details LIKE :term OR l.module LIKE :term
                ORDER BY l.created_at DESC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('term', "%$term%");
        $stmt->bindValue('lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countActionsByModule() {
        $sql = "SELECT module, COUNT(*) as total FROM journal_activite GROUP BY module ORDER BY total DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getTotalLogs() {
        return $this->count();
    }
}
