<?php
// carregar_slots.php
require_once 'conexao.php';

$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-d');
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d', strtotime('+1 month'));

header('Content-Type: application/javascript');

try {
    $stmt = $conn->prepare("
        SELECT 
            id_slot,
            data_slot,
            horario_inicio,
            horario_fim,
            duracao_minutos,
            status
        FROM slots_disponiveis 
        WHERE data_slot BETWEEN ? AND ?
          AND status = 'disponivel'
        ORDER BY data_slot, horario_inicio
    ");
    
    $stmt->bind_param("ss", $data_inicio, $data_fim);
    $stmt->execute();
    $result = $stmt->get_result();
    $slots = $result->fetch_all(MYSQLI_ASSOC);
    
    echo "var slotsDisponiveis = [\n";
    foreach($slots as $slot) {
        echo "  {\n";
        echo "    id_slot: " . $slot['id_slot'] . ",\n";
        echo "    data_slot: '" . $slot['data_slot'] . "',\n";
        echo "    horario_inicio: '" . $slot['horario_inicio'] . "',\n";
        echo "    horario_fim: '" . $slot['horario_fim'] . "',\n";
        echo "    duracao_minutos: " . ($slot['duracao_minutos'] ?? 60) . ",\n";
        echo "    status: '" . $slot['status'] . "'\n";
        echo "  },\n";
    }
    echo "];\n";
    
} catch(Exception $e) {
    echo "var slotsDisponiveis = [];\n";
    echo "console.error('Erro ao carregar slots: " . addslashes($e->getMessage()) . "');\n";
}
?>