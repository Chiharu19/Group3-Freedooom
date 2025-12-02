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

}
