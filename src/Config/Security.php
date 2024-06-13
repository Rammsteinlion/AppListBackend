<?php

namespace App\Config;

use Dotenv\Dotenv;
use Dotenv\Util\Str;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;


session_start();


class Security
{

    private static $jwt_data;
    private static $_uri = array();
    private static $_action = array();


    final public static function secretKey(): string
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
        return $_ENV['SECRET_KEY'];
    }

    final public static function createPassowrd(string $pw): string
    {
        $pass = password_hash($pw, PASSWORD_DEFAULT);
        return $pass;
    }

    final public static function validatePassword(string $pw, string $pwh): bool
    {

        if (password_verify($pw, $pwh)) {
            return true;
        } else {
            error_log('La contraseña es incorrecta');
            return false;
        }
    }

    final public static function createTokenJwt(string $key, array $data): string
    {
        $payload = array(
            'iat' => time(),
            'exp' => time() + (30 * 60),
            'data' => $data,
        );

        $jwt = JWT::encode($payload, $key, 'HS256');
        self::$jwt_data = $jwt;
        return $jwt;
    }


    final public static function validateTokenJwt(string $token, string $key)
    {

        if (!isset(getallheaders()['Authorization'])) {
            die(json_encode(ResponseHttp::status400('El token de acceso en requerido')));
            exit;
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $decodedArray = (array) $decoded;

            //validar tiempo
            $currentime = time();
            if ($decodedArray['exp'] - $currentime <  (30 * 60)) {
                //generar nuevo tiempo con dos minutos de mas 
                $decodedArray['exp'] = $currentime + (30 * 60);
                $newToken = JWT::encode($decodedArray, $key, 'HS256');
                return [
                    'token' => $newToken,
                    'payload' => $decodedArray
                ];
            }
            return $decodedArray;
        } catch (ExpiredException $e) {
            error_log('Token ha expirado: ' . $e->getMessage());
            die(json_encode(ResponseHttp::status401('Token ha expirado')));
        } catch (\Exception $e) {
            error_log('Token inválido: ' . $e->getMessage());
            die(json_encode(ResponseHttp::status401('Token inválido')));
        }
    }


    // final public static function validateTokenJwt(string $token, string $key)
    // {

    //     if (!isset(getallheaders()['Authorization'])) {
    //         die(json_encode(ResponseHttp::status400('El token de acceso en requerido')));
    //         exit;
    //     }

    //     try {
    //         $decoded = JWT::decode($token, new Key($key, 'HS256'));
    //         return (array) $decoded; 
    //     } catch (ExpiredException $e) {
    //         error_log('Token ha expirado: ' . $e->getMessage());
    //         die(json_encode(ResponseHttp::status401('Token ha expirado')));
    //     } catch (\Exception $e) {
    //         error_log('Token inválido: ' . $e->getMessage());
    //         die(json_encode(ResponseHttp::status401('Token inválido')));
    //     }
    // }

    //retornar los datos del jwt en un json
    final public static function getDataJwt(): array
    {
        $jwt_decode_array = json_decode(self::$jwt_data, true);
        if (isset($jwt_decode_array['data'])) {
            return $jwt_decode_array['data'];
        }
        return [];
    }

     final public static function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

     final public static function destroySession()
    {
        session_start();
        session_unset();
        session_destroy();
    }

    final public static function add($uri, $action = null) 
    {
        self::$_uri[] = '/' . trim($uri, '/');

        if ($action != null) 
        {
            self::$_action[] = $action;
        }
    }
}
