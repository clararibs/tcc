<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!isset($data['id']) && !isset($data['nome'])) {
        echo json_encode([
            'success' => false,
            'message' => 'ID ou nome do paciente não fornecido'
        ]);
        exit;
    }
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Buscar por ID ou nome
        if (isset($data['id'])) {
            $sql = "SELECT * FROM pacientes WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $data['id']);
        } else {
            $sql = "SELECT * FROM pacientes WHERE nome LIKE :nome";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':nome', '%' . $data['nome'] . '%');
        }
        
        $stmt->execute();
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($paciente) {
            // Buscar procedimentos do paciente (se tiver tabela separada)
            $sql_procedimentos = "SELECT * FROM procedimentos WHERE paciente_id = :paciente_id ORDER BY data_procedimento DESC";
            $stmt_procedimentos = $conn->prepare($sql_procedimentos);
            $stmt_procedimentos->bindValue(':paciente_id', $paciente['id']);
            $stmt_procedimentos->execute();
            $procedimentos = $stmt_procedimentos->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'paciente' => $paciente,
                'procedimentos' => $procedimentos
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Paciente não encontrado'
            ]);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao buscar paciente: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
}
?>