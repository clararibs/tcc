<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Clínica Estética</title>
    
    <!-- Fontes e ícones -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }

        /* Container principal */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Cabeçalho */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 25px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }

        .header-left h1 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-left h1 i {
            color: #2a5a3c;
        }

        .header-left p {
            color: #7f8c8d;
            margin-top: 5px;
            font-size: 14px;
        }

        /* Botão voltar */
        .back-button {
            background: #2a5a3c;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .back-button:hover {
            background: #1a3c27;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(42, 90, 60, 0.3);
        }

        /* Card de pesquisa */
        .search-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }

        .search-card h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-card h3 i {
            color: #3498db;
        }

        .search-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 300px;
            padding: 14px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background: #f9f9f9;
        }

        .search-input:focus {
            outline: none;
            border-color: #3498db;
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .search-button {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .search-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.3);
        }

        /* Card de resultados */
        .results-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }

        .results-header {
            display: grid;
            grid-template-columns: 80px 1fr 1fr 200px 120px;
            padding: 20px 25px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .results-body {
            max-height: 600px;
            overflow-y: auto;
        }

        .client-row {
            display: grid;
            grid-template-columns: 80px 1fr 1fr 200px 120px;
            padding: 18px 25px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s;
            align-items: center;
        }

        .client-row:hover {
            background: #f8fafc;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }

        .client-id {
            color: #7f8c8d;
            font-weight: 500;
            font-size: 14px;
        }

        .client-name {
            color: #2c3e50;
            font-weight: 500;
            font-size: 16px;
        }

        .client-email {
            color: #3498db;
            font-size: 15px;
            word-break: break-all;
        }

        .client-phone {
            color: #27ae60;
            font-size: 15px;
        }

        /* Botão Ver Detalhes */
        .details-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
            justify-self: start;
        }

        .details-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
        }

        /* Botão limpar busca */
        .clear-search {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            margin-top: 10px;
            text-decoration: none;
        }

        .clear-search:hover {
            background: #c0392b;
            transform: translateY(-1px);
        }

        /* MODAL - Pop-up de detalhes */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease-out;
        }

        .modal {
            background: white;
            width: 90%;
            max-width: 700px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 22px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .close-modal {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .close-modal:hover {
            background: rgba(255,255,255,0.3);
            transform: rotate(90deg);
        }

        .modal-content {
            padding: 30px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .client-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #3498db;
        }

        .info-card h4 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-card p {
            color: #333;
            font-size: 16px;
            line-height: 1.5;
        }

        .description-card {
            background: #fff8e1;
            padding: 25px;
            border-radius: 10px;
            border-left: 4px solid #ff9800;
            margin-top: 20px;
        }

        .description-card h4 {
            color: #e65100;
            margin-bottom: 15px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .description-content {
            color: #5d4037;
            line-height: 1.6;
            font-size: 15px;
            white-space: pre-wrap;
            max-height: 200px;
            overflow-y: auto;
            padding: 10px;
            background: white;
            border-radius: 8px;
            border: 1px solid #ffe0b2;
        }

        .empty-description {
            color: #999;
            font-style: italic;
            text-align: center;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 8px;
        }

        /* Badges para status */
        .info-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-top: 5px;
        }

        .badge-age {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-date {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        /* Botões de ação no modal */
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #eee;
        }

        .action-btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-edit {
            background: #4caf50;
            color: white;
        }

        .btn-edit:hover {
            background: #388e3c;
            transform: translateY(-2px);
        }

        .btn-consulta {
            background: #9c27b0;
            color: white;
        }

        .btn-consulta:hover {
            background: #7b1fa2;
            transform: translateY(-2px);
        }

        /* Responsividade */
        @media (max-width: 992px) {
            .results-header,
            .client-row {
                grid-template-columns: 60px 1fr 120px;
            }
            
            .results-header div:nth-child(3),
            .results-header div:nth-child(4),
            .client-row div:nth-child(3),
            .client-row div:nth-child(4) {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-input {
                min-width: 100%;
            }
            
            .client-info-grid {
                grid-template-columns: 1fr;
            }
            
            .modal {
                width: 95%;
                margin: 10px;
            }
        }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>

    <div class="main-container">
        
        <!-- Cabeçalho -->
        <div class="header fade-in">
            <div class="header-left">
                <h1><i class="fas fa-users"></i> Clientes da Clínica</h1>
                <p>Pesquise e visualize os dados dos seus clientes</p>
            </div>
            <a href="entrar_aline.html" class="back-button">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

        <!-- Card de pesquisa -->
        <div class="search-card fade-in">
            <h3><i class="fas fa-search"></i> Pesquisar Clientes</h3>
            <form action="" method="GET" class="search-form">
                <input 
                    type="text" 
                    name="search" 
                    value="<?php if(isset($_GET['search'])){ echo htmlspecialchars($_GET['search']); } ?>" 
                    class="search-input" 
                    placeholder="Digite nome, email, telefone ou ID..."
                >
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </form>
            
            <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
                <a href="?" class="clear-search">
                    <i class="fas fa-times"></i> Limpar busca
                </a>
            <?php endif; ?>
        </div>

        <!-- Card de resultados -->
        <div class="results-card fade-in">
            <div class="results-header">
                <div>ID</div>
                <div>Nome Completo</div>
                <div>Email</div>
                <div>Telefone</div>
                <div>Ações</div>
            </div>
            
            <div class="results-body" id="clientResults">
                <?php 
                include 'conexao.php';
                $con = mysqli_connect("localhost", "root", "", "teste1");
                
                if(isset($_GET['search']) && !empty($_GET['search'])) {
                    $search = mysqli_real_escape_string($con, $_GET['search']);
                    
                    $query = "SELECT * FROM clientes 
                              WHERE id_cliente = '$search' 
                              OR LOWER(nome_completo) LIKE LOWER('%$search%') 
                              OR LOWER(email) LIKE LOWER('%$search%')
                              OR telefone LIKE '%$search%'
                              ORDER BY nome_completo";
                    
                    $query_run = mysqli_query($con, $query);

                    if(mysqli_num_rows($query_run) > 0) {
                        while($items = mysqli_fetch_assoc($query_run)) {
                            ?>
                            <div class="client-row" data-client-id="<?= $items['id_cliente']; ?>">
                                <div class="client-id">#<?= $items['id_cliente']; ?></div>
                                <div class="client-name"><?= htmlspecialchars($items['nome_completo']); ?></div>
                                <div class="client-email"><?= htmlspecialchars($items['email'] ?: '-'); ?></div>
                                <div class="client-phone"><?= htmlspecialchars($items['telefone'] ?: '-'); ?></div>
                                <button class="details-btn" onclick="showClientDetails(
                                    '<?= $items['id_cliente']; ?>',
                                    '<?= addslashes(htmlspecialchars($items['nome_completo'])); ?>',
                                    '<?= addslashes(htmlspecialchars($items['email'] ?: 'Não informado')); ?>',
                                    '<?= addslashes(htmlspecialchars($items['telefone'] ?: 'Não informado')); ?>',
                                    '<?= addslashes(htmlspecialchars($items['idade'] ?: 'Não informada')); ?>',
                                    '<?= addslashes(htmlspecialchars($items['descricao'] ?: '')); ?>',
                                    '<?= $items['data_cadastro']; ?>'
                                )">
                                    <i class="fas fa-eye"></i> Ver mais
                                </button>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="no-results">
                            <i class="fas fa-user-slash"></i>
                            <h3>Nenhum cliente encontrado</h3>
                            <p>Tente buscar por outro termo</p>
                        </div>
                        <?php
                    }
                } else {
                    // Mostra TODOS os clientes se não houver busca
                    $default_query = "SELECT * FROM clientes ORDER BY nome_completo ASC";
                    $default_result = mysqli_query($con, $default_query);
                    
                    if(mysqli_num_rows($default_result) > 0) {
                        while($items = mysqli_fetch_assoc($default_result)) {
                            ?>
                            <div class="client-row" data-client-id="<?= $items['id_cliente']; ?>">
                                <div class="client-id">#<?= $items['id_cliente']; ?></div>
                                <div class="client-name"><?= htmlspecialchars($items['nome_completo']); ?></div>
                                <div class="client-email"><?= htmlspecialchars($items['email'] ?: '-'); ?></div>
                                <div class="client-phone"><?= htmlspecialchars($items['telefone'] ?: '-'); ?></div>
                                <button class="details-btn" onclick="showClientDetails(
                                    '<?= $items['id_cliente']; ?>',
                                    '<?= addslashes(htmlspecialchars($items['nome_completo'])); ?>',
                                    '<?= addslashes(htmlspecialchars($items['email'] ?: 'Não informado')); ?>',
                                    '<?= addslashes(htmlspecialchars($items['telefone'] ?: 'Não informado')); ?>',
                                    '<?= addslashes(htmlspecialchars($items['idade'] ?: 'Não informada')); ?>',
                                    '<?= addslashes(htmlspecialchars($items['descricao'] ?: '')); ?>',
                                    '<?= $items['data_cadastro']; ?>'
                                )">
                                    <i class="fas fa-eye"></i> Ver mais
                                </button>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="no-results">
                            <i class="fas fa-user-plus"></i>
                            <h3>Nenhum cliente cadastrado</h3>
                            <p>Cadastre seu primeiro cliente!</p>
                        </div>
                        <?php
                    }
                }
                
                mysqli_close($con);
                ?>
            </div>
        </div>

    </div>

    <!-- MODAL - Pop-up de detalhes -->
    <div class="modal-overlay" id="clientModal">
        <div class="modal">
            <div class="modal-header">
                <h2><i class="fas fa-user-circle"></i> Detalhes do Cliente</h2>
                <button class="close-modal" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="client-info-grid">
                    <div class="info-card">
                        <h4><i class="fas fa-id-card"></i> ID do Cliente</h4>
                        <p id="modal-client-id">-</p>
                    </div>
                    
                    <div class="info-card">
                        <h4><i class="fas fa-user"></i> Nome Completo</h4>
                        <p id="modal-client-name">-</p>
                    </div>
                    
                    <div class="info-card">
                        <h4><i class="fas fa-envelope"></i> Email</h4>
                        <p id="modal-client-email">-</p>
                    </div>
                    
                    <div class="info-card">
                        <h4><i class="fas fa-phone"></i> Telefone</h4>
                        <p id="modal-client-phone">-</p>
                        <span class="info-badge badge-age">
                            <i class="fas fa-birthday-cake"></i>
                            <span id="modal-client-age">-</span> anos
                        </span>
                    </div>
                    
                    <div class="info-card">
                        <h4><i class="fas fa-calendar-plus"></i> Data de Cadastro</h4>
                        <p id="modal-client-date">-</p>
                        <span class="info-badge badge-date">
                            <i class="fas fa-clock"></i>
                            <span id="modal-client-days">-</span> dias
                        </span>
                    </div>
                </div>
                
                <div class="description-card">
                    <h4><i class="fas fa-file-alt"></i> Descrição e Observações</h4>
                    <div id="modal-client-description" class="description-content">
                        Carregando descrição...
                    </div>
                </div>
                
                
            </div>
        </div>
    </div>

    <script>
        // Armazena dados do cliente atual
        let currentClientData = {};
        
        // Mostra modal com detalhes do cliente
        function showClientDetails(id, nome, email, telefone, idade, descricao, dataCadastro) {
            currentClientData = {
                id: id,
                nome: nome,
                email: email,
                telefone: telefone,
                idade: idade,
                descricao: descricao,
                dataCadastro: dataCadastro
            };
            
            // Preenche os dados no modal
            document.getElementById('modal-client-id').textContent = '#' + id;
            document.getElementById('modal-client-name').textContent = nome;
            document.getElementById('modal-client-email').textContent = email;
            document.getElementById('modal-client-phone').textContent = telefone;
            document.getElementById('modal-client-age').textContent = idade;
            
            // Formata data de cadastro
            const dataCadastroObj = new Date(dataCadastro);
            const dataFormatada = dataCadastroObj.toLocaleDateString('pt-BR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('modal-client-date').textContent = dataFormatada;
            
            // Calcula dias desde o cadastro
            const hoje = new Date();
            const diferenca = hoje.getTime() - dataCadastroObj.getTime();
            const diasDesdeCadastro = Math.floor(diferenca / (1000 * 3600 * 24));
            document.getElementById('modal-client-days').textContent = diasDesdeCadastro;
            
            // Preenche descrição
            const descricaoElement = document.getElementById('modal-client-description');
            if (descricao && descricao.trim() !== '' && descricao !== 'Não informado') {
                descricaoElement.innerHTML = descricao.replace(/\n/g, '<br>');
                descricaoElement.classList.remove('empty-description');
            } else {
                descricaoElement.innerHTML = '<div class="empty-description">Nenhuma descrição ou observação cadastrada para este cliente.</div>';
                descricaoElement.classList.add('empty-description');
            }
            
            // Mostra o modal
            document.getElementById('clientModal').style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Previne scroll da página
        }
        
        // Fecha o modal
        function closeModal() {
            document.getElementById('clientModal').style.display = 'none';
            document.body.style.overflow = 'auto'; // Restaura scroll
        }
        
        // Fecha modal ao clicar fora
        document.getElementById('clientModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Fecha modal com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('clientModal').style.display === 'flex') {
                closeModal();
            }
        });
        
      
        
        // Foco no campo de busca quando a página carrega
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.search-input');
            if(searchInput) {
                searchInput.focus();
            }
        });
        
        // Limpa a busca ao clicar no botão
        document.querySelectorAll('.clear-search').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = window.location.pathname;
            });
        });
    </script>

</body>
</html>