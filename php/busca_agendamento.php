<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'conexao.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $sql = "SELECT * FROM agendamentos ORDER BY data_consulta DESC, hora_inicio DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $agendamentos
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar agendamentos: ' . $e->getMessage()
    ]);
}
?>