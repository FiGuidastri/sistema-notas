<?php
session_start();
require_once '../conexao.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) die("ID inválido");

try {
    // Query corrigida com todos os campos
    $stmt = $conn->prepare("UPDATE notas_fiscais SET
        numero_requisicao = ?,
        numero_pedido = ?,
        protocolo = ?,
        responsavel = ?,
        numero_nota = ?,
        valor = ?,
        data_emissao = ?,
        condicao_pagamento = ?
        WHERE id = ?");

    // Formatando os dados
    $protocolo = date('Y-m-d', strtotime($_POST['protocolo']));
    $numero_requisicao = $_POST['numero_requisicao'];
    $numero_pedido = $_POST['numero_pedido'];
    $responsavel = $_POST['responsavel'];
    $numero_nota = $_POST['numero_nota'];
    $valor = (float)$_POST['valor'];
    $data_emissao = $_POST['data_emissao'];
    $condicao_pagamento = $_POST['condicao_pagamento'];

    // Bind dos parâmetros na ordem correta
    $stmt->bind_param(
        "sssssdssi", // Tipos: 7 strings, 1 double, 1 integer
        $numero_requisicao,
        $numero_pedido,
        $protocolo,
        $responsavel,
        $numero_nota,
        $valor,
        $data_emissao,
        $condicao_pagamento,
        $id
    );

    if ($stmt->execute()) {
        $_SESSION['msg'] = "Nota atualizada com sucesso!";
    } else {
        $_SESSION['msg'] = "Erro ao atualizar: " . $stmt->error;
    }

    $stmt->close();

} catch (Exception $e) {
    $_SESSION['msg'] = "Erro: " . $e->getMessage();
}

$conn->close();
header("Location: listar_notas.php");
exit();
?>