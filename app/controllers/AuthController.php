<?php

class AuthController {

    public function login() {
        
        // Any data you want to use in the view/page will be defined here

        // Load the view
        require __DIR__ . '/../views/login.php';
    }

}
