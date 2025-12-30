<?php
// carregar_slots_simples.php
header('Content-Type: application/javascript; charset=utf-8');
require_once 'conexao.php';

// Pega período para carregar
$start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
$end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d', strtotime('+1 month'));

try {
    // 1. Busca TODOS os slots disponíveis no período
    $sql = "
        SELECT * FROM slots_disponiveis 
        WHERE status = 'disponivel'
        AND data_slot BETWEEN ? AND ?
        ORDER BY data_slot, horario_inicio
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $slots = [];
    while ($row = $result->fetch_assoc()) {
        $slots[] = $row;
    }
    $stmt->close();
    
    // 2. Busca TODAS as consultas no período para verificar disponibilidade
    $sql = "
        SELECT * FROM consultas 
        WHERE data_consulta BETWEEN ? AND ?
        ORDER BY data_consulta, hora_inicio
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $consultas = [];
    while ($row = $result->fetch_assoc()) {
        $consultas[] = $row;
    }
    $stmt->close();
    
    // 3. Filtra slots que NÃO têm consulta agendada
    $slots_disponiveis = [];
    
    foreach ($slots as $slot) {
        $slot_disponivel = true;
        
        foreach ($consultas as $consulta) {
            if ($slot['data_slot'] == $consulta['data_consulta']) {
                // Verifica sobreposição de horários
                $slot_inicio = strtotime($slot['horario_inicio']);
                $slot_fim = strtotime($slot['horario_fim']);
                $consulta_inicio = strtotime($consulta['hora_inicio']);
                $consulta_fim = strtotime($consulta['hora_fim']);
                
                if ($slot_inicio < $consulta_fim && $slot_fim > $consulta_inicio) {
                    $slot_disponivel = false;
                    break;
                }
            }
        }
        
        if ($slot_disponivel) {
            $slots_disponiveis[] = $slot;
        }
    }
    
    // 4. Gera JavaScript com os slots disponíveis
    echo "window.slotsDisponiveisCalendario = [\n";
    
    foreach($slots_disponiveis as $slot) {
        // Formata para FullCalendar
        $startStr = $slot['data_slot'] . 'T' . $slot['horario_inicio'];
        $endStr = $slot['data_slot'] . 'T' . $slot['horario_fim'];
        
        // Formata hora para exibição
        $hora_inicio = date('H:i', strtotime($slot['horario_inicio']));
        
        echo "  {\n";
        echo "    id: 'slot_" . $slot['id_slot'] . "',\n";
        echo "    title: '✅ Disponível (" . $slot['duracao_minutos'] . "min)',\n";
        echo "    start: '" . $startStr . "',\n";
        echo "    end: '" . $endStr . "',\n";
        echo "    color: '#4CAF50',\n";
        echo "    textColor: '#2e7d32',\n";
        echo "    extendedProps: {\n";
        echo "      tipo: 'slot_disponivel',\n";
        echo "      duracao: " . $slot['duracao_minutos'] . ",\n";
        echo "      id_slot: " . $slot['id_slot'] . ",\n";
        echo "      data_slot: '" . $slot['data_slot'] . "',\n";
        echo "      horario_inicio: '" . $slot['horario_inicio'] . "',\n";
        echo "      horario_fim: '" . $slot['horario_fim'] . "'\n";
        echo "    }\n";
        echo "  },\n";
    }
    
    echo "];\n";
    
    echo "// DEBUG: " . count($slots_disponiveis) . " slots disponíveis encontrados\n";
    
} catch (Exception $e) {
    echo "window.slotsDisponiveisCalendario = [];\n";
    echo "// Erro: " . addslashes($e->getMessage()) . "\n";
}

$conn->close();
?>