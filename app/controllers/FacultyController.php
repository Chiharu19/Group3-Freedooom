<?php

class FacultyController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function checkAuth()
    {
        // Check if user is logged in and has the 'faculty' role
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
            // Redirect to the login page if not authorized.
            // Adjust the path if your login page is located elsewhere.
            header('Location: /Group3-Freedooom/login.html');
            exit;
        }
    }

    public function dashboard()
    {
        $this->checkAuth(); // First, check for authorization

        // While the proper way is to use .php views, the current existing views might be .html
        // and designed to be accessed directly. However, to support the routing in index.php:

        // Check if html file exists, otherwise fallback or error
        if (file_exists(__DIR__ . '/../views/facultyViews/dashboard.html')) {
            require __DIR__ . '/../views/facultyViews/dashboard.html';
        } else {
            // Fallback or just let it fail naturally/create empty
            echo "Faculty Dashboard not found.";
        }
    }
}
?>