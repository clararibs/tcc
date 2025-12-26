<?php
// agenda_crud.php
session_start();

// Inclui a conexão com o banco de dados
require_once 'conexao.php';

// Verifica a ação solicitada
$acao = isset($_GET['acao']) ? $_GET['acao'] : (isset($_POST['acao']) ? $_POST['acao'] : '');

// SWITCH principal das ações
switch($acao) {
    
    // ========== LISTAR CONSULTAS ==========
    case 'listar_consultas':
        $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
        $end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d', strtotime('+1 month'));
    
        $stmt = $conn->prepare("
            SELECT 
                id_consulta as id,
                nome_paciente as name,
                telefone as phone,
                email,
                procedimento as procedure,
                data_consulta as data_consulta,
                hora_inicio as hora_inicio,
                duracao_minutos as duration
            FROM consultas 
            WHERE data_consulta BETWEEN ? AND ?
            ORDER BY data_consulta, hora_inicio
        ");
        
        $stmt->bind_param("ss", $start, $end);
        $stmt->execute();
        $result = $stmt->get_result();
        $consultas = $result->fetch_all(MYSQLI_ASSOC);
        
        // Gera JavaScript para o FullCalendar
        header('Content-Type: application/javascript');
        echo "var consultasCalendario = [\n";
        
        foreach($consultas as $consulta) {
            // Calcula hora_fim baseado na duração
            $hora_fim = date('H:i:s', strtotime($consulta['hora_inicio'] . " + {$consulta['duration']} minutes"));
            
            echo "  {\n";
            echo "    id: '" . $consulta['id'] . "',\n";
            echo "    title: '" . addslashes(($consulta['procedure'] ?? 'Consulta') . ' - ' . ($consulta['name'] ?? '')) . "',\n";
            echo "    start: '" . $consulta['data_consulta'] . "T" . $consulta['hora_inicio'] . "',\n";
            echo "    end: '" . $consulta['data_consulta'] . "T" . $hora_fim . "',\n";
            echo "    extendedProps: {\n";
            echo "      name: '" . addslashes($consulta['name'] ?? '') . "',\n";
            echo "      phone: '" . addslashes($consulta['phone'] ?? '') . "',\n";
            echo "      email: '" . addslashes($consulta['email'] ?? '') . "',\n";
            echo "      procedure: '" . addslashes($consulta['procedure'] ?? '') . "',\n";
            echo "      durationText: '" . ($consulta['duration'] ?? 0) . " minutos',\n";
            echo "      id_consulta: " . ($consulta['id'] ?? 0) . "\n";
            echo "    }\n";
            echo "  },\n";
        }
        
        echo "];\n";
        exit;
        break;
    
    // ========== CARREGAR CONFIGURAÇÕES ==========
    case 'carregar_configuracoes':
        // Carrega disponibilidades ativas
        $result = $conn->query("
            SELECT 
                dia_semana,
                TIME_FORMAT(horario_inicio, '%H:%i:%s') as horario_inicio,
                TIME_FORMAT(horario_fim, '%H:%i:%s') as horario_fim
            FROM config_disponibilidade 
            WHERE tipo = 'disponibilidade' AND ativo = 1
            ORDER BY dia_semana, horario_inicio
        ");
        
        $disponibilidades = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $disponibilidades[] = $row;
            }
            $result->free();
        }
        
        // Carrega intervalos ativos
        $result = $conn->query("
            SELECT 
                dia_semana,
                TIME_FORMAT(horario_inicio, '%H:%i:%s') as horario_inicio,
                TIME_FORMAT(horario_fim, '%H:%i:%s') as horario_fim
            FROM config_disponibilidade 
            WHERE tipo = 'intervalo' AND ativo = 1
            ORDER BY dia_semana, horario_inicio
        ");
        
        $intervalos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $intervalos[] = $row;
            }
            $result->free();
        }
        
        // Carrega exceções
        $result = $conn->query("
            SELECT 
                DATE_FORMAT(data_excecao, '%Y-%m-%d') as data_excecao,
                TIME_FORMAT(horario_inicio, '%H:%i:%s') as horario_inicio,
                TIME_FORMAT(horario_fim, '%H:%i:%s') as horario_fim,
                motivo
            FROM config_excecoes 
            WHERE data_excecao >= CURDATE()
            ORDER BY data_excecao, horario_inicio
        ");
        
        $excecoes = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $excecoes[] = $row;
            }
            $result->free();
        }
        
        // Gera JavaScript com as configurações
        header('Content-Type: application/javascript');
        
        echo "// Configurações de disponibilidade\n";
        echo "var disponibilidadesCalendario = [\n";
        foreach($disponibilidades as $disp) {
            echo "  {\n";
            echo "    dia_semana: '" . addslashes($disp['dia_semana'] ?? '') . "',\n";
            echo "    horario_inicio: '" . addslashes($disp['horario_inicio'] ?? '') . "',\n";
            echo "    horario_fim: '" . addslashes($disp['horario_fim'] ?? '') . "',\n";
            echo "  },\n";
        }
        echo "];\n\n";
        
        echo "// Configurações de intervalo\n";
        echo "var intervalosCalendario = [\n";
        foreach($intervalos as $int) {
            echo "  {\n";
            echo "    dia_semana: '" . addslashes($int['dia_semana'] ?? '') . "',\n";
            echo "    horario_inicio: '" . addslashes($int['horario_inicio'] ?? '') . "',\n";
            echo "    horario_fim: '" . addslashes($int['horario_fim'] ?? '') . "',\n";
            echo "  },\n";
        }
        echo "];\n\n";
        
        echo "// Configurações de exceção\n";
        echo "var excecoesCalendario = [\n";
        foreach($excecoes as $exc) {
            echo "  {\n";
            echo "    data_excecao: '" . addslashes($exc['data_excecao'] ?? '') . "',\n";
            echo "    horario_inicio: '" . addslashes($exc['horario_inicio'] ?? '') . "',\n";
            echo "    horario_fim: '" . addslashes($exc['horario_fim'] ?? '') . "',\n";
            echo "    motivo: '" . addslashes($exc['motivo'] ?? '') . "',\n";
            echo "  },\n";
        }
        echo "];\n";
        exit;
        break;
    
    // ========== SALVAR CONSULTA ==========
    case 'salvar_consulta':
        $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
        $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $procedimento = isset($_POST['procedimento']) ? trim($_POST['procedimento']) : '';
        $data = isset($_POST['data']) ? $_POST['data'] : '';
        $hora = isset($_POST['hora']) ? $_POST['hora'] : '';
        $duracao = isset($_POST['duracao']) ? intval($_POST['duracao']) : 0;
        
        // Validações básicas
        if(empty($nome) || empty($telefone) || empty($procedimento) || empty($data) || empty($hora) || $duracao <= 0) {
            header('Location: agenda.html?msg=' . urlencode('Preencha todos os campos obrigatórios!') . '&tipo=error');
            exit;
        }
        
        // Formata hora para HH:MM:SS
        $hora_formatada = (strlen($hora) == 5) ? $hora . ':00' : $hora;
        $hora_fim = date('H:i:s', strtotime("$hora_formatada + $duracao minutes"));
        
        try {
            // Verifica se já existe consulta neste horário
            $stmtCheck = $conn->prepare("
                SELECT id_consulta FROM consultas 
                WHERE data_consulta = ? 
                  AND (
                    (hora_inicio < ? AND hora_fim > ?) OR
                    (hora_inicio >= ? AND hora_inicio < ?)
                  )
                LIMIT 1
            ");
            $stmtCheck->bind_param("sssss", $data, $hora_fim, $hora_formatada, $hora_formatada, $hora_fim);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            
            if($resultCheck->num_rows > 0) {
                header('Location: agenda.html?msg=' . urlencode('Já existe uma consulta agendada neste horário!') . '&tipo=error');
                exit;
            }
            
            // Insere a consulta
            $stmt = $conn->prepare("
                INSERT INTO consultas 
                (nome_paciente, telefone, email, procedimento, data_consulta, hora_inicio, hora_fim, duracao_minutos)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param("sssssssi", $nome, $telefone, $email, $procedimento, $data, $hora_formatada, $hora_fim, $duracao);
            
            if ($stmt->execute()) {
                // Sucesso - redireciona para a agenda.html
                header('Location: agenda.html?msg=' . urlencode('Consulta agendada com sucesso!') . '&tipo=success');
                exit;
            } else {
                throw new Exception("Erro ao executar query: " . $stmt->error);
            }
            
        } catch(Exception $e) {
            header('Location: agenda.html?msg=Erro ao agendar consulta: ' . urlencode($e->getMessage()) . '&tipo=error');
        }
        exit;
        break;
    
    // ========== EXCLUIR CONSULTA ==========
    case 'excluir_consulta':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if($id > 0) {
            try {
                // Primeiro busca os dados da consulta
                $stmt = $conn->prepare("
                    SELECT data_consulta, hora_inicio 
                    FROM consultas 
                    WHERE id_consulta = ?
                ");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $consulta = $result->fetch_assoc();
                
                if($consulta) {
                    // Exclui a consulta
                    $stmt = $conn->prepare("DELETE FROM consultas WHERE id_consulta = ?");
                    $stmt->bind_param("i", $id);
                    
                    if ($stmt->execute()) {
                        // Sucesso
                        header('Location: agenda.html?msg=' . urlencode('Consulta excluída com sucesso!') . '&tipo=success');
                        exit;
                    } else {
                        throw new Exception("Erro ao excluir consulta: " . $stmt->error);
                    }
                    
                } else {
                    header('Location: agenda.html?msg=' . urlencode('Consulta não encontrada!') . '&tipo=error');
                    exit;
                }
                
            } catch(Exception $e) {
                header('Location: agenda.html?msg=' . urlencode('Erro ao excluir consulta: ') . urlencode($e->getMessage()) . '&tipo=error');
                exit;
            }
        } else {
            header('Location: agenda.html?msg=' . urlencode('ID inválido!') . '&tipo=error');
            exit;
        }
        break;
    
    // ========== AÇÃO DEFAULT ==========
    default:
        // Se acessado diretamente, apenas exibe status
        echo "<h1>Sistema de Agenda - CRUD</h1>";
        echo "<p>Status: Conectado ao banco de dados</p>";
        echo "<p>Ações disponíveis:</p>";
        echo "<ul>";
        echo "<li><a href='?acao=listar_consultas'>listar_consultas</a> - Retorna eventos para o calendário</li>";
        echo "<li><a href='?acao=carregar_configuracoes'>carregar_configuracoes</a> - Carrega disponibilidades, intervalos e exceções</li>";
        echo "<li><a href='agenda.html'>Voltar para a agenda</a></li>";
        echo "</ul>";
        break;
}

// Fecha conexão
if (isset($conn)) {
    $conn->close();
}
?>