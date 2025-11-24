<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Inclui a conexão com o banco
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Lê os dados JSON do corpo da requisição
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if ($data) {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            // Prepara a SQL para inserir o agendamento
            $sql = "INSERT INTO agendamentos 
                    (id_agendamento, nome_paciente, procedimento, data_consulta, hora_inicio, duracao, status, telefone, email, observacoes) 
                    VALUES 
                    (:id, :paciente, :procedimento, :data_consulta, :hora_inicio, :duracao, :status, :telefone, :email, :observacoes)";
            
            $stmt = $conn->prepare($sql);
            
            // Bind dos parâmetros
            $stmt->bindValue(':id', $data['id']);
            $stmt->bindValue(':paciente', $data['paciente']);
            $stmt->bindValue(':procedimento', $data['procedimento']);
            $stmt->bindValue(':data_consulta', $data['data']);
            $stmt->bindValue(':hora_inicio', $data['horaInicio']);
            $stmt->bindValue(':duracao', $data['duracao']);
            $stmt->bindValue(':status', $data['status']);
            $stmt->bindValue(':telefone', $data['telefone'] ?? '');
            $stmt->bindValue(':email', $data['email'] ?? '');
            $stmt->bindValue(':observacoes', $data['observacoes'] ?? '');
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Agendamento salvo com sucesso',
                    'id' => $data['id']
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erro ao executar a query'
                ]);
            }
            
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Erro de banco de dados: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Dados JSON inválidos ou vazios'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Método não permitido. Use POST.'
    ]);
}
?>