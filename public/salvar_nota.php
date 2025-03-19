<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../conexao.php';

try {
    // Validação dos campos obrigatórios
    $camposObrigatorios = ['responsavel', 'numero_nota', 'valor', 'data_emissao', 'condicao_pagamento'];
    foreach ($camposObrigatorios as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("O campo <strong>$campo</strong> é obrigatório!");
        }
    }

    // Atribui valores às variáveis (IMPORTANTE PARA bind_param)
    $responsavel = $_POST['responsavel'];
    $numero_nota = $_POST['numero_nota'];
    $valor = (float) str_replace(['.', ','], ['', '.'], $_POST['valor']);
    $data_emissao = $_POST['data_emissao'];
    $condicao_pagamento = $_POST['condicao_pagamento'];
    $numero_requisicao = $_POST['numero_requisicao'] ?? '';
    $numero_pedido = $_POST['numero_pedido'] ?? '';
    $protocolo = $_POST['protocolo'] ?? null;

    // Validação do valor
    if ($valor > 99999999.99) {
        throw new Exception("Valor máximo permitido: R$ 99.999.999,99");
    }

    // Query com prepared statement
    $stmt = $conn->prepare("INSERT INTO notas_fiscais (
        responsavel,
        numero_nota,
        valor,
        data_emissao,
        condicao_pagamento,
        numero_requisicao,
        numero_pedido,
        protocolo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        throw new Exception("Erro na preparação da query: " . $conn->error);
    }

    // Bind dos parâmetros (agora usando variáveis)
    $stmt->bind_param(
        "ssdsssss",
        $responsavel,
        $numero_nota,
        $valor,
        $data_emissao,
        $condicao_pagamento,
        $numero_requisicao,
        $numero_pedido,
        $protocolo
    );

    if (!$stmt->execute()) {
        throw new Exception("Erro ao salvar: " . $stmt->error);
    }

    $_SESSION['msg'] = "✅ Nota cadastrada com sucesso!";
    $stmt->close();

} catch (Exception $e) {
    $_SESSION['msg'] = "❌ " . $e->getMessage();
}

$conn->close();
header("Location: formulario.html");
exit();
?>