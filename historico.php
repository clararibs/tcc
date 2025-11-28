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
        <div class="client-header">
            <button onclick="Alerta()" class="botao">oi</button>
        </div>
    </div>


    <script>

        const clientes = [
            { id: 1, nome: "Ana Silva", telefone: "(11) 99999-1234", email: "ana.silva@email.com", ultimaConsulta: "15/03/2023" },
            { id: 2, nome: "Carlos Oliveira", telefone: "(11) 98888-5678", email: "carlos.oliveira@email.com", ultimaConsulta: "10/03/2023" },
            { id: 3, nome: "Mariana Santos", telefone: "(11) 97777-9012", email: "mariana.santos@email.com", ultimaConsulta: "05/03/2023" },
            { id: 4, nome: "João Pereira", telefone: "(11) 96666-3456", email: "joao.pereira@email.com", ultimaConsulta: "28/02/2023" },
            { id: 5, nome: "Fernanda Lima", telefone: "(11) 95555-7890", email: "fernanda.lima@email.com", ultimaConsulta: "25/02/2023" },
            { id: 6, nome: "Roberto Alves", telefone: "(11) 94444-1234", email: "roberto.alves@email.com", ultimaConsulta: "20/02/2023" },
            { id: 7, nome: "Patrícia Costa", telefone: "(11) 93333-5678", email: "patricia.costa@email.com", ultimaConsulta: "18/02/2023" },
            { id: 8, nome: "Ricardo Souza", telefone: "(11) 92222-9012", email: "ricardo.souza@email.com", ultimaConsulta: "15/02/2023" }
        ];


        function carregarClientes() {
            const clientList = document.getElementById('clientList');

            let html = `

                <div class="client-header">
                    <div>Nome</div>
                    <div>Telefone</div>
                    <div>E-mail</div>
                    <div>Última <br> Consulta</div>
                    <div>Ação</div>
                </div>
            `;


            clientes.forEach(cliente => {
                html += `
                    <div class="client-row">
                        <div>${cliente.nome}</div>
                        <div>${cliente.telefone}</div>
                        <div>${cliente.email}</div>
                        <div>${cliente.ultimaConsulta}</div>
                        <div> <button onclick="Alerta()" class="botao">oi</button></div>
                    </div>
                `;
            });

            clientList.innerHTML = html;
        }


        function filtrarClientes() {
            const searchInput = document.getElementById('searchInput');
            const searchTerm = searchInput.value.toLowerCase();
            const clientList = document.getElementById('clientList');


            const clientesFiltrados = clientes.filter(cliente =>
                cliente.nome.toLowerCase().includes(searchTerm)
            );

            let html = `
                <div class="client-header">
                    <div>Nome</div>
                    <div>Telefone</div>
                    <div>E-mail</div>
                    <div>Última Consulta</div>
                    <div>Ação</div>
                </div>
            `;

            if (clientesFiltrados.length > 0) {
                clientes.forEach(cliente => {
                    html += `
                    <div class="client-row">
                        <div>${cliente.nome}</div>
                        <div>${cliente.telefone}</div>
                        <div>${cliente.email}</div>
                        <div>${cliente.ultimaConsulta}</div>
                        <div> <button onclick="Alerta()" class="botao">oi</button></div>
                        

                    </div>
                `;
                });
            } else {
                html += `<div class="no-results">Nenhum cliente encontrado com o nome "${searchTerm}"</div>`;
            }

            clientList.innerHTML = html;
        }
        function Alerta() {
            alert("Nome:");

        }





        window.onload = carregarClientes;
    </script>
</body>

</html>