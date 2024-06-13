<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Models\UserModel;
use Rakit\Validation\Validator;


class UserController
{

    private static $valdiate_rol  = '/^[1,2,3]{1,1}$/';
    private static $valdiate_number  = '/^[0-9]+$/';
    private static $valdiate_text  = '/^[a-zA-Z]+$/';

    private static string $method;
    private static string $route;
    private static array $params;
    private static $data;
    private static $headers;

    public function __construct(
        string $method,
        string $route,
        array $params,
        $data,
        $headers
    ) {
        self::$method = $method;
        self::$route = $route;
        self::$params = $params;
        self::$data = $data;
        self::$headers = $headers;
    }


    final public function postSave(string $endPoint): void
    {

        if (self::$method == 'post' && $endPoint == self::$route) {


            $validator = new Validator;
            $validation = $validator->validate(self::$data, [
                'name' => 'required|regex:/^[a-zA-Z\s]+$/',
                'username' => 'required|regex:/^[a-zA-Z\s]+$/',
                'email' => 'required|email',
                'rol'   => 'required|numeric|min:1|regex:/^[12]+$/',
                'password' => 'required|min:8',
                'confirmPassword'    => 'required|same:password'
            ]);


            if ($validation->fails()) {
                $errors = $validation->errors()->all();
                foreach ($errors as $error) {
                    echo $error . "<br>";
                }
                echo json_encode(ResponseHttp::status400('Error en los campos'));
            } else {
                new UserModel(self::$data);
                echo json_encode(UserModel::postSave());
            }
        }
    }

    final public function getLogin(string $endPoint): void
    {
        if (self::$method == 'post' && $endPoint == self::$route) {
            $username = (self::$params[1]);
            $password = (self::$params[2]);

            if (empty($username) || empty($password)) {
                echo json_encode(ResponseHttp::status400('Todos los campos son necesarios'));
            } else {
                UserModel::setUsername($username);
                UserModel::setPassword($password);
                session_start();
                echo json_encode(UserModel::Login());
            }
        } else {
            echo json_encode(ResponseHttp::status400('Todos los campos son necesarios'));
        }
    }
}

?>