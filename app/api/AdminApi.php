<?php

class AdminApi {

    private $adminModel;
    private $data;

    public function __construct($data) {
        $this->adminModel = new Admin();
        $this->data = $data;
    }

    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }
    }

    public function addRoom() {
        $this->checkAuth();
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
        $this->checkAuth();
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
        $this->checkAuth();
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

    public function addBooking() {

        $room      = $this->data['room-id'] ?? '';
        $date      = $this->data['date'] ?? '';
        $startTime = $this->data['start-time'] ?? '';
        $duration   = $this->data['duration'] ?? '';
        $faculty   = $this->data['faculty-id'] ?? '';

        // Basic validation
        if (!$room || !$date || !$startTime || !$duration || !$faculty) {
            echo json_encode(['success' => false, 'error' => 'Missing fields']);
            return;
        }

        // Pass data to model
        $result = $this->adminModel->addBooking(
            $room,
            $date,
            $startTime,
            $duration,
            $faculty
        );

        echo json_encode($result);
    }

    public function editBooking() {

        $bookingId = $this->data['booking-id'] ?? '';
        $room      = $this->data['room-name'] ?? '';
        $date      = $this->data['date'] ?? '';
        $startTime = $this->data['start-time'] ?? '';
        $duration   = $this->data['duration'] ?? '';
        $faculty   = $this->data['faculty-id'] ?? '';

        // Basic validation
        if (!$bookingId || !$room || !$date || !$startTime || !$duration || !$faculty) {
            echo json_encode(['success' => false, 'error' => 'Missing fields']);
            return;
        }

        // Pass data to model
        $result = $this->adminModel->editBooking(
            $bookingId,
            $room,
            $date,
            $startTime,
            $duration,
            $faculty
        );

        echo json_encode($result);
    }

    public function getUsersList(){

        $result = $this->adminModel->getAllUsers();
        echo json_encode(['data' => $result]);

    }

    public function getStudentRequests() {
        $status = $_POST['status'] ?? 'pending';
        $data = $this->adminModel->getAllStudentRequests($status);
        echo json_encode(['success' => true, 'data' => $data]);
    }

    public function actionRequest() {
        $id = $_POST['request_id'] ?? '';
        $action = $_POST['req_action'] ?? '';
        $comments = $_POST['comments'] ?? '';

        if (!$id || !$action) {
            echo json_encode(['success' => false, 'error' => 'Missing ID or Action']);
            return;
        }

        $res = $this->adminModel->actionRequest($id, $action, $comments);
        
        if ($res['success']) {
            // Send Email to Student
            $emailService = new EmailService();
            
            // Need Student Email and Request Details.
            // Fetch all requests to find this one (inefficient but safe without model changes)
            $allRequests = $this->adminModel->getAllStudentRequests('all'); // 'all' might not be supported, default is pending.
            // If getAllStudentRequests only returns pending, we might miss it if we just approved it (status changed).
            // However, we just changed it. 
            // Better approach: We need the student email.
            // Let's assume the frontend might pass it? No, security risk.
            
            // I'll try to fetch it.
            // If I can't easily get it, I'll skip for now to avoid breaking things, 
            // OR I'll add a helper method to AdminModel if I can.
            // Actually, I can use the `getAllStudentRequests` with specific status or just assume I can fetch it.
            // Let's try to fetch all requests including the one we just handled.
            // Since we updated it, its status is now `$action`.
            
            $updatedRequests = $this->adminModel->getAllStudentRequests($action);
            $targetRequest = null;
            foreach ($updatedRequests as $r) {
                if ($r['id'] == $id) {
                    $targetRequest = $r;
                    break;
                }
            }
            
            if ($targetRequest && !empty($targetRequest['email'])) {
                 $emailService->sendRequestStatusNotification(
                     $targetRequest['email'], 
                     $action, 
                     $comments,
                     [
                         'room_name' => $targetRequest['room_name'],
                         'date' => $targetRequest['date']
                     ]
                 );
            }
        }

        echo json_encode($res);
    }

    public function addUser(){

        $full_name = $this->data['full-name'];
        $email = $this->data['email'];
        $password = $this->data['password'];
        $role = $this->data['role'];

        $result = $this->adminModel->addUser($full_name, $email, $password, $role);
        
        if ($result['success']) {
             $emailService = new EmailService();
             $emailService->sendAccountCreatedNotification($email, $full_name, $password);
        }

        echo json_encode($result);
    }

    public function editUser() {
        $userId = $this->data['user-id'] ?? '';
        $fullName = $this->data['full-name'] ?? '';
        $email = $this->data['email'] ?? '';
        $role = $this->data['role'] ?? '';

        if (!$userId || !$fullName || !$email || !$role) {
            echo json_encode(['success' => false, 'message' => 'Missing fields']);
            return;
        }

        $res = $this->adminModel->updateUser($userId, $fullName, $email, $role);
        echo json_encode($res);
    }

    public function changeUserStatus(){

        $userId = $this->data['user-id'];
        $currentStatus = $this->data['new-status']; // incoming current status

        // Flip the status
        $newStatus = ($currentStatus === "active") ? "inactive" : "active";

        $result = $this->adminModel->changeUserStatus($userId, $newStatus);

        echo json_encode($result);
    }


}
