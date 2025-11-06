<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pagamento Aprovado</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="checkout-container">
    <h2>🎉 Pagamento aprovado!</h2>
    <p>Seu pedido foi registrado com sucesso. Obrigado por comprar conosco!</p>
    <a href="loja.php" class="back-to-shop-btn">Voltar à loja</a>
</div>
<div id="preloader" class="preloader-overlay" style="display: none;">
    <div class="spinner-border"></div>
</div>
</body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // VARIÁVEIS ESSENCIAIS
    const preloader = document.getElementById('preloader');
    const confirmBtn = document.getElementById('confirm-payment-btn');

    // 1. FUNÇÃO PARA MOSTRAR O PRELOADER (AGORA DEFINIDA AQUI)
    function showPreloader() {
        if (preloader) {
            preloader.style.display = 'flex';
        }
    }
    // LISTENER 12: Ativar ao clicar no link de Voltar à Loja (loja.php)
    const backstoreBtn = document.querySelector('.back-to-shop-btn');
    if (backstoreBtn) {
        backstoreBtn.addEventListener('click', showPreloader);
    }

    // Se você tiver outros scripts de checkout em pagamento.php, 
    // certifique-se de que estejam neste mesmo bloco DOMContentLoaded.
    function hidePreloader() {
        if (preloader) {
            preloader.style.display = 'none';
        }
    }
    // 2. CORREÇÃO PARA O BOTÃO VOLTAR DO NAVEGADOR
    // O evento pageshow é disparado quando a página é carregada (incluindo BFCache)
    window.addEventListener('pageshow', function(event) {
        // Se a propriedade persisted for true, a página foi restaurada do cache.
        if (event.persisted) {
            hidePreloader();
        }
    });
    
    // Garante que o preloader esteja escondido por padrão ao carregar
    hidePreloader();

    
});
</script>
</html>
