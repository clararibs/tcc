<?php include "conexao.php"; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha do Paciente - Hedone</title>
    <link rel="stylesheet" href="ficha.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

    <header class="cabecalho-ficha">
        <a href="entrar_aline.html" class="btn-voltar" onclick="confirmarVoltar(event)">&#8592; Voltar</a>
    </header>

    <main class="container-ficha">
        <div class="ficha-conteudo">
            <h1>FICHA DO PACIENTE</h1>

            <form id="fichaFormulario" action="#" method="post">

    <div class="campo">
        <label for="nome">Nome Completo *</label>
        <input type="text" id="nome" name="nome" required>
    </div>

    <div class="campo">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email">
    </div>

    <div class="campo">
        <label for="telefone">Telefone *</label>
        <input type="tel" id="telefone" name="telefone" required>
    </div>

    <div class="campo">
        <label for="idade">Idade</label>
        <input type="number" id="idade" name="idade">
    </div>

    <div class="campo campo-descricao">
        <label for="descricao">Descrição/Anotações</label>
        <textarea id="descricao" name="descricao" rows="5"></textarea>
    </div>
</form>

        </div>
    </main>

    <script src="js/ficha-paciente.js"></script>
</body>
</html>