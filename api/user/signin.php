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

    $password = $data->password;
    $email = $data->email;

    if(isset($password) && isset($email)){
        $password=trim(htmlspecialchars($password));
        $email=trim(htmlspecialchars($email));
        if(filter_var($email, FILTER_VALIDATE_EMAIL) && mb_strlen($password) >= 6) {
            if (file_exists('users.json')){
                $isEmailExists = false;
                $passwordHash = null;
                $uid = null;
                $users =  json_decode(file_get_contents('users.json'), true);
                foreach ($users['users'] as $user){
                    if ($user['email']===$email){
                        $isEmailExists = true;
                        $passwordHash = $user['password'];
                        $uid = $user['uid'];
                    }
                }
                if ($isEmailExists){
                    if (password_verify($password, $passwordHash)){
                        $token = array(
                            "iss" => $iss,
                            "aud" => $aud,
                            "iat" => $iat,
                            "nbf" => $nbf,
                            "data" => array(
                                "uid" => $uid,
                                "password" => $password,
                                "email" => $email
                            )
                        );

                        http_response_code(200);

                        $jwt = JWT::encode($token, $key);
                        echo json_encode(
                            array(
                                "email" => $email,
                                "jwt"=>$jwt,
                                "message" => "Успешный вход"
                            )
                        );
                    }
                    else{
                        http_response_code(400);
                        echo json_encode(
                            array(
                                "error" => "Пароль неверен"
                            )
                        );
                    }
                }
                else{
                    http_response_code(400);
                    echo json_encode(
                        array(
                            "error" => "Данная почта не зарегистрирована"
                        )
                    );
                }
            }
            else{
                http_response_code(400);
                echo json_encode(
                    array(
                        "error" => "Данная почта не зарегистрирована"
                    )
                );
            }
        }
        else{
            http_response_code(400);
            echo json_encode(
                array(
                    "error" => "Неверный формат"
                )
            );
        }
    }
    else{
        http_response_code(400);
        echo json_encode(
            array(
                "error" => "Поля не заполнены"
            )
        );
    }

}


