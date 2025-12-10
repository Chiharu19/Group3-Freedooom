<?php
// verification_fix.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/Admin.php';
require_once __DIR__ . '/../app/models/Faculty.php';

$admin = new Admin();
$faculty = new Faculty();

echo "<h1>Verification Test</h1>";

// 1. Create Dummy Room
$roomName = "TestRoom_" . rand(1000,9999);
$admin->addRoom($roomName, "CICS", 50);
$roomId = $conn->insert_id;
echo "Created Room ID: $roomId ($roomName)<br>";

// 2. Create Dummy Student (if needed, or use existing)
// We'll just pick a random student or create one.
$studentId = 1; // Assuming ID 1 exists and is a student/user. Check first.
$res = $conn->query("SELECT id FROM users WHERE role='student' LIMIT 1");
if ($res->num_rows > 0) {
    $studentId = $res->fetch_assoc()['id'];
} else {
    // Create one
    $conn->query("INSERT INTO users (full_name, email, password, role) VALUES ('Test Student', 'test".rand()."@test.com', '123', 'student')");
    $studentId = $conn->insert_id;
}
echo "Using Student ID: $studentId<br>";

// 3. Create Request
$conn->query("INSERT INTO student_booking_requests (student_id, room_id, faculty_id, date, start_time, duration, purpose, status) VALUES ($studentId, $roomId, 0, CURDATE(), '10:00:00', 1, 'Testing Fix', 'pending')");
$reqId = $conn->insert_id;
echo "Created Request ID: $reqId<br>";

// 4. Verify it appears in Admin Requests
$requests = $admin->getAllStudentRequests();
$found = false;
foreach($requests as $r) {
    if ($r['id'] == $reqId) {
        $found = true;
        echo "Request found BEFORE deletion. Room Name: " . $r['room_name'] . "<br>";
        break;
    }
}

if (!$found) {
    echo "<strong style='color:red'>FAILED: Request not found even before deletion!</strong><br>";
}

// 5. Delete Room
$admin->deleteRoom($roomId);
echo "Deleted Room ID: $roomId<br>";

// 6. Verify it STILL appears in Admin Requests (The Fix)
$requests = $admin->getAllStudentRequests();
$foundAfter = false;
$roomNameAfter = "NOT SET";
foreach($requests as $r) {
    if ($r['id'] == $reqId) {
        $foundAfter = true;
        $roomNameAfter = $r['room_name'];
        break;
    }
}

if ($foundAfter) {
    echo "<strong style='color:green'>PASSED: Request found AFTER deletion.</strong> Room Name: " . ($roomNameAfter ? $roomNameAfter : "NULL (Correct)") . "<br>";
} else {
    echo "<strong style='color:red'>FAILED: Request disappeared after room deletion! Left JOIN not working?</strong><br>";
}

// Cleanup
$conn->query("DELETE FROM student_booking_requests WHERE id = $reqId");
// User cleanup not needed if we reused
if ($roomNameAfter !== "NULL (Correct)" && $roomNameAfter !== null) {
     // If it wasn't null, maybe delete didn't work?
     // double check check
     $check = $conn->query("SELECT * FROM rooms WHERE id = $roomId");
     if ($check->num_rows > 0) echo "WARNING: Room was not actually deleted.<br>";
}

?>
