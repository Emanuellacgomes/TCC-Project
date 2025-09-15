<?php
session_start();
require_once "db_connection.php";

// Apenas o gerente pode acessar esta página
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'gerente') {
    header("Location: loja.php");
    exit();
}

// Lógica para gerenciar usuários será adicionada aqui.
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Painel de Gerenciamento de Usuários</h2>
    <p>Funcionalidade de gerenciamento de usuários virá aqui.</p>
</body>
</html>