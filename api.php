<?php
session_start();
include_once "api_method.php";
include_once "validate_token.php";
include_once "api/config.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if(isset($_GET["api_method"])) {
    $api_method = $_GET['api_method'];
    $requestData = json_decode(file_get_contents('php://input'), true);
    
    $email = isset($requestData['email']) ? $requestData['email'] : null;
    $password = isset($requestData['password']) ? $requestData['password'] : null;

    switch ($api_method) {
        case "register":
            if ($email && $password) {
                $new_user = new user($email, $password);
                $new_user->registerUser();
            }
            break;
        case "authorize":
            if ($email && $password) {
                $new_user = new user($email, $password);
                $jwt_token = $new_user->auth();
            }
            break;
        case "feed":
            $jwt_token = isset($requestData['jwt']) ? $requestData['jwt'] : null;
            if ($jwt_token) {
                access_token($jwt_token, $key);
            }
            break;
    }
}

?>
