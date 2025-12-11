<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/SuperAdmin.php';

echo "<h1>Debug Info</h1>";

global $conn;
if (!$conn) {
    echo "Database connection failed.<br>";
} else {
    echo "Database connection successful.<br>";
}

$model = new SuperAdmin();
$count = $model->countSuperAdmins();
echo "<strong>countSuperAdmins() result:</strong> " . var_export($count, true) . "<br><br>";

echo "<h2>All Users</h2>";
$sql = "SELECT id, full_name, email, role, status FROM users";
$result = $conn->query($sql);

if ($result) {
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $val) {
            echo "<td>" . htmlspecialchars($val) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error getting users: " . $conn->error;
}
