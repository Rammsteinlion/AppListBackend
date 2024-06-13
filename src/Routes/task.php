<?php

// use App\Config\ResponseHttp;
// use App\Config\Security;
// use App\Controllers\TaskController;

// // Obtener todos los encabezados
// $headers = getallheaders();

// // Verificar si el token está presente en los encabezados
// if (!isset($headers['Authorization']) || $headers['Authorization'] == null) {
//     echo(json_encode(ResponseHttp::status401(401, 'Unauthorized', 'Token missing')));
//     exit();
// }


// // Extraer el token de los encabezados
// $token = $headers['Authorization'];
// $validateJwt = Security::validateTokenJwt($token, Security::secretKey());

// $method = strtolower($_SERVER['REQUEST_METHOD']);
// $route = isset($_GET['route']) ? $_GET['route'] : null;
// $params = explode('/', $route);
// $input = file_get_contents('php://input');
// $data = json_decode($input, true);
// $headers = getallheaders();

// if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
//     // Si la decodificación JSON falla, podrías intentar procesar los datos de otra manera
//     parse_str($input, $data); // Intenta analizar los datos como www-form-urlencoded
// }

// $task = new TaskController($method,$route,$params,$data,$headers);
// $task->taskSave('task/',$data);
// $task->deleteTask('task/deleteTask/');

?>
