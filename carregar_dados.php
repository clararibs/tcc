<?php
// carregar_dados.php
require_once 'conexao.php';

$tipo = $_GET['tipo'] ?? '';

header('Content-Type: application/javascript');

switch ($tipo) {
    case 'disponibilidade':
        carregarDisponibilidade($conn);
        break;
    case 'intervalo':
        carregarIntervalo($conn);
        break;
    case 'excecoes':
        carregarExcecoes($conn);
        break;
    default:
        echo "// Tipo não especificado: " . htmlspecialchars($tipo);
        break;
}

function carregarDisponibilidade($conn) {
    try {
        $result = $conn->query("
            SELECT * FROM config_disponibilidade 
            WHERE tipo = 'disponibilidade' AND ativo = 1
            ORDER BY 
                CASE dia_semana
                    WHEN 'segunda' THEN 1
                    WHEN 'terca' THEN 2
                    WHEN 'quarta' THEN 3
                    WHEN 'quinta' THEN 4
                    WHEN 'sexta' THEN 5
                    WHEN 'sabado' THEN 6
                    WHEN 'domingo' THEN 7
                    ELSE 8
                END,
                horario_inicio
        ");
        
        echo "window.disponibilidades = [\n";
        if ($result && $result->num_rows > 0) {
            $first = true;
            while ($row = $result->fetch_assoc()) {
                if (!$first) echo ",\n";
                $first = false;
                
                echo "  {\n";
                echo "    id_config: " . (int)$row['id_config'] . ",\n";
                echo "    dia_semana: '" . addslashes($row['dia_semana']) . "',\n";
                echo "    horario_inicio: '" . ($row['horario_inicio'] ? substr($row['horario_inicio'], 0, 5) : '') . "',\n";
                echo "    horario_fim: '" . ($row['horario_fim'] ? substr($row['horario_fim'], 0, 5) : '') . "',\n";
                echo "    tipo: '" . addslashes($row['tipo']) . "'\n";
                echo "  }";
            }
        }
        echo "\n];\n";
        
        if ($result) {
            $result->free();
        }
        
    } catch (Exception $e) {
        echo "window.disponibilidades = [];\n";
        echo "console.error('Erro ao carregar disponibilidades: " . addslashes($e->getMessage()) . "');\n";
    }
}

function carregarIntervalo($conn) {
    try {
        $result = $conn->query("
            SELECT * FROM config_disponibilidade 
            WHERE tipo = 'intervalo' AND ativo = 1
            ORDER BY 
                CASE dia_semana
                    WHEN 'segunda' THEN 1
                    WHEN 'terca' THEN 2
                    WHEN 'quarta' THEN 3
                    WHEN 'quinta' THEN 4
                    WHEN 'sexta' THEN 5
                    WHEN 'sabado' THEN 6
                    WHEN 'domingo' THEN 7
                    ELSE 8
                END,
                horario_inicio
        ");
        
        echo "window.intervalos = [\n";
        if ($result && $result->num_rows > 0) {
            $first = true;
            while ($row = $result->fetch_assoc()) {
                if (!$first) echo ",\n";
                $first = false;
                
                echo "  {\n";
                echo "    id_config: " . (int)$row['id_config'] . ",\n";
                echo "    dia_semana: '" . addslashes($row['dia_semana']) . "',\n";
                echo "    horario_inicio: '" . ($row['horario_inicio'] ? substr($row['horario_inicio'], 0, 5) : '') . "',\n";
                echo "    horario_fim: '" . ($row['horario_fim'] ? substr($row['horario_fim'], 0, 5) : '') . "',\n";
                echo "    tipo: '" . addslashes($row['tipo']) . "'\n";
                echo "  }";
            }
        }
        echo "\n];\n";
        
        if ($result) {
            $result->free();
        }
        
    } catch (Exception $e) {
        echo "window.intervalos = [];\n";
        echo "console.error('Erro ao carregar intervalos: " . addslashes($e->getMessage()) . "');\n";
    }
}

function carregarExcecoes($conn) {
    try {
        $result = $conn->query("
            SELECT * FROM config_excecoes 
            ORDER BY data_excecao
        ");
        
        echo "window.excecoes = [\n";
        if ($result && $result->num_rows > 0) {
            $first = true;
            while ($row = $result->fetch_assoc()) {
                if (!$first) echo ",\n";
                $first = false;
                
                echo "  {\n";
                echo "    id_excecao: " . (int)$row['id_excecao'] . ",\n";
                echo "    data_excecao: '" . addslashes($row['data_excecao']) . "',\n";
                echo "    horario_inicio: " . ($row['horario_inicio'] ? "'" . substr($row['horario_inicio'], 0, 5) . "'" : 'null') . ",\n";
                echo "    horario_fim: " . ($row['horario_fim'] ? "'" . substr($row['horario_fim'], 0, 5) . "'" : 'null') . ",\n";
                echo "    motivo: '" . addslashes($row['motivo']) . "'\n";
                echo "  }";
            }
        }
        echo "\n];\n";
        
        if ($result) {
            $result->free();
        }
        
    } catch (Exception $e) {
        echo "window.excecoes = [];\n";
        echo "console.error('Erro ao carregar exceções: " . addslashes($e->getMessage()) . "');\n";
    }
}

$conn->close();
?>