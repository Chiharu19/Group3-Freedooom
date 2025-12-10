<?php

class Auth{

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // ======================
    // LOGIN (EMAIL + PASSWORD)
    // ======================
    public function login($email, $password, $isSuperAdminLogin = false) {
        $user = $this->getUserByEmail($email);

        // checks if email is present
        if (!$user) {
            return ['success' => false, 'message' => 'Email or Password is incorrect'];
        }

        // checks password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Email or Password is incorrect'];
        }

        // checks if its active
        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account inactive'];
        }

        // Block Super Admin from general login unless strictly allowed
        if (!$isSuperAdminLogin && ($user['role'] === 'super_admin' || $user['role'] === 'super')) {
             return ['success' => false, 'message' => 'Email or Password is incorrect'];
        }

        return [
            'success' => true,
            'user' => [
                'id'        => $user['id'],
                'full_name' => $user['full_name'],
                'email'     => $user['email'],
                'role'      => $user['role']
            ]
        ];
    }

    // ======================
    // FETCH USER BY EMAIL
    // ======================
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) return null;

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    // ======================
    // REGISTER (optional)
    // ======================
    public function register($full_name, $email, $password, $role = 'student') {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) return false;

        $stmt->bind_param("ssss", $full_name, $email, $hashed, $role);

        return $stmt->execute();
    }

    // ======================
    // PASSWORD RESET
    // ======================
    public function saveResetToken($email, $token) {
        // Set expiry to 1 hour from now
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $sql = "UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        
        $stmt->bind_param("sss", $token, $expires, $email);
        return $stmt->execute();
    }

    public function verifyResetToken($token) {
        // Fetch user by token only first
        $sql = "SELECT * FROM users WHERE reset_token = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("s", $token);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            // Token not found
            return null;
        }

        // Check expiry in PHP to be safe against DB timezone mismatch
        // reset_expires should be in 'Y-m-d H:i:s' format
        if (strtotime($user['reset_expires']) < time()) {
            // Token expired
            return null;
        }
        
        return $user;
    }

    public function resetPassword($token, $newPassword) {
        $user = $this->verifyResetToken($token);
        if (!$user) return false;

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update password and clear token
        $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("si", $hashed, $user['id']);
        return $stmt->execute();
    }
}