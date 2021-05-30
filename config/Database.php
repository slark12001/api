<?php


class Database
{
    private string $host = "localhost";
    private string $db_name = "test";
    private string $username = "root";
    private string $password = "";
    public PDO|null $conn;

    // получаем соединение с БД
    public function getConnection(): PDO|null
    {

        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}