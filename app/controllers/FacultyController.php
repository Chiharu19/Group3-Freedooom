<?php

class FacultyController
{

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Basic auth check
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
            // header('Location: index.php?page=login');
            // exit;
            // For dev/demo if login is bypassed, comment out. But should be active.
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
