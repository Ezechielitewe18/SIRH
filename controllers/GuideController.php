<?php
require_once __DIR__ . '/../models/ServiceModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';

class GuideController {
    public function index() {
        $stats = ['services' => 0, 'employes' => 0];
        try {
            $stats['services'] = (int) (new ServiceModel())->getTotalServices();
        } catch (Exception $e) {}
        try {
            $stats['employes'] = (int) (new EmployeeModel())->getActiveCount();
        } catch (Exception $e) {}

        $pageTitle = 'Guide d\'utilisation';
        require __DIR__ . '/../views/guide/index.php';
    }
}
