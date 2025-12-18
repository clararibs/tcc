<?php
// ============================================
// CONFIGURAÇÃO DA CONEXÃO - AJUSTE PARA SUA CASA!
// ============================================

// OPÇÃO A: Para CASA (XAMPP padrão) - TENTE ESTA PRIMEIRO
$host = 'localhost';     // ou '127.0.0.1'
$port = 3306;           // padrão do MySQL
$dbname = 'teste1';     // seu banco
$username = 'root';     // usuário mais comum
$password = '';         // senha vazia no XAMPP

// OPÇÃO B: Se a de cima não funcionar, tente sem porta
// $host = 'localhost';
// $port = null;
// $username = 'root';
// $password = '';

// OPÇÃO C: Para ESCOLA (com porta 3307)
// $host = 'localhost';
// $port = 3307;
// $username = 'root';
// $password = '';

// ============================================
// NÃO MUDE DAQUI PARA BAIXO
// ============================================

try {
    // Monta a string de conexão
    $dsn = "mysql:host=$host";
    if ($port) {
        $dsn .= ":$port";
    }
    $dsn .= ";dbname=$dbname;charset=utf8";
    
    // Tenta conectar
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // DEBUG: Log de sucesso
    error_log("✅ Conexão estabelecida com $host" . ($port ? ":$port" : ""));
    
} catch (PDOException $e) {
    // Se falhar, mostra erro detalhado
    die("<script>
        alert('❌ ERRO DE CONEXÃO COM O BANCO!\\\\\\\\n\\\\\\\\nERRO: " . addslashes($e->getMessage()) . "\\\\\\\\n\\\\\\\\nVERIFIQUE:\\\\\\\\n1. XAMPP está aberto?\\\\\\\\n2. MySQL está iniciado (botão verde)?\\\\\\\\n3. Banco \\'hedone\\' existe?\\\\\\\\n\\\\\\\\nConfiguração tentada:\\\\\\\\nHost: $host\\\\\\\\nPorta: " . ($port ?: 'padrão') . "\\\\\\\\nUsuário: $username\\\\\\\\nSenha: " . ($password ?: '(vazia)') . "');
        console.error('Erro DB: " . addslashes($e->getMessage()) . "');
    </script>");
}

// ============================================
// PROCESSAMENTO DO FORMULÁRIO
// ============================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // DEBUG: Mostra o que está chegando
    error_log("=== DADOS RECEBIDOS DO FORMULÁRIO ===");
    foreach ($_POST as $key => $value) {
        error_log("POST['$key'] = '$value'");
    }
    
    // Recebe e limpa os dados
    $nome = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data = trim($_POST['data'] ?? '');
    $hora = trim($_POST['hora'] ?? '');
    
    // Validação básica
    if (empty($nome) || empty($telefone) || empty($email) || empty($data) || empty($hora)) {
        echo "<script>
            alert('❌ Preencha todos os campos!');
            window.history.back();
        </script>";
        exit;
    }
    
    // Valida email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
            alert('❌ E-mail inválido!');
            window.history.back();
        </script>";
        exit;
    }
    
    try {
        // ============================================
        // VERIFICA SE O HORÁRIO JÁ ESTÁ OCUPADO
        // ============================================
        
        // 1. Verifica em SOLICITAÇÕES CONFIRMADAS
        $stmt = $pdo->prepare("
            SELECT id_solicitacao 
            FROM solicitacoes_agendamento 
            WHERE data_desejada = ? 
            AND hora_desejada = ?
            AND status = 'confirmada'
        ");
        $stmt->execute([$data, $hora]);
        
        if ($stmt->rowCount() > 0) {
            echo "<script>
                alert('❌ Este horário já foi reservado por outra pessoa!\\\\\\\\nEscolha outro horário.');
                window.history.back();
            </script>";
            exit;
        }
        
        // 2. Verifica em CONSULTAS AGENDADAS (tabela consultas)
        $stmt2 = $pdo->prepare("
            SELECT id_consulta 
            FROM consultas 
            WHERE data_consulta = ? 
            AND hora_inicio = ?
        ");
        $stmt2->execute([$data, $hora]);
        
        if ($stmt2->rowCount() > 0) {
            echo "<script>
                alert('❌ Este horário já tem uma consulta agendada!\\\\\\\\nEscolha outro horário.');
                window.history.back();
            </script>";
            exit;
        }
        
        // ============================================
        // SALVA A SOLICITAÇÃO COMO CONFIRMADA
        // ============================================
        
        $stmt = $pdo->prepare("
            INSERT INTO solicitacoes_agendamento 
            (nome_completo, email, telefone, data_desejada, hora_desejada, status) 
            VALUES (?, ?, ?, ?, ?, 'confirmada')
        ");
        
        $stmt->execute([$nome, $email, $telefone, $data, $hora]);
        $id_solicitacao = $pdo->lastInsertId();
        
        // DEBUG: Log do sucesso
        error_log("✅ Solicitação salva! ID: $id_solicitacao - Data: $data - Hora: $hora");
        
        // ============================================
        // TAMBÉM SALVA NA TABELA DE CONSULTAS
        // (para ocupar o horário na agenda)
        // ============================================
        
        try {
            $stmt3 = $pdo->prepare("
                INSERT INTO consultas 
                (nome_paciente, telefone, email, procedimento, data_consulta, hora_inicio, duracao_minutos) 
                VALUES (?, ?, ?, 'Avaliação Inicial', ?, ?, 60)
            ");
            
            $stmt3->execute([$nome, $telefone, $email, $data, $hora]);
            $id_consulta = $pdo->lastInsertId();
            
            error_log("✅ Também salvo na tabela consultas! ID: $id_consulta");
            
        } catch (PDOException $e2) {
            // Se falhar, apenas registra o erro mas não impede
            error_log("⚠️ Não salvou na tabela consultas: " . $e2->getMessage());
        }
        
        // ============================================
        // SUCESSO - REDIRECIONA COM MENSAGEM
        // ============================================
        
        // Formata a data para exibição
        $data_formatada = date('d/m/Y', strtotime($data));
        
        echo "<script>
            alert('✅ AVALIAÇÃO AGENDADA COM SUCESSO!\\\\\\\\n\\\\\\\\n📅 Data: $data_formatada\\\\\\\\n⏰ Horário: $hora\\\\\\\\n👤 Nome: $nome\\\\\\\\n📞 Telefone: $telefone\\\\\\\\n\\\\\\\\nChegue 10 minutos antes do horário!');
            window.location.href = 'solicitar_avaliacao.html';
        </script>";
        
    } catch (PDOException $e) {
        // ERRO NO BANCO
        error_log("❌ ERRO AO SALVAR NO BANCO: " . $e->getMessage());
        
        echo "<script>
            alert('❌ ERRO NO SERVIDOR!\\\\\\\\n\\\\\\\\n" . addslashes($e->getMessage()) . "\\\\\\\\n\\\\\\\\nTente novamente ou entre em contato.');
            window.history.back();
        </script>";
    }
    
} else {
    // Se não for POST
    echo "<script>
        alert('Método inválido');
        window.location.href = 'solicitar_avaliacao.html';
    </script>";
}
?>