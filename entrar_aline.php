<?php include "conexao.php"; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Hedone Clínica Estética</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="aline.css">
</head>
<body>

    <div class="background-design">
        <div class="geometric-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
    </div>

    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1 class="clinic-name">Hedonê</h1>
            <h2 class="welcome-message">Bem-vinda, Dra. Aline</h2>
            <p class="subtitle">Gerencie sua clínica com elegância e precisão</p>
        </header>

        <section class="dashboard-content">
            <div class="actions-grid">
                <div class="action-card">
                    <a href="agenda_adm.php">
                        <div class="action-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="action-title">Agendar Pacientes</h3>
                        <div class="action-divider"></div>
                        <p class="action-description">Agende e visualize consultas de forma organizada</p>
                    </a>
                </div>

                <div class="action-card">
                    <a href="ficha-paciente.php">
                        <div class="action-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <h3 class="action-title">Adicionar Ficha</h3>
                        <div class="action-divider"></div>
                        <p class="action-description">Registre informações dos pacientes</p>
                    </a>
                </div>

                <div class="action-card">
                    <a href="historico.php">
                        <div class="action-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="action-title">Consultar Fichas</h3>
                        <div class="action-divider"></div>
                        <p class="action-description">Acesse históricos e dados dos pacientes</p>
                    </a>
                </div>

                <div class="action-card">
                    <a href="configurar_disponibilidade.php">
                        <div class="action-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h3 class="action-title">Configurações</h3>
                        <div class="action-divider"></div>
                        <p class="action-description">Personalize o sistema conforme suas necessidades</p>
                    </a>
                </div>
            </div>
        </section>

    </div>



    <!-- Ícone de Notificação -->
<div class="notification-bell" id="openNotifications">
    <i class="fas fa-bell"></i>
    <span class="notification-badge">3</span>
</div>

<!-- Sidebar de Notificações -->
<div class="notification-sidebar" id="notificationSidebar">
    <div class="sidebar-header">
        <h3>Solicitações de Agendamento</h3>
        <span class="close-sidebar" id="closeSidebar">&times;</span>
    </div>

    <div class="notifications-list">
        <!-- Exemplo de solicitação (depois você preenche com PHP) -->

        <div class="notification-item">
            <p><strong>Nome:</strong> Maria Silva</p>
            <p><strong>Email:</strong> maria@email.com</p>
            <p><strong>Data:</strong> 25/11/2025</p>
            <p><strong>Hora:</strong> 14:00</p>

            <div class="notification-actions">
                <button class="accept-btn">Aceitar</button>
                <button class="reject-btn">Recusar</button>
            </div>
        </div>

        <div class="notification-item">
            <p><strong>Nome:</strong> João Pereira</p>
            <p><strong>Email:</strong> joao@email.com</p>
            <p><strong>Data:</strong> 27/11/2025</p>
            <p><strong>Hora:</strong> 10:30</p>

            <div class="notification-actions">
                <button class="accept-btn">Aceitar</button>
                <button class="reject-btn">Recusar</button>
            </div>
        </div>
    </div>
</div>

</body>

<script>
    const bell = document.getElementById("openNotifications");
    const sidebar = document.getElementById("notificationSidebar");
    const closeSidebar = document.getElementById("closeSidebar");

    bell.addEventListener("click", () => {
        sidebar.classList.add("open");
    });

    closeSidebar.addEventListener("click", () => {
        sidebar.classList.remove("open");
    });
</script>

</html>