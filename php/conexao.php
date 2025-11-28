<?php

class Database {
    private $host = "3.150.114.68";
    private $db_name = "hedone_bd"; // Nome do seu banco
    private $username = "tcc_hedone"; // Seu usuário MySQL
    private $password = "1hN^83}"; // Sua senha MySQL
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>