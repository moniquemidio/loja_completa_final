<?php
session_start();
include 'db_connect.php';

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Consultar o usuário no banco de dados
    $sql = "SELECT id, nome, senha FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($usuario_id, $user_name, $hashed_password);
        $stmt->fetch();

        // Verificar a senha
        if (password_verify($senha, $hashed_password)) {
            // Definir as variáveis de sessão
            $_SESSION['usuario_id'] = $usuario_id;
            $_SESSION['user_name'] = $user_name;
            
            // Redirecionar para a página inicial ou onde o usuário estava
            header('Location: index.php');
            exit();
        } else {
            // Senha incorreta
            echo "Senha incorreta.";
        }
    } else {
        // Usuário não encontrado
        echo "Usuário não encontrado.";
    }
}
?>
