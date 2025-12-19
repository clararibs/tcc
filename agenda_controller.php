<?php
require_once 'conexao.php';

$action = $_POST['action'] ?? '';

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
        echo "<script>alert('Ação não reconhecida'); window.history.back();</script>";
        break;
}

function salvarConfiguracao($conn) {
    
    $tipo = $_POST['tipo'] ?? '';
    $dia_semana = $_POST['dia_semana'] ?? '';
    $horario_inicio = $_POST['horario_inicio'] ?? '';
    $horario_fim = $_POST['horario_fim'] ?? '';
    
    if (empty($tipo) || empty($dia_semana) || empty($horario_inicio) || empty($horario_fim)) {
        echo "<script>alert('Preencha todos os campos!'); window.history.back();</script>";
        exit;
    }
    
    try {
        // DELETE
        $stmt = $conn->prepare("DELETE FROM config_disponibilidade WHERE tipo = ? AND dia_semana = ?");
        $stmt->bind_param("ss", $tipo, $dia_semana);
        $stmt->execute();
        $stmt->close();
        
        // INSERT
        $stmt = $conn->prepare("INSERT INTO config_disponibilidade (tipo, dia_semana, horario_inicio, horario_fim) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $tipo, $dia_semana, $horario_inicio, $horario_fim);
        $stmt->execute();
        $stmt->close();
        
        echo "<script>
            alert('✅ Configuração salva!');
            window.location.href = 'configurar_disponibilidade.html';
        </script>";
        
    } catch (Exception $e) {
        echo "<script>alert('Erro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
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
            // Inserir com NULL para horários
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
    
    $duracao = $_POST['duracao'] ?? 60;
    $duracao = intval($duracao);
    
    try {
        // Limpa slots existentes
        $conn->query("DELETE FROM slots_disponiveis WHERE status = 'disponivel'");
        
        // Obtém as disponibilidades
        $result = $conn->query("
            SELECT * FROM config_disponibilidade 
            WHERE tipo = 'disponibilidade' AND ativo = 1
            ORDER BY dia_semana, horario_inicio
        ");
        
        $slotsGerados = 0;
        $diasParaGerar = 60; // Gerar para 60 dias à frente
        
        if ($result && $result->num_rows > 0) {
            while ($disponibilidade = $result->fetch_assoc()) {
                // Converte dia da semana para número (segunda=1, terça=2, etc.)
                $diaSemanaNumero = diaSemanaParaNumero($disponibilidade['dia_semana']);
                
                // Para cada um dos próximos dias
                for ($i = 0; $i < $diasParaGerar; $i++) {
                    $dataAtual = date('Y-m-d', strtotime("+$i days"));
                    $diaSemanaAtual = date('N', strtotime($dataAtual)); // 1=segunda, 7=domingo
                    
                    // Verifica se é o dia correto da semana
                    if ($diaSemanaAtual == $diaSemanaNumero) {
                        // Verifica se há exceção para esta data
                        $excecaoResult = $conn->query("
                            SELECT * FROM config_excecoes 
                            WHERE data_excecao = '$dataAtual'
                        ");
                        
                        // Se não houver exceção, gera slots
                        if ($excecaoResult->num_rows == 0) {
                            $slotsGerados += gerarSlotsParaDisponibilidade(
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
        }
        
        if ($result) {
            $result->free();
        }
        
        echo "<script>
            alert('✅ Slots gerados com sucesso! Foram criados $slotsGerados slots.');
            window.location.href = 'configuracao_agenda.html';
        </script>";
        
    } catch (Exception $e) {
        echo "<script>alert('Erro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}

function gerarSlotsParaDisponibilidade($conn, $disponibilidade, $data, $duracao) {
    $slotsGerados = 0;
    
    $horaInicio = strtotime($disponibilidade['horario_inicio']);
    $horaFim = strtotime($disponibilidade['horario_fim']);
    
    // Verifica intervalos fixos neste dia
    $diaSemana = $disponibilidade['dia_semana'];
    $intervaloResult = $conn->query("
        SELECT * FROM config_disponibilidade 
        WHERE tipo = 'intervalo' AND dia_semana = '$diaSemana' AND ativo = 1
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
    
    // Gera slots dentro do horário disponível
    $slotAtual = $horaInicio;
    
    while ($slotAtual + ($duracao * 60) <= $horaFim) {
        $slotInicio = $slotAtual;
        $slotFim = $slotAtual + ($duracao * 60);
        
        // Verifica se o slot não está dentro de um intervalo
        $dentroDeIntervalo = false;
        foreach ($intervalos as $intervalo) {
            if ($slotInicio >= $intervalo['inicio'] && $slotFim <= $intervalo['fim']) {
                $dentroDeIntervalo = true;
                break;
            }
        }
        
        if (!$dentroDeIntervalo) {
            // Insere o slot no banco
            $inicioTime = date('H:i:s', $slotInicio);
            $fimTime = date('H:i:s', $slotFim);
            
            $stmt = $conn->prepare("
                INSERT INTO slots_disponiveis (data_slot, horario_inicio, horario_fim, status, duracao_minutos)
                VALUES (?, ?, ?, 'disponivel', ?)
            ");
            $stmt->bind_param("sssi", $data, $inicioTime, $fimTime, $duracao);
            $stmt->execute();
            $stmt->close();
            
            $slotsGerados++;
        }
        
        // Próximo slot
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
        // Usando query direta para DELETE
        $conn->query("DELETE FROM slots_disponiveis WHERE status = 'disponivel'");
        
        echo "<script>
            alert('✅ Slots limpos!');
            window.location.href = 'configurar_disponibilidade.html';
        </script>";
        
    } catch (Exception $e) {
        echo "<script>alert('Erro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>