-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/12/2025 às 23:07
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `teste1`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) NOT NULL,
  `idade` int(11) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `config_disponibilidade`
--

CREATE TABLE `config_disponibilidade` (
  `id_config` int(11) NOT NULL,
  `tipo` enum('disponibilidade','intervalo') NOT NULL,
  `dia_semana` varchar(20) NOT NULL,
  `horario_inicio` time NOT NULL,
  `horario_fim` time NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `config_disponibilidade`
--

INSERT INTO `config_disponibilidade` (`id_config`, `tipo`, `dia_semana`, `horario_inicio`, `horario_fim`, `ativo`, `data_criacao`) VALUES
(11, 'disponibilidade', 'quinta', '17:07:00', '20:08:00', 1, '2025-12-23 20:08:10'),
(12, 'intervalo', 'segunda', '20:08:00', '21:08:00', 1, '2025-12-23 20:08:36'),
(13, 'intervalo', 'quinta', '18:08:00', '19:08:00', 1, '2025-12-23 20:08:57'),
(17, 'disponibilidade', 'segunda', '08:40:00', '21:40:00', 1, '2025-12-23 21:40:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `config_excecoes`
--

CREATE TABLE `config_excecoes` (
  `id_excecao` int(11) NOT NULL,
  `data_excecao` date NOT NULL,
  `horario_inicio` time DEFAULT NULL COMMENT 'NULL = dia todo',
  `horario_fim` time DEFAULT NULL COMMENT 'NULL = dia todo',
  `motivo` varchar(255) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consultas`
--

CREATE TABLE `consultas` (
  `id_consulta` int(11) NOT NULL,
  `nome_paciente` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `procedimento` varchar(100) NOT NULL,
  `data_consulta` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `duracao_minutos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `procedimentos`
--

CREATE TABLE `procedimentos` (
  `id_procedimento` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `duracao_minutos` int(11) DEFAULT 60,
  `preco` decimal(10,2) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `slots_disponiveis`
--

CREATE TABLE `slots_disponiveis` (
  `id_slot` int(11) NOT NULL,
  `data_slot` date NOT NULL,
  `horario_inicio` time NOT NULL,
  `horario_fim` time NOT NULL,
  `duracao_minutos` int(11) NOT NULL,
  `status` enum('disponivel','reservado','bloqueado') DEFAULT 'disponivel',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `slots_disponiveis`
--

INSERT INTO `slots_disponiveis` (`id_slot`, `data_slot`, `horario_inicio`, `horario_fim`, `duracao_minutos`, `status`, `data_criacao`) VALUES
(1332, '2025-12-25', '17:07:00', '18:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1333, '2025-12-25', '18:07:00', '19:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1334, '2025-12-25', '19:07:00', '20:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1335, '2026-01-01', '17:07:00', '18:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1336, '2026-01-01', '18:07:00', '19:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1337, '2026-01-01', '19:07:00', '20:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1338, '2026-01-08', '17:07:00', '18:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1339, '2026-01-08', '18:07:00', '19:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1340, '2026-01-08', '19:07:00', '20:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1341, '2026-01-15', '17:07:00', '18:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1342, '2026-01-15', '18:07:00', '19:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1343, '2026-01-15', '19:07:00', '20:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1344, '2026-01-22', '17:07:00', '18:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1345, '2026-01-22', '18:07:00', '19:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1346, '2026-01-22', '19:07:00', '20:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1347, '2026-01-29', '17:07:00', '18:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1348, '2026-01-29', '18:07:00', '19:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1349, '2026-01-29', '19:07:00', '20:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1350, '2026-02-05', '17:07:00', '18:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1351, '2026-02-05', '18:07:00', '19:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1352, '2026-02-05', '19:07:00', '20:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1353, '2026-02-12', '17:07:00', '18:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1354, '2026-02-12', '18:07:00', '19:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1355, '2026-02-12', '19:07:00', '20:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1356, '2026-02-19', '17:07:00', '18:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1357, '2026-02-19', '18:07:00', '19:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1358, '2026-02-19', '19:07:00', '20:07:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1359, '2025-12-29', '08:40:00', '09:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1360, '2025-12-29', '09:40:00', '10:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1361, '2025-12-29', '10:40:00', '11:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1362, '2025-12-29', '11:40:00', '12:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1363, '2025-12-29', '12:40:00', '13:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1364, '2025-12-29', '13:40:00', '14:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1365, '2025-12-29', '14:40:00', '15:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1366, '2025-12-29', '15:40:00', '16:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1367, '2025-12-29', '16:40:00', '17:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1368, '2025-12-29', '17:40:00', '18:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1369, '2025-12-29', '18:40:00', '19:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1370, '2025-12-29', '19:40:00', '20:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1371, '2025-12-29', '20:40:00', '21:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1372, '2026-01-05', '08:40:00', '09:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1373, '2026-01-05', '09:40:00', '10:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1374, '2026-01-05', '10:40:00', '11:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1375, '2026-01-05', '11:40:00', '12:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1376, '2026-01-05', '12:40:00', '13:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1377, '2026-01-05', '13:40:00', '14:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1378, '2026-01-05', '14:40:00', '15:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1379, '2026-01-05', '15:40:00', '16:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1380, '2026-01-05', '16:40:00', '17:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1381, '2026-01-05', '17:40:00', '18:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1382, '2026-01-05', '18:40:00', '19:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1383, '2026-01-05', '19:40:00', '20:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1384, '2026-01-05', '20:40:00', '21:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1385, '2026-01-12', '08:40:00', '09:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1386, '2026-01-12', '09:40:00', '10:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1387, '2026-01-12', '10:40:00', '11:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1388, '2026-01-12', '11:40:00', '12:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1389, '2026-01-12', '12:40:00', '13:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1390, '2026-01-12', '13:40:00', '14:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1391, '2026-01-12', '14:40:00', '15:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1392, '2026-01-12', '15:40:00', '16:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1393, '2026-01-12', '16:40:00', '17:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1394, '2026-01-12', '17:40:00', '18:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1395, '2026-01-12', '18:40:00', '19:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1396, '2026-01-12', '19:40:00', '20:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1397, '2026-01-12', '20:40:00', '21:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1398, '2026-01-19', '08:40:00', '09:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1399, '2026-01-19', '09:40:00', '10:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1400, '2026-01-19', '10:40:00', '11:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1401, '2026-01-19', '11:40:00', '12:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1402, '2026-01-19', '12:40:00', '13:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1403, '2026-01-19', '13:40:00', '14:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1404, '2026-01-19', '14:40:00', '15:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1405, '2026-01-19', '15:40:00', '16:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1406, '2026-01-19', '16:40:00', '17:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1407, '2026-01-19', '17:40:00', '18:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1408, '2026-01-19', '18:40:00', '19:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1409, '2026-01-19', '19:40:00', '20:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1410, '2026-01-19', '20:40:00', '21:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1411, '2026-01-26', '08:40:00', '09:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1412, '2026-01-26', '09:40:00', '10:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1413, '2026-01-26', '10:40:00', '11:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1414, '2026-01-26', '11:40:00', '12:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1415, '2026-01-26', '12:40:00', '13:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1416, '2026-01-26', '13:40:00', '14:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1417, '2026-01-26', '14:40:00', '15:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1418, '2026-01-26', '15:40:00', '16:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1419, '2026-01-26', '16:40:00', '17:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1420, '2026-01-26', '17:40:00', '18:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1421, '2026-01-26', '18:40:00', '19:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1422, '2026-01-26', '19:40:00', '20:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1423, '2026-01-26', '20:40:00', '21:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1424, '2026-02-02', '08:40:00', '09:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1425, '2026-02-02', '09:40:00', '10:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1426, '2026-02-02', '10:40:00', '11:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1427, '2026-02-02', '11:40:00', '12:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1428, '2026-02-02', '12:40:00', '13:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1429, '2026-02-02', '13:40:00', '14:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1430, '2026-02-02', '14:40:00', '15:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1431, '2026-02-02', '15:40:00', '16:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1432, '2026-02-02', '16:40:00', '17:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1433, '2026-02-02', '17:40:00', '18:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1434, '2026-02-02', '18:40:00', '19:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1435, '2026-02-02', '19:40:00', '20:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1436, '2026-02-02', '20:40:00', '21:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1437, '2026-02-09', '08:40:00', '09:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1438, '2026-02-09', '09:40:00', '10:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1439, '2026-02-09', '10:40:00', '11:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1440, '2026-02-09', '11:40:00', '12:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1441, '2026-02-09', '12:40:00', '13:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1442, '2026-02-09', '13:40:00', '14:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1443, '2026-02-09', '14:40:00', '15:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1444, '2026-02-09', '15:40:00', '16:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1445, '2026-02-09', '16:40:00', '17:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1446, '2026-02-09', '17:40:00', '18:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1447, '2026-02-09', '18:40:00', '19:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1448, '2026-02-09', '19:40:00', '20:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1449, '2026-02-09', '20:40:00', '21:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1450, '2026-02-16', '08:40:00', '09:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1451, '2026-02-16', '09:40:00', '10:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1452, '2026-02-16', '10:40:00', '11:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1453, '2026-02-16', '11:40:00', '12:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1454, '2026-02-16', '12:40:00', '13:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1455, '2026-02-16', '13:40:00', '14:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1456, '2026-02-16', '14:40:00', '15:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1457, '2026-02-16', '15:40:00', '16:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1458, '2026-02-16', '16:40:00', '17:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1459, '2026-02-16', '17:40:00', '18:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1460, '2026-02-16', '18:40:00', '19:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1461, '2026-02-16', '19:40:00', '20:40:00', 60, 'disponivel', '2025-12-23 22:02:39'),
(1462, '2026-02-16', '20:40:00', '21:40:00', 60, 'disponivel', '2025-12-23 22:02:39');

-- --------------------------------------------------------

--
-- Estrutura para tabela `solicitacoes_agendamento`
--

CREATE TABLE `solicitacoes_agendamento` (
  `id_solicitacao` int(11) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `data_desejada` date NOT NULL,
  `hora_desejada` time NOT NULL,
  `status` enum('confirmada','cancelada') DEFAULT 'confirmada',
  `data_solicitacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` enum('admin','funcionario') DEFAULT 'funcionario',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_slots_ativos`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_slots_ativos` (
`id_slot` int(11)
,`data_slot` date
,`horario_inicio` time
,`horario_fim` time
,`duracao_minutos` int(11)
,`status` enum('disponivel','reservado','bloqueado')
,`data_criacao` timestamp
);

-- --------------------------------------------------------

--
-- Estrutura para view `view_slots_ativos`
--
DROP TABLE IF EXISTS `view_slots_ativos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_slots_ativos`  AS SELECT `slots_disponiveis`.`id_slot` AS `id_slot`, `slots_disponiveis`.`data_slot` AS `data_slot`, `slots_disponiveis`.`horario_inicio` AS `horario_inicio`, `slots_disponiveis`.`horario_fim` AS `horario_fim`, `slots_disponiveis`.`duracao_minutos` AS `duracao_minutos`, `slots_disponiveis`.`status` AS `status`, `slots_disponiveis`.`data_criacao` AS `data_criacao` FROM `slots_disponiveis` WHERE `slots_disponiveis`.`status` = 'disponivel' ORDER BY `slots_disponiveis`.`data_slot` ASC, `slots_disponiveis`.`horario_inicio` ASC ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Índices de tabela `config_disponibilidade`
--
ALTER TABLE `config_disponibilidade`
  ADD PRIMARY KEY (`id_config`);

--
-- Índices de tabela `config_excecoes`
--
ALTER TABLE `config_excecoes`
  ADD PRIMARY KEY (`id_excecao`);

--
-- Índices de tabela `consultas`
--
ALTER TABLE `consultas`
  ADD PRIMARY KEY (`id_consulta`);

--
-- Índices de tabela `procedimentos`
--
ALTER TABLE `procedimentos`
  ADD PRIMARY KEY (`id_procedimento`);

--
-- Índices de tabela `slots_disponiveis`
--
ALTER TABLE `slots_disponiveis`
  ADD PRIMARY KEY (`id_slot`),
  ADD UNIQUE KEY `unique_slot` (`data_slot`,`horario_inicio`);

--
-- Índices de tabela `solicitacoes_agendamento`
--
ALTER TABLE `solicitacoes_agendamento`
  ADD PRIMARY KEY (`id_solicitacao`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `config_disponibilidade`
--
ALTER TABLE `config_disponibilidade`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `config_excecoes`
--
ALTER TABLE `config_excecoes`
  MODIFY `id_excecao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `consultas`
--
ALTER TABLE `consultas`
  MODIFY `id_consulta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `procedimentos`
--
ALTER TABLE `procedimentos`
  MODIFY `id_procedimento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `slots_disponiveis`
--
ALTER TABLE `slots_disponiveis`
  MODIFY `id_slot` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1463;

--
-- AUTO_INCREMENT de tabela `solicitacoes_agendamento`
--
ALTER TABLE `solicitacoes_agendamento`
  MODIFY `id_solicitacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
