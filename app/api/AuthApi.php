<?php

class AuthApi
{
    private $auth;
    private $data;

    public function __construct($data)
    {
        $this->auth = new Auth();
        $this->data = $data;
    }

    public function login()
    {

        $email = $this->data['email'] ?? '';
        $password = $this->data['password'] ?? '';

        if (!$email || !$password) {
            echo json_encode(['success' => false, 'error' => 'Missing credentials']);
            return;
        }

        $result = $this->auth->login($email, $password);

        if ($result['success']) {
            $_SESSION['user'] = $result['user'];
        }

        echo json_encode($result);
    }

    public function requestReset() {
        $email = $this->data['email'] ?? '';
        if (!$email) {
            echo json_encode(['success' => false, 'error' => 'Email is required']);
            return;
        }

        // Rate Limiting
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (!$this->auth->checkRateLimit($ip, 'password_reset')) {
             echo json_encode(['success' => false, 'error' => 'Too many requests. Please try again later.']);
             return;
        }

        $user = $this->auth->getUserByEmail($email);
        if (!$user) {
            // For security, do not reveal if email exists. BUT for this internal school projects, maybe useful?
            // stick to standard: return success but don't send if not found. Or return error if user logic requires it.
            // Let's return error for better UX in this context.
            echo json_encode(['success' => false, 'error' => 'Email not found']);
            return;
        }

        $token = bin2hex(random_bytes(32));
        if ($this->auth->saveResetToken($email, $token)) {
            // Send email
            if (class_exists('EmailService')) {
                $emailService = new EmailService();
                $emailService->sendPasswordResetLink($email, $token);
            }
            echo json_encode(['success' => true, 'message' => 'Password reset link sent to your email']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to generate token']);
        }
    }

    public function resetPassword() {
        $token = $this->data['token'] ?? '';
        $newPassword = $this->data['password'] ?? '';

        if (!$token || !$newPassword) {
            echo json_encode(['success' => false, 'error' => 'Missing token or password']);
            return;
        }

        if ($this->auth->resetPassword($token, $newPassword)) {
            echo json_encode(['success' => true, 'message' => 'Password has been reset successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
        }
    }

}
