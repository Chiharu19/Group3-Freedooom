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
        $stmt->bind_param("is", $facultyId, $today); 
        $stmt->execute();
        $resBookings = $stmt->get_result()->fetch_assoc();
        $todayCount = $resBookings['count'] ?? 0;

        // Count pending assigned requests
        $sqlRequests = "SELECT COUNT(*) as count FROM student_booking_requests WHERE faculty_id = ? AND status = 'pending'";
        $stmt = $this->conn->prepare($sqlRequests);
        $stmt->bind_param("i", $facultyId);
        $stmt->execute();
        $resRequests = $stmt->get_result()->fetch_assoc();
        $pendingCount = $resRequests['count'] ?? 0;

        // Get today's bookings list (brief)
        // Schema: duration exists, end_time does not.
        $sqlTodayList = "SELECT b.start_time, b.duration, r.room_name 
                         FROM bookings b 
                         JOIN rooms r ON b.room_id = r.id 
                         WHERE b.user_id = ? AND b.date = ? 
                         ORDER BY b.start_time ASC";
        $stmt = $this->conn->prepare($sqlTodayList);
        $stmt->bind_param("is", $facultyId, $today);
        $stmt->execute();
        $todayList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Calculate end_time for frontend display
        foreach ($todayList as &$item) {
            $start = strtotime($item['start_time']);
            $end = $start + ($item['duration'] * 3600);
            $item['end_time'] = date('H:i:s', $end);
        }

        return [
            'today_count' => $todayCount,
            'pending_count' => $pendingCount,
            'today_list' => $todayList
        ];
    }

    // 2. Get My Bookings
    public function getMyBookings($facultyId)
    {
        // Status column does not exist in bookings. Assume 'Confirmed'.
        // Duration exists, not end_time.
        $sql = "SELECT b.id, b.date, b.start_time, b.duration, b.purpose, r.room_name 
                FROM bookings b
                JOIN rooms r ON b.room_id = r.id
                WHERE b.user_id = ?
                ORDER BY b.date DESC, b.start_time ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $facultyId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while($row = $result->fetch_assoc()){
            $row['status'] = 'Confirmed';
            $start = strtotime($row['start_time']);
            $end = $start + ($row['duration'] * 3600);
            $row['end_time'] = date('H:i:s', $end);
            $data[] = $row;
        }
        return $data;
    }

    // 3. Get Assigned Student Requests
    public function getAssignedRequests($facultyId)
    {
        $sql = "SELECT r.id, r.date, r.start_time, r.duration, r.purpose, r.status, r.notes as comments, 
                       rm.room_name, u.full_name as student_name, u.email as student_email
                FROM student_booking_requests r
                LEFT JOIN rooms rm ON r.room_id = rm.id
                LEFT JOIN users u ON r.student_id = u.id
                WHERE r.faculty_id = ?
                ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $facultyId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Add end_time for convenience
        foreach ($res as &$r) {
            $start = strtotime($r['start_time']);
            $end = $start + ($r['duration'] * 3600);
            $r['end_time'] = date('H:i:s', $end);
        }
        return $res;
    }

    // 4. Create Booking (Self)
    public function createBooking($facultyId, $roomId, $date, $startTime, $duration, $purpose)
    {
        // Validation
        if ($duration < 1) $duration = 1;

        // Check conflicts
        if ($this->checkConflict($roomId, $date, $startTime, $duration)) {
            return ['success' => false, 'message' => 'Conflict detected with another booking.'];
        }

        $sql = "INSERT INTO bookings (user_id, room_id, date, start_time, duration, purpose) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
             return ['success' => false, 'message' => 'DB Prepare Error: '.$this->conn->error];
        }

        $stmt->bind_param("iissis", $facultyId, $roomId, $date, $startTime, $duration, $purpose);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Booking created successfully.'];
        } else {
            return ['success' => false, 'message' => 'Database error: ' . $stmt->error];
        }
    }

    // Check Conflict Helper
    private function checkConflict($roomId, $date, $startTime, $duration)
    {
        // New start
        // New end
        $startTs = strtotime($startTime);
        $endTs = $startTs + ($duration * 3600);
        $newStart = date('H:i:s', $startTs);
        
        // Logic: 
        // Existing booking: start_time, duration
        // Existing end = start_time + duration*3600
        // Overlap: existing_start < new_end AND new_start < existing_end
        
        $sql = "SELECT start_time, duration FROM bookings WHERE room_id = ? AND date = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $roomId, $date);
        $stmt->execute();
        $res = $stmt->get_result();
        
        while($row = $res->fetch_assoc()) {
            $existStart = strtotime($row['start_time']);
            $existDuration = $row['duration'];
            $existEnd = $existStart + ($existDuration * 3600);
            
            if ($startTs < $existEnd && $existStart < $endTs) {
                return true;
            }
        }
        return false;
    }

    // 5. Action Request (Approve/Reject)
    public function actionRequest($requestId, $facultyId, $action, $comments = '')
    {
        if ($action === 'approve') {
            // IDOR FIX: Check faculty_id
            $q = "SELECT * FROM student_booking_requests WHERE id = ? AND faculty_id = ?";
            $s = $this->conn->prepare($q);
            $s->bind_param("ii", $requestId, $facultyId);
            $s->execute();
            $req = $s->get_result()->fetch_assoc();

            if (!$req) return ['success' => false, 'message' => 'Request not found or not assigned to you'];
            if ($req['status'] !== 'pending') return ['success' => false, 'message' => 'Request is not pending'];

            // Conflict check
            if ($this->checkConflict($req['room_id'], $req['date'], $req['start_time'], $req['duration'])) {
                return ['success' => false, 'message' => 'Cannot approve: Room is already booked for this time.'];
            }

            // Create booking
            $sqlBook = "INSERT INTO bookings (user_id, room_id, date, start_time, duration, purpose) VALUES (?, ?, ?, ?, ?, ?)";
            $sb = $this->conn->prepare($sqlBook);
            $sb->bind_param("iissis", $req['student_id'], $req['room_id'], $req['date'], $req['start_time'], $req['duration'], $req['purpose']);
            
            if (!$sb->execute()) {
                return ['success' => false, 'message' => 'Failed to create booking record: ' . $sb->error];
            }
            
            $newStatus = 'approved';
        } else {
            $newStatus = 'denied';
        }

        $upd = "UPDATE student_booking_requests SET status = ?, notes = ? WHERE id = ?";
        $u = $this->conn->prepare($upd);
        $u->bind_param("ssi", $newStatus, $comments, $requestId);
        if ($u->execute()) {
            return ['success' => true, 'message' => "Request $newStatus successfully"];
        }
        return ['success' => false, 'message' => 'Database update error'];
    }

    // 6. Cancel Booking (Own bookings only)
    public function cancelBooking($bookingId, $facultyId) {
        // Verify ownership
        $chk = $this->conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ?");
        $chk->bind_param("ii", $bookingId, $facultyId);
        $chk->execute();
        if ($chk->get_result()->num_rows === 0) {
            return ['success' => false, 'message' => 'Booking not found or access denied'];
        }

        $del = $this->conn->prepare("DELETE FROM bookings WHERE id = ?");
        $del->bind_param("i", $bookingId);
        return $del->execute() ? ['success' => true, 'message' => 'Booking cancelled successfully'] : ['success' => false, 'message' => $del->error];
    }

    // 7. Update Booking (Own bookings only)
    public function updateBooking($bookingId, $facultyId, $roomId, $date, $startTime, $duration, $purpose) {
         // Verify ownership
        $chk = $this->conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ?");
        $chk->bind_param("ii", $bookingId, $facultyId);
        $chk->execute();
        if ($chk->get_result()->num_rows === 0) {
            return ['success' => false, 'message' => 'Booking not found or access denied'];
        }

        // Conflict check (exclude self)
        // This conflict check is more robust than the simple checkConflict helper
        // It checks for overlap with other bookings, excluding the one being updated.
        // ADDTIME(start_time, SEC_TO_TIME(duration * 3600)) calculates the end time of existing bookings.
        // The condition ( ? < existing_end AND new_end > existing_start ) checks for overlap.
        $sqlOverlap = "SELECT id FROM bookings 
                       WHERE room_id = ? AND date = ? 
                       AND id != ?
                       AND ( ? < ADDTIME(start_time, SEC_TO_TIME(duration * 3600)) 
                       AND ADDTIME(?, SEC_TO_TIME(?*3600)) > start_time )";
        
        $so = $this->conn->prepare($sqlOverlap);
        $so->bind_param("isissii", $roomId, $date, $bookingId, $startTime, $startTime, $duration);
        $so->execute();
        if ($so->get_result()->num_rows > 0) {
             return ['success' => false, 'message' => 'Conflict with existing booking'];
        }

        // Update
        $sql = "UPDATE bookings SET room_id = ?, date = ?, start_time = ?, duration = ?, purpose = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issisi", $roomId, $date, $startTime, $duration, $purpose, $bookingId);
        
        return $stmt->execute() ? ['success' => true, 'message' => 'Booking updated successfully'] : ['success' => false, 'message' => $stmt->error];
    }

    // 8. Get Available Rooms
    public function getAvailableRooms($date, $startTime, $endTime) {
         // Calculate duration to help with overlap check logic or just use explicit time check
         // We need to find rooms where NO booking overlaps with requested range.
         
         // Overlap logic: (StartA < EndB) and (EndA > StartB)
         // Request: StartReq, EndReq.
         // Existing Booking: StartBook, EndBook (calculated from duration).
         
         // Query: Select all rooms NOT IN (Select room_id from bookings where overlap)
         
         // We need EndReq. Input is HH:MM.
         
         $sql = "SELECT r.id, r.room_name, r.capacity, r.building, r.status
                 FROM rooms r
                 WHERE r.status = 'available'
                 AND r.id NOT IN (
                    SELECT b.room_id 
                    FROM bookings b
                    WHERE b.date = ?
                    AND (
                        ? < ADDTIME(b.start_time, SEC_TO_TIME(b.duration * 3600))
                        AND ? > b.start_time
                    )
                 )";
                 
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $date, $startTime, $endTime);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
