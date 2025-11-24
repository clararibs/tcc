<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        echo json_encode([
            'success' => false,
            'message' => 'Dados inválidos ou vazios'
        ]);
        exit;
    }
    
    // Validações básicas
    if (empty($data['nome'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Nome é obrigatório'
        ]);
        exit;
    }
    
    if (empty($data['telefone'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Telefone é obrigatório'
        ]);
        exit;
    }
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Verificar se paciente já existe (por telefone ou email)
        if (!empty($data['email'])) {
            $sqlVerifica = "SELECT id FROM pacientes WHERE email = :email OR telefone = :telefone";
            $stmtVerifica = $conn->prepare($sqlVerifica);
            $stmtVerifica->bindValue(':email', $data['email']);
            $stmtVerifica->bindValue(':telefone', $data['telefone']);
            $stmtVerifica->execute();
            
            if ($stmtVerifica->fetch()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Paciente já cadastrado com este e-mail ou telefone'
                ]);
                exit;
            }
        }
        
        // Inserir novo paciente
        $sql = "INSERT INTO pacientes 
                (nome, email, telefone, idade, descricao, data_cadastro) 
                VALUES 
                (:nome, :email, :telefone, :idade, :descricao, NOW())";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bindValue(':nome', $data['nome']);
        $stmt->bindValue(':email', $data['email'] ?? '');
        $stmt->bindValue(':telefone', $data['telefone']);
        $stmt->bindValue(':idade', $data['idade'] ?? NULL, PDO::PARAM_INT);
        $stmt->bindValue(':descricao', $data['descricao'] ?? '');
        
        if ($stmt->execute()) {
            $pacienteId = $conn->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'message' => 'Paciente cadastrado com sucesso',
                'id' => $pacienteId,
                'data' => [
                    'nome' => $data['nome'],
                    'email' => $data['email'] ?? '',
                    'telefone' => $data['telefone'],
                    'idade' => $data['idade'] ?? NULL
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao salvar no banco de dados'
            ]);
        }
        
    } catch (PDOException $e) {
        // Verificar se é erro de duplicação
        if ($e->getCode() == 23000) {
            echo json_encode([
                'success' => false,
                'message' => 'Paciente já cadastrado com este e-mail ou telefone'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erro de banco de dados: ' . $e->getMessage()
            ]);
        }
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido. Use POST.'
    ]);
}
?>