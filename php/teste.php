<?php
session_start();
include_once("conexao.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redireciona se não estiver logado
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Painel Administrativo</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <style>
        a, p {
            font-size: medium;
        }
        body {
            margin: 20px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Painel Administrativo</h2>
        <p>Bem-vindo, <?php echo $_SESSION['user']; ?>!</p>
        
        <?php if ($_SESSION['tipo'] == 'A') { ?>
            <div class="mb-4">
                <h4>Cadastros</h4>
                <a href="novoProduto.php" class="btn btn-primary mb-2">Cadastrar Produto</a><br>
                <a href="novoUsuario.php" class="btn btn-primary mb-2">Cadastrar Usuário</a><br>
                <a href="novoCat.php" class="btn btn-primary mb-2">Cadastrar Categoria</a><br>
            </div>
        <?php } ?>
        
        <div class="mt-4">
            <h4>Pesquisar usuário</h4>
            <form id="formBusca">
                <div class="form-group">
                    <input type="text" id="buscar" name="buscar" class="form-control" 
                           placeholder="Digite o nome ou email..." style="width: 50%">
                </div>
            </form>
            
            <div id="conteudopesquisa" class="mt-4">
                <!-- Resultados aparecerão aqui -->
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Carregar todos os usuários ao abrir a página
        carregarUsuarios();
        
        // Pesquisar enquanto digita
        $('#buscar').keyup(function() {
            carregarUsuarios();
        });
        
        // Função para carregar usuários
        function carregarUsuarios() {
            var busca = $('#buscar').val();
            
            $.ajax({
                url: 'pesquisarUsuario.php',
                method: 'POST',
                data: { busca: busca },
                success: function(data) {
                    $('#conteudopesquisa').html(data);
                },
                error: function() {
                    $('#conteudopesquisa').html('<div class="alert alert-danger">Erro ao carregar dados</div>');
                }
            });
        }
        
        // Função para confirmar exclusão
        window.apagar = function(id, desc) {
            if (confirm("Deseja realmente apagar este registro:\n" + id + " - " + desc + "\n\n???")) {
                window.location = 'excluirUsuario.php?idUsuario=' + id;
            }
        };
    });
    </script>
</body>
</html>