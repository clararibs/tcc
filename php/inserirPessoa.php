<?php
//incluir o arquivo de conexão
include_once('conexao.php');

$nome = $_POST["nome"];
$email = $_POST["email"]
$telefone = $_POST["telefone"];
$idade = $_POST["idade"];
$descricao = $_POST["descricao"];




$sql = "INSERT INTO cliente (nomePessoa, emailPessoa, telefonePessoa, idadePessoa, descricaoPessoa)
            VALUES ('$nome', '$email', '$telefone', $idade, '$descricao')";


if ($conn->query($sql) === TRUE) {
?>
    <script>
        alert("Registro salvo com sucesso!");
        window.location = "fichaPaciente.php";
    </script>

<?php
} else {
?>
    <script>
        alert("Erro ao inserir o registro");
        window.history.back();
    </script>

<?php
}

?>