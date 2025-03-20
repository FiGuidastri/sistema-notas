<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../conexao.php';

$sql = "SELECT 
            id,
            responsavel,
            numero_nota,
            fornecedor,
            valor,
            data_emissao,
            condicao_pagamento,
            numero_requisicao,
            numero_pedido,
            protocolo,
            CASE
                WHEN (TRIM(COALESCE(numero_requisicao, '')) = '' 
                      AND TRIM(COALESCE(numero_pedido, '')) = '' 
                      AND protocolo IS NULL) 
                    THEN 'Requisição Pendente'
                    
                WHEN (TRIM(COALESCE(numero_requisicao, '')) != '' 
                      AND TRIM(COALESCE(numero_pedido, '')) = '' 
                      AND protocolo IS NULL) 
                    THEN 'Pedido Pendente'
                    
                WHEN (TRIM(COALESCE(numero_requisicao, '')) != '' 
                      AND TRIM(COALESCE(numero_pedido, '')) != '' 
                      AND protocolo IS NULL) 
                    THEN 'Protocolo Pendente'
                    
                ELSE 'OK'
            END AS status_nota
        FROM notas_fiscais
        ORDER BY data_emissao DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Notas Fiscais</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .btn-nova-nota {
            background-color: #27ae60;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-nova-nota:hover {
            background-color: #219150;
        }
        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #34495e;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .status-pendente {
            background-color: #e74c3c;
            color: white;
        }
        .status-ok {
            background-color: #2ecc71;
            color: white;
        }
        .acoes a {
            text-decoration: none;
            padding: 5px 8px;
            border-radius: 4px;
            font-size: 14px;
        }
        .editar {
            background-color: #f39c12;
            color: white;
        }
        .editar:hover {
            background-color: #e67e22;
        }
        .sem-registros {
            text-align: center;
            font-size: 18px;
            color: #555;
        }
        @media (max-width: 768px) {
            table {
                width: 100%;
                font-size: 14px;
            }
            .header {
                flex-direction: column;
                text-align: center;
            }
            .btn-nova-nota {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gestão de Notas Fiscais</h1>
        <a href="formulario.html" class="btn-nova-nota">➕ Nova Nota</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Responsável</th>
                    <th>Número</th>
                    <th>Fornecedor</th>
                    <th>Valor</th>
                    <th>Emissão</th>
                    <th>Pagamento</th>
                    <th>Requisição</th>
                    <th>Pedido</th>
                    <th>Protocolo</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['responsavel']) ?></td>
                    <td><?= htmlspecialchars($row['numero_nota']) ?></td>
                    <td><?= htmlspecialchars(string: $row['fornecedor']) ?></td>
                    <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                    <td><?= date('d/m/Y', strtotime($row['data_emissao'])) ?></td>
                    <td><?= htmlspecialchars($row['condicao_pagamento']) ?></td>
                    <td><?= $row['numero_requisicao'] ? htmlspecialchars($row['numero_requisicao']) : 'N/A' ?></td>
                    <td><?= $row['numero_pedido'] ? htmlspecialchars($row['numero_pedido']) : 'N/A' ?></td>
                    <td>
                        <?php if (!empty($row['protocolo']) && 
                                !in_array($row['protocolo'], ['0000-00-00', '1970-01-01'])): ?>
                            <?= date('d/m/Y', strtotime($row['protocolo'])) ?>
                        <?php else: ?>
                            <span class="data-invalida">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="status <?= strpos($row['status_nota'], 'Pendente') !== false ? 'status-pendente' : 'status-ok' ?>">
                            <?= $row['status_nota'] ?>
                        </div>
                    </td>
                    <td>
                        <a href="editar_nota.php?id=<?= $row['id'] ?>">✏️ Editar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="sem-registros">Nenhuma nota fiscal cadastrada.</p>
    <?php endif; ?>

</body>
</html>

<?php 
$conn->close();
?>