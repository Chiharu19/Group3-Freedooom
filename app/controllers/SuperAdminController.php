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
        
        // New Stats
        $totalRooms = $this->superAdminModel->getTotalRooms();
        $totalBookings = $this->superAdminModel->getTotalBookings();
        $pendingRequests = $this->superAdminModel->getPendingRequests();

        require __DIR__ . '/../views/superAdminViews/superAdminDashboard.php';
    }

    public function users() {
        $this->checkSession();
        require __DIR__ . '/../views/superAdminViews/superAdminUsers.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: ?page=super-admin-login');
        exit;
    }

    private function checkSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
            header('Location: ?page=super-admin-login');
            exit;
        }
    }
}
