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
<html>
<head>
    <title>Editar Nota Fiscal</title>
    <style>
        .form-group { margin-bottom: 15px; }
        input, select { width: 100%; padding: 8px; }
    </style>
</head>
<body>
    <h2>Editar Nota Fiscal #<?= $nota['numero_nota'] ?></h2>
    
    <form action="atualizar_nota.php" method="POST">
        <input type="hidden" name="id" value="<?= $nota['id'] ?>">

        <!-- Campos originais -->
        <div class="form-group">
            <label>Responsável:</label>
            <input type="text" name="responsavel" value="<?= htmlspecialchars($nota['responsavel']) ?>" required>
        </div>

        <div class="form-group">
            <label>Número da Nota:</label>
            <input type="text" name="numero_nota" value="<?= htmlspecialchars($nota['numero_nota']) ?>" required>
        </div>

        <div class="form-group">
            <label>Valor:</label>
            <input type="number" step="0.01" name="valor" value="<?= $nota['valor'] ?>" required>
        </div>

        <div class="form-group">
            <label>Data de Emissão:</label>
            <input type="date" name="data_emissao" value="<?= $nota['data_emissao'] ?>" required>
        </div>

        <div class="form-group">
            <label>Condição de Pagamento:</label>
            <select name="condicao_pagamento" required>
                <option value="À Vista" <?= $nota['condicao_pagamento'] == 'À Vista' ? 'selected' : '' ?>>À Vista</option>
                <option value="30 dias" <?= $nota['condicao_pagamento'] == '30 dias' ? 'selected' : '' ?>>30 dias</option>
                <option value="60 dias" <?= $nota['condicao_pagamento'] == '60 dias' ? 'selected' : '' ?>>60 dias</option>
            </select>
        </div>
        
        <!-- Campos novos -->
        <div class="form-group">
            <label>Número da Requisição:</label>
            <input type="text" name="numero_requisicao" 
                   value="<?= htmlspecialchars($nota['numero_requisicao'] ?? '') ?>" 
                   required>
        </div>

        <div class="form-group">
            <label>Número do Pedido:</label>
            <input type="text" name="numero_pedido" 
                   value="<?= htmlspecialchars($nota['numero_pedido'] ?? '') ?>" 
                   required>
        </div>

                <!-- Campo Protocolo (Data) -->
        <div class="form-group">
            <label>Protocolo (Data):</label>
            <input type="date" name="protocolo" 
                   value="<?= htmlspecialchars($nota['protocolo'] ?? date('Y-m-d')) ?>" 
                   required>
        </div>

        <button type="submit">Atualizar Nota</button>
    </form>
</body>
</html>

<?php
$conn->close(); // Feche a conexão apenas uma vez no final
?>