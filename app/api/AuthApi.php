<?php

class AuthApi {

    public function __construct($data) {
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

        $result = $this->auth->login($email, $password);

        echo json_encode($result);
    }

}
