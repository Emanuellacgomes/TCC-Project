<?php
// create_preference.php
include "conexao.php";

$access_token = "APP_USR-5880562592274061-100112-8ecca3eaff0ff2aff31c493116724eb8-2725312304"; // seu Access Token de TESTE ou PRODUÇÃO

$pedido_id = intval($_GET['pedido_id']);

// Buscar pedido
$sql = "SELECT * FROM pedidos WHERE id_pedido=$pedido_id";
$res = mysqli_query($conn, $sql);
if (!$res) {
    die("Erro ao buscar pedido: " . mysqli_error($conn));
}
$pedido = mysqli_fetch_assoc($res);

// Buscar itens do pedido
$sql_itens = "SELECT i.*, b.nome, b.preco 
              FROM itens_pedido i 
              JOIN brinquedos b ON i.codigo_brinquedo=b.codigo
              WHERE i.id_pedido=$pedido_id";
$res_itens = mysqli_query($conn, $sql_itens);

$items = array();
while ($row = mysqli_fetch_assoc($res_itens)) {
    $items[] = array(
        "id" => $row["codigo_brinquedo"],
        "title" => $row["nome"],
        "quantity" => intval($row["quantidade"]),
        "unit_price" => floatval($row["preco"]),
        "currency_id" => "BRL"
    );
}

// Montar preferência
$preference = array(
    "items" => $items,
    "external_reference" => $pedido_id,
    "back_urls" => array(
        "success" => "https://dudley-mudfat-anastasia.ngrok-free.dev/loja_brinquedos/retorno_sucesso.php",
        "failure" => "https://dudley-mudfat-anastasia.ngrok-free.dev/loja_brinquedos/retorno_erro.php",
        "pending" => "https://dudley-mudfat-anastasia.ngrok-free.dev/loja_brinquedos/retorno_pendente.php"
    ),
    "auto_return" => "approved", // redireciona automático ao sucesso
    "notification_url" => "https://dudley-mudfat-anastasia.ngrok-free.dev/loja_brinquedos/notification.php",
    "payment_methods" => array(
        "excluded_payment_types" => array(
            array("id" => "ticket") // exclui boleto
        ),
        "installments" => 12
    )
);

// Enviar para API Mercado Pago
$ch = curl_init("https://api.mercadopago.com/checkout/preferences");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "Authorization: Bearer " . $access_token
));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($preference));
$response = curl_exec($ch);

if ($response === false) {
    die("Erro cURL: " . curl_error($ch));
}
curl_close($ch);

$data = json_decode($response, true);

// Redireciona para o checkout
if (isset($data["init_point"])) {
    header("Location: " . $data["init_point"]);
    exit;
} else {
    echo "<h3>Erro ao criar preferência</h3><pre>";
    print_r($data);
    echo "</pre>";
}
