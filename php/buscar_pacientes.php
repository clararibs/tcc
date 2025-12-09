<?php
header("Content-Type: application/json; charset=UTF-8");
include_once("conexao.php");

try {
    // Consulta simples para buscar todos os pacientes
    $sql = "SELECT 
                idPessoa AS id,
                CONCAT(nomePessoa, ' ', sobrenomePessoa) AS nome,
                telefone,
                email,
                dataNasc
            FROM tbpessoa
            ORDER BY idPessoa DESC";

    $result = $conn->query($sql);

    $pacientes = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            // Calcular idade automaticamente
            $idade = null;
            if (!empty($row["dataNasc"])) {
                $dataNasc = new DateTime($row["dataNasc"]);
                $hoje = new DateTime();
                $idade = $dataNasc->diff($hoje)->y;
            }

            $pacientes[] = [
                "id" => $row["id"],
                "nome" => $row["nome"],
                "telefone" => $row["telefone"] ?? "",
                "email" => $row["email"] ?? "",
                "idade" => $idade
            ];
        }
    }

    echo json_encode([
        "success" => true,
        "data" => $pacientes,
        "total" => count($pacientes)
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao buscar pacientes: " . $e->getMessage()
    ]);
}
