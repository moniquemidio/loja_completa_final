<?php
session_start();
include 'admin/db_connect.php';

// Contagem de itens do carrinho
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantidade')) : 0;

// Verificar se o carrinho está vazio
$is_cart_empty = empty($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container mt-4">
    <h2>Carrinho de Compras</h2>

    <?php if ($is_cart_empty): ?>
        <p>Seu carrinho está vazio.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Total</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $id => $item):
                    $item_total = $item['preco'] * $item['quantidade'];
                    $total += $item_total;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nome']); ?></td>
                        <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($item['quantidade']); ?></td>
                        <td>R$ <?php echo number_format($item_total, 2, ',', '.'); ?></td>
                        <td>
                            <a href="remover_carrinho.php?id=<?php echo $id; ?>" class="btn btn-danger btn-sm">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong>R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <a href="checkout.php" class="btn btn-success">Finalizar Compra</a>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>