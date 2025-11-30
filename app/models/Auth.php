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
    public function login($email, $password) {
        $user = $this->getUserByEmail($email);

        // checks if email is present
        if (!$user) {
            return ['success' => false, 'message' => 'Email not found'];
        }

        // checks password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Incorrect password'];
        }

        // checks if its active
        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account inactive'];
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
    private function getUserByEmail($email) {
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
}