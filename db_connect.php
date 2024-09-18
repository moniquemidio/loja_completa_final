<?php
$servername = "localhost";
$username = "monemd_loja_virtual"; // seu usuário
$password = "22y6ZcOFi("; // sua senha
$dbname = "monemd_loja_virtual"; // nome do banco de dados

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>