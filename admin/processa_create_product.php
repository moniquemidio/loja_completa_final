<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $foto = $_FILES['foto']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);

    // Verifica se o diretório 'uploads/' existe
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Tenta mover o arquivo para o diretório de uploads
    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        // Preparar e executar a consulta SQL
        $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, foto) VALUES (?, ?, ?, ?)");

        // Verifique se a preparação da consulta foi bem-sucedida
        if ($stmt === false) {
            die("Erro ao preparar a consulta: " . $conn->error);
        }

        $stmt->bind_param("ssis", $nome, $descricao, $preco, $foto);

        if ($stmt->execute()) {
            header("Location: list_products.php");
            exit;
        } else {
            echo "Erro ao inserir produto: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erro ao fazer upload da foto.";
    }

    $conn->close();
}
?>
