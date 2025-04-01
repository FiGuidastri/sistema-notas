<?php
session_start();
require_once '../conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) die("ID inválido");

if ($conn->connect_error) die("Erro de conexão: " . $conn->connect_error);

// QUERY CORRIGIDA (removido comentário HTML)
$stmt = $conn->prepare("SELECT 
    id,
    responsavel,
    numero_nota,
    fornecedor,
    valor,
    data_emissao,
    condicao_pagamento,
    numero_requisicao,
    numero_pedido,
    protocolo
FROM notas_fiscais 
WHERE id = ?");

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) die("Nota não encontrada");
$nota = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Nota Fiscal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .btn-primary {
            width: 100%;
            padding: 10px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Editar Nota Fiscal #<?= $nota['numero_nota'] ?></h2>
        
        <form action="atualizar_nota.php" method="POST">
            <input type="hidden" name="id" value="<?= $nota['id'] ?>">

            <!-- Campos originais -->
            <div class="form-group">
                <label for="responsavel">Responsável:</label>
                <input type="text" class="form-control" id="responsavel" name="responsavel" value="<?= htmlspecialchars($nota['responsavel']) ?>" required>
            </div>

            <div class="form-group">
                <label for="fornecedor">Fornecedor:</label>
                <input type="text" class="form-control" id="fornecedor" name="fornecedor" value="<?= htmlspecialchars($nota['fornecedor']) ?>" required>
            </div>

            <div class="form-group">
                <label for="numero_nota">Número da Nota:</label>
                <input type="text" class="form-control" id="numero_nota" name="numero_nota" value="<?= htmlspecialchars($nota['numero_nota']) ?>" required>
            </div>

            <div class="form-group">
                <label for="valor">Valor:</label>
                <input type="number" step="0.01" class="form-control" id="valor" name="valor" value="<?= $nota['valor'] ?>" required>
            </div>

            <div class="form-group">
                <label for="data_emissao">Data de Emissão:</label>
                <input type="date" class="form-control" id="data_emissao" name="data_emissao" value="<?= $nota['data_emissao'] ?>" required>
            </div>

            <div class="form-group">
                <label for="condicao_pagamento">Condição de Pagamento:</label>
                <select class="form-control" id="condicao_pagamento" name="condicao_pagamento" required>
                    <option value="À Vista" <?= $nota['condicao_pagamento'] == 'À Vista' ? 'selected' : '' ?>>À Vista</option>
                    <option value="30 dias" <?= $nota['condicao_pagamento'] == '30 dias' ? 'selected' : '' ?>>30 dias</option>
                    <option value="60 dias" <?= $nota['condicao_pagamento'] == '60 dias' ? 'selected' : '' ?>>60 dias</option>
                </select>
            </div>
            
            <!-- Campos novos -->
            <div class="form-group">
                <label for="numero_requisicao">Número da Requisição:</label>
                <input type="text" class="form-control" id="numero_requisicao" name="numero_requisicao" value="<?= htmlspecialchars($nota['numero_requisicao'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="numero_pedido">Número do Pedido:</label>
                <input type="text" class="form-control" id="numero_pedido" name="numero_pedido" value="<?= htmlspecialchars($nota['numero_pedido'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="protocolo">Protocolo (Data):</label>
                <input type="date" class="form-control" id="protocolo" name="protocolo" value="<?= htmlspecialchars($nota['protocolo'] ?? '')?>">
            </div>
    <!--todo criar campo de observação--> 

            <button type="submit" class="btn btn-primary">Atualizar Nota</button>
        </form>
    </div>

    <!-- Botão de voltar -->
    <button onclick="window.history.back()">Voltar</button>

    <!-- Bootstrap JS (opcional, se precisar de funcionalidades JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$protocolo = !empty($_POST['protocolo']) ? $_POST['protocolo'] : null;

if ($protocolo) {
    $timestamp = strtotime($protocolo);
    if (!$timestamp) {
        throw new Exception("Formato de data do protocolo inválido!");
    }
    $protocolo = date('Y-m-d', $timestamp);
}

$conn->close(); // Feche a conexão apenas uma vez no final
?>