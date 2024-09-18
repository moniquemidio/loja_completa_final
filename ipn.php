<?php
// Conectar ao banco de dados
include 'admin/db_connect.php';

// Lógica de IPN

// Ler os dados do IPN vindos do PayPal
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
    $keyval = explode('=', $keyval);
    if (count($keyval) == 2) {
        $myPost[$keyval[0]] = urldecode($keyval[1]);
    }
}

// Adicionar "cmd=_notify-validate" ao início dos dados
$req = 'cmd=_notify-validate';
foreach ($myPost as $key => $value) {
    $value = urlencode($value);
    $req .= "&$key=$value";
}

// Enviar os dados de volta ao PayPal para validação
$paypal_url = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'; // Use o link sandbox para testes
$ch = curl_init($paypal_url);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
$res = curl_exec($ch);
curl_close($ch);

// Verifique se o PayPal confirmou o IPN
if (strcmp($res, "VERIFIED") == 0) {
    // O IPN foi validado pelo PayPal
    $payment_status = $_POST['payment_status'];
    $txn_id = $_POST['txn_id'];
    $mc_gross = $_POST['mc_gross'];
    $payer_email = $_POST['payer_email'];
    $item_name = $_POST['item_name'];
    $custom = $_POST['custom']; // Pode ser usado para passar ID do pedido, por exemplo

    // Verifique se o pagamento foi concluído
    if ($payment_status == 'Completed') {
        // Atualizar o banco de dados com as informações de pagamento
        $pedido_id = intval($custom); // Supondo que você passe o ID do pedido no campo "custom"
        
        $stmt = $conn->prepare("UPDATE pedidos SET status = 'pago' WHERE id = ?");
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $stmt->close();
        
        // Você pode salvar as informações do IPN para auditoria, se necessário
        $stmt = $conn->prepare("INSERT INTO ipn_logs (txn_id, payer_email, mc_gross, item_name, payment_status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $txn_id, $payer_email, $mc_gross, $item_name, $payment_status);
        $stmt->execute();
        $stmt->close();
    }
} else if (strcmp($res, "INVALID") == 0) {
    echo "deu xabu"; // O IPN não foi verificado - você pode registrar o erro
}
?>