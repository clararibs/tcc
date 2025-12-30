<?php
// buscar_slots_html.php
require_once 'conexao.php';

header('Content-Type: text/html; charset=utf-8');

// Recebe a data no formato YYYY-MM-DD
$data = $_GET['data'] ?? '';

if (empty($data)) {
    echo '<div class="no-times-message">Data não informada</div>';
    exit;
}

try {
    // 1. Busca slots disponíveis para esta data
    $sql = "
        SELECT s.* 
        FROM slots_disponiveis s
        WHERE s.status = 'disponivel'
        AND s.data_slot = ?
        ORDER BY s.horario_inicio
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $data);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $slots = [];
    while ($row = $result->fetch_assoc()) {
        $slots[] = $row;
    }
    $stmt->close();
    
    // 2. Busca consultas já agendadas nesta data
    $sql = "
        SELECT * FROM consultas 
        WHERE data_consulta = ?
        AND status != 'cancelada'
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $data);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $consultas = [];
    while ($row = $result->fetch_assoc()) {
        $consultas[] = $row;
    }
    $stmt->close();
    
    // 3. Busca solicitações confirmadas nesta data
    $sql = "
        SELECT * FROM solicitacoes_agendamento 
        WHERE data_desejada = ?
        AND status = 'confirmada'
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $data);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $solicitacoes = [];
    while ($row = $result->fetch_assoc()) {
        $solicitacoes[] = $row;
    }
    $stmt->close();
    
    // 4. Filtra slots que NÃO têm consulta ou solicitação confirmada
    $slots_disponiveis = [];
    
    foreach ($slots as $slot) {
        $slot_disponivel = true;
        
        // Verifica se há consulta neste slot
        foreach ($consultas as $consulta) {
            $slot_inicio = strtotime($slot['horario_inicio']);
            $slot_fim = strtotime($slot['horario_fim']);
            $consulta_inicio = strtotime($consulta['hora_inicio']);
            $consulta_fim = strtotime($consulta['hora_fim']);
            
            // Se os horários se sobrepõem, slot não está disponível
            if ($slot_inicio < $consulta_fim && $slot_fim > $consulta_inicio) {
                $slot_disponivel = false;
                break;
            }
        }
        
        // Verifica se há solicitação confirmada neste slot
        if ($slot_disponivel) {
            foreach ($solicitacoes as $solicitacao) {
                if ($solicitacao['hora_desejada'] == $slot['horario_inicio']) {
                    $slot_disponivel = false;
                    break;
                }
            }
        }
        
        if ($slot_disponivel) {
            $slots_disponiveis[] = $slot;
        }
    }
    
    // 5. GERA HTML com os slots disponíveis
    if (empty($slots_disponiveis)) {
        // Formata data para exibição
        $data_formatada = date('d/m/Y', strtotime($data));
        echo '<div class="no-times-message">';
        echo 'Não há horários disponíveis para ' . $data_formatada . '.';
        echo '<br><small>Selecione outra data ou entre em contato.</small>';
        echo '</div>';
        exit;
    }
    
    // Gera os botões de horário
    foreach ($slots_disponiveis as $slot) {
        // Formata o horário
        $hora_inicio = substr($slot['horario_inicio'], 0, 5);
        $hora_fim = substr($slot['horario_fim'], 0, 5);
        $texto_horario = $hora_inicio . ' - ' . $hora_fim;
        $id_slot = $slot['id_slot'];
        $duracao = $slot['duracao_minutos'] ?? 60;
        
        echo '<button type="button" class="time-option" ';
        echo 'data-slot-id="' . $id_slot . '" ';
        echo 'data-hora-inicio="' . $slot['horario_inicio'] . '" ';
        echo 'title="Duração: ' . $duracao . ' minutos">';
        echo $texto_horario;
        echo '</button>';
    }
    
} catch (Exception $e) {
    echo '<div class="no-times-message">';
    echo 'Erro ao carregar horários.<br>';
    echo '<small>Tente novamente mais tarde.</small>';
    echo '</div>';
    error_log("Erro buscar_slots_html.php: " . $e->getMessage());
}

$conn->close();
?>