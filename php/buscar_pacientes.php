<?php
// buscar_clientes.php - Retorna todos os clientes para o histórico
header('Content-Type: application/json; charset=utf-8');
include 'conexao.php';

// Parâmetros de busca
$search = $_GET['search'] ?? '';

try {
    if (!empty($search)) {
        // Busca com filtro
        $sql = "SELECT * FROM clientes 
                WHERE nome_completo LIKE ? OR email LIKE ? OR telefone LIKE ?
                ORDER BY data_cadastro DESC";
        $searchTerm = "%{$search}%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // Busca todos
        $sql = "SELECT * FROM clientes ORDER BY data_cadastro DESC";
        $result = $conn->query($sql);
    }
    
    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = [
            'id' => $row['id_cliente'],
            'nome' => $row['nome_completo'],
            'telefone' => $row['telefone'],
            'email' => $row['email'],
            'idade' => $row['idade'],
            'descricao' => $row['descricao'],
            'ultimaConsulta' => date('d/m/Y', strtotime($row['data_cadastro']))
        ];
    }
    
    echo json_encode([
        'success' => true,
        'clientes' => $clientes,
        'total' => count($clientes)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}

$conn->close();
?>