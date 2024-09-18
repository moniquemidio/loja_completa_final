<?php
// admin/db_connect.php

$servername = "localhost";
$username = "monemd_loja_virtual"; // seu usuário
$password = "22y6ZcOFi("; // sua senha
$dbname = "monemd_loja_virtual"; // nome do banco de dados

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>