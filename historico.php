<?php include "conexao.php"; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="historico.css">
    <title>Consulta de Clientes - Clínica</title>
</head>

<body>
    <div class="container">
        <header>
            <button class="back-button" onclick="voltar()">
                <a href="entrar_aline.html"><i>←</i> Voltar</a>
            </button>
            <h1>Consulta de Clientes</h1>
        </header>

        <div class="search-container">
            <input type="text" class="search-input" id="searchInput" placeholder="Digite o nome do cliente..."
                onkeyup="filtrarClientes()">
            <button class="search-button" onclick="filtrarClientes()">Pesquisar</button>
        </div>

        <div class="client-list" id="clientList">

        </div>
       
    </div>


   
    <script>
        async function carregarClientes() {
            try {
                const response = await fetch('buscar_clientes.php');
                const resultado = await response.json();
                
                if (resultado.success) {
                    exibirClientes(resultado.clientes);
                } else {
                    alert('Erro ao carregar clientes: ' + resultado.message);
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro de conexão com o servidor');
            }
        }
        
        async function filtrarClientes() {
            const searchTerm = document.getElementById('searchInput').value;
            
            try {
                const response = await fetch(`buscar_clientes.php?search=${encodeURIComponent(searchTerm)}`);
                const resultado = await response.json();
                
                if (resultado.success) {
                    exibirClientes(resultado.clientes);
                }
            } catch (error) {
                console.error('Erro:', error);
            }
        }
        
        function exibirClientes(clientes) {
            const clientList = document.getElementById('clientList');
            
            let html = `
                <div class="client-header">
                    <div>Nome</div>
                    <div>Telefone</div>
                    <div>E-mail</div>
                    <div>Idade</div>
                    <div>Último Cadastro</div>
                    <div>Ação</div>
                </div>
            `;
            
            if (clientes.length > 0) {
                clientes.forEach(cliente => {
                    html += `
                        <div class="client-row">
                            <div>${cliente.nome}</div>
                            <div>${cliente.telefone}</div>
                            <div>${cliente.email || 'N/A'}</div>
                            <div>${cliente.idade || 'N/A'}</div>
                            <div>${cliente.ultimaConsulta}</div>
                            <div>
                                <button onclick="verDetalhes(${cliente.id})" class="botao">
                                    Ver
                                </button>
                            </div>
                        </div>
                    `;
                });
            } else {
                html += `<div class="no-results">Nenhum cliente encontrado</div>`;
            }
            
            clientList.innerHTML = html;
        }
        
        function verDetalhes(idCliente) {
            alert(`Detalhes do cliente ID: ${idCliente}`);
            // Aqui você pode redirecionar para uma página de detalhes ou abrir modal
        }
        
        function voltar() {
            window.history.back();
        }
        
        // Carregar clientes ao abrir a página
        window.onload = carregarClientes;
    </script>
</body>

</html>