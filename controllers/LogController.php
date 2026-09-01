<?php
require_once __DIR__ . '/../models/LogModel.php';

class LogController {
    private $logModel;

    public function __construct() {
        $this->logModel = new LogModel();
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $module = $_GET['module'] ?? '';

        if (!empty($search)) {
            $logs = $this->logModel->search($search, 500);
        } elseif (!empty($module)) {
            $logs = $this->logModel->findByModule($module, 500);
        } else {
            $logs = $this->logModel->findAllWithUser(500);
        }

        $modulesCounter = $this->logModel->countActionsByModule();
        require __DIR__ . '/../views/journal/index.php';
    }

    public function clear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sql = "TRUNCATE TABLE journal_activite";
            (Database::getInstance()->getConnection())->exec($sql);
            $_SESSION['success'] = 'Journal d\'activité vidé';
        }
        header('Location: ' . APP_URL . '/journal');
        exit;
    }
}
