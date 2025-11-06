<?php
session_start();
require_once "db_connection.php";

// Verifica se o usuário é um gerente, caso contrário, redireciona
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'gerente') {
    header("Location: loja.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="icone.png" type="image/png">
    <title>Painel de Administração</title>
</head>
<body>

<div class="header">
    <a href="loja.php" id="logo-link"> <img src="logo.png" alt="Logo da Loja de Brinquedos" class="logo-loja-adm">
    </a>
    <div class="user-actions">
        <span class="welcome-message">Olá, Fábio da Costa!</span>
        <a href="loja.php" class="home-btn">Voltar para a Loja</a>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>
</div>

<div class="admin-container">
    <h2>Painel de Administração</h2>
    <p>Bem-vindo ao painel de administração. Escolha uma opção abaixo para gerenciar a loja.</p>

    <div class="admin-menu">
        <a href="gerenciar_brinquedos.php" class="admin-menu-item">
            Gerenciar Brinquedos
        </a>
        <a href="gerenciar_fabricantes.php" class="admin-menu-item">
            Gerenciar Fabricantes
        </a>
        <a href="gerenciar_categorias.php" class="admin-menu-item">
            Gerenciar Categorias
        </a>
    </div>
</div>
<div id="preloader" class="preloader-overlay" style="display: none;">
    <div class="spinner-border"></div>
</div>
</body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // VARIÁVEIS ESSENCIAIS
    const preloader = document.getElementById('preloader');
    
    // VARIÁVEIS DE AÇÃO
    const logoLink = document.getElementById('logo-link'); // Link da Logo
    const homeBtn = document.querySelector('.home-btn'); // Voltar para a Loja
    const logoutBtn = document.querySelector('.logout-btn'); // Sair
    const adminMenuLinks = document.querySelectorAll('.admin-menu-item'); // Links do Painel

    // 1. FUNÇÕES DO PRELOADER
    function showPreloader() {
        if (preloader) {
            preloader.style.display = 'flex';
        }
    }
    
    function hidePreloader() {
        if (preloader) {
            preloader.style.display = 'none';
        }
    }

    // 2. CORREÇÃO PARA O BOTÃO VOLTAR DO NAVEGADOR (BFCache)
    window.addEventListener('pageshow', function(event) {
        // Se a página foi restaurada do cache (botão Voltar), esconde o preloader
        if (event.persisted) {
            hidePreloader();
        }
    });
    
    // Garante que o preloader comece escondido
    hidePreloader(); 

    // --- LISTENERS DE AÇÃO ---

    // 3. LISTENERS DE LINKS DO MENU (Gerenciar Brinquedos, Fabricantes, Categorias)
    adminMenuLinks.forEach(link => {
        link.addEventListener('click', showPreloader);
    });

    // 4. LISTENERS DE LINKS DO HEADER (Redirecionamento)
    if (logoLink) {
        logoLink.addEventListener('click', showPreloader);
    }
    if (homeBtn) {
        homeBtn.addEventListener('click', showPreloader);
    }
    if (logoutBtn) {
        logoutBtn.addEventListener('click', showPreloader);
    }
});
</script>
</html>