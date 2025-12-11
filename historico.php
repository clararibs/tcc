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
            const response = await fetch('buscar_pacientes.php');
            const texto = await response.text();
            
            // Verificar se a resposta é um erro HTML do servidor
            if (texto.includes('<!DOCTYPE html>') || texto.includes('<html>')) {
                console.error('Resposta HTML inesperada:', texto.substring(0, 200));
                alert('Erro: O servidor retornou uma página HTML. Verifique se o arquivo PHP existe.');
                return;
            }
            
            // Processar resposta em texto simples
            const resultado = processarResposta(texto);
            
            if (resultado.success) {
                exibirClientes(resultado.clientes);
            } else {
                alert('Erro: ' + resultado.message);
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro de conexão com o servidor');
        }
    }
    
    async function filtrarClientes() {
        const searchTerm = document.getElementById('searchInput').value;
        
        try {
            const response = await fetch(`buscar_pacientes.php?search=${encodeURIComponent(searchTerm)}`);
            const texto = await response.text();
            const resultado = processarResposta(texto);
            
            if (resultado.success) {
                exibirClientes(resultado.clientes);
            } else {
                // Mostrar apenas se for um erro diferente de "nenhum cliente"
                if (!resultado.message.includes('nenhum') && !resultado.message.includes('Nenhum')) {
                    alert('Erro: ' + resultado.message);
                }
            }
        } catch (error) {
            console.error('Erro:', error);
        }
    }
    
    function processarResposta(texto) {
        const resposta = texto.trim();
        
        // Se for uma mensagem de erro simples
        if (resposta.startsWith('ERRO:')) {
            return {
                success: false,
                message: resposta.substring(5)
            };
        }
        
        // Se for uma mensagem de sucesso simples
        if (resposta.startsWith('SUCESSO:')) {
            return {
                success: true,
                message: resposta.substring(8)
            };
        }
        
        // Formato: clientes=nome1,telefone1,email1,idade1;nome2,telefone2,email2,idade2
        if (resposta.startsWith('clientes=')) {
            const dados = resposta.substring(9);
            const clientesArray = dados.split(';');
            const clientes = [];
            
            for (const clienteStr of clientesArray) {
                if (clienteStr.trim() === '') continue;
                
                const campos = clienteStr.split(',');
                if (campos.length >= 4) {
                    clientes.push({
                        id: campos[0] || '',
                        nome: campos[1] || '',
                        telefone: campos[2] || '',
                        email: campos[3] || 'N/A',
                        idade: campos[4] || 'N/A',
                        ultimaConsulta: campos[5] || 'N/A'
                    });
                }
            }
            
            return {
                success: true,
                clientes: clientes
            };
        }
        
        // Formato alternativo: cada linha é um cliente
        const linhas = resposta.split('\n').filter(line => line.trim() !== '');
        if (linhas.length > 0 && linhas[0].includes(',')) {
            const clientes = linhas.map(linha => {
                const campos = linha.split(',');
                return {
                    id: campos[0] || '',
                    nome: campos[1] || '',
                    telefone: campos[2] || '',
                    email: campos[3] || 'N/A',
                    idade: campos[4] || 'N/A',
                    ultimaConsulta: campos[5] || 'N/A'
                };
            });
            
            return {
                success: true,
                clientes: clientes
            };
        }
        
        // Se for apenas "NENHUM_CLIENTE" ou similar
        if (resposta === 'NENHUM_CLIENTE' || resposta === 'nenhum') {
            return {
                success: true,
                clientes: []
            };
        }
        
        // Se não reconhecer o formato
        console.log('Resposta não processada:', resposta);
        return {
            success: false,
            message: 'Formato de resposta não reconhecido: ' + resposta.substring(0, 100)
        };
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
                        <div>${cliente.nome || 'N/A'}</div>
                        <div>${cliente.telefone || 'N/A'}</div>
                        <div>${cliente.email || 'N/A'}</div>
                        <div>${cliente.idade || 'N/A'}</div>
                        <div>${cliente.ultimaConsulta || 'N/A'}</div>
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