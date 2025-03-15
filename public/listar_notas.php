<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../conexao.php';

$sql = "SELECT * FROM notas_fiscais ORDER BY data_emissao DESC";
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
    <title>Lista de Notas</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #4CAF50; color: white; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Notas Fiscais Cadastradas</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Responsável</th>
                <th>Número</th>
                <th>Valor</th>
                <th>Emissão</th>
                <th>Pagamento</th>
                <th>Requisição</th>
                <th>Pedido</th>
                <th>Protocolo</th>
                <th>Ações</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['responsavel'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['numero_nota'] ?? '') ?></td>
                <td>R$ <?= number_format($row['valor'] ?? 0, 2, ',', '.') ?></td>
                <td><?= date('d/m/Y', strtotime($row['data_emissao'])) ?></td>
                <td><?= htmlspecialchars($row['condicao_pagamento'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['numero_requisicao'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($row['numero_pedido'] ?? 'N/A') ?></td>
                <td><?= date('d/m/Y', strtotime($row['protocolo'])) ?></td>
                <td>
                    <a href="editar_nota.php?id=<?= $row['id'] ?>">✏️ Editar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Nenhuma nota fiscal cadastrada.</p>
    <?php endif; ?>

</body>
</html>

<?php 
$conn->close();
?>