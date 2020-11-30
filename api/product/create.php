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

    $images = $data->images;
    $jwt = $data->jwt;
    if (!empty($jwt)) {
        try {
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $hasError = false;
            foreach ($images as $image){
                if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                    $image = substr($image, strpos($image, ',') + 1);
                    $type = strtolower($type[1]);

                    if (!in_array($type, [ 'jpg', 'jpeg', 'png', 'bmp' ])) {
                        $hasError = true;
                        http_response_code(400);
                        echo json_encode(array(
                            "error" => "Неверный формат",
                        ));
                        break;
                    }
                    $image = str_replace( ' ', '+', $image );
                    $image = base64_decode($image);

                    if ($image === false) {
                        $hasError = true;
                        http_response_code(400);
                        echo json_encode(array(
                            "error" => "Не удалоось загрузить изображение",
                        ));
                        break;
                    }
                    else{
                        $name = uniqid();
                        file_put_contents("src/{$name}.{$type}", $image);
                    }
                } else {
                    $hasError = true;
                    http_response_code(400);
                    echo json_encode(array(
                        "error" => "Не удалоось загрузить изображение",
                    ));
                    break;
                }
            }
            if (!$hasError){
                http_response_code(200);
                echo json_encode(array(
                    "message" => "Успех",
                ));
            }

        }
        catch (Exception $e) {
            http_response_code(401);
            echo json_encode(array(
                "error" => "Доступ закрыт.",
            ));
        }
    }
    else{
        http_response_code(401);
        echo json_encode(array(
            "error" => "Доступ закрыт.",
        ));
    }

}


