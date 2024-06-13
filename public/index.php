<?php

use App\Config\ErrorLog;
use App\Config\ResponseHttp;
use App\Config\Router;
use App\Controllers\TaskController;
use App\Controllers\UserController;

require dirname(__DIR__) . '/vendor/autoload.php';

ResponseHttp::headerHttpDev($_SERVER['REQUEST_METHOD']);
ErrorLog::activateErrorLog();


// Verificar si el token está presente en los encabezados
// if (!isset($headers['Authorization']) || $headers['Authorization'] == null) {
//     echo(json_encode(ResponseHttp::status401(401, 'Unauthorized', 'Token missing')));
//     exit();
// }



// Agregar rutas de forma estática
// Router::add('auth', function() {
//     require dirname(__DIR__) . '/src/Routes/auth.php';
// });

// Router::add('user', function() {
//     require dirname(__DIR__) . '/src/Routes/user.php';
// });

// Router::add('task', function() {
//     require dirname(__DIR__) . '/src/Routes/task.php';
// });

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = isset($_GET['route']) ? $_GET['route'] : null;
$params = explode('/', $route);
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$headers = getallheaders();

Router::add('task/save', function() use ($method, $route, $params, $data, $headers) {
    $taskController = new TaskController($method, $route, $params, $data, $headers);
    $taskController->taskSave('task/save/');
});

Router::add("auth/{$params[1]}/{$params[2]}/", function() use ($method, $route, $params, $data, $headers) {
    $taskController = new UserController($method, $route, $params, $data, $headers);
    $taskController->getLogin("auth/{$params[1]}/{$params[2]}/");
});


Router::add('task/deleteTask/', function() use ($method, $route, $params, $data, $headers) {
    $taskController = new TaskController($method, $route, $params, $data, $headers);
    $taskController->deleteTask('task/deleteTask/');
});


Router::add('task/updateTask/', function() use ($method, $route, $params, $data, $headers) {
    $taskController = new TaskController($method, $route, $params, $data, $headers);
    $taskController->updateTask('task/updateTask/');
});
    
// Ejecutar el router de forma estática
Router::run();

?>
