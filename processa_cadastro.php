<?php
// processa_cadastro.php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografar a senha

    // Verificar se o email já está cadastrado
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "Email já cadastrado. <a href='cadastrar.php'>Tente novamente</a>.";
        exit;
    }

    // Inserir novo usuário
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senha);
    
    if ($stmt->execute()) {
        echo "Cadastro realizado com sucesso. <a href='login.php'>Faça login</a>.";
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>
