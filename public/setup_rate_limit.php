<?php
require_once __DIR__ . '/../app/config/database.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    action VARCHAR(50) NOT NULL,
    attempt_count INT DEFAULT 1,
    last_attempt DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attempt (ip_address, action)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'rate_limits' created successfully or already exists.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
