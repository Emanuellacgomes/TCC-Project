<?php
session_start();
require_once "db_connection.php";
$status = "";

if (isset($_SESSION['user_codigo'])) {
    header("Location: loja.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $senha_digitada = $_POST['senha'];

    $sql = "SELECT codigo, nome, senha, tipo FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $status = "<div class='box' style='color:red;'>Erro na preparação da consulta: " . htmlspecialchars($conn->error) . "</div>";
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // CORREÇÃO: Comparação direta de senhas (sem hash)
            if ($senha_digitada === $user['senha']) {
                $_SESSION['user_codigo'] = $user['codigo'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_type'] = $user['tipo'];

                header("Location: loja.php");
                exit();
            } else {
                $status = "<div class='box' style='color:red;'>Senha incorreta.</div>";
            }
        } else {
            $status = "<div class='box' style='color:red;'>Usuário não encontrado.</div>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="icone.png" type="image/png">
    <title>Login</title>
</head>
<body>

<div class="login-container">
    <h2>Login de Usuário</h2>
    <?php if (isset($status) && $status != ""): ?>
        <div class="message-box box"><?php echo $status; ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['registration_success'])): ?>
        <div class="message-box success-message box"><?php echo $_SESSION['registration_success']; ?></div>
        <?php unset($_SESSION['registration_success']); ?>
    <?php endif; ?>

    <form action="login.php" method="POST" class="login-form"> 
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
        
        <button class="entrar-btn" type="submit" name="login">Entrar</button>
    </form>
    
    <p class="register-link">
        Não tem uma conta? <a href="cadastro.php" id="cadastro-link">Crie uma aqui</a>
    </p>
</div>

<div id="preloader" class="preloader-overlay" style="display: none;">
    <div class="spinner-border"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // VARIÁVEIS ESSENCIAIS
    const preloader = document.getElementById('preloader');
    
    // VARIÁVEIS DE AÇÃO
    const loginForm = document.querySelector('.login-form');
    const cadastroLink = document.getElementById('cadastro-link');

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

    // 3. LISTENER DE FORMULÁRIO (Login)
    if (loginForm) {
        // Usamos o 'submit' para capturar a submissão do formulário
        loginForm.addEventListener('submit', showPreloader);
    }
    
    // 4. LISTENER DE LINK (Ir para Cadastro)
    if (cadastroLink) {
        cadastroLink.addEventListener('click', showPreloader);
    }
});
</script>
</body>
</html>