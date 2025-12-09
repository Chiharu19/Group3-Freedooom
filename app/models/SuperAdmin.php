<?php

class SuperAdmin {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // ------------------------------------------
    // 1. Total Admins
    // ------------------------------------------
    public function getTotalAdmins() {
        $sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'admin'";
        $res = $this->conn->query($sql);
        return $res->fetch_assoc()['total'];
    }

    // ------------------------------------------
    // 2. All Users Count (for overview)
    // ------------------------------------------
    public function getTotalUsers() {
        $sql = "SELECT COUNT(*) AS total FROM users";
        $res = $this->conn->query($sql);
        return $res->fetch_assoc()['total'];
    }

    // ------------------------------------------
    // 3. Get All Admins
    // ------------------------------------------
    public function getAllAdmins() {
        $sql = "SELECT * FROM users WHERE role = 'admin' ORDER BY full_name ASC";
        $res = $this->conn->query($sql);
        
        $data = [];
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // ------------------------------------------
    // 4. Add Admin
    // ------------------------------------------
    public function addAdmin($fullName, $email, $password) {
        // Check if email exists
        $check = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            return ["success" => false, "message" => "Email already exists"];
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $role = 'admin';
        $status = 'active';

        $sql = "INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $fullName, $email, $hashed, $role, $status);

        if ($stmt->execute()) {
            return ["success" => true];
        }

        return ["success" => false, "message" => $stmt->error];
    }

    // ------------------------------------------
    // 5. Edit Admin
    // ------------------------------------------
    public function editAdmin($id, $fullName, $email) {
        // Check if email exists for OTHER users
        $check = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            return ["success" => false, "message" => "Email already exists"];
        }

        $sql = "UPDATE users SET full_name = ?, email = ? WHERE id = ? AND role = 'admin'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $fullName, $email, $id);

        if ($stmt->execute()) {
            return ["success" => true];
        }

        return ["success" => false, "message" => $stmt->error];
    }

    // ------------------------------------------
    // 6. Toggle Status (Active/Inactive)
    // ------------------------------------------
    public function toggleStatus($id) {
        // Get current status
        $get = $this->conn->prepare("SELECT status FROM users WHERE id = ? AND role = 'admin'");
        $get->bind_param("i", $id);
        $get->execute();
        $res = $get->get_result();
        
        if ($res->num_rows === 0) {
            return ["success" => false, "message" => "Admin not found"];
        }

        $currentStatus = $res->fetch_assoc()['status'];
        $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

        $update = $this->conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $update->bind_param("si", $newStatus, $id);

        if ($update->execute()) {
            return ["success" => true, "new_status" => $newStatus];
        }

        return ["success" => false, "message" => $update->error];
    }
}
