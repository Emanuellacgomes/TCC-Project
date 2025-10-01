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
    <a href="loja.php">
        <img src="logo.png" alt="Logo da Loja de Brinquedos" class="logo-loja-adm">
    </a>
    <div class="user-actions">
        <span class="welcome-message">Olá, Gerente!</span>
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

</body>
</html>