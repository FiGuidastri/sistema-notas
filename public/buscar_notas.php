<?php
// Exibir erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../conexao.php';

// Capturar a data de vencimento enviada via GET
$data_vencimento = $_GET['data_vencimento'] ?? null;

if ($data_vencimento) {
    // Consultar as notas com a data de vencimento selecionada
    $sql = "SELECT numero_nota, status_nota FROM notas_fiscais WHERE data_vencimento = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        // Retornar erro se a consulta falhar
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao preparar a consulta: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param('s', $data_vencimento);
    $stmt->execute();
    $result = $stmt->get_result();

    $notas = [];
    while ($row = $result->fetch_assoc()) {
        $notas[] = $row;
    }

    $stmt->close();
    $conn->close();

    // Retornar as notas como JSON
    header('Content-Type: application/json');
    echo json_encode($notas);
} else {
    // Retornar erro se a data de vencimento não for fornecida
    http_response_code(400);
    echo json_encode(['error' => 'Data de vencimento não fornecida.']);
}
?>