<?php
session_start();
include "conexao.php";

if (!isset($_GET['pedido_id'])) {
    die("Pedido não informado.");
}

$pedido_id = intval($_GET['pedido_id']);

// Buscar pedido
$sql = "SELECT * FROM pedidos WHERE id_pedido=$pedido_id";
$res = mysqli_query($conn, $sql);
if (!$res || mysqli_num_rows($res) == 0) {
    die("Pedido não encontrado.");
}
$pedido = mysqli_fetch_assoc($res);

// Buscar itens do pedido (Esta busca é mantida apenas para fins de debug e histórico)
$sql_itens = "SELECT i.*, b.nome, b.preco 
              FROM itens_pedido i 
              JOIN brinquedos b ON i.codigo_brinquedo=b.codigo
              WHERE i.id_pedido=$pedido_id";
$res_itens = mysqli_query($conn, $sql_itens);

// *** CORREÇÃO CRUCIAL: ENVIAR O VALOR FINAL (COM DESCONTO) COMO UM ITEM ÚNICO ***
// O valor $pedido['valor_total'] lido do banco JÁ CONTÉM O DESCONTO.

$items = array(
    array(
        "id" => "PEDIDO-" . $pedido_id, // Identificador do pedido
        "title" => "Compra Playtopia - Pedido #".$pedido_id,
        "quantity" => 1,
        "unit_price" => floatval($pedido['valor_total']), // <<-- VALOR COM DESCONTO
        "currency_id" => "BRL"
    )
);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Redirecionando para Pagamento</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="checkout-container">
    <h2>Aguarde, estamos redirecionando para o Mercado Pago...</h2>
    <p>Valor final (com desconto): R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></p>
</div>

<script>
(async () => {
    const preferenceData = {
        items: <?php echo json_encode($items); ?>,
        external_reference: "<?php echo $pedido_id; ?>",
        back_urls: {
            success: "https://dudley-mudfat-anastasia.ngrok-free.dev/loja_brinquedos/retorno_processar.php",
            failure: "https://dudley-mudfat-anastasia.ngrok-free.dev/loja_brinquedos/retorno_processar.php",
            pending: "https://dudley-mudfat-anastasia.ngrok-free.dev/loja_brinquedos/retorno_processar.php"
        },
        auto_return: "approved",
        notification_url: "https://dudley-mudfat-anastasia.ngrok-free.dev/loja_brinquedos/notification.php",
        payment_methods: {
            excluded_payment_types: [{ id: "ticket" }], // exclui boleto
            installments: 12
        }
    };

    try {
        const response = await fetch("https://api.mercadopago.com/checkout/preferences", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer APP_USR-5880562592274061-100112-8ecca3eaff0ff2aff31c493116724eb8-2725312304"
            },
            body: JSON.stringify(preferenceData)
        });

        const data = await response.json();
        console.log("Resposta da API:", data);

        if (data.init_point) {
            window.location.href = data.init_point;
        } else {
            document.body.innerHTML += "<pre>Erro ao criar preferência:<br>" + JSON.stringify(data, null, 2) + "</pre>";
        }
    } catch (error) {
        console.error("Erro:", error);
        document.body.innerHTML += "<p>Erro ao conectar com o Mercado Pago.</p>";
    }
})();
</script>
</body>
</html>