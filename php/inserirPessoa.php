<?php
header('Content-Type: application/json; charset=utf-8');

include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$idade = $_POST['idade'] ?? NULL;
$descricao = $_POST['descricao'] ?? '';

if (empty($nome) || empty($telefone)) {
    echo json_encode(['success' => false, 'message' => 'Nome e telefone são obrigatórios']);
    exit;
}

if ($idade !== NULL && (!is_numeric($idade) || $idade < 0 || $idade > 150)) {
    echo json_encode(['success' => false, 'message' => 'Idade inválida']);
    exit;
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

try {
    $sql = "INSERT INTO clientes (nome_completo, email, telefone, idade, descricao) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
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