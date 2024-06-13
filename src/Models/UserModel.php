<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\DB\ConnectionDB;
use App\DB\Sql;
use App\Config\Security;

session_start();

class UserModel extends ConnectionDB
{

    private static string $id_user;
    private static string $name;
    private static string $username;
    private static string $email;
    private static string $password;
    private static int $rol;
    private static string $idToken;
    private static string $created_at;

    public function __construct(array $data)
    {
        self::$name = $data['name'];
        self::$username = $data['username'];
        self::$email = $data['email'];
        self::$rol = $data['rol'];
        self::$password = $data['password'];
    }

    final public static function getName(): string
    {
        return self::$name;
    }
    final public static function getUsername(): string
    {
        return self::$username;
    }
    final public static function getEmail(): string
    {
        return self::$email;
    }
    final public static function getRol(): int
    {
        return self::$rol;
    }
    final public static function getPassword(): string
    {
        return self::$password;
    }

    final public static function getIDToken(): string
    {
        return self::$idToken;
    }

    final public static function getDate(): string
    {
        return self::$created_at;
    }


    final public static function setName(string $name): void
    {
        self::$name = $name;
    }
    final public static function setUsername(string $username): void
    {
        self::$username = $username;
    }
    final public static function setEmail(string $email): void
    {
        self::$email = $email;
    }
    final public static function setRol(int $rol): void
    {
        self::$rol = $rol;
    }
    final public static function setPassword(string $password): void
    {
        self::$password = $password;
    }

    final public static function setIDToken(string $idToken): string
    {
        return self::$idToken = $idToken;
    }

    final public static function setDate(string $created_at): string
    {
        return self::$created_at = $created_at;
    }


    final public static function postSave(): void

    {
        if (Sql::exists("SELECT username FROM user WHERE username = :username", ":username", self::getUsername())) {
            echo json_encode(ResponseHttp::status400('El username ya se encuentra registrado'));
        } else if (Sql::exists("SELECT email FROM user WHERE email = :email", ":email", self::getEmail())) {
            echo json_encode(ResponseHttp::status400('El email ya se encuentra registrado'));
        } else {
            self::setIDToken(hash('sha512', self::getUsername() . self::getEmail()));
            self::setDate(date("d-m-y H:i:s"));

            try {
                $con = self::getConnection();
                $query1 = "INSERT INTO user (name,username,email,password,rol,idToken,created_at) VALUES";
                $query2 = "(:name,:username,:email,:password,:rol,:idToken,:created_at)";
                $query = $con->prepare($query1 . $query2);
                $query->execute([
                    ':name' => self::getName(),
                    ':username' => self::getUsername(),
                    ':email' => self::getEmail(),
                    ':password' => Security::createPassowrd(self::getPassword()),
                    ':rol' => self::getRol(),
                    ':idToken' => self::getIDToken(),
                    ':created_at' => self::getDate(),
                ]);

                if ($query->rowCount() > 0) {
                    echo json_encode(ResponseHttp::status200("Usuario registrado con exito"));
                } else {
                    echo json_encode(ResponseHttp::status500("Error al registrar Usuario"));
                }
            } catch (\PDOException $pdo) {
                error_log('UploadModel::post ->' . $pdo);
                die(json_encode(ResponseHttp::status500()));
            }
        }
    }


    final public static function Login(): array
    {
        Security::startSession();

        try {
            $con = self::getConnection()->prepare("SELECT * FROM user WHERE username = :username");
            $con->execute([
                'username' => self::getUsername()
            ]);

            if ($con->rowCount() === 0) {
                return ResponseHttp::status400('EL usuario o contraseña son incorrectas');
            } else {
                foreach ($con as $res) {
                    if (Security::validatePassword(self::getPassword(), $res['password'])) {
                        $payload = ['idToken' => $res['idToken']];
                        $token = Security::createTokenJwt(Security::secretKey(), $payload);

                        $data = [
                            'name' => $res['name'],
                            'rol' => $res['rol'],
                            'token' => $token
                        ];
                        $dataSession =[
                            'name' => (int)$res['name'],
                            'id_user' => $res['id_user'],
                        ];
                        $_SESSION['user'] = $dataSession;
                        return ResponseHttp::status200($data);
                        exit;
                    } else {
                        return ResponseHttp::status400('El usuario o contraseña incorrecto');
                    }
                }
            }
        } catch (\PDOException $pdo) {
            error_log("UserModel::Login ->" . $pdo);
            die(json_encode(ResponseHttp::status201()));
        }
    }
}
