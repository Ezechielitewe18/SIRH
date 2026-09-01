<?php
require_once __DIR__ . '/../models/NotificationModel.php';

class NotificationController {
    private $notificationModel;

    public function __construct() {
        $this->notificationModel = new NotificationModel();
    }

    public function index() {
        $notifications = $this->notificationModel->getByUser($_SESSION['user_id'], 50);
        $this->notificationModel->markAllRead($_SESSION['user_id']);
        require __DIR__ . '/../views/notifications/index.php';
    }

    public function unreadCount() {
        header('Content-Type: application/json');
        echo json_encode(['count' => $this->notificationModel->getUnreadCount($_SESSION['user_id'])]);
    }

    public function markRead($id) {
        $this->notificationModel->markRead($id);
        header('Location: ' . APP_URL . '/notifications');
        exit;
    }
}
