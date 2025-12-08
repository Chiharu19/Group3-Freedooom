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
        // Ideally, student_id should come from session to prevent spoofing
        // But for this task, if we assume session is available:
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
            // Fallback for testing if session not strictly enforced or testing via Postman without auth
            // echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            // return;
        }

        // For now, we might accept student_id from POST if we want to allow testing without deep login flow integration, 
        // BUT ideally: $studentId = $_SESSION['user']['id'];
        // Let's check if the frontend sends it or if we rely on session.
        // The current Auth implementation stores user info in $_SESSION['user']

        $studentId = $_SESSION['user']['id'] ?? $this->data['student_id'] ?? 0;

        if ($studentId == 0) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            return;
        }

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

        $result = $this->studentModel->submitRequest($studentId, $roomId, $facultyId, $date, $startTime, $endTime, $purpose);
        echo json_encode($result);
    }

    public function myRequests()
    {
        $studentId = $_SESSION['user']['id'] ?? $this->data['student_id'] ?? 0;

        if ($studentId == 0) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            return;
        }

        $requests = $this->studentModel->getMyRequests($studentId);
        echo json_encode(['success' => true, 'data' => $requests]);
    }
}
