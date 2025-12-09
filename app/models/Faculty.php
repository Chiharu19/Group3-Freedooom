<?php

class Faculty
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // 1. Get Dashboard Stats
    public function getDashboardStats($facultyId)
    {
        $today = date('Y-m-d');
        
        // Count bookings for today
        $sqlBookings = "SELECT COUNT(*) as count FROM bookings WHERE user_id = ? AND date = ?";
        $stmt = $this->conn->prepare($sqlBookings);
        $stmt->bind_param("is", $facultyId, $today); // Assuming user_id is integer facultyId
        $stmt->execute();
        $resBookings = $stmt->get_result()->fetch_assoc();
        $todayCount = $resBookings['count'] ?? 0;

        // Count pending assigned requests
        // Assuming faculty_id in student_booking_requests refers to the faculty assigned
        $sqlRequests = "SELECT COUNT(*) as count FROM student_booking_requests WHERE faculty_id = ? AND status = 'pending'";
        $stmt = $this->conn->prepare($sqlRequests);
        $stmt->bind_param("i", $facultyId);
        $stmt->execute();
        $resRequests = $stmt->get_result()->fetch_assoc();
        $pendingCount = $resRequests['count'] ?? 0;

        // Get today's bookings list (brief)
        $sqlTodayList = "SELECT b.start_time, b.end_time, r.room_name 
                         FROM bookings b 
                         JOIN rooms r ON b.room_id = r.id 
                         WHERE b.user_id = ? AND b.date = ? 
                         ORDER BY b.start_time ASC";
        $stmt = $this->conn->prepare($sqlTodayList);
        $stmt->bind_param("is", $facultyId, $today);
        $stmt->execute();
        $todayList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'today_count' => $todayCount,
            'pending_count' => $pendingCount,
            'today_list' => $todayList
        ];
    }

    // 2. Get My Bookings
    public function getMyBookings($facultyId)
    {
        $sql = "SELECT b.id, b.date, b.start_time, b.end_time, b.purpose, b.status, r.room_name 
                FROM bookings b
                JOIN rooms r ON b.room_id = r.id
                WHERE b.user_id = ?
                ORDER BY b.date DESC, b.start_time ASC";
        
        // Note: 'status' column might not exist in bookings table based on previous exploration. 
        // If not, we might assume 'Confirmed' or check schema. 
        // For now, I'll select what's likely there. If status is missing in DB, we'll hardcode 'Confirmed'.
        
        // Let's check columns in bookings table if possible. 
        // Assuming user_id exists.

        $stmt = $this->conn->prepare($sql);
        // If status column error occurs, we fix it.
        if (!$stmt) {
             // Fallback if status doesn't exist
             $sql = "SELECT b.id, b.date, b.start_time, b.end_time, b.purpose, r.room_name 
                FROM bookings b
                JOIN rooms r ON b.room_id = r.id
                WHERE b.user_id = ?
                ORDER BY b.date DESC, b.start_time ASC";
             $stmt = $this->conn->prepare($sql);
        }

        $stmt->bind_param("i", $facultyId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while($row = $result->fetch_assoc()){
            // If status is missing, add it
            if(!isset($row['status'])) $row['status'] = 'Confirmed';
            $data[] = $row;
        }
        return $data;
    }

    // 3. Get Assigned Student Requests
    public function getAssignedRequests($facultyId)
    {
        $sql = "SELECT r.id, r.date, r.start_time, r.duration, r.purpose, r.status, r.comments, 
                       rm.room_name, u.full_name as student_name
                FROM student_booking_requests r
                JOIN rooms rm ON r.room_id = rm.id
                JOIN users u ON r.student_id = u.id
                WHERE r.faculty_id = ?
                ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $facultyId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 4. Create Booking (Self)
    public function createBooking($facultyId, $roomId, $date, $startTime, $endTime, $purpose)
    {
        // Calculate duration logic if needed or just insert
        // Check conflicts first
        if ($this->checkConflict($roomId, $date, $startTime, $endTime)) {
            return ['success' => false, 'message' => 'Conflict detected with another booking.'];
        }

        $sql = "INSERT INTO bookings (user_id, room_id, date, start_time, end_time, purpose) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iissss", $facultyId, $roomId, $date, $startTime, $endTime, $purpose);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Booking created successfully.'];
        } else {
            return ['success' => false, 'message' => 'Database error: ' . $stmt->error];
        }
    }

    // Check Conflict Helper
    private function checkConflict($roomId, $date, $startTime, $endTime)
    {
        $sql = "SELECT COUNT(*) as count FROM bookings 
                WHERE room_id = ? AND date = ? 
                AND (
                    (start_time < ? AND end_time > ?) OR
                    (start_time >= ? AND start_time < ?) OR 
                    (end_time > ? AND end_time <= ?)
                )";
        // Simplified overlap logic: startA < endB && startB < endA
        // DB uses Time type likely.
        
        $sql = "SELECT COUNT(*) as count FROM bookings 
                WHERE room_id = ? AND date = ? 
                AND NOT (end_time <= ? OR start_time >= ?)";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isss", $roomId, $date, $startTime, $endTime);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['count'] > 0;
    }

    // 5. Action Request (Approve/Reject)
    public function actionRequest($requestId, $action, $comments = '')
    {
        // If approving, we must also check conflict and create a real booking
        if ($action === 'approve') {
            // Get request details
            $q = "SELECT * FROM student_booking_requests WHERE id = ?";
            $s = $this->conn->prepare($q);
            $s->bind_param("i", $requestId);
            $s->execute();
            $req = $s->get_result()->fetch_assoc();

            if (!$req) return ['success' => false, 'message' => 'Request not found'];
            if ($req['status'] !== 'pending') return ['success' => false, 'message' => 'Request is not pending'];

            // Calculate end time
            // duration is hours
            $start = strtotime($req['start_time']);
            $end = $start + ($req['duration'] * 3600);
            $endTime = date('H:i:s', $end);

            if ($this->checkConflict($req['room_id'], $req['date'], $req['start_time'], $endTime)) {
                return ['success' => false, 'message' => 'Cannot approve: Room is already booked for this time.'];
            }

            // Create booking
            // user_id in bookings table? It should probably be the student's ID or the faculty's ID on behalf of student?
            // Usually bookings.user_id refers to who owns it. If student, use student_id.
            $sqlBook = "INSERT INTO bookings (user_id, room_id, date, start_time, end_time, purpose) VALUES (?, ?, ?, ?, ?, ?)";
            $sb = $this->conn->prepare($sqlBook);
            $sb->bind_param("iissss", $req['student_id'], $req['room_id'], $req['date'], $req['start_time'], $endTime, $req['purpose']);
            
            if (!$sb->execute()) {
                return ['success' => false, 'message' => 'Failed to create booking record.'];
            }
            
            $status = 'Approved';
        } else {
            $status = 'Rejected';
        }

        $sqlUpdate = "UPDATE student_booking_requests SET status = ?, comments = ? WHERE id = ?";
        $su = $this->conn->prepare($sqlUpdate);
        $su->bind_param("ssi", $status, $comments, $requestId);
        
        if ($su->execute()) {
            return ['success' => true, 'message' => "Request $status successfully"];
        }
        return ['success' => false, 'message' => 'Database update error'];
    }
}
