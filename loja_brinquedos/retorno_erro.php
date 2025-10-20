<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Erro no Pagamento</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="checkout-container">
    <h2>⚠️ O pagamento falhou / O pagamento foi cancelado</h2>
    <p>Infelizmente não conseguimos processar o seu pagamento. Tente novamente.</p>
    <a href="pagamento.php" class="back-to-shop-btn">Tentar novamente</a>
    <a href="loja.php" class="back-to-shop-btn">Voltar para Loja</a>
</div>
</body>
</html>
