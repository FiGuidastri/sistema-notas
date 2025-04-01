<?php
var_dump($_POST);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../conexao.php';

try {
    // Validação dos campos obrigatórios
    $camposObrigatorios = ['responsavel', 'numero_nota', 'fornecedor', 'valor', 'data_emissao', 'condicao_pagamento'];
    foreach ($camposObrigatorios as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("O campo <strong>$campo</strong> é obrigatório!");
        }
    }

    // Atribui valores às variáveis
    $responsavel = trim($_POST['responsavel']);
    $numero_nota = trim($_POST['numero_nota']);
    $fornecedor = trim($_POST['fornecedor']);
    if (empty($fornecedor)) {
        throw new Exception("Fornecedor não pode ser vazio!");
    }

    error_log("Fornecedor recebido: " . $fornecedor);

    // Converte valor corretamente
    $valor = floatval(str_replace(',', '.', $_POST['valor']));
    error_log("Valor recebido: " . $valor);

    $data_emissao = $_POST['data_emissao'];
    error_log("Data de emissão recebida: " . $data_emissao);

    $condicao_pagamento = $_POST['condicao_pagamento'];
    error_log("Condição de pagamento recebida: " . $condicao_pagamento);

    // Calcular a data de vencimento
    if (!empty($_POST['data_emissao']) && !empty($_POST['condicao_pagamento'])) {
        $data_emissao = $_POST['data_emissao'];
        $prazo_pagamento = intval(preg_replace('/[^0-9]/', '', $_POST['condicao_pagamento'])); // Extrai o número de dias
        $data_vencimento = date('Y-m-d', strtotime("+$prazo_pagamento days", strtotime($data_emissao)));
    } else {
        $data_vencimento = null; // Caso não tenha data de emissão ou condição de pagamento
    }

    // Verifica campos opcionais
    $numero_requisicao = $_POST['numero_requisicao'] ?? '';
    $numero_pedido = $_POST['numero_pedido'] ?? '';
    $protocolo = empty($_POST['protocolo']) ? null : $_POST['protocolo'];

    // Validação do valor
    if ($valor > 99999999.99) {
        throw new Exception("Valor máximo permitido: R$ 99.999.999,99");
    }

    // Query preparada
    $stmt = $conn->prepare("INSERT INTO notas_fiscais (
        responsavel,
        numero_nota,
        fornecedor,
        valor,
        data_emissao,
        condicao_pagamento,
        numero_requisicao,
        numero_pedido,
        protocolo,
        data_vencimento
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        throw new Exception("Erro na preparação da query: " . $conn->error);
    }

    // Log para verificar os valores antes do bind_param
    error_log("Valores para bind_param:");
    error_log("Responsável: $responsavel");
    error_log("Número Nota: $numero_nota");
    error_log("Fornecedor: $fornecedor");
    error_log("Valor: $valor");
    error_log("Data Emissão: $data_emissao");
    error_log("Condição Pagamento: $condicao_pagamento");
    error_log("Número Requisição: $numero_requisicao");
    error_log("Número Pedido: $numero_pedido");
    error_log("Protocolo: $protocolo");
    error_log("Data Vencimento: $data_vencimento");

    // Bind dos parâmetros
    $stmt->bind_param(
        "sssdssssss", // Adicione o tipo correspondente ao novo campo
        $responsavel,
        $numero_nota,
        $fornecedor,
        $valor,
        $data_emissao,
        $condicao_pagamento,
        $numero_requisicao,
        $numero_pedido,
        $protocolo,
        $data_vencimento
    );

    if (!$stmt->execute()) {
        throw new Exception("Erro ao salvar: " . $stmt->error);
    }

    $_SESSION['msg'] = "✅ Nota cadastrada com sucesso!";
    error_log("Nota cadastrada com sucesso!");
    $stmt->close();

} catch (Exception $e) {
    $_SESSION['msg'] = "❌ " . $e->getMessage();
    error_log("Erro: " . $e->getMessage());
}

$conn->close();
header("Location: formulario.html?msg=" . urlencode($_SESSION['msg']));
exit();
?>
