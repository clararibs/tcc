//php da consfiguração da agenda

<?php
require_once 'conexao.php';

$tipo = $_GET['tipo'] ?? '';

header('Content-Type: application/javascript');

switch ($tipo) {
    case 'disponibilidade':
        carregarDisponibilidade();
        break;
    case 'intervalo':
        carregarIntervalo();
        break;
    case 'excecoes':
        carregarExcecoes();
        break;
    default:
        echo "// Tipo não especificado";
        break;
}

function carregarDisponibilidade() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT * FROM config_disponibilidade 
            WHERE tipo = 'disponibilidade' AND ativo = 1
            ORDER BY dia_semana, horario_inicio
        ");
        
        echo "window.disponibilidades = [\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  {\n";
            echo "    id_config: " . $row['id_config'] . ",\n";
            echo "    dia_semana: '" . $row['dia_semana'] . "',\n";
            echo "    horario_inicio: '" . $row['horario_inicio'] . "',\n";
            echo "    horario_fim: '" . $row['horario_fim'] . "',\n";
            echo "    tipo: '" . $row['tipo'] . "'\n";
            echo "  },\n";
        }
        echo "];\n";
        
    } catch (PDOException $e) {
        echo "window.disponibilidades = [];\n";
    }
}

function carregarIntervalo() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT * FROM config_disponibilidade 
            WHERE tipo = 'intervalo' AND ativo = 1
            ORDER BY dia_semana, horario_inicio
        ");
        
        echo "window.intervalos = [\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  {\n";
            echo "    id_config: " . $row['id_config'] . ",\n";
            echo "    dia_semana: '" . $row['dia_semana'] . "',\n";
            echo "    horario_inicio: '" . $row['horario_inicio'] . "',\n";
            echo "    horario_fim: '" . $row['horario_fim'] . "',\n";
            echo "    tipo: '" . $row['tipo'] . "'\n";
            echo "  },\n";
        }
        echo "];\n";
        
    } catch (PDOException $e) {
        echo "window.intervalos = [];\n";
    }
}

function carregarExcecoes() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT * FROM config_excecoes 
            ORDER BY data_excecao
        ");
        
        echo "window.excecoes = [\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  {\n";
            echo "    id_excecao: " . $row['id_excecao'] . ",\n";
            echo "    data_excecao: '" . $row['data_excecao'] . "',\n";
            echo "    horario_inicio: " . ($row['horario_inicio'] ? "'" . $row['horario_inicio'] . "'" : 'null') . ",\n";
            echo "    horario_fim: " . ($row['horario_fim'] ? "'" . $row['horario_fim'] . "'" : 'null') . ",\n";
            echo "    motivo: '" . addslashes($row['motivo']) . "'\n";
            echo "  },\n";
        }
        echo "];\n";
        
    } catch (PDOException $e) {
        echo "window.excecoes = [];\n";
    }
}
?>