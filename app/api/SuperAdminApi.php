<?php

class SuperAdminApi {

    private $superAdminModel;
    private $data;
    private $auth;

    public function __construct($data) {
        $this->superAdminModel = new SuperAdmin();
        $this->auth = new Auth();
        $this->data = $data;
    }

    public function login() {
        $email = $this->data['email'] ?? '';
        $password = $this->data['password'] ?? '';

        if (!$email || !$password) {
            echo json_encode(['success' => false, 'error' => 'Missing credentials']);
            return;
        }

        $result = $this->auth->login($email, $password, true); // true = allow super admin

        if ($result['success']) {
            if ($result['user']['role'] === 'super' || $result['user']['role'] === 'super_admin') {
                $_SESSION['user'] = $result['user'];
                // Normalize session role to what code expects if needed, or just allow 'super'
                $_SESSION['user']['role'] = 'super_admin'; 
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unauthorized Access']);
            }
        } else {
            echo json_encode($result);
        }
    }

    public function getAdminsList() {
        $admins = $this->superAdminModel->getAllAdmins();
        echo json_encode(['success' => true, 'data' => $admins]);
    }

    public function addAdmin() {
        $name = $this->data['full_name'] ?? '';
        $email = $this->data['email'] ?? '';
        $password = $this->data['password'] ?? '';

        if (!$name || !$email || !$password) {
            echo json_encode(['success' => false, 'message' => 'Missing fields']);
            return;
        }

        $result = $this->superAdminModel->addAdmin($name, $email, $password);
        
        if ($result['success']) {
             $emailService = new EmailService();
             $emailService->sendAccountCreatedNotification($email, $name, $password);
        }

        echo json_encode($result);
    }

    public function editAdmin() {
        $id = $this->data['id'] ?? '';
        $name = $this->data['full_name'] ?? '';
        $email = $this->data['email'] ?? '';

        if (!$id || !$name || !$email) {
            echo json_encode(['success' => false, 'message' => 'Missing fields']);
            return;
        }

        $result = $this->superAdminModel->editAdmin($id, $name, $email);
        echo json_encode($result);
    }

    public function toggleStatus() {
        $id = $this->data['id'] ?? '';
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Missing ID']);
            return;
        }

        $result = $this->superAdminModel->toggleStatus($id);
        echo json_encode($result);
    }
}
