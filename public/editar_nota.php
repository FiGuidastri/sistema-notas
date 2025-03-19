<?php
session_start();
require_once '../conexao.php';

// Validação do ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) die("ID inválido");

// Consulta ao banco
$stmt = $conn->prepare("SELECT * FROM notas_fiscais WHERE id = ?");
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
    <link rel="stylesheet" href="../assets/estilo.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .required {
            color: red;
        }

        .btn {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-voltar {
            display: block;
            text-align: center;
            margin-top: 10px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Editar Nota Fiscal #<?= htmlspecialchars($nota['numero_nota']) ?></h2>

        <form action="atualizar_nota.php" method="POST" onsubmit="return validarFormulario()">
            <input type="hidden" name="id" value="<?= $nota['id'] ?>">

            <div class="form-group">
                <label>Responsável: <span class="required">*</span></label>
                <input type="text" name="responsavel" value="<?= htmlspecialchars($nota['responsavel']) ?>" required>
            </div>

            <div class="form-group">
                <label>Número da Nota: <span class="required">*</span></label>
                <input type="text" name="numero_nota" value="<?= htmlspecialchars($nota['numero_nota']) ?>" required>
            </div>

            <div class="form-group">
                <label>Valor (R$): <span class="required">*</span></label>
                <input type="number" step="0.01" name="valor" value="<?= htmlspecialchars($nota['valor']) ?>" required>
            </div>

            <div class="form-group">
                <label>Data de Emissão: <span class="required">*</span></label>
                <input type="date" name="data_emissao" value="<?= htmlspecialchars($nota['data_emissao']) ?>" required>
            </div>

            <div class="form-group">
                <label>Condição de Pagamento: <span class="required">*</span></label>
                <select name="condicao_pagamento" required>
                    <option value="À Vista" <?= $nota['condicao_pagamento'] == 'À Vista' ? 'selected' : '' ?>>À Vista</option>
                    <option value="30 dias" <?= $nota['condicao_pagamento'] == '30 dias' ? 'selected' : '' ?>>30 dias</option>
                    <option value="60 dias" <?= $nota['condicao_pagamento'] == '60 dias' ? 'selected' : '' ?>>60 dias</option>
                </select>
            </div>

            <div class="form-group">
                <label>Número da Requisição:</label>
                <input type="text" name="numero_requisicao" value="<?= htmlspecialchars($nota['numero_requisicao']) ?>">
            </div>

            <div class="form-group">
                <label>Número do Pedido:</label>
                <input type="text" name="numero_pedido" value="<?= htmlspecialchars($nota['numero_pedido']) ?>">
            </div>

            <div class="form-group">
                <label>Protocolo (Data):</label>
                <input type="date" name="protocolo" value="<?= htmlspecialchars($nota['protocolo'] ?? '') ?>">
            </div>

            <button type="submit" class="btn">Atualizar Nota</button>
        </form>

        <a href="listar_notas.php" class="btn-voltar">🔙 Voltar</a>
    </div>

    <script>
        function validarFormulario() {
            const valor = document.querySelector('input[name="valor"]').value;
            if (valor <= 0) {
                alert("O valor da nota fiscal deve ser maior que zero!");
                return false;
            }
            return true;
        }
    </script>

</body>
</html>

<?php
$conn->close();
?>
