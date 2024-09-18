<?php
session_start();
include 'admin/db_connect.php';

// Verificar se o ID do produto foi passado corretamente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = intval($_GET['id']);
$quantidade = 1; // Ajuste conforme necessário

// Consultar o produto pelo ID
$sql = "SELECT * FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $produto = $result->fetch_assoc();
    
    // Adicionar produto ao carrinho na sessão
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantidade'] += $quantidade;
    } else {
        $_SESSION['cart'][$id] = array(
            'nome' => $produto['nome'],
            'preco' => $produto['preco'],
            'quantidade' => $quantidade
        );
    }
    
    // Atualizar contagem do carrinho
    $cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantidade')) : 0;
    $_SESSION['cart_count'] = $cart_count;

    // Redirecionar para o carrinho
    header('Location: cart.php');
    exit();
} else {
    // Produto não encontrado
    echo "Produto não encontrado.";
    exit();
}
