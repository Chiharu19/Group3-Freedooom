<?php
require_once __DIR__ . '/../app/config/database.php';

$sql = "SELECT * FROM student_booking_requests";
$res = $conn->query($sql);

echo "<h2>Student Booking Requests</h2>";
echo "<table border='1'><tr><th>ID</th><th>Student ID</th><th>Faculty ID</th><th>Room ID</th><th>Status</th><th>Date</th></tr>";
while($row = $res->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['student_id'] . "</td>";
    echo "<td>" . $row['faculty_id'] . "</td>";
    echo "<td>" . $row['room_id'] . "</td>";
    echo "<td>" . $row['status'] . "</td>";
    echo "<td>" . $row['date'] . "</td>";
    echo "</tr>";
}
echo "</table>";

$sql = "SELECT * FROM users WHERE role='faculty'";
$res = $conn->query($sql);
echo "<h2>Faculty Users</h2>";
echo "<table border='1'><tr><th>ID</th><th>Name</th></tr>";
while($row = $res->fetch_assoc()) {
    echo "<tr><td>" . $row['id'] . "</td><td>" . $row['full_name'] . "</td></tr>";
}
echo "</table>";
?>
