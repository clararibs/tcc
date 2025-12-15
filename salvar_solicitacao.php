<?php
// salvar_solicitacao.php - Processa solicitações de agendamento SEM JSON
session_start();

// Incluir conexão
include 'conexao.php';

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: solicitar_agendamento.php?erro=Método não permitido');
    exit;
}

// Receber dados via POST
$nome_completo = $_POST['fullname'] ?? '';
$telefone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$data_desejada = $_POST['date'] ?? '';
$hora = $_POST['hour'] ?? '';
$minuto = $_POST['minute'] ?? '';

// Formatar hora
$hora_desejada = sprintf("%02d:%02d:00", $hora, $minuto);

// Validar campos
$erros = [];

if (empty($nome_completo)) $erros[] = "Nome é obrigatório";
if (empty($telefone)) $erros[] = "Telefone é obrigatório";
if (empty($email)) $erros[] = "Email é obrigatório";
if (empty($data_desejada)) $erros[] = "Data é obrigatória";
if (empty($hora) || empty($minuto)) $erros[] = "Horário é obrigatório";

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "Email inválido";

$hoje = date('Y-m-d');
if ($data_desejada < $hoje) $erros[] = "Data não pode ser no passado";

$hora_int = (int)$hora;
if ($hora_int < 8 || $hora_int > 18) $erros[] = "Horário deve ser entre 8:00 e 18:00";

// Se houver erros, redirecionar de volta
if (!empty($erros)) {
    $_SESSION['erros'] = $erros;
    $_SESSION['dados_form'] = $_POST;
    header('Location: solicitar_agendamento.php');
    exit;
}

// Inserir no banco
$sql = "INSERT INTO solicitacoes_agendamento 
        (nome_completo, email, telefone, data_desejada, hora_desejada, status) 
        VALUES (?, ?, ?, ?, ?, 'pendente')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $nome_completo, $email, $telefone, $data_desejada, $hora_desejada);

if ($stmt->execute()) {
    $_SESSION['sucesso'] = true;
    $_SESSION['nome_cliente'] = $nome_completo;
    $_SESSION['data'] = date('d/m/Y', strtotime($data_desejada));
    $_SESSION['hora'] = substr($hora_desejada, 0, 5);
    
    // CORREÇÃO: adicionar .php
    header('Location: solicitar_agendamento.php?sucesso=true');
} else {
    $_SESSION['erro_banco'] = "Erro ao salvar: " . $stmt->error;
    // CORREÇÃO: adicionar .php
    header('Location: solicitar_agendamento.php?erro=banco');
}

$stmt->close();
$conn->close();
?>