<?php

class AuthController
{

    public function login()
    {

        // Any data you want to use in the view/page will be defined here

        // Load the view
        require __DIR__ . '/../views/login.php';
    }

    public function landing()
    {
        require __DIR__ . '/../views/landing.php';
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }

}
