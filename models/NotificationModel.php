<?php
require_once __DIR__ . '/../core/Model.php';

class NotificationModel extends Model {
    protected $table = 'notifications';
    protected $primaryKey = 'id_notification';

    public function add($idUtilisateur, $titre, $message, $type = 'systeme', $lien = null) {
        return $this->create([
            'id_utilisateur' => $idUtilisateur,
            'titre' => $titre,
            'message' => $message,
            'type_notif' => $type,
            'lien' => $lien
        ]);
    }

    public function getByUser($idUtilisateur, $limit = 20) {
        $sql = "SELECT * FROM notifications WHERE id_utilisateur = :id 
                ORDER BY created_at DESC LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('id', $idUtilisateur, PDO::PARAM_INT);
        $stmt->bindValue('lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUnreadCount($idUtilisateur) {
        $sql = "SELECT COUNT(*) as total FROM notifications 
                WHERE id_utilisateur = :id AND est_lu = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $idUtilisateur]);
        return (int)$stmt->fetch()['total'];
    }

    public function markAllRead($idUtilisateur) {
        $sql = "UPDATE notifications SET est_lu = 1 WHERE id_utilisateur = :id AND est_lu = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $idUtilisateur]);
    }

    public function markRead($idNotification) {
        return $this->update($idNotification, ['est_lu' => 1]);
    }

    /**
     * Notifier tous les utilisateurs ayant un rôle donné
     */
    public function notifyAllByRole($roles, $titre, $message, $type = 'systeme', $lien = null) {
        $roleList = implode(',', array_map(function($r){ return "'" . addslashes($r) . "'"; }, (array)$roles));
        $sql = "SELECT id_utilisateur FROM utilisateurs WHERE role IN ($roleList) AND statut = 'actif'";
        $stmt = $this->db->query($sql);
        $users = $stmt->fetchAll();

        $count = 0;
        foreach ($users as $u) {
            $this->add($u['id_utilisateur'], $titre, $message, $type, $lien);
            $count++;
        }
        return $count;
    }

    /**
     * Récupérer les adresses email des utilisateurs d'un rôle donné
     */
    public function getEmailRecipients($roles) {
        $roleList = implode(',', array_map(function($r){ return "'" . addslashes($r) . "'"; }, (array)$roles));
        $sql = "SELECT email, nom_complet FROM utilisateurs WHERE role IN ($roleList) AND statut = 'actif' AND email IS NOT NULL";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Envoi d'email simple via PHP mail() ou démo
     */
    public function sendEmail($to, $subject, $body) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: GLOBIT <no-reply@" . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'globit.local') . ">\r\n";

        return @mail($to, $subject, $body, $headers);
    }

    /**
     * Générer un lien de notification (email HTML)
     */
    public function emailTemplate($titre, $message, $lien = null) {
        $html = "<!DOCTYPE html><html><body style='font-family:Arial,sans-serif;background:#f4f6f9;padding:20px;'>";
        $html .= "<div style='max-width:600px;margin:auto;background:#fff;border-radius:8px;overflow:hidden;'>";
        $html .= "<div style='background:#007bff;color:#fff;padding:15px 20px;'>";
        $html .= "<h2 style='margin:0;'>{$titre}</h2></div>";
        $html .= "<div style='padding:20px;color:#333;'>" . nl2br($message) . "</div>";
        if ($lien) {
            $html .= "<div style='padding:0 20px 20px;'>";
            $html .= "<a href='{$lien}' style='background:#007bff;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;'>Voir</a>";
            $html .= "</div>";
        }
        $html .= "<div style='padding:15px 20px;background:#f8f9fa;color:#888;font-size:12px;'>";
        $html .= "© " . date('Y') . " GLOBIT - Système d'Information des Ressources Humaines. Développé par " . (defined('AUTHOR_NAME') ? AUTHOR_NAME : 'Ezechiel Itewe Nzukumayi');
        $html .= "</div></div></body></html>";
        return $html;
    }
}
