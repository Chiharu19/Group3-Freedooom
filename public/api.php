<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . '/../app/config/database.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/api/' . $class . '.php',
        __DIR__ . '/../app/models/' . $class . '.php',
    ];
    foreach ($paths as $p)
        if (file_exists($p))
            require $p;
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'POST only']);
    exit;
}

$action = $_POST['action'] ?? '';

$routes = [
    // Auth
    'logIn' => ['AuthApi', 'login'],

    // Admin Routes
    'getUsersList' => ['AdminApi', 'getUsersList'],
    'addRoom' => ['AdminApi', 'addRoom'],
    'updateRoom' => ['AdminApi', 'updateRoom'],
    'deleteRoom' => ['AdminApi', 'deleteRoom'],
    'deleteBooking' => ['AdminApi', 'deleteBooking'],
    'getBookingList' => ['AdminApi', 'getBookingList'],
    'addBooking'    => ['AdminApi', 'addBooking'],
    'editBooking' => ['AdminApi', 'editBooking'],

    // Student Routes
    'getRooms' => ['StudentApi', 'getRooms'],
    'getFaculty' => ['StudentApi', 'getFaculty'],
    'submitRequest' => ['StudentApi', 'submitRequest'],
    'myRequests' => ['StudentApi', 'myRequests'],
    'dashboard' => ['StudentApi', 'dashboard'],
    'getRoomSchedule' => ['StudentApi', 'getRoomSchedule'],
    'getRoomSchedule' => ['StudentApi', 'getRoomSchedule'],
    'cancelRequest' => ['StudentApi', 'cancelRequest'],

    // Super Admin Routes
    'superAdminLogin' => ['SuperAdminApi', 'login'],
    'getAdminsList' => ['SuperAdminApi', 'getAdminsList'],
    'addAdmin' => ['SuperAdminApi', 'addAdmin'],
    'editAdmin' => ['SuperAdminApi', 'editAdmin'],
    'toggleAdminStatus' => ['SuperAdminApi', 'toggleStatus'],




];

if (!isset($routes[$action])) {
    echo json_encode(['success' => false, 'error' => 'Unknown action']);
    exit;
}

[$apiName, $method] = $routes[$action];
$api = new $apiName($_POST);
$api->$method();