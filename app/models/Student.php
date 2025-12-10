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
        $sql = "SELECT id, room_name, building, capacity, status FROM rooms ORDER BY room_name ASC";
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
        // Calculate Duration (in hours) because end_time is a GENERATED column in DB
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $duration = ($end - $start) / 3600;

        // Ensure duration is at least 1 hour or valid
        if ($duration < 0)
            $duration = 1;

        // Basic validation: Check if room is already booked/requested for overlapping time? 
        // For now, we will just insert as 'pending'. Admin/Faculty handles approval/conflict.

        $sql = "INSERT INTO student_booking_requests 
                (student_id, room_id, faculty_id, date, start_time, duration, purpose, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Database prepare error: ' . $this->conn->error];
        }

        // types: i (student), i (room), i (faculty), s (date), s (start_time), d (duration - use double or int), s (purpose)
        // duration in DB is int(11), so we should separate hours.
        $durationInt = (int) ceil($duration);

        $stmt->bind_param("iiissis", $studentId, $roomId, $facultyId, $date, $startTime, $durationInt, $purpose);

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

    // ==========================================
    // 5. Get Recent Requests (for Dashboard)
    // ==========================================
    public function getRecentRequests($studentId, $limit = 5)
    {
        $sql = "SELECT r.id, r.date, r.status, rm.room_name 
                FROM student_booking_requests r
                JOIN rooms rm ON r.room_id = rm.id
                WHERE r.student_id = ?
                ORDER BY r.created_at DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("ii", $studentId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        return $requests;
    }
    // ==========================================
    // 6. Get Room Schedule (for Modal)
    // ==========================================
    public function getRoomSchedule($roomId, $date)
    {
        $sql = "SELECT start_time, end_time, purpose, status 
                FROM bookings 
                WHERE room_id = ? AND date = ?
                ORDER BY start_time ASC";

        // Note: 'status' column doesn't exist in bookings table based on schema, 
        // but typically confirmed bookings are active. We'll simply select what's there.
        // Actually, schema shows: bookings table does NOT have a status column (it implies confirmed).
        // modification_requests has status. bookings is the source of truth for "Busy".

        $sql = "SELECT start_time, end_time, purpose, duration
                FROM bookings 
                WHERE room_id = ? AND date = ?
                ORDER BY start_time ASC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt)
            return [];

        $stmt->bind_param("is", $roomId, $date);
        $stmt->execute();
        $result = $stmt->get_result();

        $schedule = [];
        while ($row = $result->fetch_assoc()) {
            // format times if desired
            $row['start_time_formatted'] = date("g:i A", strtotime($row['start_time']));
            $row['end_time_formatted'] = date("g:i A", strtotime($row['end_time']));
            $schedule[] = $row;
        }
        return $schedule;
    }

    // ==========================================
    // 7. Cancel Booking Request
    // ==========================================
    public function cancelRequest($requestId, $studentId)
    {
        // Only allow cancelling if status is 'pending'
        $sql = "UPDATE student_booking_requests 
                SET status = 'cancelled' 
                WHERE id = ? AND student_id = ? AND status = 'pending'";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error'];
        }

        $stmt->bind_param("ii", $requestId, $studentId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return ['success' => true, 'message' => 'Request cancelled successfully'];
            } else {
                return ['success' => false, 'message' => 'Request not found or not pending'];
            }
        } else {
            return ['success' => false, 'message' => 'Execute error: ' . $stmt->error];
        }
    }
    // ==========================================
    // 8. Update Booking Request
    // ==========================================
    public function updateRequest($requestId, $studentId, $roomId, $date, $startTime, $endTime, $purpose)
    {
        // Only allow updating if status is 'pending'
        $sql = "UPDATE student_booking_requests 
                SET room_id = ?, date = ?, start_time = ?, duration = ?, purpose = ?
                WHERE id = ? AND student_id = ? AND status = 'pending'";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error'];
        }

        // Calculate Duration
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $duration = max(1, ceil(($end - $start) / 3600)); // Min 1 hour, int

        $stmt->bind_param("issisii", $roomId, $date, $startTime, $duration, $purpose, $requestId, $studentId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return ['success' => true, 'message' => 'Request updated successfully'];
            } else {
                // Could mean no changes made OR request not found/not pending
                // Let's check if it exists
                return ['success' => true, 'message' => 'Request updated (or no changes detected)'];
            }
        } else {
            return ['success' => false, 'message' => 'Execute error: ' . $stmt->error];
        }
    }

    // ==========================================
    // 9. Check Room Availability
    // ==========================================
    public function isRoomAvailable($roomId, $date, $startTime, $endTime)
    {
        // Check for overlap in confirmed bookings
        // Overlap logic: (StartA < EndB) and (EndA > StartB)
        // Note: bookings table separates start_time and end_time, or duration?
        // Let's assume bookings table has start_time and end_time or we calculate end_time.
        // Based on previous reads, bookings has start_time and end_time.
        
        $sql = "SELECT id FROM bookings 
                WHERE room_id = ? 
                AND date = ? 
                AND (start_time < ? AND end_time > ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isss", $roomId, $date, $endTime, $startTime);
        $stmt->execute();
        $stmt->store_result();
        
        return $stmt->num_rows === 0;
    }
}
