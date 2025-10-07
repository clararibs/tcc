<?php
// ===== Dados do banco =====
$host = "3.150.114.68";        // Geralmente localhost
$usuario = "tcc_hedone";   // Seu usuário do banco
$senha = "1hN^83}";       // Sua senha do banco
$banco = "hedone_db";       // Nome do banco de dados

// ===== Criando a conexão =====
$conn = new mysqli($host, $usuario, $senha, $banco);

// ===== Checando a conexão =====
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
} else {
    echo "Conexão realizada com sucesso!";
}

// ===== Fechando a conexão =====
$conn->close();
?>
