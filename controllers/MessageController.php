<?php
require_once __DIR__ . '/../models/MessageModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../models/LogModel.php';

class MessageController {
    private $messageModel;
    private $notificationModel;
    private $logModel;

    public function __construct() {
        $this->messageModel = new MessageModel();
        $this->notificationModel = new NotificationModel();
        $this->logModel = new LogModel();
    }

    public function index() {
        $conversations = $this->messageModel->listConversations($_SESSION['user_id']);
        $annonces = $this->messageModel->listAnnonces();
        require __DIR__ . '/../views/messages/index.php';
    }

    public function nouveau() {
        $personnel = $this->messageModel->listPersonnel($_SESSION['user_id'], $_SESSION['user_role']);
        require __DIR__ . '/../views/messages/nouveau.php';
    }

    public function creer() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/messages');
            exit;
        }
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Session expirée, réessayez';
            header('Location: ' . APP_URL . '/messages/nouveau');
            exit;
        }

        $idDestinataire = (int)($_POST['id_destinataire'] ?? 0);
        $contenu = trim($_POST['contenu'] ?? '');

        if ($idDestinataire <= 0) {
            $_SESSION['error'] = 'Sélectionnez un destinataire';
            header('Location: ' . APP_URL . '/messages/nouveau');
            exit;
        }
        if ($idDestinataire === (int)$_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous ne pouvez pas vous écrire à vous-même';
            header('Location: ' . APP_URL . '/messages/nouveau');
            exit;
        }

        $allowed = $this->isContactAllowed($idDestinataire);
        if (!$allowed) {
            $_SESSION['error'] = 'Destinataire invalide';
            header('Location: ' . APP_URL . '/messages/nouveau');
            exit;
        }

        $this->sendMessage($_SESSION['user_id'], $idDestinataire, $contenu);
        header('Location: ' . APP_URL . '/messages');
        exit;
    }

    public function conversation($id) {
        $idConversation = (int)$id;
        if (!$this->messageModel->conversationBelongsToUser($idConversation, $_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/messages');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Session expirée, réessayez';
                header('Location: ' . APP_URL . '/messages/conversation/' . $idConversation);
                exit;
            }
            $contenu = trim($_POST['contenu'] ?? '');
            $idDestinataire = $this->messageModel->getInterlocuteur($idConversation, $_SESSION['user_id']);
            $this->sendMessage($_SESSION['user_id'], $idDestinataire, $contenu);
            header('Location: ' . APP_URL . '/messages/conversation/' . $idConversation);
            exit;
        }

        $messages = $this->messageModel->getMessages($idConversation, $_SESSION['user_id']);
        $interlocuteur = $this->messageModel->getInterlocuteur($idConversation, $_SESSION['user_id']);
        $nomInterlocuteur = $this->getUserName($interlocuteur);
        require __DIR__ . '/../views/messages/conversation.php';
    }

    public function annonces() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Session expirée, réessayez';
                header('Location: ' . APP_URL . '/messages/annonces');
                exit;
            }
            if (!in_array($_SESSION['user_role'], ['admin', 'rh'])) {
                header('Location: ' . APP_URL . '/messages');
                exit;
            }

            $titre = trim($_POST['titre'] ?? 'Annonce');
            $contenu = trim($_POST['contenu'] ?? '');
            if ($contenu !== '') {
                $this->messageModel->creerAnnonce($_SESSION['user_id'], $titre, $contenu);
                $this->notificationModel->notifyAllByRole(
                    ['employe'],
                    '📢 Nouvelle annonce',
                    $titre,
                    'systeme',
                    APP_URL . '/messages'
                );
                $this->logModel->log('Annonce', 'Annonce publiée : ' . $titre, 'messages');
                $_SESSION['success'] = 'Annonce publiée';
            } else {
                $_SESSION['error'] = 'Le contenu de l\'annonce est requis';
            }
            header('Location: ' . APP_URL . '/messages/annonces');
            exit;
        }

        $annonces = $this->messageModel->listAnnonces();
        require __DIR__ . '/../views/messages/annonces.php';
    }

    public function unreadCount() {
        header('Content-Type: application/json');
        echo json_encode(['count' => $this->messageModel->getUnreadCount($_SESSION['user_id'])]);
    }

    private function sendMessage($idExpediteur, $idDestinataire, $contenu) {
        $contenu = trim($contenu);
        if ($contenu === '') return;

        $idConversation = $this->messageModel->getOrCreateConversation($idExpediteur, $idDestinataire);
        $this->messageModel->envoyer($idConversation, $idExpediteur, $contenu);
        $this->notificationModel->add(
            $idDestinataire,
            'Nouveau message',
            'Vous avez reçu un message de ' . $_SESSION['user_name'],
            'message',
            APP_URL . '/messages/conversation/' . $idConversation
        );
    }

    private function isContactAllowed($idDestinataire) {
        if ($_SESSION['user_role'] !== 'employe') {
            return true;
        }
        $personnel = $this->messageModel->listPersonnel($_SESSION['user_id'], $_SESSION['user_role']);
        foreach ($personnel as $p) {
            if ((int)$p['id_utilisateur'] === $idDestinataire) {
                return true;
            }
        }
        return false;
    }

    private function getUserName($idUtilisateur) {
        $sql = "SELECT nom_complet FROM utilisateurs WHERE id_utilisateur = :id";
        $stmt = (Database::getInstance()->getConnection())->prepare($sql);
        $stmt->execute(['id' => $idUtilisateur]);
        $row = $stmt->fetch();
        return $row ? $row['nom_complet'] : 'Utilisateur';
    }
}