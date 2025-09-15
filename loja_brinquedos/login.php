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

    <form action="login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
        
        <button type="submit" name="login">Entrar</button>
    </form>
    
    <p class="register-link">
        Não tem uma conta? <a href="cadastro.php">Crie uma aqui</a>
    </p>
</div>

</body>
</html>