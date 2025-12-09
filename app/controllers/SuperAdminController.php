<?php

class SuperAdminController {

    private $superAdminModel;

    public function __construct() {
        $this->superAdminModel = new SuperAdmin();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Separate View file entirely
    public function loginView() {
        // If already logged in as super admin, redirect to dashboard
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'super_admin') {
            header('Location: ?page=super-admin');
            exit;
        }
        require __DIR__ . '/../views/superAdminLogin.php';
    }

    public function dashboard() {
        $this->checkSession();

        $totalAdmins = $this->superAdminModel->getTotalAdmins();
        $totalUsers = $this->superAdminModel->getTotalUsers();

        require __DIR__ . '/../views/superAdminViews/superAdminDashboard.php';
    }

    public function users() {
        $this->checkSession();
        require __DIR__ . '/../views/superAdminViews/superAdminUsers.php';
    }

    private function checkSession() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
            header('Location: ?page=super-admin-login');
            exit;
        }
    }
}
