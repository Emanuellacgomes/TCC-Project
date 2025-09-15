<?php
session_start();
require_once "db_connection.php";
$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($senha !== $confirmar_senha) {
        $status = "<div class='box' style='color:red;'>As senhas não coincidem.</div>";
    } else {
        // CORREÇÃO: Armazenando a senha em texto puro (sem hash)
        $senha_hash = $senha;

        $sql_check = "SELECT codigo FROM usuario WHERE email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $status = "<div class='box' style='color:red;'>Este email já está cadastrado.</div>";
        } else {
            $sql_insert = "INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql_insert);
            $stmt->bind_param("sss", $nome, $email, $senha_hash);

            if ($stmt->execute()) {
                $_SESSION['registration_success'] = "Cadastro realizado com sucesso! Faça login para continuar.";
                header("Location: login.php");
                exit();
            } else {
                $status = "<div class='box' style='color:red;'>Erro ao cadastrar usuário.</div>";
            }
        }
        $stmt_check->close();
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
    <title>Cadastro</title>
</head>
<body>
<div class="login-container">
    <h2>Criar Nova Conta</h2>
    <?php if (isset($status) && $status != ""): ?>
        <div class="message-box box"><?php echo $status; ?></div>
    <?php endif; ?>
    <form action="cadastro.php" method="POST">
        <label for="nome">Nome Completo:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
        
        <label for="confirmar_senha">Confirmar Senha:</label>
        <input type="password" id="confirmar_senha" name="confirmar_senha" required>

        <button type="submit">Cadastrar</button>
    </form>
    <p class="register-link">
        Já tem uma conta? <a href="login.php">Fazer Login</a>
    </p>
</div>
</body>
</html>