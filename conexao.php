<?php
// conexao.php
$servidor = "localhost";
$usuario = "root";
$senha = "root";
$banco = "sistema_notas";

$conn = new mysqli($servidor, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>