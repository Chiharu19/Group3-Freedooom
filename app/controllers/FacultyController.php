<?php

class FacultyController
{

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'faculty') {
            header('Location: index.php?page=login');
            exit;
        }
    }

    public function dashboard()
    {
        require __DIR__ . '/../views/facultyViews/faculty_dashboard.php';
    }

    public function rooms()
    {
        require __DIR__ . '/../views/facultyViews/room_availability.php';
    }

    public function bookRoom()
    {
        require __DIR__ . '/../views/facultyViews/book_room.php';
    }

    public function myBookings()
    {
        require __DIR__ . '/../views/facultyViews/my_bookings.php';
    }

    public function studentRequests()
    {
        require __DIR__ . '/../views/facultyViews/student_requests.php';
    }
}
?>