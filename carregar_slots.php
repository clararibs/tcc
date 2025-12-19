<?php
// carregar_slots.php
require_once 'conexao.php';

header('Content-Type: application/javascript');

try {
    $result = $conn->query("
        SELECT * FROM slots_disponiveis 
        WHERE status = 'disponivel'
        ORDER BY data_slot, horario_inicio
        LIMIT 500
    ");
    
    echo "window.slots = [\n";
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "  {\n";
            echo "    id_slot: " . $row['id_slot'] . ",\n";
            echo "    data_slot: '" . $row['data_slot'] . "',\n";
            echo "    horario_inicio: '" . $row['horario_inicio'] . "',\n";
            echo "    horario_fim: '" . $row['horario_fim'] . "',\n";
            echo "    duracao_minutos: " . $row['duracao_minutos'] . ",\n";
            echo "    status: '" . $row['status'] . "'\n";
            echo "  },\n";
        }
    }
    echo "];\n";
    
    if ($result) {
        $result->free();
    }
    
} catch (Exception $e) {
    echo "window.slots = [];\n";
    echo "// Erro: " . $e->getMessage() . "\n";
}
?>