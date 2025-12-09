<?php

class AdminController {

    private $adminModel;

    public function __construct() {
        $this->adminModel = new Admin();
    }

    public function dashboard() {
        
        // $adminModel = new Admin();
        $totalRooms = $this->adminModel->getTotalRooms();
        $totalFacultyStaff = $this->adminModel->getTotalFaculty();
        $totalPendingStudentRequests = $this->adminModel->getPendingStudentRequests();
        $totalTodaysBookings = $this->adminModel->getTodaysBookings();

        // Load the view
        require __DIR__ . '/../views/adminViews/adminDashboard.php';
    }

    public function rooms() {
        
        $allRoomsList = $this->adminModel->getAllRooms();

        // Load the view
        require __DIR__ . '/../views/adminViews/adminRooms.php';
    }

    public function schedules() {
        
        $allRoomsList = $this->adminModel->getAllRooms();
        $allFacultyUserList = $this->adminModel->getAllUsers("faculty");
        // Load the view
        require __DIR__ . '/../views/adminViews/adminSchedules.php';
    }

    public function users() {
        
        // Any data you want to use in the view/page will be defined here

        // Load the view
        require __DIR__ . '/../views/adminViews/adminUsers.php';
    }

}
