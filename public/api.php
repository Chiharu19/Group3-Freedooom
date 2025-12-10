<?php
// Disable error reporting to output
ini_set('display_errors', 0);
error_reporting(E_ALL);

header("Content-Type: application/json");

// Error Handler to return JSON
function jsonErrorHandler($errno, $errstr, $errfile, $errline) {
    echo json_encode(['success' => false, 'error' => "Error: $errstr in $errfile line $errline"]);
    exit;
}
set_error_handler("jsonErrorHandler");

try {
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
        'addUser'      => ['AdminApi', 'addUser'],
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
        'cancelRequest' => ['StudentApi', 'cancelRequest'],

        // Faculty Routes
        'facultyDashboard' => ['FacultyApi', 'getDashboard'],
        'facultyRooms' => ['FacultyApi', 'getRooms'],
        'facultyMyBookings' => ['FacultyApi', 'myBookings'],
        'facultyRequests' => ['FacultyApi', 'studentRequests'],
        'facultyActionRequest' => ['FacultyApi', 'actionRequest'],
        'facultyCreateBooking' => ['FacultyApi', 'createBooking'],

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
    
    // Ensure session is saved before exit
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Exception: ' . $e->getMessage()]);
}