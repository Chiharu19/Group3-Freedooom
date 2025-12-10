<?php

class Admin {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Helper function/s
    private function formatTime12($time) {
        if (!$time) return null;
        return date("g:i A", strtotime($time));
    }


    // GET METHODS

    // ------------------------------------------
    // 1. Total Rooms
    // ------------------------------------------
    public function getTotalRooms() {
        $sql = "SELECT COUNT(*) AS total FROM rooms";
        $res = $this->conn->query($sql);
        return $res->fetch_assoc()['total'];
    }

    // ------------------------------------------
    // 2. Total Faculty / Staff (role = faculty)
    // ------------------------------------------
    public function getTotalFaculty() {
        $sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'faculty'";
        $res = $this->conn->query($sql);
        return $res->fetch_assoc()['total'];
    }

    // ------------------------------------------
    // 3. Pending Student Requests
    // ------------------------------------------
    public function getPendingStudentRequests() {
        $sql = "SELECT COUNT(*) AS total 
                FROM student_booking_requests 
                WHERE status = 'pending'";

        $res = $this->conn->query($sql);
        return $res->fetch_assoc()['total'];
    }

    // ------------------------------------------
    // 4. Today's Bookings
    // ------------------------------------------
    public function getTodaysBookings() {
        $sql = "SELECT COUNT(*) AS total 
                FROM bookings 
                WHERE date = CURDATE()";

        $res = $this->conn->query($sql);
        return $res->fetch_assoc()['total'];
    }

    // ------------------------------------------
    // 6. Full list of rooms
    // ------------------------------------------
    public function getAllRooms() {
        $sql = "SELECT * FROM rooms ORDER BY room_name ASC";
        $res = $this->conn->query($sql);

        $data = [];

        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    // ------------------------------------------
    // 7. List of every bookings (w/ optional filters): 
    // ------------------------------------------
    public function getBookingList($room = null, $date = null, $faculty = null) {
        $sql = "SELECT b.*, u.full_name, r.room_name
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN rooms r ON b.room_id = r.id
                WHERE 1 = 1";

        // dynamic conditions
        if (!empty($room)) {
            $sql .= " AND b.room_id = '" . $this->conn->real_escape_string($room) . "'";
        }

        if (!empty($date)) {
            $sql .= " AND b.date = '" . $this->conn->real_escape_string($date) . "'";
        }

        if (!empty($faculty)) {
            $sql .= " AND b.user_id = '" . $this->conn->real_escape_string($faculty) . "'";
        }

        $sql .= " ORDER BY b.start_time ASC";

        $res = $this->conn->query($sql);
        $data = [];

        while ($row = $res->fetch_assoc()) {

            // Convert to 12-hour format
            $row['start_time'] = $this->formatTime12($row['start_time']);
            $row['end_time']   = $this->formatTime12($row['end_time']);

            $data[] = $row;
        }

        return $data;
    }


    // ------------------------------------------
    // 8. Checks if the combination of room name and building already exists
    // ------------------------------------------
    public function roomExists($roomName, $building, $excludeId = null) {
        $sql = "SELECT id FROM rooms WHERE room_name = ? AND building = ?";
        
        // If editing, exclude the current room ID
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
        }

        $stmt = $this->conn->prepare($sql);

        if ($excludeId !== null) {
            $stmt->bind_param("ssi", $roomName, $building, $excludeId);
        } else {
            $stmt->bind_param("ss", $roomName, $building);
        }

        $stmt->execute();
        $res = $stmt->get_result();

        return $res->num_rows > 0; // true if exists
    }

    // ------------------------------------------
    // 6. Full list of users
    // ------------------------------------------
    public function getAllUsers($role = null) {
        // If no role provided → fetch all users
        if ($role === null || $role === "") {
            $sql = "SELECT * FROM users ORDER BY full_name ASC";
            $stmt = $this->conn->prepare($sql);
        } 
        else {
            // Fetch only matching role
            $sql = "SELECT * FROM users WHERE role = ? ORDER BY full_name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $role);
        }

        $stmt->execute();
        $res = $stmt->get_result();

        $data = [];
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }



    
    // INSERTION METHODS

    
    // ------------------------------------------
    // 8. Add new room
    // ------------------------------------------
    public function addRoom($roomName, $building, $capacity) {
        // check duplicates
        if ($this->roomExists($roomName, $building)) {
            return [
                "success" => false,
                "message" => "Room already exists in this building"
            ];
        }

        $sql = "INSERT INTO rooms (room_name, building, capacity, status)
                VALUES (?, ?, ?, 'available')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $roomName, $building, $capacity);

        if ($stmt->execute()) {
            return ["success" => true];
        }

        return ["success" => false, "message" => $stmt->error];
    }

    // ------------------------------------------
    // 8. Add new booking
    // ------------------------------------------
    public function addBooking($roomId, $date, $startTime, $duration, $facultyId) {
        // normalize start_time input to HH:MM:SS
        if (preg_match('/^\d{2}:\d{2}$/', $startTime)) {
            $startTime .= ":00";
        }

        // 1. Validate room ID
        $stmt = $this->conn->prepare("SELECT id FROM rooms WHERE id = ?");
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        $roomResult = $stmt->get_result()->fetch_assoc();

        if (!$roomResult) {
            return [
                "success" => false,
                "message" => "Invalid room"
            ];
        }

        // 2. Validate faculty (must exist + role must be faculty)
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'faculty'");
        $stmt->bind_param("i", $facultyId);
        $stmt->execute();
        $facultyResult = $stmt->get_result()->fetch_assoc();

        if (!$facultyResult) {
            return [
                "success" => false,
                "message" => "Invalid faculty ID"
            ];
        }

        $conflictSql = "
            SELECT id
            FROM bookings
            WHERE room_id = ?
            AND date = ?
            AND (
                    ? < end_time
                AND start_time < ADDTIME(?, SEC_TO_TIME(? * 3600))
            )
        ";

        $conflictStmt = $this->conn->prepare($conflictSql);
        $conflictStmt->bind_param(
            "isssi",
            $roomId,
            $date,
            $startTime,   // new booking start < existing end_time
            $startTime,   // existing start_time < new booking end_time
            $duration     // duration IN HOURS (correct!)
        );


        $conflictStmt->execute();
        $conflictResult = $conflictStmt->get_result();

        if ($conflictResult->num_rows > 0) {
            return [
                "success" => false,
                "message" => "Schedule overlaps with an existing booking"
            ];
        }

        // 4. Insert booking (SQL computes end_time)
        $sql = "INSERT INTO bookings (user_id, room_id, date, start_time, duration)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iissi",
            $facultyId,
            $roomId,
            $date,
            $startTime,
            $duration
        );

        if ($stmt->execute()) {
            return [
                "success" => true,
                "message" => "Booking added successfully",
                'data'      => $startTime
            ];
        }

        return [
            "success" => false,
            "message" => $stmt->error
        ];
    }
    
    public function addUser($name, $email, $password, $role) {

        // 1. Check if email already exists
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            return [
                "success" => false,
                "message" => "Email is already registered"
            ];
        }

        // 2. Hash password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // 3. Insert new user
        $sql = "INSERT INTO users (full_name, email, password, role) 
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $hashed, $role);

        if ($stmt->execute()) {
            return [
                "success" => true,
                "message" => "User added successfully"
            ];
        }

        return [
            "success" => false,
            "message" => $stmt->error
        ];
    }



    // UPDATING METHODS

    // ------------------------------------------
    // 8. edit existing room
    // ------------------------------------------
    public function editRoom($id, $roomName, $building, $capacity, $status) {
        // Check duplicates but exclude itself
        if ($this->roomExists($roomName, $building, $id)) {
            return [
                "success" => false,
                "message" => "Another room with this name already exists in this building"
            ];
        }

        $sql = "UPDATE rooms 
                SET room_name = ?, building = ?, capacity = ?, status = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssisi", $roomName, $building, $capacity, $status, $id);

        if ($stmt->execute()) {
            return ["success" => true];
        }

        return ["success" => false, "message" => $stmt->error];
    }

    // ------------------------------------------
    // 11. Edit Booking
    // ------------------------------------------
    public function editBooking($bookingId, $room, $date, $startTime, $duration, $faculty) {

        // Get room id
        $stmt = $this->conn->prepare("SELECT id FROM rooms WHERE room_name = ?");
        $stmt->bind_param("s", $room);
        $stmt->execute();
        $roomResult = $stmt->get_result()->fetch_assoc();
        $roomId = $roomResult['id'] ?? null;

        if (!$roomId) {
            return [
                "success" => false,
                "message" => "Invalid room",
            ];
        }

        // Update booking
        $sql = "UPDATE bookings 
                SET user_id = ?, date = ?, start_time = ?, duration = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issii", $faculty, $date, $startTime, $duration, $bookingId);

        if ($stmt->execute()) {
            return ["success" => true];
        }

        return [
            "success" => false,
            "message" => $stmt->error
        ];
    }

    public function changeUserStatus($userId, $newStatus) {
        // Validate allowed status values (optional but recommended)
        $allowed = ["active", "inactive"];
        if (!in_array($newStatus, $allowed)) {
            return [
                "success" => false,
                "message" => "Invalid status value"
            ];
        }

        // Check if user exists
        $check = $this->conn->prepare("SELECT id FROM users WHERE id = ?");
        $check->bind_param("i", $userId);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows === 0) {
            return [
                "success" => false,
                "message" => "User not found"
            ];
        }

        // Update status
        $sql = "UPDATE users SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $newStatus, $userId);

        if ($stmt->execute()) {
            return [
                "success" => true,
                "message" => "User status updated successfully",
                'data' => [$userId, $newStatus]
            ];
        }

        return [
            "success" => false,
            "message" => $stmt->error
        ];
    }




    // DELETION METHODS


    // ------------------------------------------
    // 9. Delete room
    // ------------------------------------------
    public function deleteRoom($id) {
        $sql = "DELETE FROM rooms WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // if no rows were affected, the ID didn't exist
            if ($stmt->affected_rows === 0) {
                return [
                    "success" => false,
                    "message" => "Room not found"
                ];
            }

            return ["success" => true];
        }

        return [
            "success" => false,
            "message" => $stmt->error
        ];
    }

    // ------------------------------------------
    // 10. Delete Booking
    // ------------------------------------------
    public function deleteBooking($id) {
        $sql = "DELETE FROM bookings WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // if no rows were affected, the ID didn't exist
            if ($stmt->affected_rows === 0) {
                return [
                    "success" => false,
                    "message" => "Booking not found"
                ];
            }

            return ["success" => true];
        }

        return [
            "success" => false,
            "message" => $stmt->error
        ];
    }


}
