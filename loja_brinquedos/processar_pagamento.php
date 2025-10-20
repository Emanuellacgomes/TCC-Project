<?php
session_start();
include "conexao.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['user_codigo'])) {
    die("Você precisa estar logado para finalizar a compra.");
}

$usuario_id = $_SESSION['user_codigo'];

// Dados de endereço vindos do pagamento.php
$cep       = isset($_POST['cep']) ? trim($_POST['cep']) : null;
$endereco  = isset($_POST['endereco']) ? trim($_POST['endereco']) : null;
$cidade    = isset($_POST['cidade']) ? trim($_POST['cidade']) : null;
$estado    = isset($_POST['estado']) ? trim($_POST['estado']) : null;
$latitude  = isset($_POST['latitude']) ? $_POST['latitude'] : (isset($_POST['map_latitude']) ? $_POST['map_latitude'] : null);
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : (isset($_POST['map_longitude']) ? $_POST['map_longitude'] : null);

// Buscar itens do carrinho
$sql = "SELECT c.codigo_brinquedo, c.quantidade, b.nome, b.preco 
        FROM carrinho_usuario c
        JOIN brinquedos b ON c.codigo_brinquedo = b.codigo
        WHERE c.id_usuario = " . intval($usuario_id);
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    die("Carrinho vazio!");
}

// Calcular total
$valor_total = 0;
$items = array();

while ($row = mysqli_fetch_assoc($res)) {
    $valor_total += $row['preco'] * $row['quantidade'];
    $items[] = $row;
}

// Aplicar desconto se o CEP for automático e válido
// Aprox. Linha 48: Aplicar desconto se o CEP for automático e válido
$desconto_percentual = 0;
$cep_origem = isset($_COOKIE['cep_origem']) ? $_COOKIE['cep_origem'] : 'manual';

// Pega o CEP do cookie se existir.
$cep_cookie = isset($_COOKIE['cep_usuario']) ? $_COOKIE['cep_usuario'] : null;

// Determina qual CEP usar para a busca de desconto.
// 1. Se o CEP veio do formulário (preenchimento manual), usa ele.
// 2. Se o CEP do formulário está vazio (caso do GPS) E a origem é 'auto', usa o CEP do cookie.
$cep_para_desconto = $cep;

if (empty($cep) && $cep_origem === 'auto' && !empty($cep_cookie)) {
    $cep_para_desconto = $cep_cookie;
}


if (!empty($cep_para_desconto) && $cep_origem === 'auto') {
    $cep_limpo = str_replace('-', '', $cep_para_desconto);

    $sql_desconto = "SELECT porcentagem_desconto 
                     FROM descontos_cep 
                     WHERE ? LIKE CONCAT(REPLACE(cep_prefixo, '-', ''), '%')";
    $stmt = mysqli_prepare($conn, $sql_desconto);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $cep_limpo);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $linha = mysqli_fetch_assoc($resultado);

        if ($linha && isset($linha['porcentagem_desconto'])) {
            $desconto_percentual = floatval($linha['porcentagem_desconto']);
        }
        mysqli_stmt_close($stmt);
    }
}

// Aplica o desconto apenas se for automático e válido
if ($desconto_percentual > 0) {
    $valor_total = $valor_total * (1 - ($desconto_percentual / 100));
}

// ... O restante do arquivo (salva na sessão, cria pedido, redireciona) continua inalterado.

// ✅ Salva o valor total atualizado na sessão
$_SESSION['total_carrinho'] = $valor_total;

// Criar pedido
$data = date("Y-m-d H:i:s");
$sql_pedido = "INSERT INTO pedidos (id_usuario, data_pedido, valor_total, status_pagamento) 
               VALUES (" . intval($usuario_id) . ", '" . mysqli_real_escape_string($conn, $data) . "', " . floatval($valor_total) . ", 'pendente')";
if (!mysqli_query($conn, $sql_pedido)) {
    die("Erro ao criar pedido: " . mysqli_error($conn));
}

$pedido_id = mysqli_insert_id($conn);

// Inserir itens
foreach ($items as $item) {
    $codigo_brinquedo = intval($item['codigo_brinquedo']);
    $quantidade = intval($item['quantidade']);
    $sql_item = "INSERT INTO itens_pedido (id_pedido, codigo_brinquedo, quantidade) 
                 VALUES ($pedido_id, $codigo_brinquedo, $quantidade)";
    if (!mysqli_query($conn, $sql_item)) {
        error_log("Erro inserindo item pedido: " . mysqli_error($conn));
    }
}

// Montar endereço final
if ($cep || $endereco || $cidade || $estado) {
    $endereco_final = "$endereco, $cidade - $estado, CEP: $cep";
} elseif ($latitude && $longitude) {
    $endereco_final = "Localização: $latitude, $longitude";
} else {
    $endereco_final = "Endereço não informado";
}

// Inserir entrega
$sql_entrega = "INSERT INTO entregas (id_pedido, endereco_destino, status) 
                VALUES ($pedido_id, '" . mysqli_real_escape_string($conn, $endereco_final) . "', 'aguardando_coleta')";
if (!mysqli_query($conn, $sql_entrega)) {
    error_log("Erro inserindo entrega: " . mysqli_error($conn));
}

// Limpar carrinho
mysqli_query($conn, "DELETE FROM carrinho_usuario WHERE id_usuario = " . intval($usuario_id));

// Redirecionar
header("Location: checkout.php?pedido_id=" . intval($pedido_id));
exit;
?>
