<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cep'])) {
    $_SESSION['cep_usuario'] = $_POST['cep'];
}

header("Location: loja.php");
exit();
?>