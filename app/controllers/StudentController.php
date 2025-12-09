<?php

class StudentController
{

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
            header('Location: index.php?page=login');
            exit;
        }
    }

    public function dashboard()
    {
        // While the proper way is to use .php views, the current existing student views are .html
        // and designed to be accessed directly. However, to support the routing in index.php:
        require __DIR__ . '/../views/studentViews/dashboard.html';
    }

    public function rooms()
    {
        require __DIR__ . '/../views/studentViews/room_availability.html';
    }

    public function submitRequest()
    {
        require __DIR__ . '/../views/studentViews/submit_request.html';
    }

    public function myRequests()
    {
        require __DIR__ . '/../views/studentViews/my_requests.html';
    }
}
