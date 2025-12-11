<?php
// inserirPessoa.php - Processa o formulário da Ficha Paciente
header('Content-Type: application/json; charset=utf-8');

// Incluir conexão (ajuste o caminho conforme sua estrutura)
include 'conexao.php';

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Receber dados do formulário
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$idade = $_POST['idade'] ?? NULL;
$descricao = $_POST['descricao'] ?? '';

// Validar campos obrigatórios
if (empty($nome) || empty($telefone)) {
    echo json_encode(['success' => false, 'message' => 'Nome e telefone são obrigatórios']);
    exit;
}

// Validar idade se fornecida
if ($idade !== NULL && (!is_numeric($idade) || $idade < 0 || $idade > 150)) {
    echo json_encode(['success' => false, 'message' => 'Idade inválida']);
    exit;
}

// Validar email se fornecido
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

try {
    // Inserir no banco de dados
    $sql = "INSERT INTO clientes (nome_completo, email, telefone, idade, descricao) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    // Bind dos parâmetros
    $stmt->bind_param("sssis", $nome, $email, $telefone, $idade, $descricao);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Cliente cadastrado com sucesso no histórico!',
            'id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar cliente: ' . $stmt->error]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}

$conn->close();
?>