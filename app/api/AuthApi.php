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

}
