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



    // INSERTION METHODS


    // ------------------------------------------
    // 7. Add new room
    // ------------------------------------------
    public function addRoom($roomName, $building, $capacity){
        $sql = "INSERT INTO rooms (room_name, building, capacity) VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return [
                "success" => false,
                "message" => "Failed to prepare statement"
            ];
        }

        $stmt->bind_param("ssi", $roomName, $building, $capacity);

        if ($stmt->execute()) {
            return [
                "success" => true,
                "message" => "Room added successfully"
            ];
        } else {
            return [
                "success" => false,
                "message" => "Database error: " . $stmt->error
            ];
        }
    }
}
