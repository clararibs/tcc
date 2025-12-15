<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicite sua Avaliação</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #95b495 0%, #90ac90 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            display: flex;
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(46, 125, 50, 0.15);
            overflow: hidden;
        }
        
        .form-section {
            flex: 1;
            padding: 40px;
        }
        
        .info-section {
            flex: 1;
            padding: 40px;
            background: linear-gradient(135deg, #213d23 0%, #235f25 100%);
            color: white;
        }
        
        h1 {
            color: #143816;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4caf50;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #050505;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .time-input {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .time-input input {
            width: 80px;
            text-align: center;
        }
        
        .btn-submit {
            background: #2E7D32;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn-submit:hover {
            background: #1B5E20;
        }
        
        .alerta {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .sucesso {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .erro {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .info-list {
            list-style: none;
            margin: 20px 0;
        }
        
        .info-list li {
            margin-bottom: 15px;
            padding-left: 25px;
            position: relative;
        }
        
        .info-list li:before {
            content: "✓";
            color: #4caf50;
            position: absolute;
            left: 0;
        }
        
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-section">
            <h1>Solicite sua Avaliação</h1>
            
            <!-- Mensagens de sucesso/erro -->
            <?php if(isset($_GET['sucesso']) && $_GET['sucesso'] == 'true'): ?>
                <div class="alerta sucesso">
                    <strong>✅ Solicitação enviada com sucesso!</strong><br>
                    Em breve entraremos em contato para confirmar.
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['erro'])): ?>
                <div class="alerta erro">
                    <strong>❌ Erro no envio!</strong><br>
                    <?php 
                    if($_GET['erro'] == 'banco') echo "Erro no banco de dados";
                    if($_GET['erro'] == 'campos') echo "Preencha todos os campos";
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="salvar_solicitacao.php" method="POST" onsubmit="return validarForm()">
                <div class="form-group">
                    <label for="fullname">Nome completo:</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Telefone:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="date">Data:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                
                <div class="form-group">
                    <label>Hora:</label>
                    <div class="time-input">
                        <input type="number" id="hour" name="hour" min="8" max="18" placeholder="HH" required>
                        <span>:</span>
                        <input type="number" id="minute" name="minute" min="0" max="59" placeholder="MM" required>
                    </div>
                    <small>Horário: 8:00 às 18:00</small>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Solicitar Avaliação
                </button>
            </form>
        </div>
        
        <div class="info-section">
            <h2 style="color: white;">Informações Importantes</h2>
            
            <ul class="info-list">
                <li>Agendamento apenas para avaliação</li>
                <li>Indicações durante a consulta</li>
                <li>Confirmação por e-mail em até 2 dias úteis</li>
            </ul>
            
            <div style="margin-top: 30px;">
                <h3>Contato</h3>
                <p><i class="fas fa-phone"></i> (31) 99430-4522</p>
                <p><i class="fas fa-envelope"></i> clinicahedone@gmail.com</p>
                <p><i class="fas fa-map-marker-alt"></i> Rua Getúlio Vargas, 395 - Ouro Branco</p>
            </div>
        </div>
    </div>

    <script>
        // Validação básica no frontend
        function validarForm() {
            const hora = document.getElementById('hour').value;
            const minuto = document.getElementById('minute').value;
            
            if (hora < 8 || hora > 18) {
                alert('Horário deve ser entre 8:00 e 18:00');
                return false;
            }
            
            if (minuto < 0 || minuto > 59) {
                alert('Minutos inválidos');
                return false;
            }
            
            // Impedir datas passadas
            const dataInput = document.getElementById('date');
            const hoje = new Date().toISOString().split('T')[0];
            if (dataInput.value < hoje) {
                alert('Data não pode ser no passado');
                return false;
            }
            
            return true;
        }
        
        // Data mínima = hoje
        document.getElementById('date').min = new Date().toISOString().split('T')[0];
        
        // Foco automático nos minutos
        document.getElementById('hour').addEventListener('input', function() {
            if (this.value.length >= 2) {
                document.getElementById('minute').focus();
            }
        });
    </script>
</body>
</html>