<?php
error_reporting(E_ALL);
require 'app/config/database.php';
global $conn;

if (!$conn) {
    die("Connection failed\n");
}

$result = $conn->query("SHOW COLUMNS FROM users");
if (!$result) {
    die("Query failed: " . $conn->error . "\n");
}

while ($row = $result->fetch_assoc()) {
    print_r($row);
}
