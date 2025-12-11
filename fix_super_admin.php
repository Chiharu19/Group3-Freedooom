<?php
error_reporting(E_ALL);
require 'app/config/database.php';
global $conn;

if (!$conn) {
    die("Connection failed\n");
}

// Find users with empty role
$sql = "SELECT id, full_name, email FROM users WHERE role = ''";
$result = $conn->query($sql);
$count = $result->num_rows;

echo "Found $count users with empty role.\n";

if ($count > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Fixing user: " . $row['full_name'] . " (" . $row['email'] . ")\n";
        $update = "UPDATE users SET role = 'super' WHERE id = " . $row['id'];
        if ($conn->query($update)) {
            echo "Updated to role 'super'.\n";
        } else {
            echo "Failed to update: " . $conn->error . "\n";
        }
    }
}
