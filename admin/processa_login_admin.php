<?php
// admin/processa_login_admin.php
session_start();
include 'db_connect.php';

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM administradores WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: list_products.php");
        exit;
    } else {
        echo "Nome de usuário ou senha inválidos.";
    }
} else {
    echo "Nome de usuário ou senha inválidos.";
}

$stmt->close();
$conn->close();
?>