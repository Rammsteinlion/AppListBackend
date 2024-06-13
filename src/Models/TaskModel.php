<?php

namespace App\Models;

use App\DB\ConnectionDB;
use App\Config\ResponseHttp;
use App\Config\Security;

class TaskModel extends ConnectionDB
{
    private static string $category_id;
    private static string $description;
    private static string $state;
    private static string $priority;
    private static string $created_at;
    private static string $updated_at;
    private static string $expiration_date;
    private static string $task_id;
    private static int $user_assigned;
    private static int $headers;


    public function __construct(array $data)
    {
        self::$category_id = $data['category_id'];
        self::$description = $data['description'];
        self::$state = $data['state'];
        self::$priority = $data['priority'];
        self::$created_at = $data['created_at'];
        self::$updated_at = date('Y-m-d H:i:s');
        self::$expiration_date =  $data['expiration_date'];
        self::$task_id =  $data['id'];
        self::$headers = getallheaders();
    }


    // Getter for category_id
    final public static function getCategoryID(): string
    {
        return self::$category_id;
    }

    // Setter for category_id
    final public static function setCategoryID(string $category_id): void
    {
        self::$category_id = $category_id;
    }

    // Getter for description
    final public static function getDescription(): string
    {
        return self::$description;
    }

    // Setter for description
    final public static function setDescription(string $description): void
    {
        self::$description = $description;
    }

    // Getter for state
    final public static function getState(): string
    {
        return self::$state;
    }

    // Setter for status
    final public static function setState(string $state): void
    {
        self::$state = $state;
    }

    // Getter for state
    final public static function getPriority(): string
    {
        return self::$priority;
    }

    // Setter for status
    final public static function sePriority(string $priority): void
    {
        self::$priority = $priority;
    }

    // Getter for created_at
    final public static function getCreatedAt(): string
    {
        return self::$created_at;
    }

    // Setter for created_at
    final public static function setCreatedAt(string $created_at): void
    {
        self::$created_at = $created_at;
    }

    // Getter for updated_at
    final public static function getUpdatedAt(): string
    {
        return self::$updated_at;
    }

    // Setter for updated_at
    final public static function setUpdatedAt(string $updated_at): void
    {
        self::$updated_at = $updated_at;
    }

    // Static getter method
    public static function getExpirationDate(): string
    {
        return self::$expiration_date;
    }

    // Static setter method
    public static function setExpirationDate(string $expiration_date): void
    {
        self::$expiration_date = $expiration_date;
    }

    // Getter para $task_id
    public static function getTaskId(): string
    {
        return self::$task_id;
    }

    // Setter para $task_id
    public static function setTaskId(string $task_id): void
    {
        self::$task_id = $task_id;
    }


    final public static function saveTask(string $saludo): void
    {
        var_dump($saludo);
        echo json_encode(ResponseHttp::status200("Tarea registrada con exito"));
        // var_dump('GUNCIONO');
        // die();
        // session_start();
        
        // try {

        //     die('GUARDAR');
        //     $con = self::getConnection();
        //     $query1 = "INSERT INTO task (category_id,user_assigned,description,priority,state,created_at,expiration_date) VALUES";
        //     $query2 = "(:category_id,:user_assigned,:description,:priority,:state,:created_at,:expiration_date)";
        //     $query = $con->prepare($query1 . $query2);
        //     $query->execute([
        //         "category_id" => 20,
        //         "user_assigned" => $_SESSION['user']['id_user'],
        //         "description" => self::getDescription(),
        //         "priority" => self::getState(),
        //         "state" => self::getState(),
        //         "created_at" =>  self::getCreatedAt(),
        //         "expiration_date" => self::getExpirationDate()
        //     ]);

        //     if ($query->rowCount() > 0) {
        //         echo json_encode(ResponseHttp::status200("Tarea registrada con exito"));
        //     } else {
        //         echo json_encode(ResponseHttp::status500("Error al registrar tarea"));
        //     }
        // } catch (\PDOException $pdo) {
        //     error_log("TaskModel::postSaveTask -> . $pdo");
        //     die(json_encode(ResponseHttp::status500()));
        // }
    }

    final public static function postUpdateTask(): array
    {
        Security::startSession();

        try {
            self::$user_assigned = $_SESSION['user']['id_user'];
            $con = self::getConnection();
            $query = $con->prepare("UPDATE task SET task_id = :task_id and user_assigned = :user_assigned");
            $query->execute(
                [
                    "task_id" => self::getTaskId(),
                    "user_assigned" => self::$user_assigned
                ]
            );
            if ($query->rowCount() > 0) {
                return ResponseHttp::status200("Tarea eliminada con éxito");
                Security::destroySession();
            } else {
                return ResponseHttp::status500("Error al eliminar tarea");
            }
        } catch (\PDOException $pdo) {
            error_log("TaskModel::postDeleteTask -> $pdo");
            return ResponseHttp::status500("Error en el servidor");
        }

        Security::destroySession();
    }

    final public static function postDeleteTask(): array
    {
        Security::startSession();

        try {
            self::$user_assigned = $_SESSION['user']['id_user'];
            $con = self::getConnection();
            $query = $con->prepare("DELETE FROM task WHERE task_id = :task_id and user_assigned = :user_assigned");
            $query->execute(
                [
                    "task_id" => self::getTaskId(),
                    "user_assigned" => self::$user_assigned
                ]
            );

            if ($query->rowCount() > 0) {
                return ResponseHttp::status200("Tarea eliminada con éxito");
                Security::destroySession();
            } else {
                return ResponseHttp::status500("Error al eliminar tarea");
            }
        } catch (\PDOException $pdo) {
            error_log("TaskModel::postDeleteTask -> $pdo");
            return ResponseHttp::status500("Error en el servidor");
        }
    }
}
