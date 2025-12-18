//php da consfiguração da agenda
//era pra estar funcioando
<?php
require_once 'conexao.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'salvar_config':
        salvarConfiguracao();
        break;
    case 'salvar_excecao':
        salvarExcecao();
        break;
    case 'excluir_excecao':
        excluirExcecao();
        break;
    case 'gerar_slots':
        gerarSlots();
        break;
    case 'limpar_slots':
        limparSlots();
        break;
    default:
        echo "<script>alert('Ação não reconhecida'); window.history.back();</script>";
        break;
}

function salvarConfiguracao() {
    global $pdo;
    
    $tipo = $_POST['tipo'] ?? '';
    $dia_semana = $_POST['dia_semana'] ?? '';
    $horario_inicio = $_POST['horario_inicio'] ?? '';
    $horario_fim = $_POST['horario_fim'] ?? '';
    
    if (empty($tipo) || empty($dia_semana) || empty($horario_inicio) || empty($horario_fim)) {
        echo "<script>alert('Preencha todos os campos!'); window.history.back();</script>";
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            DELETE FROM config_disponibilidade 
            WHERE tipo = ? AND dia_semana = ?
        ");
        $stmt->execute([$tipo, $dia_semana]);
        
        $stmt = $pdo->prepare("
            INSERT INTO config_disponibilidade (tipo, dia_semana, horario_inicio, horario_fim)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$tipo, $dia_semana, $horario_inicio, $horario_fim]);
        
        echo "<script>
            alert('✅ Configuração salva!');
            window.location.href = '../configuracao_agenda.html';
        </script>";
        
    } catch (PDOException $e) {
        echo "<script>alert('Erro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}

function salvarExcecao() {
    global $pdo;
    
    $data_excecao = $_POST['data_excecao'] ?? '';
    $horario_inicio = $_POST['horario_inicio'] ?? '';
    $horario_fim = $_POST['horario_fim'] ?? '';
    $motivo = $_POST['motivo'] ?? 'Sem atendimento';
    
    if (empty($data_excecao)) {
        echo "<script>alert('Selecione uma data!'); window.history.back();</script>";
        exit;
    }
    
    try {
        if (empty($horario_inicio) || empty($horario_fim)) {
            $horario_inicio = NULL;
            $horario_fim = NULL;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO config_excecoes (data_excecao, horario_inicio, horario_fim, motivo)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$data_excecao, $horario_inicio, $horario_fim, $motivo]);
        
        echo "<script>
            alert('✅ Exceção salva!');
            window.location.href = '../configuracao_agenda.html';
        </script>";
        
    } catch (PDOException $e) {
        echo "<script>alert('Erro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}

function excluirExcecao() {
    global $pdo;
    
    $id_excecao = $_POST['id_excecao'] ?? '';
    
    if (empty($id_excecao)) {
        echo "<script>alert('ID não especificado!'); window.history.back();</script>";
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM config_excecoes WHERE id_excecao = ?");
        $stmt->execute([$id_excecao]);
        
        echo "<script>
            alert('✅ Exceção excluída!');
            window.location.href = '../configuracao_agenda.html';
        </script>";
        
    } catch (PDOException $e) {
        echo "<script>alert('Erro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}

function gerarSlots() {
    global $pdo;
    
    $duracao = $_POST['duracao'] ?? 60;
    $duracao = intval($duracao);
    
    try {
        $pdo->query("DELETE FROM slots_disponiveis WHERE status = 'disponivel'");
        
        echo "<script>
            alert('⚠️ Função gerarSlots() precisa ser implementada!');
            window.location.href = '../configuracao_agenda.html';
        </script>";
        
    } catch (PDOException $e) {
        echo "<script>alert('Erro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}

function limparSlots() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM slots_disponiveis WHERE status = 'disponivel'");
        $stmt->execute();
        
        echo "<script>
            alert('✅ Slots limpos!');
            window.location.href = '../configuracao_agenda.html';
        </script>";
        
    } catch (PDOException $e) {
        echo "<script>alert('Erro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>