<?php
// notification.php
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
} else {
    ini_set('session.use_trans_sid', 1);
    ini_set('session.use_only_cookies', 0);
    session_start();
    $BASE_URL = '';
}

require_once "conexao.php";

// Mercado Pago envia dados em JSON
$body = @file_get_contents("php://input");
$data = json_decode($body, true);

// apenas loga o que chegou (ajuda debug)
file_put_contents("notificacoes.log", date("Y-m-d H:i:s") . " - " . $body . "\n", FILE_APPEND);

// se for pagamento aprovado, podemos atualizar o pedido
if ($data && isset($data["type"]) && $data["type"] === "payment") {
    // aqui você poderia chamar a API de pagamento do Mercado Pago
    // para obter os detalhes e atualizar o status do pedido no seu banco
    // exemplo:
    // $payment_id = $data["data"]["id"];
    // atualizar status pedido_id -> "pago"
}

http_response_code(200);
echo "OK";
