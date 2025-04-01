<?php
// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexão com o banco de dados
require_once '../conexao.php';

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validação dos campos obrigatórios
        $camposObrigatorios = ['responsavel', 'numero_nota', 'fornecedor', 'valor', 'data_emissao', 'condicao_pagamento'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($_POST[$campo])) {
                throw new Exception("O campo <strong>$campo</strong> é obrigatório!");
            }
        }

        // Atribuir valores às variáveis
        $responsavel = trim($_POST['responsavel']);
        $numero_nota = trim($_POST['numero_nota']);
        $fornecedor = trim($_POST['fornecedor']);
        $valor = floatval(str_replace(',', '.', $_POST['valor']));
        $data_emissao = $_POST['data_emissao'];
        $condicao_pagamento = $_POST['condicao_pagamento'];

        // Calcular a data de vencimento com base na condição de pagamento
        $prazo_pagamento = 0;
        switch ($condicao_pagamento) {
            case 'À Vista':
                $prazo_pagamento = 1;
                break;
            case '30 dias':
                $prazo_pagamento = 30;
                break;
            case '60 dias':
                $prazo_pagamento = 60;
                break;
        }
        $data_vencimento = date('Y-m-d', strtotime("+$prazo_pagamento days", strtotime($data_emissao)));

        // Inserir os dados no banco de dados
        $stmt = $conn->prepare("INSERT INTO notas_fiscais (
            responsavel,
            numero_nota,
            fornecedor,
            valor,
            data_emissao,
            condicao_pagamento,
            data_vencimento
        ) VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssdsss",
            $responsavel,
            $numero_nota,
            $fornecedor,
            $valor,
            $data_emissao,
            $condicao_pagamento,
            $data_vencimento
        );

        if (!$stmt->execute()) {
            throw new Exception("Erro ao salvar a nota: " . $stmt->error);
        }

        // Redirecionar para a página de listagem com mensagem de sucesso
        header("Location: listar_notas.php?msg=Nota cadastrada com sucesso!");
        exit();
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Notas Fiscais</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 20px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; margin-bottom: 10px; }
        button { background: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background: #45a049; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h2>Cadastrar Nota Fiscal</h2>

    <?php if (!empty($erro)): ?>
        <p class="error"><?= $erro ?></p>
    <?php endif; ?>

    <form id="formNota" action="" method="POST">
        <div class="form-group">
            <label>Responsável:</label>
            <input type="text" name="responsavel" required>
        </div>

        <div class="form-group">
            <label>Número da Nota:</label>
            <input type="text" name="numero_nota" required>
        </div>

        <div class="form-group">
            <label>Fornecedor:</label>
            <input type="text" name="fornecedor" required>
        </div>

        <div class="form-group">
            <label>Valor:</label>
            <input type="number" step="0.01" name="valor" required>
        </div>

        <div class="form-group">
            <label>Data de Emissão:</label>
            <input type="date" name="data_emissao" required>
        </div>

        <div class="form-group">
            <label>Condição de Pagamento:</label>
            <select name="condicao_pagamento" required>
                <option value="">Selecione</option>
                <option value="À Vista">À Vista</option>
                <option value="30 dias">30 dias</option>
                <option value="60 dias">60 dias</option>
            </select>
        </div>

        <button type="submit">Salvar Nota</button>
        <button type="button" onclick="window.location.href='listar_notas.php'" style="background: #0977d8; color: white; margin-left: 10px;">Voltar</button>
    </form>
</body>
</html>