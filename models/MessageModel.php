<?php
require_once __DIR__ . '/../core/Model.php';

class MessageModel extends Model {
    protected $table = 'messages';
    protected $primaryKey = 'id_message';

    public function getOrCreateConversation($userA, $userB) {
        $ids = [(int)$userA, (int)$userB];
        sort($ids);
        $a = $ids[0];
        $b = $ids[1];

        $sql = "SELECT id_conversation FROM conversations
                WHERE id_utilisateur_a = :a AND id_utilisateur_b = :b";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['a' => $a, 'b' => $b]);
        $conv = $stmt->fetch();

        if ($conv) {
            return (int)$conv['id_conversation'];
        }

        $sql = "INSERT INTO conversations (id_utilisateur_a, id_utilisateur_b) VALUES (:a, :b)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['a' => $a, 'b' => $b]);
        return (int)$this->db->lastInsertId();
    }

    public function envoyer($idConversation, $idExpediteur, $contenu) {
        $sql = "INSERT INTO messages (id_conversation, id_expediteur, contenu) VALUES (:conversation, :expediteur, :contenu)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'conversation' => $idConversation,
            'expediteur' => $idExpediteur,
            'contenu' => $contenu
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function listConversations($idUtilisateur) {
        $sql = "SELECT c.id_conversation,
                       CASE WHEN c.id_utilisateur_a = :user THEN c.id_utilisateur_b ELSE c.id_utilisateur_a END AS id_interlocuteur,
                       CASE WHEN c.id_utilisateur_a = :user2 THEN u_b.nom_complet ELSE u_a.nom_complet END AS nom_interlocuteur,
                       (SELECT contenu FROM messages m
                        WHERE m.id_conversation = c.id_conversation
                        ORDER BY m.id_message DESC LIMIT 1) AS dernier_message,
                       (SELECT created_at FROM messages m
                        WHERE m.id_conversation = c.id_conversation
                        ORDER BY m.id_message DESC LIMIT 1) AS dernier_date,
                       (SELECT COUNT(*) FROM messages m
                        WHERE m.id_conversation = c.id_conversation
                          AND m.id_expediteur != :user3
                          AND m.est_lu = 0) AS non_lus
                FROM conversations c
                JOIN utilisateurs u_a ON c.id_utilisateur_a = u_a.id_utilisateur
                JOIN utilisateurs u_b ON c.id_utilisateur_b = u_b.id_utilisateur
                WHERE c.id_utilisateur_a = :user4 OR c.id_utilisateur_b = :user5
                ORDER BY dernier_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user' => $idUtilisateur,
            'user2' => $idUtilisateur,
            'user3' => $idUtilisateur,
            'user4' => $idUtilisateur,
            'user5' => $idUtilisateur
        ]);
        return $stmt->fetchAll();
    }

    public function getMessages($idConversation, $idUtilisateur) {
        $sql = "SELECT m.*, u.nom_complet
                FROM messages m
                JOIN utilisateurs u ON m.id_expediteur = u.id_utilisateur
                WHERE m.id_conversation = :conv
                ORDER BY m.id_message ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['conv' => $idConversation]);
        $messages = $stmt->fetchAll();

        $sql = "UPDATE messages SET est_lu = 1
                WHERE id_conversation = :conv AND id_expediteur != :user AND est_lu = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['conv' => $idConversation, 'user' => $idUtilisateur]);

        return $messages;
    }

    public function conversationBelongsToUser($idConversation, $idUtilisateur) {
        $sql = "SELECT id_conversation FROM conversations
                WHERE id_conversation = :conv AND (id_utilisateur_a = :ua OR id_utilisateur_b = :ub)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['conv' => $idConversation, 'ua' => $idUtilisateur, 'ub' => $idUtilisateur]);
        return (bool)$stmt->fetch();
    }

    public function getInterlocuteur($idConversation, $idUtilisateur) {
        $sql = "SELECT CASE WHEN id_utilisateur_a = :user THEN id_utilisateur_b ELSE id_utilisateur_a END AS id_interlocuteur
                FROM conversations WHERE id_conversation = :conv";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user' => $idUtilisateur, 'conv' => $idConversation]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id_interlocuteur'] : 0;
    }

    public function getUnreadCount($idUtilisateur) {
        $sql = "SELECT COUNT(*) AS total FROM messages m
                JOIN conversations c ON m.id_conversation = c.id_conversation
                WHERE (c.id_utilisateur_a = :ua OR c.id_utilisateur_b = :ub)
                  AND m.id_expediteur != :me
                  AND m.est_lu = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ua' => $idUtilisateur, 'ub' => $idUtilisateur, 'me' => $idUtilisateur]);
        $row = $stmt->fetch();
        return (int)$row['total'];
    }

    public function listPersonnel($idUtilisateur, $role) {
        if ($role === 'employe') {
            $sql = "SELECT u.id_utilisateur, u.nom_complet, u.role, u.photo
                    FROM utilisateurs u
                    WHERE u.id_utilisateur != :user AND u.statut = 'actif' AND u.role IN ('admin', 'rh')
                    ORDER BY u.nom_complet ASC";
        } else {
            $sql = "SELECT u.id_utilisateur, u.nom_complet, u.role, u.photo
                    FROM utilisateurs u
                    WHERE u.id_utilisateur != :user AND u.statut = 'actif'
                    ORDER BY u.nom_complet ASC";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user' => $idUtilisateur]);
        return $stmt->fetchAll();
    }

    public function listAnnonces() {
        $sql = "SELECT a.*, u.nom_complet
                FROM annonces a
                JOIN utilisateurs u ON a.id_expediteur = u.id_utilisateur
                ORDER BY a.id_annonce DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function creerAnnonce($idExpediteur, $titre, $contenu) {
        $sql = "INSERT INTO annonces (id_expediteur, titre, contenu) VALUES (:exp, :titre, :contenu)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'exp' => $idExpediteur,
            'titre' => $titre,
            'contenu' => $contenu
        ]);
        return (int)$this->db->lastInsertId();
    }
}