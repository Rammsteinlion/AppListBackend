<?php

namespace App\DB;

use App\Config\ResponseHttp;

class Sql extends ConnectionDB
{

    public static function exists(string $request, string $condition, $param): bool
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare($request);
            $query->execute([
                $condition => $param
            ]);

            $result = ($query->rowCount() == 0) ? false : true;
            return $result;

        } catch (\PDOException $pdo) {
            error_log('Sql::exist -> ' .$pdo);
            die(json_encode(ResponseHttp::status500()));
        }
    }
}

?>