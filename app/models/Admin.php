<?php

class Admin {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

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
    // 7. Full list of rooms
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
    // 5. Full list for today's bookings: UNUSED
    // ------------------------------------------
    public function getTodaysBookingList() {
        $sql = "SELECT b.*, u.full_name, r.room_name
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN rooms r ON b.room_id = r.id
                WHERE b.date = CURDATE()
                ORDER BY b.start_time ASC";

        $res = $this->conn->query($sql);
        $data = [];

        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    // ------------------------------------------
    // 6. Full list for every bookings: UNUSED
    // ------------------------------------------
    public function getAllBookingList() {
        $sql = "SELECT b.*, u.full_name, r.room_name
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN rooms r ON b.room_id = r.id
                ORDER BY b.start_time ASC";

        $res = $this->conn->query($sql);
        $data = [];

        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    // ------------------------------------------
    // 7. Checks if the combination of room name and building already exists
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



}
