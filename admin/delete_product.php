<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}

include 'db_connect.php'; // Conexão com o banco de dados

// Verificar se o ID do produto foi enviado
if (isset($_POST['id'])) {
    $product_id = intval($_POST['id']);

    // Preparar e executar a exclusão
    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        header("Location: list_products.php?status=success");
    } else {
        header("Location: list_products.php?status=error");
    }

    $stmt->close();
}

$conn->close();
?>