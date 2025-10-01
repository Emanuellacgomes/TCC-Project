<?php
session_start();
include "conexao.php";

// Usuário logado (exemplo fixo, depois troque pela sessão real)
$usuario_id = 3;

// Dados de endereço vindos do pagamento.php
$cep       = isset($_POST['cep']) ? $_POST['cep'] : null;
$endereco  = isset($_POST['endereco']) ? $_POST['endereco'] : null;
$cidade    = isset($_POST['cidade']) ? $_POST['cidade'] : null;
$estado    = isset($_POST['estado']) ? $_POST['estado'] : null;
$latitude  = isset($_POST['latitude']) ? $_POST['latitude'] : (isset($_POST['map_latitude']) ? $_POST['map_latitude'] : null);
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : (isset($_POST['map_longitude']) ? $_POST['map_longitude'] : null);

// Buscar itens do carrinho do usuário
$sql = "SELECT c.codigo_brinquedo, c.quantidade, b.nome, b.preco 
        FROM carrinho_usuario c
        JOIN brinquedos b ON c.codigo_brinquedo = b.codigo
        WHERE c.id_usuario = $usuario_id";
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    die("Carrinho vazio!");
}

// Calcular valor total e armazenar os itens
$valor_total = 0;
$items = array();
while ($row = mysqli_fetch_assoc($res)) {
    $valor_total += $row['preco'] * $row['quantidade'];
    $items[] = $row;
}

// Criar o pedido com status "pendente"
$data = date("Y-m-d H:i:s");
$sql_pedido = "INSERT INTO pedidos (id_usuario, data_pedido, valor_total, status_pagamento) 
               VALUES ($usuario_id, '$data', $valor_total, 'pendente')";
if (!mysqli_query($conn, $sql_pedido)) {
    die("Erro ao criar pedido: " . mysqli_error($conn));
}
$pedido_id = mysqli_insert_id($conn);

// Inserir itens do pedido
foreach ($items as $item) {
    $codigo_brinquedo = $item['codigo_brinquedo'];
    $quantidade = $item['quantidade'];
    $sql_item = "INSERT INTO itens_pedido (id_pedido, codigo_brinquedo, quantidade) 
                 VALUES ($pedido_id, $codigo_brinquedo, $quantidade)";
    if (!mysqli_query($conn, $sql_item)) {
        // não interrompe o fluxo, só coloca no log (mas você pode tratar melhor)
        error_log("Erro inserindo item pedido: " . mysqli_error($conn));
    }
}

// Montar endereço final
$endereco_final = "";
if ($cep || $endereco || $cidade || $estado) {
    $endereco_final = "$endereco, $cidade - $estado, CEP: $cep";
} elseif ($latitude && $longitude) {
    $endereco_final = "Localização: $latitude, $longitude";
} else {
    $endereco_final = "Endereço não informado";
}

// Inserir na tabela de entregas (crie a tabela entregas se necessário)
$sql_entrega = "INSERT INTO entregas (id_pedido, endereco_destino, status) 
                VALUES ($pedido_id, '" . mysqli_real_escape_string($conn, $endereco_final) . "', 'aguardando_coleta')";
if (!mysqli_query($conn, $sql_entrega)) {
    error_log("Erro inserindo entrega: " . mysqli_error($conn));
}

// Limpar o carrinho do usuário
mysqli_query($conn, "DELETE FROM carrinho_usuario WHERE id_usuario = $usuario_id");

// ✅ Redirecionar para o checkout.php (pedido criado com status pendente)
header("Location: checkout.php?pedido_id=$pedido_id");
exit;
?>
