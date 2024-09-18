<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}

include 'db_connect.php'; // Conexão com o banco de dados

// Consulta para obter todos os pedidos e informações relacionadas
$query = "SELECT p.id, p.data_pedido, u.nome AS comprador, p.total, p.status 
          FROM pedidos p 
          JOIN usuarios u ON p.usuario_id = u.id 
          ORDER BY p.data_pedido DESC";
$result = $conn->query($query);

// Verificar status da mensagem
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Consulta para obter os detalhes dos produtos comprados
function getOrderItems($orderId, $conn) {
    $query = "SELECT p.nome AS produto, oi.quantidade, oi.preco
              FROM itens_pedido oi
              JOIN produtos p ON oi.produto_id = p.id
              WHERE oi.pedido_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Pedidos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="list_products.php">Admin</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <!-- Botão para listar produtos -->
                <li class="nav-item">
                    <a href="list_products.php" class="btn btn-info mr-2">Listar Produtos</a>
                </li>
                <!-- Botão para sair -->
                <li class="nav-item">
                    <a href="logout.php" class="btn btn-danger">Sair</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Consultar Pedidos</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Data do Pedido</th>
                    <th>Comprador</th>
                    <th>Detalhes</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($row['data_pedido'])); ?></td>
                            <td><?php echo $row['comprador']; ?></td>
                            <td>
                                <div>
                                    <?php
                                    $itemsResult = getOrderItems($row['id'], $conn);
                                    if ($itemsResult->num_rows > 0):
                                        while($item = $itemsResult->fetch_assoc()):
                                            echo "<div>{$item['produto']} - R$ " . number_format($item['preco'], 2, ',', '.') . " x {$item['quantidade']}</div>";
                                        endwhile;
                                    else:
                                        echo "Nenhum item encontrado.";
                                    endif;
                                    ?>
                                    <div class="font-weight-bold mt-2">Total: R$ <?php echo number_format($row['total'], 2, ',', '.'); ?></div>
                                </div>
                            </td>
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <!-- Botão para abrir o modal de confirmação de exclusão -->
                                <button class="btn btn-danger" data-toggle="modal" data-target="#Modal<?php echo $row['id']; ?>">Excluir</button>
                            </td>
                        </tr>

                        <!-- Modal de confirmação para excluir pedido -->
                        <div class="modal fade" id="Modal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="ModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="ModalLabel<?php echo $row['id']; ?>">Confirmar Exclusão</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Tem certeza que deseja excluir o pedido #<?php echo $row['id']; ?>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <a href="delete_order.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Excluir</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Nenhum pedido encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de sucesso -->
    <?php if ($status === 'success'): ?>
        <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="successModalLabel">Sucesso</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-3x text-success mr-3"></i>
                            <div>
                                Pedido excluído com sucesso!
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Mostrar o modal de sucesso se o status for success -->
    <?php if ($status === 'success'): ?>
        <script>
            $(document).ready(function() {
                $('#successModal').modal('show');
            });
        </script>
    <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?>