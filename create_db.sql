CREATE DATABASE IF NOT EXISTS sistema_notas;
USE sistema_notas;

CREATE TABLE IF NOT EXISTS notas_fiscais (
    id INT PRIMARY KEY AUTO_INCREMENT,
    responsavel VARCHAR(100) NOT NULL,
    numero_nota VARCHAR(20) NOT NULL UNIQUE,
    valor DECIMAL(10,2) NOT NULL,
    data_emissao DATE NOT NULL,
    condicao_pagamento VARCHAR(50) NOT NULL,
    numero_requisicao VARCHAR(20) NOT NULL DEFAULT '',
    numero_pedido VARCHAR(20) NOT NULL DEFAULT '',
    protocolo DATE NULL,
    status_nota VARCHAR(20) AS (
        CASE
            WHEN numero_requisicao = '' AND numero_pedido = '' AND protocolo IS NULL THEN 'Requisição Pendente'
            WHEN numero_requisicao != '' AND numero_pedido = '' AND protocolo IS NULL THEN 'Pedido Pendente'
            WHEN numero_requisicao != '' AND numero_pedido != '' AND protocolo IS NULL THEN 'Protocolo Pendente'
            ELSE 'OK'
        END
    ) STORED
);

-- Permissões (ajuste o usuário e senha)
CREATE USER 'app_notas'@'localhost' IDENTIFIED BY 'SenhaForte@123';
GRANT ALL PRIVILEGES ON sistema_notas.* TO 'app_notas'@'localhost';
FLUSH PRIVILEGES;


SHOW COLUMNS FROM notas_fiscais LIKE 'valor';

ALTER TABLE notas_fiscais 
MODIFY COLUMN protocolo DATE DEFAULT NULL;


ALTER TABLE notas_fiscais 
MODIFY COLUMN numero_requisicao VARCHAR(20) DEFAULT '',
MODIFY COLUMN numero_pedido VARCHAR(20) DEFAULT '';

ALTER TABLE notas_fiscais 
MODIFY COLUMN numero_requisicao VARCHAR(20) NULL DEFAULT NULL,
MODIFY COLUMN numero_pedido VARCHAR(20) NULL DEFAULT NULL,
MODIFY COLUMN protocolo DATE NULL DEFAULT NULL;

-- Corrigir dados existentes
UPDATE notas_fiscais SET
    numero_requisicao = NULL WHERE numero_requisicao = '';
UPDATE notas_fiscais SET
    numero_pedido = NULL WHERE numero_pedido = '';
UPDATE notas_fiscais SET
    protocolo = NULL WHERE protocolo IN ('0000-00-00', '1970-01-01');

-- Ajustar estrutura da tabela
ALTER TABLE notas_fiscais 
MODIFY COLUMN numero_requisicao VARCHAR(20) NULL,
MODIFY COLUMN numero_pedido VARCHAR(20) NULL,
MODIFY COLUMN protocolo DATE NULL;

INSERT INTO notas_fiscais (responsavel, numero_nota, valor, data_emissao, condicao_pagamento, numero_requisicao, numero_pedido, protocolo)
VALUES ('Teste Pedido Pendente', 'NF-TEST1', 100.00, '2024-03-15', '30 dias', 'REQ-123', '', NULL);

-- Limpe registros de teste
DELETE FROM notas_fiscais WHERE numero_nota LIKE 'NF-TEST%';

-- Verifique a estrutura da tabela
SHOW CREATE TABLE notas_fiscais;

--TODO ADICIONAR COLUNA COM NOME DO FORNECEDOR
ALTER TABLE notas_fiscais
ADD COLUMN fornecedor VARCHAR(100) NOT NULL;
