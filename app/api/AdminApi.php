<?php

class AdminApi {

    public function __construct($data) {
        $this->adminModel = new Admin();
        $this->data = $data;
    }

    public function addRoom() {

        $roomName = $this->data['room-name'] ?? '';
        $building = $this->data['building'] ?? '';
        $capacity = $this->data['capacity'] ?? '';

        if (!$roomName || !$building || !$capacity) {
            echo json_encode(['success' => false, 'error' => 'Missing credentials']);
            return;
        }

        $result = $this->adminModel->addRoom($roomName, $building, $capacity);

        echo json_encode($result);
    }

    public function updateRoom() {

        $roomId   = $this->data['room-id'] ?? '';
        $roomName = $this->data['room-name'] ?? '';
        $building = $this->data['building'] ?? '';
        $capacity = $this->data['capacity'] ?? '';
        $status   = $this->data['status'] ?? '';

        // Basic validation
        if (!$roomId || !$roomName || !$building || !$capacity || !$status) {
            echo json_encode(['success' => false, 'error' => 'Missing fields']);
            return;
        }

        // Pass data to model
        $result = $this->adminModel->editRoom($roomId, $roomName, $building, $capacity, $status);

        echo json_encode($result);
    }

    public function deleteRoom(){

        $roomId   = $this->data['room-id'] ?? '';
        
        if (!$roomId) {
            echo json_encode(['success' => false, 'error' => 'Missing fields']);
            return;
        }
        
        $result = $this->adminModel->deleteRoom($roomId);
        echo json_encode($result);

    }

    public function deleteBooking(){

        $bookingId   = $this->data['booking-id'] ?? '';
        
        if (!$bookingId) {
            echo json_encode(['success' => false, 'error' => 'Missing fields']);
            return;
        }
        
        $result = $this->adminModel->deleteBooking($bookingId);
        echo json_encode($result);
    }

    public function getBookingList(){
        $room = $_POST['room'] ?? null;
        $date = $_POST['date'] ?? null;
        $faculty = $_POST['faculty'] ?? null;

        $bookings = $this->adminModel->getBookingList($room, $date, $faculty);

        echo json_encode([
            "success" => true,
            "data" => $bookings
        ]);
    }

}
