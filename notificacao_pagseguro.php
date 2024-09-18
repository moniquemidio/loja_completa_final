<?php
include 'admin/db_connect.php';

$email = 'moniquemidio@gmail.com'; // E-mail fornecido no sandbox
$token = 'f10386ae-16c2-4164-b666-c6476a9cd568448dda8d437a88b798ddb6df402c449cf0d1-ff97-4e61-a89f-4c7986725be5'; // Token fornecido no sandbox

// Receber o código de notificação do PagSeguro
if (isset($_POST['notificationCode']) && isset($_POST['notificationType']) && $_POST['notificationType'] == 'transaction') {
    $notificationCode = preg_replace('/[^a-zA-Z0-9]/', '', $_POST['notificationCode']); // Limpeza de dados

    // Montar a URL para consultar o status da transação
    $url = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/' . $notificationCode . '?email=' . $email . '&token=' . $token;

    // Fazer a requisição à API do PagSeguro
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Processar a resposta
    if ($response) {
        $xml = simplexml_load_string($response);

        // Verificar se o pagamento foi aprovado
        if ($xml->status == 3) { // Status 3 significa "paga"
            $reference = $xml->reference; // Referência do pedido

            // Atualizar o status do pedido no banco de dados
            $stmt = $conn->prepare("UPDATE pedidos SET status = 'pago' WHERE id = ?");
            $stmt->bind_param("i", $reference);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>
