<?php

class Student
{

    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // ==========================================
    // 1. Get All Rooms (for dropdown)
    // ==========================================
    public function getRooms()
    {
        $sql = "SELECT id, room_name, building, capacity FROM rooms WHERE status = 'available' ORDER BY room_name ASC";
        $result = $this->conn->query($sql);

        $rooms = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rooms[] = $row;
            }
        }
        return $rooms;
    }

    // ==========================================
    // 2. Get All Faculty (for dropdown)
    // ==========================================
    public function getFaculty()
    {
        $sql = "SELECT id, full_name, email FROM users WHERE role = 'faculty' ORDER BY full_name ASC";
        $result = $this->conn->query($sql);

        $faculty = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $faculty[] = $row;
            }
        }
        return $faculty;
    }

    // ==========================================
    // 3. Submit Booking Request
    // ==========================================
    public function submitRequest($studentId, $roomId, $facultyId, $date, $startTime, $endTime, $purpose)
    {
        // Basic validation: Check if room is already booked/requested for overlapping time? 
        // For now, we will just insert as 'pending'. Admin/Faculty handles approval/conflict.

        $sql = "INSERT INTO student_booking_requests 
                (student_id, room_id, faculty_id, date, start_time, end_time, purpose, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Database prepare error: ' . $this->conn->error];
        }

        $stmt->bind_param("iiissss", $studentId, $roomId, $facultyId, $date, $startTime, $endTime, $purpose);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Request submitted successfully'];
        } else {
            return ['success' => false, 'message' => 'Execute error: ' . $stmt->error];
        }
    }

    // ==========================================
    // 4. Get My Requests
    // ==========================================
    public function getMyRequests($studentId)
    {
        $sql = "SELECT r.*, rm.room_name, u.full_name as faculty_name 
                FROM student_booking_requests r
                JOIN rooms rm ON r.room_id = rm.id
                LEFT JOIN users u ON r.faculty_id = u.id
                WHERE r.student_id = ?
                ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();

        $requests = [];
        while ($row = $result->fetch_assoc()) {
            // Format times for display if needed
            $row['start_time_formatted'] = date("g:i A", strtotime($row['start_time']));
            $row['end_time_formatted'] = date("g:i A", strtotime($row['end_time']));
            $requests[] = $row;
        }
        return $requests;
    }
}
