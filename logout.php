<?php
session_start();
session_unset();
session_destroy();

// Redirecionar para a página inicial ou onde o usuário estava
header('Location: index.php');
exit();
?>
