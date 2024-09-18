<?php
session_start();

// Verificar se o ID do produto foi passado
if (!isset($_GET['id'])) {
    header('Location: cart.php');
    exit();
}

$id = intval($_GET['id']);

// Remover produto do carrinho
if (isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

// Redirecionar de volta para o carrinho
header('Location: cart.php');
exit();
?>
