<?php
session_start();
include 'db_connect.php'; // Ajuste o caminho se necessário

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se o carrinho não está vazio
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Código para processar o pedido
$usuario_id = $_SESSION['usuario_id'];
$cart = $_SESSION['cart'];
$total = 0;

foreach ($cart as $item) {
    $total += $item['preco'] * $item['quantidade'];
}

// Inserir o pedido no banco de dados
$sql = "INSERT INTO pedidos (usuario_id, total, data_pedido) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}

$stmt->bind_param("id", $usuario_id, $total);

if (!$stmt->execute()) {
    die("Erro ao executar a consulta: " . $stmt->error);
}

$pedido_id = $stmt->insert_id; // ID do pedido inserido

// Inserir os itens do pedido
$sql = "INSERT INTO itens_pedido (id, produto_id, quantidade, preco) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}

foreach ($cart as $id => $item) {
    $stmt->bind_param("iiid", $pedido_id, $id, $item['quantidade'], $item['preco']);
    
    if (!$stmt->execute()) {
        die("Erro ao executar a consulta: " . $stmt->error);
    }
}

// Limpar o carrinho
unset($_SESSION['cart']);

// Redirecionar para a página de confirmação de pagamento
header('Location: confirmacao_pagamento.php');
exit();
?>
