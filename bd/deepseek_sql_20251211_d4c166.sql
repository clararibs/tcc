-- ============================================
-- BANCO DE DADOS COMPLETO HEDONE
-- ============================================

-- 1. TABELA CLIENTES (Ficha Paciente)
CREATE TABLE IF NOT EXISTS clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefone VARCHAR(20) NOT NULL,
    idade INT,
    descricao TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. TABELA SOLICITAÇÕES AGENDAMENTO (Formulário Site)
CREATE TABLE IF NOT EXISTS solicitacoes_agendamento (
    id_solicitacao INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    data_desejada DATE NOT NULL,
    hora_desejada TIME NOT NULL,
    status ENUM('pendente', 'confirmada', 'cancelada') DEFAULT 'pendente',
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. TABELA CONSULTAS (Agenda Admin)
CREATE TABLE IF NOT EXISTS consultas (
    id_consulta INT AUTO_INCREMENT PRIMARY KEY,
    nome_paciente VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(100),
    procedimento VARCHAR(100) NOT NULL,
    data_consulta DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    duracao_minutos INT NOT NULL
);

-- 4. TABELA PROCEDIMENTOS (Catálogo - Opcional)
CREATE TABLE IF NOT EXISTS procedimentos (
    id_procedimento INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    duracao_minutos INT DEFAULT 60,
    preco DECIMAL(10,2),
    ativo BOOLEAN DEFAULT TRUE
);

-- 5. TABELA USUÁRIOS (Login Admin - Opcional)
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nome VARCHAR(100) NOT NULL,
    tipo ENUM('admin', 'funcionario') DEFAULT 'funcionario',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);