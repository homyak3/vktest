<?php
include_once "connection.php";
include_once "libs/php-jwt/JWTExceptionWithPayloadInterface.php";
include_once "libs/php-jwt/BeforeValidException.php";
include_once "libs/php-jwt/ExpiredException.php";
include_once "libs/php-jwt/SignatureInvalidException.php";
include_once "libs/php-jwt/JWT.php";
use \Firebase\JWT\JWT;
class user{
    private $email;
    private $password;
    private $conn;

    private $user_id;
    private $password_check_status;

    private $key = "696969";
    private $iss = "http://vktest.org";
    private $aud = "http://vktest.com";
    private $iat = 1679903524;
    private $nbf = 1679904000;

    public function __construct(string $email_user, string $password_user)
    {   
        if (filter_var($email_user, FILTER_VALIDATE_EMAIL)){
            $database = new DBconnection();
            $db = $database->Connection();
            $this->conn = $db;
            $this->email = $email_user;
            $this->password = $password_user;
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "invalid mail"));
            exit();
        }
    }

    public function findUser($mail){
        $sql = 'SELECT * FROM `users` WHERE email = :email';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $mail);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if ($result){
            return $result;
        }
    }

    public function registerUser(){
        self::checkPassword($this->password);
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $email_find = self::findUser($this->email);
        echo $email_find;
        if (empty($email_find)){
            $sql = 'INSERT INTO `users`(`email`, `password`) VALUES (:email, :password)';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $password_hash);
            $stmt->execute();
            $email_find = self::findUser($this->email);
            foreach($email_find as $row){
                $this->user_id = $row["id"];
            }
            echo json_encode(array("password_check_status" => $this->password_check_status, "user_id" => $this->user_id));
        } else {
            echo json_encode(array("message" => "This user register"));
            exit();
        }
    }
    private function checkPassword($pwd) {
        $passwordComplexity = 0;
        if(strlen($pwd) < 8){
            $passwordComplexity += 1;
        }
        if(!preg_match("#[0-9]+#", $pwd)){
            $passwordComplexity += 1;
        }
        if(!preg_match("#[a-zA-Z]+#", $pwd)){
            $passwordComplexity += 1;
        }
        if ($passwordComplexity == 0) {
            $this->password_check_status = "perfect";
        } elseif($passwordComplexity == 1) {
            $this->password_check_status = "good";
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "weak_password"));
            exit();
        }
    }

    public function auth(){
        $result = self::findUser($this->email);
        if (!empty($result)){
            foreach($result as $row){
                $this->user_id = $row["id"];
                if(password_verify($this->password, $row["password"])){
                    $token = array(
                        "iss" => $this->iss,
                        "aud" => $this->aud,
                        "iat" => $this->iat,
                        "nbf" => $this->nbf,
                        "data" => array(
                            "id" => $this->user_id,
                        )
                     );
                    $jwt = JWT::encode($token, $this->key, 'HS256');
                    echo json_encode(array("access_token" => $jwt));
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "incorrect mail or password"));
                    exit();
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "incorrect mail or password"));
            exit();
        }
        return $jwt;
    }
}
?>