<?php
// Adjust path to config
require_once __DIR__ . '/../app/config/database.php';
global $conn;

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function addColumn($conn, $table, $column, $type) {
    $check = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    if ($check && $check->num_rows == 0) {
        $sql = "ALTER TABLE $table ADD COLUMN $column $type";
        if ($conn->query($sql) === TRUE) {
            echo "Added $column to $table.\n";
        } else {
            echo "Error adding $column: " . $conn->error . "\n";
        }
    } else {
        echo "$column already exists in $table.\n";
    }
}

echo "Starting migration...\n";
addColumn($conn, 'users', 'reset_token', 'VARCHAR(255) NULL');
addColumn($conn, 'users', 'reset_expires', 'DATETIME NULL');
echo "Migration complete.\n";
