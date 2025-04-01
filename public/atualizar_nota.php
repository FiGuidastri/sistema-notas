<?php
session_start();
require_once '../conexao.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) die("ID inválido");

try {
    // Processamento dos valores
    $valor = (float) str_replace(',', '.', $_POST['valor']);
    
    // Validação do valor
    if ($valor > 99999999.99 || $valor <= 0) {
        throw new Exception("Valor inválido! Deve ser entre R$ 0,01 e R$ 99.999.999,99");
    }

    // Tratamento de campos opcionais (convertendo strings vazias para NULL)
    $responsavel = !empty($_POST['responsavel']) ? $_POST['responsavel'] : null;
    $numero_nota = !empty($_POST['numero_nota']) ? $_POST['numero_nota'] : null;
    $fornecedor = !empty($_POST['fornecedor']) ? $_POST['fornecedor'] : null;
    $data_emissao = !empty($_POST['data_emissao']) ? $_POST['data_emissao'] : null;
    $condicao_pagamento = !empty($_POST['condicao_pagamento']) ? $_POST['condicao_pagamento'] : null;
    $numero_requisicao = !empty($_POST['numero_requisicao']) ? $_POST['numero_requisicao'] : null;
    $numero_pedido = !empty($_POST['numero_pedido']) ? $_POST['numero_pedido'] : null;
    $protocolo = !empty($_POST['protocolo']) ? $_POST['protocolo'] : null;

    // Validação e formatação da data do protocolo
    if ($protocolo) {
        $timestamp = strtotime($protocolo);
        if (!$timestamp) {
            throw new Exception("Formato de data do protocolo inválido!");
        }
        $protocolo = date('Y-m-d', $timestamp);
    }

    // Validação e formatação da data de emissão
    if ($data_emissao) {
        $timestamp = strtotime($data_emissao);
        if (!$timestamp) {
            throw new Exception("Formato de data de emissão inválido!");
        }
        $data_emissao = date('Y-m-d', $timestamp);
    }

    // Calcular a data de vencimento
    if (!empty($_POST['data_emissao']) && !empty($_POST['condicao_pagamento'])) {
        $data_emissao = $_POST['data_emissao'];
        $prazo_pagamento = intval(preg_replace('/[^0-9]/', '', $_POST['condicao_pagamento'])); // Extrai o número de dias
        $data_vencimento = date('Y-m-d', strtotime("+$prazo_pagamento days", strtotime($data_emissao)));
    } else {
        $data_vencimento = null; // Caso não tenha data de emissão ou condição de pagamento
    }

    // Query de atualização com prepared statement
    $stmt = $conn->prepare("UPDATE notas_fiscais SET
        responsavel = ?,
        numero_nota = ?,
        fornecedor = ?,
        valor = ?,
        data_emissao = ?,
        condicao_pagamento = ?,
        numero_requisicao = ?,
        numero_pedido = ?,
        protocolo = ?,
        data_vencimento = ?
        WHERE id = ?");

    $stmt->bind_param(
        "sssdssssssi", // Adicione o tipo correspondente ao novo campo
        $responsavel,
        $numero_nota,
        $fornecedor,
        $valor,
        $data_emissao,
        $condicao_pagamento,
        $numero_requisicao,
        $numero_pedido,
        $protocolo,
        $data_vencimento,
        $id
    );

    if (!$stmt->execute()) {
        throw new Exception("Erro na atualização: " . $stmt->error);
    }

    $_SESSION['msg'] = "✅ Nota atualizada com sucesso!";
    $stmt->close();

} catch (Exception $e) {
    $_SESSION['msg'] = "❌ Erro: " . $e->getMessage();
}

$conn->close();
header("Location: editar_nota.php?id=$id");
exit();
?>