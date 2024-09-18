<?php
// Dados de configuração do PagSeguro
$email = 'moniquemidio@gmail.com'; // E-mail fornecido no sandbox
$token = 'f10386ae-16c2-4164-b666-c6476a9cd568448dda8d437a88b798ddb6df402c449cf0d1-ff97-4e61-a89f-4c7986725be5'; // Token fornecido no sandbox

// Configurar a URL do sandbox
$url = 'https://sandbox.api.pagseguro.com/v2/checkout?email=' . $email . '&token=' . $token;

// Dados do checkout
$produto_id = $_GET['produto_id'];
$produto_nome = $_GET['produto_nome'];
$produto_preco = $_GET['produto_preco'];
$quantidade = $_GET['quantidade'];

// Dados do checkout em XML
$xml = '<?xml version="1.0" encoding="UTF-8"?>
<checkout>
    <items>
        <item>
            <id>' . htmlspecialchars($produto_id) . '</id>
            <description>' . htmlspecialchars($produto_nome) . '</description>
            <amount>' . $produto_preco . '</amount>
            <quantity>' . $quantidade . '</quantity>
        </item>
    </items>
</checkout>';

// Enviar a requisição para criar o checkout
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/xml',
));
$response = curl_exec($ch);
curl_close($ch);

// Processar a resposta
$responseXml = simplexml_load_string($response);
if ($responseXml === false) {
    die('Erro ao processar a resposta do PagSeguro.');
}

$code = isset($responseXml->code) ? (string)$responseXml->code : null;
if ($code) {
    $url_pagamento = 'https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=' . $code;
    header('Location: ' . $url_pagamento);
    exit();
} else {
    die('Erro ao criar o checkout. Verifique a resposta do PagSeguro.');
}
?>
