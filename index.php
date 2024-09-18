<?php
session_start();
include 'admin/db_connect.php';

// Contagem de itens do carrinho
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantidade')) : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja Virtual</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <!-- Exemplo de card de produto -->
        <?php
        include 'admin/db_connect.php';
        $result = $conn->query("SELECT * FROM produtos");

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $nome = htmlspecialchars($row['nome']);
                $descricao = htmlspecialchars($row['descricao']);
                $preco = number_format($row['preco'], 2, ',', '.');
                $foto = htmlspecialchars($row['foto']);
                $id = $row['id'];
                ?>
                <div class="card" style="width: 18rem;">
                    <img src="admin/uploads/<?php echo $foto; ?>" class="card-img-top" alt="<?php echo $nome; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $nome; ?></h5>
                        <p class="card-text"><?php echo $descricao; ?></p>
                        <p class="card-text">R$ <?php echo $preco; ?></p>
                        <a href="produto.php?id=<?php echo $id; ?>" class="btn btn-primary">Ver Detalhes</a>
                        <a href="adicionar_carrinho.php?id=<?php echo $id; ?>" class="btn btn-success">Comprar</a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "Nenhum produto encontrado.";
        }

        $conn->close();
        ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
