<?php

class AuthApi {

    public function login() {
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // $userModel = new User();
        // $user = $userModel->checkLogin($email, $password);

        // if ($user) {
        //     $_SESSION['user'] = $user; // store session
        //     return ['success' => true];
        // }

        // return ['success' => false, 'error' => 'Invalid email or password'];
        return ['success' => false];
    }

}
