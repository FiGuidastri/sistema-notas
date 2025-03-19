<?php
session_start();
require_once '../conexao.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) die("ID inválido");

try {
    // Validação básica dos campos obrigatórios
    $camposObrigatorios = ['responsavel', 'numero_nota', 'valor', 'data_emissao', 'condicao_pagamento'];
    foreach ($camposObrigatorios as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("O campo $campo é obrigatório!");
        }
    }

    // Processamento dos valores
    $valor = str_replace(['.', ','], ['', '.'], $_POST['valor']);
    $valor = (float)$valor;
    
    // Validação do valor
    if ($valor > 99999999.99 || $valor <= 0) {
        throw new Exception("Valor inválido! Deve ser entre R$ 0,01 e R$ 99.999.999,99");
    }

    // Tratamento de campos opcionais (convertendo strings vazias para NULL)
    $numero_requisicao = !empty($_POST['numero_requisicao']) ? $_POST['numero_requisicao'] : null;
    $numero_pedido = !empty($_POST['numero_pedido']) ? $_POST['numero_pedido'] : null;
    $protocolo = !empty($_POST['protocolo']) ? $_POST['protocolo'] : null;

    // Formatação da data do protocolo
    if ($protocolo) {
        $protocolo = date('Y-m-d', strtotime($protocolo));
        if (!$protocolo) {
            throw new Exception("Formato de data do protocolo inválido!");
        }
    }

    // Query de atualização com prepared statement
    $stmt = $conn->prepare("UPDATE notas_fiscais SET
        responsavel = ?,
        numero_nota = ?,
        valor = ?,
        data_emissao = ?,
        condicao_pagamento = ?,
        numero_requisicao = ?,
        numero_pedido = ?,
        protocolo = ?
        WHERE id = ?");

    $stmt->bind_param(
        "ssdsssssi", // Tipos: s=string, d=double, i=integer
        $_POST['responsavel'],
        $_POST['numero_nota'],
        $valor,
        $_POST['data_emissao'],
        $_POST['condicao_pagamento'],
        $numero_requisicao,
        $numero_pedido,
        $protocolo,
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