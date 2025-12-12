<?php
session_start();
include_once("conexao.php");

if (!isset($_SESSION['user'])) {
    die("Acesso não autorizado");
}

$busca = $_POST['busca'] ?? '';

// Consulta SQL
$sql = "SELECT * FROM usuarios WHERE 1=1";
if (!empty($busca)) {
    $sql .= " AND (nome LIKE '%$busca%' OR email LIKE '%$busca%' OR id LIKE '%$busca%')";
}
$sql .= " ORDER BY nome";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-bordered">';
    echo '<thead class="thead-dark">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Nome</th>';
    echo '<th>Email</th>';
    echo '<th>Tipo</th>';
    echo '<th>Ações</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    while($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['nome'] . '</td>';
        echo '<td>' . $row['email'] . '</td>';
        echo '<td>' . $row['tipo'] . '</td>';
        echo '<td>';
        echo '<a href="editarUsuario.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning mr-2">Editar</a>';
        echo '<button onclick="apagar(' . $row['id'] . ', \'' . $row['nome'] . '\')" class="btn btn-sm btn-danger">Excluir</button>';
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
} else {
    echo '<div class="alert alert-info">Nenhum usuário encontrado.</div>';
}

$conn->close();
?>