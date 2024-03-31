<?php
include_once "libs/php-jwt/JWTExceptionWithPayloadInterface.php";
include_once "libs/php-jwt/BeforeValidException.php";
include_once "libs/php-jwt/ExpiredException.php";
include_once "libs/php-jwt/SignatureInvalidException.php";
include_once "libs/php-jwt/JWT.php";
include_once "libs/php-jwt/Key.php";

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

function access_token($jwt, $key){
    if ($jwt) {
        try {
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
            http_response_code(200);
        }
        catch (Exception $e) {
            http_response_code(401);
            echo json_encode(array(
                "message" => "Время токена истекло",
            ));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("error" => "unauthorized"));
    }
    
}

?>