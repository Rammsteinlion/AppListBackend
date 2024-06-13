<?php 

namespace App\Config;

date_default_timezone_set('America/Bogota');

class ErrorLog{

    public static function activateErrorLog():void{
        error_reporting(E_ALL); //habilitando todos los errores
        ini_set('ignore_repeated_errors', TRUE); //Ignorar errores repetidos
        ini_set('display_errors', FALSE); //no muestre los errores en el navegador
        ini_set('log_errors', TRUE); //HABILITANDO EL LOG DE ERRORES
        ini_set('error_log', dirname(__DIR__) . '/Logs/php-error.log'); //
    }

}

?>
