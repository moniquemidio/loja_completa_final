<?php
session_start();
include 'admin/db_connect.php';

// Verificar se o PayerID está presente na URL
$payerID = isset($_GET['PayerID']) ? $_GET['PayerID'] : null;

// Verificar se o pedido_id está disponível na sessão
$pedido_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

// Inicializa a variável de pagamento confirmado como falso
$pagamento_confirmado = false;

// Se o PayerID existir na URL, o pagamento será considerado confirmado
if ($payerID) {
    $pagamento_confirmado = true;

    // Verificar se o carrinho contém itens
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Inserir pedido no banco de dados
        $usuario_id = $_SESSION['usuario_id']; // Certifique-se de que o usuário está logado
        $total = array_sum(array_map(function($item) {
            return $item['preco'] * $item['quantidade'];
        }, $_SESSION['cart']));

        // Inserir o pedido na tabela `pedidos`
        $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total, status, data_pedido) VALUES (?, ?, 'pago', NOW())");
        $stmt->bind_param("id", $usuario_id, $total);
        $stmt->execute();
        $pedido_id = $conn->insert_id; // Obtém o ID do pedido recém-criado
        $stmt->close();

        // Inserir itens do pedido na tabela `pedido_itens`
        foreach ($_SESSION['cart'] as $produto_id => $item) {
            $stmt = $conn->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $pedido_id, $produto_id, $item['quantidade'], $item['preco']);
            $stmt->execute();
            $stmt->close();
        }

        // Esvaziar o carrinho após a confirmação do pedido
        unset($_SESSION['cart']);
    }
}

// Limpar as variáveis de sessão relacionadas ao pedido
unset($_SESSION['id']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Pagamento</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
        <h2>Confirmação de Pagamento</h2>

        <?php if ($pagamento_confirmado): ?>
            <p>Seu pedido com ID <?php echo htmlspecialchars($pedido_id); ?> foi confirmado com sucesso.</p>
        <?php else: ?>
            <p>Seu pagamento ainda não foi confirmado. Por favor, aguarde.</p>
        <?php endif; ?>
    </div>
</body>
</html>