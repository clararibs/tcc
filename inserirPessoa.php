<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conexao.php';

echo "DEBUG: Script iniciado<br>";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "ERRO:Método não permitido. Método atual: " . $_SERVER['REQUEST_METHOD'];
    exit;
}

echo "DEBUG: Método POST OK<br>";

// Mostrar todos os dados recebidos
echo "DEBUG: Dados recebidos:<br>";
foreach ($_POST as $key => $value) {
    echo "$key = $value<br>";
}

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$idade = $_POST['idade'] ?? NULL;
$descricao = $_POST['descricao'] ?? '';

// Validações
if (empty($nome) || empty($telefone)) {
    echo "ERRO:Nome e telefone são obrigatórios";
    exit;
}

if ($idade !== NULL && (!is_numeric($idade) || $idade < 0 || $idade > 150)) {
    echo "ERRO:Idade inválida: $idade";
    exit;
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "ERRO:Email inválido: $email";
    exit;
}

echo "DEBUG: Validações passadas<br>";

try {
    // Testar conexão primeiro
    if ($conn->connect_error) {
        echo "ERRO:Conexão falhou: " . $conn->connect_error;
        exit;
    }
    
    echo "DEBUG: Conexão com BD OK<br>";
    
    // Verificar se a tabela existe
    $result = $conn->query("SHOW TABLES LIKE 'clientes'");
    if ($result->num_rows == 0) {
        echo "ERRO:Tabela 'clientes' não existe<br>";
        // Criar a tabela se não existir
        $sql_create = "CREATE TABLE clientes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome_completo VARCHAR(100) NOT NULL,
            email VARCHAR(100),
            telefone VARCHAR(20) NOT NULL,
            idade INT,
            descricao TEXT,
            data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($sql_create)) {
            echo "DEBUG: Tabela 'clientes' criada<br>";
        } else {
            echo "ERRO:Falha ao criar tabela: " . $conn->error;
            exit;
        }
    }
    
    $sql = "INSERT INTO clientes (nome_completo, email, telefone, idade, descricao) 
            VALUES (?, ?, ?, ?, ?)";
    
    echo "DEBUG: SQL: $sql<br>";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo "ERRO:Falha no prepare: " . $conn->error;
        exit;
    }
    
    $stmt->bind_param("sssis", $nome, $email, $telefone, $idade, $descricao);
    
    echo "DEBUG: Parâmetros binded<br>";
    
    if ($stmt->execute()) {
        $id = $conn->insert_id;
        echo "SUCESSO:Cliente cadastrado com sucesso!|$id";
    } else {
        echo "ERRO:Erro ao executar: " . $stmt->error;
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo "ERRO:Exceção: " . $e->getMessage();
}

$conn->close();
?>