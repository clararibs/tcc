<?php
// carregar_slots.php
header('Content-Type: application/javascript; charset=utf-8');

require_once 'conexao.php';

// Configurar fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Obter parâmetros
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-d');
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d', strtotime('+30 days'));

// Valida datas
if (!strtotime($data_inicio) || !strtotime($data_fim)) {
    echo "window.slots = [];";
    exit();
}

// Converter para objetos DateTime
$dt_inicio = new DateTime($data_inicio);
$dt_fim = new DateTime($data_fim);
$dt_fim->modify('+1 day'); // Incluir o último dia

// Função para obter disponibilidades
function obterDisponibilidades($conn) {
    $disponibilidades = [];
    
    $sql = "SELECT dia_semana, horario_inicio, horario_fim 
            FROM disponibilidades 
            ORDER BY 
                CASE dia_semana 
                    WHEN 'segunda' THEN 1
                    WHEN 'terca' THEN 2
                    WHEN 'quarta' THEN 3
                    WHEN 'quinta' THEN 4
                    WHEN 'sexta' THEN 5
                    WHEN 'sabado' THEN 6
                    WHEN 'domingo' THEN 7
                END";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $disponibilidades[] = [
                'dia_semana' => $row['dia_semana'],
                'horario_inicio' => $row['horario_inicio'],
                'horario_fim' => $row['horario_fim']
            ];
        }
    }
    
    return $disponibilidades;
}

// Função para obter intervalos fixos
function obterIntervalos($conn) {
    $intervalos = [];
    
    $sql = "SELECT dia_semana, horario_inicio, horario_fim 
            FROM intervalos 
            ORDER BY 
                CASE dia_semana 
                    WHEN 'segunda' THEN 1
                    WHEN 'terca' THEN 2
                    WHEN 'quarta' THEN 3
                    WHEN 'quinta' THEN 4
                    WHEN 'sexta' THEN 5
                    WHEN 'sabado' THEN 6
                    WHEN 'domingo' THEN 7
                END";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $intervalos[] = [
                'dia_semana' => $row['dia_semana'],
                'horario_inicio' => $row['horario_inicio'],
                'horario_fim' => $row['horario_fim']
            ];
        }
    }
    
    return $intervalos;
}

// Função para obter exceções
function obterExcecoes($conn, $data_inicio, $data_fim) {
    $excecoes = [];
    
    $sql = "SELECT data_excecao, horario_inicio, horario_fim, motivo 
            FROM excecoes 
            WHERE data_excecao BETWEEN ? AND ? 
            ORDER BY data_excecao";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $data_inicio, $data_fim);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $excecoes[] = [
                'data_excecao' => $row['data_excecao'],
                'horario_inicio' => $row['horario_inicio'],
                'horario_fim' => $row['horario_fim'],
                'motivo' => $row['motivo']
            ];
        }
    }
    
    return $excecoes;
}

// Mapeamento de dias da semana
$dias_map = [
    0 => 'domingo',
    1 => 'segunda',
    2 => 'terca',
    3 => 'quarta',
    4 => 'quinta',
    5 => 'sexta',
    6 => 'sabado'
];

// Obter configurações
$disponibilidades = obterDisponibilidades($conn);
$intervalos = obterIntervalos($conn);
$excecoes = obterExcecoes($conn, $data_inicio, $dt_fim->format('Y-m-d'));

// Verificar se existem disponibilidades configuradas
if (empty($disponibilidades)) {
    echo "window.slots = [];";
    $conn->close();
    exit();
}

// Array para armazenar slots
$slots = [];

// Gerar slots para cada dia no período
$current_date = clone $dt_inicio;

while ($current_date < $dt_fim) {
    $data_atual = $current_date->format('Y-m-d');
    $dia_semana_num = $current_date->format('w'); // 0=domingo, 1=segunda...
    $dia_semana_nome = $dias_map[$dia_semana_num];
    
    // Encontrar disponibilidade para este dia
    $disponibilidade_dia = null;
    foreach ($disponibilidades as $disp) {
        if (strtolower($disp['dia_semana']) === $dia_semana_nome) {
            $disponibilidade_dia = $disp;
            break;
        }
    }
    
    // Verificar exceções para este dia
    $excecao_dia = null;
    foreach ($excecoes as $exc) {
        if ($exc['data_excecao'] == $data_atual) {
            $excecao_dia = $exc;
            break;
        }
    }
    
    // Se não há exceção de dia inteiro e há disponibilidade
    if (!$excecao_dia && $disponibilidade_dia) {
        $hora_inicio = $disponibilidade_dia['horario_inicio'];
        $hora_fim = $disponibilidade_dia['horario_fim'];
        
        // Converter para DateTime para facilitar cálculos
        $dt_hora_inicio = DateTime::createFromFormat('H:i:s', $hora_inicio);
        $dt_hora_fim = DateTime::createFromFormat('H:i:s', $hora_fim);
        
        // Verificar intervalos para este dia
        $intervalos_dia = [];
        foreach ($intervalos as $int) {
            if (strtolower($int['dia_semana']) === $dia_semana_nome) {
                $intervalos_dia[] = $int;
            }
        }
        
        // Obter duração do slot (padrão 60 minutos)
        $sql_duracao = "SELECT valor FROM configuracoes WHERE chave = 'duracao_slot'";
        $result_duracao = $conn->query($sql_duracao);
        $duracao_slot = 60; // padrão
        if ($result_duracao && $row = $result_duracao->fetch_assoc()) {
            $duracao_slot = intval($row['valor']);
        }
        
        // Gerar slots
        $dt_slot_atual = clone $dt_hora_inicio;
        
        while ($dt_slot_atual < $dt_hora_fim) {
            // Calcular fim do slot
            $dt_slot_fim = clone $dt_slot_atual;
            $dt_slot_fim->modify("+{$duracao_slot} minutes");
            
            // Se passar do horário disponível, parar
            if ($dt_slot_fim > $dt_hora_fim) {
                break;
            }
            
            // Verificar se o slot está dentro de algum intervalo
            $dentro_intervalo = false;
            foreach ($intervalos_dia as $intervalo) {
                $dt_int_inicio = DateTime::createFromFormat('H:i:s', $intervalo['horario_inicio']);
                $dt_int_fim = DateTime::createFromFormat('H:i:s', $intervalo['horario_fim']);
                
                // Verifica sobreposição
                if (!($dt_slot_fim <= $dt_int_inicio || $dt_slot_atual >= $dt_int_fim)) {
                    $dentro_intervalo = true;
                    break;
                }
            }
            
            // Se não está dentro de intervalo, criar slot
            if (!$dentro_intervalo) {
                // Verificar se slot já existe no banco
                $slot_inicio = $dt_slot_atual->format('H:i:s');
                $slot_fim = $dt_slot_fim->format('H:i:s');
                
                $sql_slot = "SELECT id_slot, duracao_minutos FROM slots 
                            WHERE data_slot = ? 
                            AND horario_inicio = ? 
                            AND horario_fim = ?";
                
                $stmt = $conn->prepare($sql_slot);
                $stmt->bind_param("sss", $data_atual, $slot_inicio, $slot_fim);
                $stmt->execute();
                $result_slot = $stmt->get_result();
                
                if ($result_slot && $row = $result_slot->fetch_assoc()) {
                    // Slot já existe no banco
                    $slots[] = [
                        'id_slot' => $row['id_slot'],
                        'data_slot' => $data_atual,
                        'horario_inicio' => $slot_inicio,
                        'horario_fim' => $slot_fim,
                        'duracao_minutos' => $row['duracao_minutos'] ?: $duracao_slot
                    ];
                } else {
                    // Criar novo slot
                    $sql_insert = "INSERT INTO slots (data_slot, horario_inicio, horario_fim, duracao_minutos, status) 
                                  VALUES (?, ?, ?, ?, 'disponivel')";
                    
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("sssi", $data_atual, $slot_inicio, $slot_fim, $duracao_slot);
                    
                    if ($stmt_insert->execute()) {
                        $id_slot = $stmt_insert->insert_id;
                        
                        $slots[] = [
                            'id_slot' => $id_slot,
                            'data_slot' => $data_atual,
                            'horario_inicio' => $slot_inicio,
                            'horario_fim' => $slot_fim,
                            'duracao_minutos' => $duracao_slot
                        ];
                    }
                }
            }
            
            // Avançar para o próximo slot
            $dt_slot_atual->modify("+{$duracao_slot} minutes");
        }
    }
    
    // Próximo dia
    $current_date->modify('+1 day');
}

// Fechar conexão
$conn->close();

// Output como JavaScript
echo "window.slots = " . json_encode($slots, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ";";
?>