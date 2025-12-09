<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'room_utilization_db';

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
} catch (Exception $e) {
    $conn = null;
}
