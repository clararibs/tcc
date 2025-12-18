<?php
// agenda_crud.php - Controlador único para todas as operações
require_once 'conexao.php';

// Configurações de erro
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica qual ação executar
$acao = $_GET['acao'] ?? $_POST['acao'] ?? '';

// ============================================
// ROTEADOR DE AÇÕES
// ============================================

switch ($acao) {
    
    // ========== CONSULTAS ==========
    case 'salvar_consulta':
        salvarConsulta();
        break;
        
    case 'excluir_consulta':
        excluirConsulta();
        break;
        
    case 'listar_consultas':
        listarConsultas();
        break;
        
    // ========== CONFIGURAÇÃO ==========
    case 'salvar_config':
        salvarConfig();
        break;
        
    case 'salvar_excecao':
        salvarExcecao();
        break;
        
    case 'excluir_excecao':
        excluirExcecao();
        break;
        
    case 'gerar_slots':
        gerarSlots();
        break;
        
    case 'limpar_slots':
        limparSlots();
        break;
        
    // ========== SOLICITAÇÕES ==========
    case 'salvar_solicitacao':
        salvarSolicitacao();
        break;
        
    case 'carregar_horarios':
        carregarHorarios();
        break;
        
    // ========== DEFAULT ==========
    default:
        // Se for requisição do FullCalendar sem ação específica, lista consultas
        if (isset($_GET['start']) && isset($_GET['end'])) {
            listarConsultas();
        }
        break;
}

// ============================================
// FUNÇÕES PRINCIPAIS
// ============================================

function salvarConsulta() {
    global $pdo;
    
    // Recebe dados
    $nome = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $procedimento = trim($_POST['procedimento'] ?? '');
    $data = trim($_POST['data'] ?? '');
    $hora = trim($_POST['hora'] ?? '');
    $duracao = intval($_POST['duracao'] ?? 0);
    
    // Validações
    if (empty($nome)) {
        redirecionarErro("Nome é obrigatório!");
        return;
    }
    if (empty($telefone)) {
        redirecionarErro("Telefone é obrigatório!");
        return;
    }
    if (empty($procedimento)) {
        redirecionarErro("Procedimento é obrigatório!");
        return;
    }
    if (empty($data)) {
        redirecionarErro("Data é obrigatória!");
        return;
    }
    if (empty($hora)) {
        redirecionarErro("Horário é obrigatório!");
        return;
    }
    if ($duracao <= 0) {
        redirecionarErro("Duração inválida!");
        return;
    }
    
    // Valida email se fornecido
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirecionarErro("E-mail inválido!");
        return;
    }
    
    // Formata hora
    $hora_inicio = $hora . ':00';
    $hora_fim = date('H:i:s', strtotime("+$duracao minutes", strtotime($hora_inicio)));
    
    try {
        // VERIFICA SE JÁ EXISTE CONSULTA NO MESMO HORÁRIO
        $stmt = $pdo->prepare("
            SELECT id_consulta 
            FROM consultas 
            WHERE data_consulta = ? 
            AND (
                (hora_inicio <= ? AND hora_inicio + INTERVAL duracao_minutos MINUTE > ?) OR
                (? <= hora_inicio AND ? >= hora_inicio)
            )
        ");
        $stmt->execute([$data, $hora_inicio, $hora_inicio, $hora_inicio, $hora_fim]);
        
        if ($stmt->rowCount() > 0) {
            redirecionarErro("❌ Já existe uma consulta neste horário!");
            return;
        }
        
        // INSERE A CONSULTA
        $stmt = $pdo->prepare("
            INSERT INTO consultas 
            (nome_paciente, telefone, email, procedimento, data_consulta, hora_inicio, duracao_minutos) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([$nome, $telefone, $email, $procedimento, $data, $hora_inicio, $duracao]);
        
        $id_consulta = $pdo->lastInsertId();
        
        // ATUALIZA SLOT SE EXISTIR
        atualizarSlot($data, $hora_inicio, 'reservado');
        
        // SUCESSO
        redirecionarSucesso("✅ Consulta agendada com sucesso!\\nID: $id_consulta\\nPaciente: $nome");
        
    } catch (PDOException $e) {
        redirecionarErro("❌ Erro ao salvar consulta: " . $e->getMessage());
    }
}

function excluirConsulta() {
    global $pdo;
    
    $id_consulta = $_GET['id'] ?? '';
    
    if (empty($id_consulta)) {
        redirecionarErro("ID da consulta não informado!");
        return;
    }
    
    try {
        // BUSCA DADOS DA CONSULTA PARA LIBERAR SLOT
        $stmt = $pdo->prepare("
            SELECT data_consulta, hora_inicio 
            FROM consultas 
            WHERE id_consulta = ?
        ");
        $stmt->execute([$id_consulta]);
        
        if ($consulta = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // LIBERA O SLOT
            atualizarSlot($consulta['data_consulta'], $consulta['hora_inicio'], 'disponivel');
        }
        
        // EXCLUI A CONSULTA
        $stmt = $pdo->prepare("DELETE FROM consultas WHERE id_consulta = ?");
        $stmt->execute([$id_consulta]);
        
        // SUCESSO
        redirecionarSucesso("✅ Consulta excluída com sucesso!");
        
    } catch (PDOException $e) {
        redirecionarErro("❌ Erro ao excluir consulta: " . $e->getMessage());
    }
}

function listarConsultas() {
    global $pdo;
    
    try {
        // Busca consultas dos últimos 30 dias e próximos 60 dias
        $stmt = $pdo->prepare("
            SELECT 
                id_consulta,
                nome_paciente,
                telefone,
                email,
                procedimento,
                data_consulta,
                hora_inicio,
                duracao_minutos
            FROM consultas 
            WHERE data_consulta >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            AND data_consulta <= DATE_ADD(CURDATE(), INTERVAL 60 DAY)
            ORDER BY data_consulta, hora_inicio
        ");
        $stmt->execute();
        
        // Formata para FullCalendar
        $eventos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Calcula hora de término
            $hora_fim = date('H:i:s', strtotime("+{$row['duracao_minutos']} minutes", strtotime($row['hora_inicio'])));
            
            // Formata título
            $titulo = $row['procedimento'] . ' - ' . $row['nome_paciente'];
            
            $eventos[] = [
                'id' => $row['id_consulta'],
                'title' => $titulo,
                'start' => $row['data_consulta'] . 'T' . $row['hora_inicio'],
                'end' => $row['data_consulta'] . 'T' . $hora_fim,
                'color' => '#2ecc71',
                'extendedProps' => [
                    'name' => $row['nome_paciente'],
                    'phone' => $row['telefone'],
                    'email' => $row['email'],
                    'procedure' => $row['procedimento'],
                    'durationText' => $row['duracao_minutos'] . ' min'
                ]
            ];
        }
        
        // Retorna como JSON para FullCalendar
        header('Content-Type: application/json');
        echo json_encode($eventos);
        
    } catch (PDOException $e) {
        // Retorna array vazio em caso de erro
        header('Content-Type: application/json');
        echo json_encode([]);
    }
}

function salvarConfig() {
    global $pdo;
    
    $tipo = $_POST['tipo'] ?? '';
    $dia_semana = $_POST['dia_semana'] ?? '';
    $horario_inicio = $_POST['horario_inicio'] ?? '';
    $horario_fim = $_POST['horario_fim'] ?? '';
    
    if (empty($tipo) || empty($dia_semana) || empty($horario_inicio) || empty($horario_fim)) {
        redirecionarErro("Preencha todos os campos da configuração!");
        return;
    }
    
    try {
        // Remove configuração existente
        $stmt = $pdo->prepare("
            DELETE FROM config_disponibilidade 
            WHERE tipo = ? AND dia_semana = ?
        ");
        $stmt->execute([$tipo, $dia_semana]);
        
        // Insere nova
        $stmt = $pdo->prepare("
            INSERT INTO config_disponibilidade (tipo, dia_semana, horario_inicio, horario_fim)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$tipo, $dia_semana, $horario_inicio, $horario_fim]);
        
        redirecionarSucesso("Configuração salva com sucesso!");
        
    } catch (PDOException $e) {
        redirecionarErro("Erro ao salvar configuração: " . $e->getMessage());
    }
}

function salvarExcecao() {
    global $pdo;
    
    $data_excecao = $_POST['data_excecao'] ?? '';
    $horario_inicio = $_POST['horario_inicio'] ?? '';
    $horario_fim = $_POST['horario_fim'] ?? '';
    $motivo = $_POST['motivo'] ?? 'Sem atendimento';
    
    if (empty($data_excecao)) {
        redirecionarErro("Selecione uma data para a exceção!");
        return;
    }
    
    try {
        // Se não tem horário, é dia todo
        if (empty($horario_inicio) || empty($horario_fim)) {
            $horario_inicio = NULL;
            $horario_fim = NULL;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO config_excecoes (data_excecao, horario_inicio, horario_fim, motivo)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$data_excecao, $horario_inicio, $horario_fim, $motivo]);
        
        redirecionarSucesso("Exceção salva com sucesso!");
        
    } catch (PDOException $e) {
        redirecionarErro("Erro ao salvar exceção: " . $e->getMessage());
    }
}

function excluirExcecao() {
    global $pdo;
    
    $id_excecao = $_GET['id'] ?? $_POST['id'] ?? '';
    
    if (empty($id_excecao)) {
        redirecionarErro("ID da exceção não informado!");
        return;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM config_excecoes WHERE id_excecao = ?");
        $stmt->execute([$id_excecao]);
        
        redirecionarSucesso("Exceção excluída com sucesso!");
        
    } catch (PDOException $e) {
        redirecionarErro("Erro ao excluir exceção: " . $e->getMessage());
    }
}

function gerarSlots() {
    global $pdo;
    
    $duracao = $_POST['duracao'] ?? 60;
    $duracao = intval($duracao);
    $dias_futuro = $_POST['dias_futuro'] ?? 60;
    
    try {
        // Lógica básica - implemente conforme sua necessidade
        redirecionarSucesso("Função gerarSlots() - Implementar conforme necessidade");
        
    } catch (PDOException $e) {
        redirecionarErro("Erro ao gerar slots: " . $e->getMessage());
    }
}

function limparSlots() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM slots_disponiveis WHERE status = 'disponivel'");
        $stmt->execute();
        
        redirecionarSucesso("Slots limpos!");
        
    } catch (PDOException $e) {
        redirecionarErro("Erro ao limpar slots: " . $e->getMessage());
    }
}

function salvarSolicitacao() {
    global $pdo;
    
    $nome = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data = trim($_POST['data'] ?? '');
    $hora = trim($_POST['hora'] ?? '');
    
    // Validações básicas
    if (empty($nome) || empty($telefone) || empty($email) || empty($data) || empty($hora)) {
        redirecionarErro("Preencha todos os campos!");
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirecionarErro("E-mail inválido!");
        return;
    }
    
    try {
        $hora_formatada = $hora . ':00';
        
        // Verifica disponibilidade
        $disponivel = verificarDisponibilidade($data, $hora_formatada);
        
        if (!$disponivel) {
            redirecionarErro("Horário não disponível!");
            return;
        }
        
        // Salva solicitação
        $stmt = $pdo->prepare("
            INSERT INTO solicitacoes_agendamento 
            (nome_completo, email, telefone, data_desejada, hora_desejada, status) 
            VALUES (?, ?, ?, ?, ?, 'confirmada')
        ");
        $stmt->execute([$nome, $email, $telefone, $data, $hora_formatada]);
        
        // Atualiza slot
        atualizarSlot($data, $hora_formatada, 'reservado');
        
        redirecionarSucesso("Solicitação enviada com sucesso!");
        
    } catch (PDOException $e) {
        redirecionarErro("Erro ao salvar solicitação: " . $e->getMessage());
    }
}

function carregarHorarios() {
    global $pdo;
    
    $data = $_GET['data'] ?? '';
    
    if (empty($data)) {
        echo "Data não informada";
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT horario_inicio 
            FROM slots_disponiveis 
            WHERE data_slot = ? 
            AND status = 'disponivel'
            ORDER BY horario_inicio
        ");
        $stmt->execute([$data]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hora = substr($row['horario_inicio'], 0, 5);
            echo "<option value='$hora'>$hora</option>";
        }
        
        if ($stmt->rowCount() == 0) {
            echo "<option value=''>Nenhum horário disponível</option>";
        }
        
    } catch (PDOException $e) {
        echo "<option value=''>Erro ao carregar</option>";
    }
}

// ============================================
// FUNÇÕES AUXILIARES
// ============================================

function atualizarSlot($data, $hora_inicio, $status) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE slots_disponiveis 
            SET status = ? 
            WHERE data_slot = ? 
            AND horario_inicio = ?
        ");
        $stmt->execute([$status, $data, $hora_inicio]);
    } catch (PDOException $e) {
        // Slot pode não existir - isso é normal
    }
}

function verificarDisponibilidade($data, $hora_inicio) {
    global $pdo;
    
    try {
        // Verifica em slots
        $stmt = $pdo->prepare("
            SELECT id_slot 
            FROM slots_disponiveis 
            WHERE data_slot = ? 
            AND horario_inicio = ? 
            AND status = 'disponivel'
        ");
        $stmt->execute([$data, $hora_inicio]);
        
        if ($stmt->rowCount() > 0) {
            return true;
        }
        
        // Verifica em consultas
        $stmt = $pdo->prepare("
            SELECT id_consulta 
            FROM consultas 
            WHERE data_consulta = ? 
            AND hora_inicio = ?
        ");
        $stmt->execute([$data, $hora_inicio]);
        
        return $stmt->rowCount() == 0;
        
    } catch (PDOException $e) {
        return false;
    }
}

function redirecionarSucesso($mensagem) {
    $msg = urlencode($mensagem);
    header("Location: ../agenda_adm.html?msg=$msg&tipo=success");
    exit;
}

function redirecionarErro($mensagem) {
    $msg = urlencode($mensagem);
    header("Location: ../agenda_adm.html?msg=$msg&tipo=error");
    exit;
}

// Se nenhuma ação foi especificada, apenas termina
?>