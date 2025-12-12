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

    public function checkRateLimit($ip, $action) {
        $limit = 3; // Max attempts
        $window = 15; // Minutes

        $sql = "SELECT id, attempt_count, last_attempt FROM rate_limits WHERE ip_address = ? AND action = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $ip, $action);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res) {
            $lastAttempt = strtotime($res['last_attempt']);
            $timeDiff = (time() - $lastAttempt) / 60; // in minutes

            if ($timeDiff > $window) {
                // Reset count
                $upd = $this->conn->prepare("UPDATE rate_limits SET attempt_count = 1, last_attempt = NOW() WHERE id = ?");
                $upd->bind_param("i", $res['id']);
                $upd->execute();
                return true; // Allowed
            } else {
                if ($res['attempt_count'] >= $limit) {
                    return false; // Blocked
                } else {
                    // Increment
                    $upd = $this->conn->prepare("UPDATE rate_limits SET attempt_count = attempt_count + 1, last_attempt = NOW() WHERE id = ?");
                    $upd->bind_param("i", $res['id']);
                    $upd->execute();
                    return true; // Allowed
                }
            }
        } else {
            // New record
            $ins = $this->conn->prepare("INSERT INTO rate_limits (ip_address, action) VALUES (?, ?)");
            $ins->bind_param("ss", $ip, $action);
            $ins->execute();
            return true; // Allowed
        }
    }
}