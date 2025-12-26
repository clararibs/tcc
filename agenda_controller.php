<?php
// agenda_controller.php - VERSÃO COMPLETA COM SLOTS
require_once 'conexao.php';

// DEBUG
error_log("=== AGENDA CONTROLLER INICIADO ===");

// SÓ ACEITA POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("ERRO: Método não permitido. Use POST.");
}

// PEGA A AÇÃO
$action = $_POST['action'] ?? '';

// LOG
error_log("Action recebida: $action");

// REDIRECIONA PARA A FUNÇÃO CORRETA
switch ($action) {
    case 'salvar_config':
        salvarConfiguracao($conn);
        break;
    case 'salvar_excecao':
        salvarExcecao($conn);
        break;
    case 'excluir_excecao':
        excluirExcecao($conn);
        break;
    case 'gerar_slots':
        gerarSlots($conn);
        break;
    case 'limpar_slots':
        limparSlots($conn);
        break;
    default:
        echo "<script>alert('Ação não reconhecida: $action'); window.history.back();</script>";
        exit;
}

// ========== FUNÇÕES ==========

function salvarConfiguracao($conn) {
    // RECEBE OS DADOS
    $tipo = $_POST['tipo'] ?? '';
    $dia_semana = $_POST['dia_semana'] ?? '';
    $horario_inicio = $_POST['horario_inicio'] ?? '';
    $horario_fim = $_POST['horario_fim'] ?? '';
    
    // VALIDAÇÃO
    if (empty($tipo) || empty($dia_semana) || empty($horario_inicio) || empty($horario_fim)) {
        echo "<script>alert('Preencha todos os campos!'); window.history.back();</script>";
        exit;
    }
    
    // CORREÇÃO DO DIA DA SEMANA
    $dia_corrigido = strtolower(trim($dia_semana));
    
    // MAPEAMENTO DIRETO
    $mapa_dias = [
        '0' => 'domingo',
        '1' => 'segunda',
        '2' => 'terca',
        '3' => 'quarta',
        '4' => 'quinta',
        '5' => 'sexta',
        '6' => 'sabado',
        '7' => 'domingo',
        'segunda' => 'segunda',
        'segunda-feira' => 'segunda',
        'terça' => 'terca',
        'terca' => 'terca',
        'terça-feira' => 'terca',
        'quarta' => 'quarta',
        'quarta-feira' => 'quarta',
        'quinta' => 'quinta',
        'quinta-feira' => 'quinta',
        'sexta' => 'sexta',
        'sexta-feira' => 'sexta',
        'sábado' => 'sabado',
        'sabado' => 'sabado',
        'domingo' => 'domingo'
    ];
    
    if (isset($mapa_dias[$dia_corrigido])) {
        $dia_final = $mapa_dias[$dia_corrigido];
    } else {
        $dia_final = $dia_corrigido;
    }
    
    // SALVAR NO BANCO
    try {
        // DELETE ANTIGO
        $sql_delete = "DELETE FROM config_disponibilidade WHERE tipo = ? AND dia_semana = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("ss", $tipo, $dia_final);
        $stmt_delete->execute();
        $stmt_delete->close();
        
        // INSERT NOVO
        $sql_insert = "INSERT INTO config_disponibilidade (tipo, dia_semana, horario_inicio, horario_fim, ativo) 
                       VALUES (?, ?, ?, ?, 1)";
        
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssss", $tipo, $dia_final, $horario_inicio, $horario_fim);
        
        if ($stmt_insert->execute()) {
            $id_novo = $stmt_insert->insert_id;
            
            echo "<script>
                alert('✅ Configuração salva com sucesso!\\\\n\\\\nTipo: $tipo\\\\nDia: $dia_final\\\\nHorário: $horario_inicio - $horario_fim');
                window.location.href = 'configurar_disponibilidade.html';
            </script>";
        } else {
            throw new Exception("Erro ao inserir: " . $stmt_insert->error);
        }
        
        $stmt_insert->close();
        
    } catch (Exception $e) {
        echo "<script>
            alert('❌ ERRO AO SALVAR: " . addslashes($e->getMessage()) . "');
            window.history.back();
        </script>";
    }
}

function salvarExcecao($conn) {
    $data_excecao = $_POST['data_excecao'] ?? '';
    $horario_inicio = $_POST['horario_inicio'] ?? '';
    $horario_fim = $_POST['horario_fim'] ?? '';
    $motivo = $_POST['motivo'] ?? 'Sem atendimento';
    
    if (empty($data_excecao)) {
        echo "<script>alert('Selecione uma data!'); window.history.back();</script>";
        exit;
    }
    
    try {
        if (empty($horario_inicio) || empty($horario_fim)) {
            $stmt = $conn->prepare("INSERT INTO config_excecoes (data_excecao, horario_inicio, horario_fim, motivo) VALUES (?, NULL, NULL, ?)");
            $stmt->bind_param("ss", $data_excecao, $motivo);
        } else {
            $stmt = $conn->prepare("INSERT INTO config_excecoes (data_excecao, horario_inicio, horario_fim, motivo) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $data_excecao, $horario_inicio, $horario_fim, $motivo);
        }
        
        $stmt->execute();
        $stmt->close();
        
        echo "<script>
            alert('✅ Exceção salva!');
            window.location.href = 'configurar_disponibilidade.html';
        </script>";
        
    } catch (Exception $e) {
        echo "<script>alert('Erro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}

function excluirExcecao($conn) {
    $id_excecao = $_POST['id_excecao'] ?? '';
    
    if (empty($id_excecao)) {
        echo "<script>alert('ID não especificado!'); window.history.back();</script>";
        exit;
    }
    
    try {
        $stmt = $conn->prepare("DELETE FROM config_excecoes WHERE id_excecao = ?");
        $stmt->bind_param("i", $id_excecao);
        $stmt->execute();
        $stmt->close();
        
        echo "<script>
            alert('✅ Exceção excluída!');
            window.location.href = 'configurar_disponibilidade.html';
        </script>";
        
    } catch (Exception $e) {
        echo "<script>alert('Erro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}

function gerarSlots($conn) {
    error_log("=== GERAR SLOTS INICIADO ===");
    
    $duracao = $_POST['duracao'] ?? 60;
    $duracao = intval($duracao);
    
    // Valida duração
    if ($duracao < 15 || $duracao > 240) {
        echo "<script>alert('Duração inválida! Use entre 15 e 240 minutos.'); window.history.back();</script>";
        exit;
    }
    
    try {
        // 1. Limpa slots existentes
        $conn->query("DELETE FROM slots_disponiveis WHERE status = 'disponivel'");
        error_log("Slots antigos limpos");
        
        // 2. Obtém as disponibilidades ativas
        $result = $conn->query("
            SELECT * FROM config_disponibilidade 
            WHERE tipo = 'disponibilidade' AND ativo = 1
            ORDER BY dia_semana, horario_inicio
        ");
        
        if (!$result || $result->num_rows == 0) {
            echo "<script>
                alert('❌ Nenhuma disponibilidade configurada!\\\\nConfigure primeiro os horários disponíveis.');
                window.location.href = 'configurar_disponibilidade.html';
            </script>";
            exit;
        }
        
        $slotsGerados = 0;
        $diasParaGerar = 60;
        $dataInicio = date('Y-m-d');
        
        // 3. Para cada disponibilidade
        while ($disponibilidade = $result->fetch_assoc()) {
            $diaSemanaTexto = $disponibilidade['dia_semana'];
            $diaSemanaNumero = diaSemanaParaNumero($diaSemanaTexto);
            
            // Para cada um dos próximos dias
            for ($i = 0; $i < $diasParaGerar; $i++) {
                $dataAtual = date('Y-m-d', strtotime("+$i days", strtotime($dataInicio)));
                $diaSemanaAtual = date('N', strtotime($dataAtual));
                
                // Verifica se é o dia correto da semana
                if ($diaSemanaAtual == $diaSemanaNumero) {
                    // Verifica se há exceção para esta data
                    $excecaoResult = $conn->query("
                        SELECT * FROM config_excecoes 
                        WHERE data_excecao = '$dataAtual'
                        AND (horario_inicio IS NULL OR horario_inicio = '')
                    ");
                    
                    // Se não houver exceção de dia inteiro, gera slots
                    if ($excecaoResult->num_rows == 0) {
                        $slotsGerados += gerarSlotsParaDia(
                            $conn, 
                            $disponibilidade, 
                            $dataAtual, 
                            $duracao
                        );
                    }
                    $excecaoResult->free();
                }
            }
        }
        
        $result->free();
        
        // 4. Retorna sucesso
        echo "<script>
            alert('✅ Slots gerados com sucesso!\\\\nForam criados $slotsGerados slots de $duracao minutos.');
            window.location.href = 'configurar_disponibilidade.html?slots_gerados=$slotsGerados';
        </script>";
        
    } catch (Exception $e) {
        error_log("ERRO em gerarSlots: " . $e->getMessage());
        echo "<script>
            alert('❌ ERRO ao gerar slots: " . addslashes($e->getMessage()) . "');
            window.history.back();
        </script>";
    }
}

function gerarSlotsParaDia($conn, $disponibilidade, $data, $duracao) {
    $slotsGerados = 0;
    
    // Horários de início e fim da disponibilidade
    $horaInicio = strtotime($disponibilidade['horario_inicio']);
    $horaFim = strtotime($disponibilidade['horario_fim']);
    
    // Obtém intervalos fixos para este dia da semana
    $diaSemana = $disponibilidade['dia_semana'];
    $intervaloResult = $conn->query("
        SELECT * FROM config_disponibilidade 
        WHERE tipo = 'intervalo' 
        AND dia_semana = '$diaSemana' 
        AND ativo = 1
        ORDER BY horario_inicio
    ");
    
    $intervalos = [];
    if ($intervaloResult && $intervaloResult->num_rows > 0) {
        while ($intervalo = $intervaloResult->fetch_assoc()) {
            $intervalos[] = [
                'inicio' => strtotime($intervalo['horario_inicio']),
                'fim' => strtotime($intervalo['horario_fim'])
            ];
        }
    }
    if ($intervaloResult) $intervaloResult->free();
    
    // Obtém exceções com horário específico para esta data
    $excecaoResult = $conn->query("
        SELECT * FROM config_excecoes 
        WHERE data_excecao = '$data'
        AND horario_inicio IS NOT NULL 
        AND horario_inicio != ''
        ORDER BY horario_inicio
    ");
    
    $excecoes = [];
    if ($excecaoResult && $excecaoResult->num_rows > 0) {
        while ($excecao = $excecaoResult->fetch_assoc()) {
            $excecoes[] = [
                'inicio' => strtotime($excecao['horario_inicio']),
                'fim' => strtotime($excecao['horario_fim'])
            ];
        }
    }
    if ($excecaoResult) $excecaoResult->free();
    
    // Gera slots dentro do horário disponível
    $slotAtual = $horaInicio;
    
    while ($slotAtual + ($duracao * 60) <= $horaFim) {
        $slotInicio = $slotAtual;
        $slotFim = $slotAtual + ($duracao * 60);
        
        // Verifica se o slot NÃO está dentro de um intervalo
        $dentroDeIntervalo = false;
        foreach ($intervalos as $intervalo) {
            if ($slotInicio >= $intervalo['inicio'] && $slotFim <= $intervalo['fim']) {
                $dentroDeIntervalo = true;
                break;
            }
        }
        
        // Verifica se o slot NÃO está dentro de uma exceção com horário
        $dentroDeExcecao = false;
        foreach ($excecoes as $excecao) {
            if ($slotInicio >= $excecao['inicio'] && $slotFim <= $excecao['fim']) {
                $dentroDeExcecao = true;
                break;
            }
        }
        
        // Se não estiver em intervalo nem exceção, cria o slot
        if (!$dentroDeIntervalo && !$dentroDeExcecao) {
            $inicioTime = date('H:i:s', $slotInicio);
            $fimTime = date('H:i:s', $slotFim);
            
            $stmt = $conn->prepare("
                INSERT INTO slots_disponiveis 
                (data_slot, horario_inicio, horario_fim, duracao_minutos, status) 
                VALUES (?, ?, ?, ?, 'disponivel')
            ");
            $stmt->bind_param("sssi", $data, $inicioTime, $fimTime, $duracao);
            
            if ($stmt->execute()) {
                $slotsGerados++;
            }
            
            $stmt->close();
        }
        
        // Avança para o próximo slot
        $slotAtual += ($duracao * 60);
    }
    
    return $slotsGerados;
}

function diaSemanaParaNumero($diaSemana) {
    $dias = [
        'segunda' => 1,
        'terca' => 2,
        'quarta' => 3,
        'quinta' => 4,
        'sexta' => 5,
        'sabado' => 6,
        'domingo' => 7
    ];
    
    return $dias[strtolower($diaSemana)] ?? 1;
}

function limparSlots($conn) {
    try {
        // Limpa apenas slots disponíveis
        $result = $conn->query("DELETE FROM slots_disponiveis WHERE status = 'disponivel'");
        $linhasAfetadas = $conn->affected_rows;
        
        echo "<script>
            alert('✅ $linhasAfetadas slots disponíveis foram removidos!');
            window.location.href = 'configurar_disponibilidade.html';
        </script>";
        
    } catch (Exception $e) {
        echo "<script>
            alert('❌ ERRO ao limpar slots: " . addslashes($e->getMessage()) . "');
            window.history.back();
        </script>";
    }
}
?>