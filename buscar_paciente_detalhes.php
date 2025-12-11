<?php
header("Content-Type: application/json; charset=UTF-8");
include_once("conexao.php");

// Receber JSON do fetch
$dados = json_decode(file_get_contents("php://input"), true);
$id = intval($dados["id"]);

try {

    // Buscar dados gerais do paciente
    $sql = "SELECT 
                idPessoa AS id,
                CONCAT(nomePessoa, ' ', sobrenomePessoa) AS nome,
                telefone,
                email,
                dataNasc,
                descricao
            FROM tbpessoa
            WHERE idPessoa = $id
            LIMIT 1";

    $result = $conn->query($sql);

    if ($result->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "Paciente não encontrado"
        ]);
        exit;
    }

    $paciente = $result->fetch_assoc();

    // Calcular idade
    $idade = null;
    if (!empty($paciente["dataNasc"])) {
        $dataNasc = new DateTime($paciente["dataNasc"]);
        $hoje = new DateTime();
        $idade = $dataNasc->diff($hoje)->y;
    }

    $paciente["idade"] = $idade;

    // Buscar procedimentos (caso sua tabela tenha isso)
    $sqlProc = "SELECT nome_procedimento, data_procedimento 
                FROM tbprocedimentos 
                WHERE idPessoa = $id
                ORDER BY data_procedimento DESC";

    $resultProc = $conn->query($sqlProc);

    $procedimentos = [];
    if ($resultProc->num_rows > 0) {
        while ($row = $resultProc->fetch_assoc()) {
            $procedimentos[] = $row;
        }
    }

    echo json_encode([
        "success" => true,
        "paciente" => $paciente,
        "procedimentos" => $procedimentos
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao buscar detalhes: " . $e->getMessage()
    ]);
}
