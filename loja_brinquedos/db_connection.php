<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brinquedos_loja";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Define a codificação para UTF-8
$conn->set_charset("utf8");

// Checa a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>