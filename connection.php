<?php
class DBconnection{
    private $host = "localhost";
    private $username = "root";
    private $db_name = "tester";
    private $pass = "";
    public $conn;
    
    public function Connection(){
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->pass);
            $this -> conn -> exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Произошла ошибка подключения: " .  $exception->getMessage();
        }
        return $this->conn;
    }
}
?>