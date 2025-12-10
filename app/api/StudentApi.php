<?php

class StudentApi
{

    private $studentModel;
    private $data;

    public function __construct($data)
    {
        $this->studentModel = new Student();
        $this->data = $data;
    }

    private function checkAuth()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
            echo json_encode(['success' => false, 'message' => 'User not logged in or authorized']);
            exit;
        }
        return $_SESSION['user']['id'];
    }

    public function getRooms()
    {
        $rooms = $this->studentModel->getRooms();
        echo json_encode(['success' => true, 'data' => $rooms]);
    }

    public function getFaculty()
    {
        $faculty = $this->studentModel->getFaculty();
        echo json_encode(['success' => true, 'data' => $faculty]);
    }

    public function submitRequest()
    {
        $studentId = $this->checkAuth();

        $roomId = $this->data['room_id'] ?? '';
        $facultyId = $this->data['faculty_id'] ?? '';
        $date = $this->data['date'] ?? '';
        $startTime = $this->data['start_time'] ?? '';
        $duration = $this->data['duration'] ?? 1;
        $purpose = $this->data['purpose'] ?? '';

        if (!$roomId || !$facultyId || !$date || !$startTime || !$duration || !$purpose) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        // Conflict Check (needs update to use duration)
        if (!$this->studentModel->isRoomAvailable($roomId, $date, $startTime, $duration)) {
             echo json_encode(['success' => false, 'message' => 'Room is already booked for this time slot.']);
             return;
        }

        $result = $this->studentModel->submitRequest($studentId, $roomId, $facultyId, $date, $startTime, $duration, $purpose);
        
        if ($result['success']) {
            // Send Email Notification to Faculty
            $emailService = new EmailService();
            // Fetch Faculty Email (Assume we have it or need to fetch it. 
            // result usually doesn't return other data. We have facultyId. 
            // Ideally we need to fetch faculty email. 
            // For now, I'll fetch it using a helper or assume the model returns it?
            // The model `submitRequest` returns ['success'=>true, 'message'=>'...'].
            // So I need to fetch faculty details first or let the user do it.
            // Let's add a quick fetch here or just log that we would send it.
            // Wait, I need to make sure I *can* get the email.
            // I'll use the `getFaculty` method pattern or similar.
            
            // To be robust, I should fetch faculty email.
            // But to save time and complexity in this restricted environment, 
            // I will look up the faculty email if possible, or skip if too complex.
            // ACTUALLY, I can't easily get the email without a new query.
            // AND I don't want to modify the model right now if I can avoid it.
            // BUT, `StudentApi::getFaculty()` calls `studentModel->getFaculty()`.
            // I'll use that to filter? No that's inefficient.
            
            // Let's look at `EmailService` usage plan. "Notify Faculty upon student booking request".
            // I will rely on `studentModel` to maybe have a helper or I'll add a quick lookup in `StudentApi`.
            // OR I can use `StudentModel` to get email. 
            
            // Let's try to get faculty email.
            // $facultyUser = $this->studentModel->getUserById($facultyId); // Hypothetical
            
            // Since I cannot allow new errors, I will implement a safe check.
            // If I can't get the email easily, I might have to modify the model.
            // BUT, `submitRequest` *could* be modified to return the inserted ID, 
            // and I could use that.
            
            // Allow me to modify `Student.php`? 
            // The user wants me to *recover* code. 
            // If I implemented it before, I probably did it efficiently.
            
            // I will assume for now I can fetch the faculty email via a direct query in the API 
            // or I'll add a helper to the model in valid "Execution" step if needed.
            // Let's check `Student` model first to see if `getFaculty` returns emails.
            // `StudentApi::getFaculty` returns data with `email`.
            // So I can fetch all faculty and find the one. It's not efficient but it works for small lists.
            
            $facultyList = $this->studentModel->getFaculty();
            $facultyEmail = '';
            foreach ($facultyList as $f) {
                if ($f['id'] == $facultyId) {
                    $facultyEmail = $f['email'];
                    break;
                }
            }
            
            if ($facultyEmail) {
                // Get Room Name for email
                // Similar inefficient lookup or separate query.
                // $rooms = $this->studentModel->getRooms(); 
                // ... find room name ...
                
                // Let's just pass IDs if names unavailable, or do the lookups.
                // It's better to be correct.
                $rooms = $this->studentModel->getRooms();
                $roomName = 'Room #' . $roomId;
                foreach ($rooms as $r) {
                    if ($r['id'] == $roomId) {
                        $roomName = $r['room_name'];
                        break;
                    }
                }
                
                $emailService->sendBookingRequestNotification($facultyEmail, [
                    'room_name' => $roomName,
                    'date' => $date,
                    'start_time' => $startTime,
                    'duration' => $duration,
                    'purpose' => $purpose
                ]);
            }
        }

        echo json_encode($result);
    }

    public function editRequest()
    {
        $studentId = $this->checkAuth();
        
        $requestId = $this->data['request_id'] ?? '';
        $roomId = $this->data['room_id'] ?? '';
        $date = $this->data['date'] ?? '';
        $startTime = $this->data['start_time'] ?? '';
        $duration = $this->data['duration'] ?? 1;
        $purpose = $this->data['purpose'] ?? '';

        if (!$requestId || !$roomId || !$date || !$startTime || !$duration || !$purpose) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        // Optional: Conflict check on edit? Yes, ideally.
        if (!$this->studentModel->isRoomAvailable($roomId, $date, $startTime, $duration)) {
             echo json_encode(['success' => false, 'message' => 'Room is already booked for this time slot.']);
             return;
        }

        $result = $this->studentModel->updateRequest($requestId, $studentId, $roomId, $date, $startTime, $duration, $purpose);
        echo json_encode($result);
    }

    public function myRequests()
    {
        $studentId = $this->checkAuth();

        $requests = $this->studentModel->getMyRequests($studentId);
        echo json_encode(['success' => true, 'data' => $requests]);
    }

    public function dashboard()
    {
        $studentId = $this->checkAuth();

        // Fetch recent requests (limit 5)
        $requests = $this->studentModel->getRecentRequests($studentId, 5);

        // Derive Notices from requests (e.g., status updates)
        $notices = [];
        foreach ($requests as $r) {
            if ($r['status'] === 'approved') {
                $notices[] = "Your request for {$r['room_name']} on {$r['date']} has been APPROVED.";
            } elseif ($r['status'] === 'denied') {
                $notices[] = "Your request for {$r['room_name']} on {$r['date']} was DENIED.";
            } elseif ($r['status'] === 'pending') {
                $notices[] = "Your request for {$r['room_name']} on {$r['date']} is currently PENDING.";
            }
        }

        if (empty($notices)) {
            $notices[] = "Welcome! You have no recent activity notices.";
        }

        echo json_encode(['success' => true, 'requests' => $requests, 'notices' => $notices]);
    }
    public function getRoomSchedule()
    {
        $roomId = $this->data['room_id'] ?? '';
        $date = $this->data['date'] ?? date('Y-m-d'); // Default to today

        if (!$roomId) {
            echo json_encode(['success' => false, 'message' => 'Room ID required']);
            return;
        }

        $schedule = $this->studentModel->getRoomSchedule($roomId, $date);
        echo json_encode(['success' => true, 'data' => $schedule]);
    }

    public function cancelRequest()
    {
        $studentId = $this->checkAuth();
        $requestId = $this->data['request_id'] ?? '';

        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID required']);
            return;
        }

        // Fetch request details before cancelling
        $requestDetails = $this->studentModel->getRequestById($requestId, $studentId);

        $result = $this->studentModel->cancelRequest($requestId, $studentId);
        
        if ($result['success'] && $requestDetails && isset($requestDetails['faculty_email'])) {
             // Send Email to Faculty
             if (class_exists('EmailService')) {
                 $emailService = new EmailService();
                 $emailService->sendBookingCancellationNotification($requestDetails['faculty_email'], $requestDetails);
             }
        }

        echo json_encode($result);
    }
}
