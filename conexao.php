<?php
// Configurações de conexão
$host = 'localhost:3306s';
$user = 'root';
$password = '';
$database = 'teste1';

// Criar conexão
$conn = new mysqli($host, $user, $password, $database);

// Checar conexão
if ($conn->connect_error) {
    die("ERRO CONEXAO:" . $conn->connect_error);
}

echo "DEBUG: Conexão estabelecida com sucesso!<br>";
?>