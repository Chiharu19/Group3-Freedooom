<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . '/../app/config/database.php';

spl_autoload_register(function($class) {
    $paths = [
        __DIR__ . '/../app/api/' . $class . '.php',
        __DIR__ . '/../app/models/' . $class . '.php',
    ];
    foreach ($paths as $p) if (file_exists($p)) require $p;
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'POST only']);
    exit;
}

$action = $_POST['action'] ?? '';

$routes = [
    'logIn'      => ['AuthApi', 'login'],
    'addRoom'   => ['AdminApi', 'addRoom'],
    'updateRoom'   => ['AdminApi', 'updateRoom']
    // 'getUsers'   => ['UserApi', 'getUsers'],
    // 'addUser'    => ['UserApi', 'addUser'],
    // 'deleteUser' => ['UserApi', 'deleteUser'],
];

if (!isset($routes[$action])) {
    echo json_encode(['success' => false, 'error' => 'Unknown action']);
    exit;
}

[$apiName, $method] = $routes[$action];
$api = new $apiName($_POST);
$api->$method();