<?php 
include "conexao.php";

// Função para salvar consulta no banco de dados
if(isset($_POST['salvar_consulta'])) {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $procedimento = $_POST['procedimento'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $duracao = $_POST['duracao'];
    
    // Calcular hora de término
    $hora_inicio = $hora;
    $hora_fim = date('H:i', strtotime("+$duracao minutes", strtotime($hora)));
    
    $sql = "INSERT INTO consultas (nome, telefone, email, procedimento, data_consulta, hora_inicio, hora_fim, duracao) 
            VALUES ('$nome', '$telefone', '$email', '$procedimento', '$data', '$hora_inicio', '$hora_fim', '$duracao')";
    
    if(mysqli_query($conn, $sql)) {
        $msg = "Consulta agendada com sucesso!";
        $msg_type = "success";
    } else {
        $msg = "Erro ao agendar consulta: " . mysqli_error($conn);
        $msg_type = "error";
    }
}

// Função para excluir consulta
if(isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM consultas WHERE id = '$id'";
    
    if(mysqli_query($conn, $sql)) {
        $msg = "Consulta excluída com sucesso!";
        $msg_type = "success";
    } else {
        $msg = "Erro ao excluir consulta: " . mysqli_error($conn);
        $msg_type = "error";
    }
}

// Buscar consultas para o calendário
$sql_consultas = "SELECT * FROM consultas ORDER BY data_consulta, hora_inicio";
$result_consultas = mysqli_query($conn, $sql_consultas);
$consultas = [];
while($row = mysqli_fetch_assoc($result_consultas)) {
    $consultas[] = [
        'id' => $row['id'],
        'title' => $row['procedimento'] . ' - ' . $row['nome'],
        'start' => $row['data_consulta'] . 'T' . $row['hora_inicio'],
        'end' => $row['data_consulta'] . 'T' . $row['hora_fim'],
        'extendedProps' => [
            'name' => $row['nome'],
            'phone' => $row['telefone'],
            'email' => $row['email'],
            'procedure' => $row['procedimento'],
            'durationText' => $row['duracao'] . ' min'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Agenda Clínica Estética Avançada</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style_agenda_adm.css">
  
  <style>
    /* Adicionar estilos para mensagens */
    .message {
        padding: 15px;
        margin: 10px;
        border-radius: 5px;
        font-weight: 500;
    }
    
    .success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    /* Botão de voltar */
    .back-button {
        position: absolute;
        top: 20px;
        left: 20px;
        background: #2a5a3c;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .back-button:hover {
        background: #1a3c27;
        transform: translateY(-2px);
    }
  </style>
</head>
<body>

  <!-- Botão para voltar ao dashboard -->
  <a href="index.html" class="back-button">
    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
      <path d="M10.5 13.5L5 8l5.5-5.5L11 1l-7 7 7 7z"/>
    </svg>
    Voltar ao Dashboard
  </a>

  <!-- Exibir mensagens -->
  <?php if(isset($msg)): ?>
    <div class="message <?php echo $msg_type; ?>">
      <?php echo $msg; ?>
    </div>
  <?php endif; ?>

  <div class="sidebar">
    <h2>Nova Consulta</h2>
    
    <form id="consultaForm" method="POST">
      <label for="name">Nome do Paciente</label>
      <input type="text" id="name" name="nome" placeholder="Ex: Aline Silva" required>

      <label for="phone">Telefone</label>
      <input type="text" id="phone" name="telefone" placeholder="(xx) xxxxx-xxxx" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="email@exemplo.com">

      <label for="procedure">Procedimento</label>
      <input type="text" id="procedure" name="procedimento" placeholder="Ex: Botox" required>

      <label for="date">Data</label>
      <input type="date" id="date" name="data" required>

      <label for="time">Horário de Início</label>
      <input type="time" id="time" name="hora" required>

      <label for="minutes">Duração (minutos)</label>
      <input type="number" id="minutes" name="duracao" placeholder="Ex: 45" min="1" required>

      <input type="hidden" name="salvar_consulta" value="true">
      <button type="submit" id="addEvent">Adicionar Consulta</button>
    </form>

    <p class="info-tip">
      Clique em um evento no calendário para ver detalhes ou excluir.
    </p>
  </div>

  <div id="calendar"></div>

  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const calendarEl = document.getElementById('calendar');

      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        editable: false, // Alterado para false pois vamos gerenciar pelo PHP
        locale: 'pt-br',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: <?php echo json_encode($consultas); ?>,
        
        eventContent: function(arg) {
          // Customizar o conteúdo do evento
          return {
            html: `<div class="fc-event-title">${arg.event.title}</div>`
          };
        },

        eventClick: function(info) {
          const paciente = info.event.extendedProps;
          const detalhes = `
✨ Procedimento: ${paciente.procedure || "não informado"}
👤 Paciente: ${paciente.name || "não informado"}
📞 Telefone: ${paciente.phone || "não informado"}
📧 Email: ${paciente.email || "não informado"}
⏳ Duração: ${paciente.durationText || "não informado"}
🗓️ Início: ${info.event.start.toLocaleString("pt-BR")}
⏰ Fim: ${info.event.end ? info.event.end.toLocaleString("pt-BR") : "não definido"}
          `;
          
          if (confirm(detalhes + "\n\nDeseja excluir esta consulta?")) {
            // Redirecionar para excluir via PHP
            const eventId = info.event.id;
            window.location.href = `agenda_adm.php?acao=excluir&id=${eventId}`;
          }
        }
      });

      calendar.render();

      // Lidar com o envio do formulário
      document.getElementById('consultaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('name').value;
        const phone = document.getElementById('phone').value;
        const email = document.getElementById('email').value;
        const procedure = document.getElementById('procedure').value;
        const date = document.getElementById('date').value;
        const time = document.getElementById('time').value;
        const minutes = parseInt(document.getElementById('minutes').value) || 0;

        if (name && procedure && date && time && minutes > 0) {
          // O formulário será enviado normalmente via POST
          // O PHP processará e recarregará a página com os dados atualizados
          this.submit();
        } else {
          alert('Preencha todos os campos obrigatórios!');
        }
      });

      // Atualizar calendário automaticamente
      function atualizarCalendario() {
        calendar.refetchEvents();
      }

      // Atualizar a cada 30 segundos (opcional)
      // setInterval(atualizarCalendario, 30000);
    });
  </script>
</body>
</html>