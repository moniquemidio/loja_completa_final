<?php
// admin/processa_edit_product.php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $foto = $_FILES['foto']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);

    // Atualizar produto
    if ($foto) {
        // Se houver nova foto, mover o arquivo e atualizar
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("UPDATE produtos SET nome = ?, descricao = ?, preco = ?, foto = ? WHERE id = ?");
            $stmt->bind_param("ssisi", $nome, $descricao, $preco, $foto, $id);
        } else {
            echo "Erro ao fazer upload da foto.";
            exit;
        }
    } else {
        // Se não houver nova foto, apenas atualizar os dados
        $stmt = $conn->prepare("UPDATE produtos SET nome = ?, descricao = ?, preco = ? WHERE id = ?");
        $stmt->bind_param("ssii", $nome, $descricao, $preco, $id);
    }

    if ($stmt->execute()) {
        header("Location: list_products.php");
        exit;
    } else {
        echo "Erro ao atualizar produto: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
