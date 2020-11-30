<?php

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers");

include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
}
else{
    $data = json_decode(file_get_contents("php://input"));
    $jwt = isset($data->jwt) ? $data->jwt : "";

    if (!empty($jwt)) {
        try {
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            http_response_code(200);
            echo json_encode(array(
                "message" => "Доступ разрешен.",
                "isValid" => true
            ));

        }
        catch (Exception $e) {
            http_response_code(401);
            echo json_encode(array(
                "message" => "Доступ закрыт.",
                "isValid" => false,
            ));
        }
    }

}


