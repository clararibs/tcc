<?php
// salvar_agendamento.php - Processa solicitações de agendamento
header('Content-Type: application/json; charset=utf-8');

// Incluir conexão
include 'conexao.php';

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Receber dados JSON do formulário
$data = json_decode(file_get_contents('php://input'), true);

// OU receber via POST tradicional (ajuste conforme seu formulário)
if (empty($data)) {
    $data = $_POST;
}

// Dados do formulário HTML (solicitar_agendamento.html)
$nome_completo = $data['fullname'] ?? ($data['nome_completo'] ?? '');
$telefone = $data['phone'] ?? ($data['telefone'] ?? '');
$email = $data['email'] ?? '';
$data_desejada = $data['date'] ?? ($data['data_desejada'] ?? '');
$hora = $data['hour'] ?? '';
$minuto = $data['minute'] ?? '';

// Formatar hora no formato HH:MM:SS
$hora_desejada = sprintf("%02d:%02d:00", $hora, $minuto);

// Validar campos obrigatórios
if (empty($nome_completo) || empty($telefone) || empty($email) || empty($data_desejada) || empty($hora_desejada)) {
    echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

// Validar data (não pode ser no passado)
$hoje = date('Y-m-d');
if ($data_desejada < $hoje) {
    echo json_encode(['success' => false, 'message' => 'Data não pode ser no passado']);
    exit;
}

// Validar hora (entre 8:00 e 18:00 por exemplo)
$hora_int = (int)$hora;
if ($hora_int < 8 || $hora_int > 18) {
    echo json_encode(['success' => false, 'message' => 'Horário fora do expediente (8:00-18:00)']);
    exit;
}

try {
    // Inserir na tabela de solicitações
    $sql = "INSERT INTO solicitacoes_agendamento 
            (nome_completo, email, telefone, data_desejada, hora_desejada, status) 
            VALUES (?, ?, ?, ?, ?, 'pendente')";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("sssss", $nome_completo, $email, $telefone, $data_desejada, $hora_desejada);
    
    if ($stmt->execute()) {
        $id_solicitacao = $conn->insert_id;
        
        // API
        
        
        echo json_encode([
            'success' => true, 
            'message' => '✅ Solicitação enviada com sucesso! Em breve entraremos em contato.',
            'id_solicitacao' => $id_solicitacao
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao enviar solicitação: ' . $stmt->error]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}

$conn->close();


function enviarEmailConfirmacao($email, $nome, $data, $hora, $id) {
    // API
    
    
    return true;
}
?>