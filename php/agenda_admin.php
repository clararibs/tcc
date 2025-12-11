<?php

include 'conexao.php';

$acao = $_GET['acao'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_consulta'])) {
    header('Content-Type: application/json');
    
    $nome = $_POST['nome'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $email = $_POST['email'] ?? '';
    $procedimento = $_POST['procedimento'] ?? '';
    $data = $_POST['data'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $duracao = $_POST['duracao'] ?? 0;

    if (empty($nome) || empty($procedimento) || empty($data) || empty($hora) || $duracao <= 0) {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios']);
        exit;
    }

    $data_formatada = date('Y-m-d', strtotime($data));
    $hora_formatada = date('H:i:s', strtotime($hora));
    
    $sql = "INSERT INTO consultas (nome_paciente, telefone, email, procedimento, data_consulta, hora_inicio, duracao_minutos) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $nome, $telefone, $email, $procedimento, $data_formatada, $hora_formatada, $duracao);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Consulta agendada com sucesso!',
            'id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar: ' . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
    exit;
}

if ($acao === 'excluir' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = intval($_GET['id']);
    
    $sql = "DELETE FROM consultas WHERE id_consulta = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Consulta excluída']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir']);
    }
    
    $stmt->close();
    $conn->close();
    exit;
}

if ($acao === 'buscar') {
    header('Content-Type: application/json');
    
    $sql = "SELECT id_consulta, nome_paciente, telefone, email, procedimento, 
                   data_consulta, hora_inicio, duracao_minutos 
            FROM consultas 
            ORDER BY data_consulta ASC, hora_inicio ASC";
    
    $result = $conn->query($sql);
    $consultas = [];
    
    while ($row = $result->fetch_assoc()) {
        $hora_fim = date('H:i:s', strtotime($row['hora_inicio']) + ($row['duracao_minutos'] * 60));
        
        $consultas[] = [
            'id' => $row['id_consulta'],
            'title' => $row['procedimento'] . ' - ' . $row['nome_paciente'],
            'start' => $row['data_consulta'] . 'T' . $row['hora_inicio'],
            'end' => $row['data_consulta'] . 'T' . $hora_fim,
            'allDay' => false,
            'color' => '#3788d8',
            'extendedProps' => [
                'id_consulta' => $row['id_consulta'],
                'name' => $row['nome_paciente'],
                'phone' => $row['telefone'],
                'email' => $row['email'],
                'procedure' => $row['procedimento'],
                'duracao' => $row['duracao_minutos']
            ]
        ];
    }
    
    echo json_encode($consultas);
    $conn->close();
    exit;
}

if ($acao) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
    exit;
}

echo "Arquivo PHP da agenda admin. Use com JavaScript.";
$conn->close();
?>