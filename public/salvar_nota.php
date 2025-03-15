<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Configurações do banco
$servidor = "localhost";
$usuario = "root";
$senha = "root";
$banco = "sistema_notas";

// Conexão com o banco
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Sanitiza os inputs
$responsavel = $conn->real_escape_string($_POST['responsavel']);
$numero_nota = $conn->real_escape_string($_POST['numero_nota']);
$valor = $conn->real_escape_string($_POST['valor']);
$data_emissao = $conn->real_escape_string($_POST['data_emissao']);
$condicao = $conn->real_escape_string($_POST['condicao_pagamento']);

// Query de inserção
$sql = "INSERT INTO notas_fiscais (responsavel, numero_nota, valor, data_emissao, condicao_pagamento)
        VALUES ('$responsavel', '$numero_nota', $valor, '$data_emissao', '$condicao')";

if ($conn->query($sql) === TRUE) {
    $_SESSION['msg'] = "Nota cadastrada com sucesso!";
} else {
    $_SESSION['msg'] = "Erro: " . $conn->error;
}

$conn->close();
header("Location: formulario.html");
exit();
?>