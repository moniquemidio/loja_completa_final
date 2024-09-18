<?php
include 'db_connect.php';

$pedido_id = $_GET['id'];

// Excluir pedido
$stmt = $conn->prepare("DELETE FROM pedidos WHERE id = ?");
$stmt->bind_param("i", $pedido_id);

if ($stmt->execute()) {
    header("Location: consultar_pedidos.php?status=success");
    exit;
} else {
    header("Location: consultar_pedidos.php?status=error");
    exit;
}

$stmt->close();
$conn->close();
?>