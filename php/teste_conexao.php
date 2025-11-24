<?php
require_once 'conexao.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    echo "Conexão com o banco de dados estabelecida com sucesso!";
} catch (Exception $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
?>