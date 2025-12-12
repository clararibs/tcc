<?php

if (ob_get_length()) ob_clean();

header('Content-Type: text/plain; charset=utf-8');


ob_start();

include 'conexao.php';

$search = $_GET['search'] ?? '';

try {
    if (!empty($search)) {
        $sql = "SELECT * FROM clientes 
                WHERE nome_completo LIKE ? OR email LIKE ? OR telefone LIKE ?
                ORDER BY data_cadastro DESC";
        $searchTerm = "%{$search}%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT * FROM clientes ORDER BY data_cadastro DESC";
        $result = $conn->query($sql);
    }
    
    // Verificar se encontrou clientes
    if ($result->num_rows === 0) {
        echo "NENHUM_CLIENTE";
        exit;
    }
    
    $output = "";
    $count = 0;
    
    while ($row = $result->fetch_assoc()) {
        // Formato: ID,Nome,Telefone,Email,Idade,DataCadastro
        $output .= $row['id_cliente'] . "," .
                   $row['nome_completo'] . "," .
                   $row['telefone'] . "," .
                   ($row['email'] ?: 'N/A') . "," .
                   ($row['idade'] ?: 'N/A') . "," .
                   date('d/m/Y', strtotime($row['data_cadastro']));
        
        $count++;
        
        // Separar clientes com ponto e vírgula, exceto no último
        if ($count < $result->num_rows) {
            $output .= ";";
        }
    }
    
    echo $output;
    
} catch (Exception $e) {
    echo "ERRO:" . $e->getMessage();
}

$conn->close();
?>










ob_end_flush();
?>