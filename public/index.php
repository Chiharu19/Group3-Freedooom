<?php
session_start();

// Generate CSRF Token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// load database and config
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';

// Auto-load controllers & models
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/controllers/' . $class . '.php',
        __DIR__ . '/../app/models/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
});

// Current page
$page = $_GET['page'] ?? 'login';

// Page routes
$routes = [
    'login' => ['AuthController', 'login'],
    'logout' => ['AuthController', 'logout'],
    'admin' => ['AdminController', 'dashboard'],
    'admin-rooms' => ['AdminController', 'rooms'],
    'admin-schedules' => ['AdminController', 'schedules'],
    'admin-users' => ['AdminController', 'users'],
    'admin-requests' => ['AdminController', 'requests'],
    'student' => ['StudentController', 'dashboard'],
    'student-rooms' => ['StudentController', 'rooms'],
    'student-submit' => ['StudentController', 'submitRequest'],
    'student-requests' => ['StudentController', 'myRequests'],
    
    // Faculty Pages
    'faculty' => ['FacultyController', 'dashboard'],
    'faculty-rooms' => ['FacultyController', 'rooms'],
    'faculty-book' => ['FacultyController', 'bookRoom'],
    'faculty-my-bookings' => ['FacultyController', 'myBookings'],
    'faculty-requests' => ['FacultyController', 'studentRequests'],

    // Super Admin Routes
    'super-admin' => ['SuperAdminController', 'dashboard'],
    'super-admin-users' => ['SuperAdminController', 'users'],
    'super-admin-login' => ['SuperAdminController', 'loginView'],
    'super-admin-logout' => ['SuperAdminController', 'logout'],

];

// 404 check
if (!isset($routes[$page])) {
    http_response_code(404);
    echo "404 - Page Not Found";
    exit;
}

[$controllerName, $method] = $routes[$page];

// Initialize controller
$controller = new $controllerName();

// Call the function
$controller->$method();
