<?php
$host = "127.0.0.1"; 
$user = "root";        // padrão do EasyPHP
$pass = "";            // senha padrão do EasyPHP (normalmente vazio)
$db   = "brinquedos_loja";  // nome do seu banco importado

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Erro na conexão com o banco: " . mysqli_connect_error());
}
?>
