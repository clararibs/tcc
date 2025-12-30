<?php
require_once 'conexao.php';
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
        alert('❌ ERRO DE CONEXÃO COM O BANCO!\\\\\\\\n\\\\\\\\nERRO: " . addslashes($e->getMessage()) . "\\\\\\\\n\\\\\\\\nVERIFIQUE:\\\\\\\\n1. XAMPP está aberto?\\\\\\\\n2. MySQL está iniciado (botão verde)?\\\\\\\\n3. Banco \\'teste1\\' existe?\\\\\\\\n\\\\\\\\nConfiguração tentada:\\\\\\\\nHost: $host\\\\\\\\nPorta: " . ($port ?: 'padrão') . "\\\\\\\\nUsuário: $username\\\\\\\\nSenha: " . ($password ?: '(vazia)') . "');
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
    $slot_id = trim($_POST['slot_id'] ?? '');
    
    // Validação básica
    if (empty($nome) || empty($telefone) || empty($email) || empty($data) || empty($hora) || empty($slot_id)) {
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
        
        // 3. Verifica se o slot ainda está disponível
        $stmt3 = $pdo->prepare("
            SELECT id_slot 
            FROM slots_disponiveis 
            WHERE id_slot = ? 
            AND status = 'disponivel'
        ");
        $stmt3->execute([$slot_id]);
        
        if ($stmt3->rowCount() == 0) {
            echo "<script>
                alert('❌ Este horário não está mais disponível!\\\\\\\\nEscolha outro horário.');
                window.history.back();
            </script>";
            exit;
        }
        
        // ============================================
        // SALVA A SOLICITAÇÃO COMO CONFIRMADA
        // ============================================
        
        $stmt = $pdo->prepare("
            INSERT INTO solicitacoes_agendamento 
            (nome_completo, email, telefone, data_desejada, hora_desejada, status, id_slot) 
            VALUES (?, ?, ?, ?, ?, 'confirmada', ?)
        ");
        
        $stmt->execute([$nome, $email, $telefone, $data, $hora, $slot_id]);
        $id_solicitacao = $pdo->lastInsertId();
        
        // DEBUG: Log do sucesso
        error_log("✅ Solicitação salva! ID: $id_solicitacao - Data: $data - Hora: $hora - Slot: $slot_id");
        
        // ============================================
        // ATUALIZA O STATUS DO SLOT PARA OCUPADO
        // ============================================
        
        $stmt_slot = $pdo->prepare("
            UPDATE slots_disponiveis 
            SET status = 'ocupado',
                id_solicitacao = ?
            WHERE id_slot = ?
        ");
        $stmt_slot->execute([$id_solicitacao, $slot_id]);
        
        error_log("✅ Slot $slot_id atualizado para 'ocupado' e vinculado à solicitação $id_solicitacao");
        
        // ============================================
        // TAMBÉM SALVA NA TABELA DE CONSULTAS
        // (para ocupar o horário na agenda)
        // ============================================
        
        try {
            $stmt3 = $pdo->prepare("
                INSERT INTO consultas 
                (nome_paciente, telefone, email, procedimento, data_consulta, hora_inicio, duracao_minutos, id_slot, id_solicitacao) 
                VALUES (?, ?, ?, 'Avaliação Inicial', ?, ?, 60, ?, ?)
            ");
            
            $stmt3->execute([$nome, $telefone, $email, $data, $hora, $slot_id, $id_solicitacao]);
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
            alert('✅ AVALIAÇÃO AGENDADA COM SUCESSO!\\\\\\\\n\\\\\\\\n📅 Data: $data_formatada\\\\\\\\n⏰ Horário: $hora\\\\\\\\n👤 Nome: $nome\\\\\\\\n📞 Telefone: $telefone\\\\\\\\n\\\\\\\\nChegue 10 minutos antes do horário!\\\\\\\\n\\\\\\\\nUma confirmação será enviada para seu e-mail.');
            window.location.href = 'solicitar_agendamento.html';
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
        window.location.href = 'solicitar_agendamento.html';
    </script>";
}
?>