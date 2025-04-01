CREATE DATABASE IF NOT EXISTS sistema_notas;
USE sistema_notas;

-- Criar usuário somente se não existir
DROP USER IF EXISTS 'app_notas'@'localhost';
CREATE USER 'app_notas'@'localhost' IDENTIFIED BY 'SenhaForte@123';
GRANT ALL PRIVILEGES ON sistema_notas.* TO 'app_notas'@'localhost';
FLUSH PRIVILEGES;

CREATE TABLE IF NOT EXISTS notas_fiscais (
    id INT PRIMARY KEY AUTO_INCREMENT,
    responsavel VARCHAR(100) NOT NULL,
    fornecedor VARCHAR(255) NOT NULL, -- Adicionado diretamente na criação
    numero_nota VARCHAR(20) NOT NULL UNIQUE,
    valor DECIMAL(10,2) NOT NULL,
    data_emissao DATE NOT NULL,
    condicao_pagamento VARCHAR(50) NOT NULL,
    numero_requisicao VARCHAR(20) NULL DEFAULT NULL,
    numero_pedido VARCHAR(20) NULL DEFAULT NULL,
    protocolo DATE NULL DEFAULT NULL,
    status_nota VARCHAR(20) AS (
        CASE
            WHEN COALESCE(numero_requisicao, '') = '' 
             AND COALESCE(numero_pedido, '') = '' 
             AND protocolo IS NULL THEN 'Requisição Pendente'
            WHEN COALESCE(numero_requisicao, '') != '' 
             AND COALESCE(numero_pedido, '') = '' 
             AND protocolo IS NULL THEN 'Pedido Pendente'
            WHEN COALESCE(numero_requisicao, '') != '' 
             AND COALESCE(numero_pedido, '') != '' 
             AND protocolo IS NULL THEN 'Protocolo Pendente'
            ELSE 'OK'
        END
    ) STORED
);

-- Ajuste de dados existentes para coerência
UPDATE notas_fiscais 
SET numero_requisicao = NULL WHERE numero_requisicao = '';

UPDATE notas_fiscais 
SET numero_pedido = NULL WHERE numero_pedido = '';

UPDATE notas_fiscais 
SET protocolo = NULL WHERE protocolo IN ('0000-00-00', '1970-01-01');

-- Teste de inserção
INSERT INTO notas_fiscais (responsavel, fornecedor, numero_nota, valor, data_emissao, condicao_pagamento, numero_requisicao, numero_pedido, protocolo)
VALUES ('Teste Pedido Pendente', 'Fornecedor X', 'NF-TEST1', 100.00, '2024-03-15', '30 dias', 'REQ-123', NULL, NULL);

-- Remover registros de teste
DELETE FROM notas_fiscais WHERE numero_nota LIKE 'NF-TEST%';

-- Verificar estrutura final da tabela
SHOW CREATE TABLE notas_fiscais;
