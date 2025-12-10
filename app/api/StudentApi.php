<?php

class StudentApi
{

    private $studentModel;
    private $data;

    public function __construct($data)
    {
        $this->studentModel = new Student();
        $this->data = $data;
    }

    private function checkAuth()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
            echo json_encode(['success' => false, 'message' => 'User not logged in or authorized']);
            exit;
        }
        return $_SESSION['user']['id'];
    }

    public function getRooms()
    {
        $rooms = $this->studentModel->getRooms();
        echo json_encode(['success' => true, 'data' => $rooms]);
    }

    public function getFaculty()
    {
        $faculty = $this->studentModel->getFaculty();
        echo json_encode(['success' => true, 'data' => $faculty]);
    }

    public function submitRequest()
    {
        $studentId = $this->checkAuth();

        $roomId = $this->data['room_id'] ?? '';
        $facultyId = $this->data['faculty_id'] ?? '';
        $date = $this->data['date'] ?? '';
        $startTime = $this->data['start_time'] ?? '';
        $endTime = $this->data['end_time'] ?? '';
        $purpose = $this->data['purpose'] ?? '';

        if (!$roomId || !$facultyId || !$date || !$startTime || !$endTime || !$purpose) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        // Conflict Check
        if (!$this->studentModel->isRoomAvailable($roomId, $date, $startTime, $endTime)) {
             echo json_encode(['success' => false, 'message' => 'Room is already booked for this time slot.']);
             return;
        }

        $result = $this->studentModel->submitRequest($studentId, $roomId, $facultyId, $date, $startTime, $endTime, $purpose);
        echo json_encode($result);
    }

    public function editRequest()
    {
        $studentId = $this->checkAuth();
        
        $requestId = $this->data['request_id'] ?? '';
        $roomId = $this->data['room_id'] ?? '';
        $date = $this->data['date'] ?? '';
        $startTime = $this->data['start_time'] ?? '';
        $endTime = $this->data['end_time'] ?? '';
        $purpose = $this->data['purpose'] ?? '';

        if (!$requestId || !$roomId || !$date || !$startTime || !$endTime || !$purpose) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        // Optional: Conflict check on edit? Yes, ideally.
        if (!$this->studentModel->isRoomAvailable($roomId, $date, $startTime, $endTime)) {
             echo json_encode(['success' => false, 'message' => 'Room is already booked for this time slot.']);
             return;
        }

        $result = $this->studentModel->updateRequest($requestId, $studentId, $roomId, $date, $startTime, $endTime, $purpose);
        echo json_encode($result);
    }

    public function myRequests()
    {
        $studentId = $this->checkAuth();

        $requests = $this->studentModel->getMyRequests($studentId);
        echo json_encode(['success' => true, 'data' => $requests]);
    }

    public function dashboard()
    {
        $studentId = $this->checkAuth();

        // Fetch recent requests (limit 5)
        $requests = $this->studentModel->getRecentRequests($studentId, 5);

        // Derive Notices from requests (e.g., status updates)
        $notices = [];
        foreach ($requests as $r) {
            if ($r['status'] === 'approved') {
                $notices[] = "Your request for {$r['room_name']} on {$r['date']} has been APPROVED.";
            } elseif ($r['status'] === 'denied') {
                $notices[] = "Your request for {$r['room_name']} on {$r['date']} was DENIED.";
            } elseif ($r['status'] === 'pending') {
                $notices[] = "Your request for {$r['room_name']} on {$r['date']} is currently PENDING.";
            }
        }

        if (empty($notices)) {
            $notices[] = "Welcome! You have no recent activity notices.";
        }

        echo json_encode(['success' => true, 'requests' => $requests, 'notices' => $notices]);
    }
    public function getRoomSchedule()
    {
        $roomId = $this->data['room_id'] ?? '';
        $date = $this->data['date'] ?? date('Y-m-d'); // Default to today

        if (!$roomId) {
            echo json_encode(['success' => false, 'message' => 'Room ID required']);
            return;
        }

        $schedule = $this->studentModel->getRoomSchedule($roomId, $date);
        echo json_encode(['success' => true, 'data' => $schedule]);
    }

    public function cancelRequest()
    {
        $studentId = $this->checkAuth();
        $requestId = $this->data['request_id'] ?? '';

        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID required']);
            return;
        }

        $result = $this->studentModel->cancelRequest($requestId, $studentId);
        echo json_encode($result);
    }
}
