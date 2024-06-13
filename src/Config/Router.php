<?php

namespace App\Config;

class Router 
{
    private static $_uri = array();
    private static $_action = array();

    public static function add($uri, $action = null) 
    {
        self::$_uri[] = trim($uri, '/');

        if ($action != null) 
        {
            self::$_action[] = $action;
        }
    }

    public static function run() 
    {
        $uriGet = isset($_GET['route']) ? trim($_GET['route'], '/') : '/';
        
        foreach (self::$_uri as $key => $value) 
        {
            if (preg_match("#^$value$#", $uriGet)) 
            {
                $action = self::$_action[$key];
                self::runAction($action);
                return;
            }
        }

        echo json_encode(ResponseHttp::status404());
        error_log('No existe la ruta');
        exit;
    }

    private static function runAction($action) 
    {
        if ($action instanceof \Closure)
        {
            $action();
        }  
        else 
        {
            $params = explode('@', $action);
            $controller = 'App\\Controllers\\' . $params[0];
            $method = $params[1];
            $obj = new $controller;
            $obj->$method();
        }
    }
}

?>
