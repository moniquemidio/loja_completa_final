<?php
include 'db_connect.php';

// Dados do administrador
$username = 'admin'; // Nome de usuário do administrador
$password = 'admin123'; // Senha do administrador

// Criptografar a senha
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Inserir o administrador no banco de dados
$stmt = $conn->prepare("INSERT INTO administradores (username, password_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $password_hash);

if ($stmt->execute()) {
    echo "Administrador criado com sucesso!";
} else {
    echo "Erro ao criar administrador: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
