<?php
session_start();
require_once "db_connection.php";

$status = "";
$pedido_id = null;
$total_price_posted = 0;
$user_codigo = isset($_SESSION['user_codigo']) ? $_SESSION['user_codigo'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_codigo) {
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
    $total_price_posted = floatval(str_replace(',', '.', $_POST['total_price']));

    $is_payment_approved = false;

    // Simulação da lógica de pagamento
    if ($payment_method === 'credit_card') {
        $numero_cartao = $_POST['numero_cartao'];
        $nome_cartao = $_POST['nome_cartao'];
        $cvv = $_POST['cvv'];

        if (
            strpos($numero_cartao, '4000') === 0 &&
            strpos($nome_cartao, 'APROVADO') !== false &&
            $cvv === '123'
        ) {
            $is_payment_approved = true;
        }
    } elseif ($payment_method === 'pix' || $payment_method === 'paypal') {
        $is_payment_approved = true;
    }

    if ($is_payment_approved) {
        $conn->query("START TRANSACTION");
        $transaction_success = true;
        
        // 1. Inserir um novo pedido na tabela de pedidos
        $sql_pedido = "INSERT INTO pedidos (id_usuario, data_pedido, valor_total) VALUES (?, NOW(), ?)";
        $stmt_pedido = $conn->prepare($sql_pedido);
        if ($stmt_pedido) {
            $stmt_pedido->bind_param("id", $user_codigo, $total_price_posted);
            if (!$stmt_pedido->execute()) {
                $status = "Erro ao executar a inserção do pedido: " . $stmt_pedido->error;
                $transaction_success = false;
            }
            $pedido_id = $stmt_pedido->insert_id;
            $stmt_pedido->close();
        } else {
            $status = "Erro ao preparar a inserção do pedido: " . $conn->error;
            $transaction_success = false;
        }

        if ($transaction_success && $pedido_id) {
            // 2. Mover itens do carrinho para a tabela de detalhes do pedido e dar baixa no estoque
            $sql_cart_items = "SELECT codigo_brinquedo, quantidade FROM carrinho_usuario WHERE id_usuario = ?";
            $stmt_cart_items = $conn->prepare($sql_cart_items);

            if (!$stmt_cart_items) {
                $status = "Erro ao preparar a busca por itens do carrinho: " . $conn->error;
                $transaction_success = false;
            } else {
                $stmt_cart_items->bind_param("i", $user_codigo);
                if (!$stmt_cart_items->execute()) {
                    $status = "Erro ao executar a busca por itens do carrinho: " . $stmt_cart_items->error;
                    $transaction_success = false;
                }
                $result_cart_items = $stmt_cart_items->get_result();

                // CORRIGIDO: Removido preco_unitario da consulta, pois a tabela não possui essa coluna
                $sql_insert_item = "INSERT INTO itens_pedido (id_pedido, codigo_brinquedo, quantidade) VALUES (?, ?, ?)";
                $stmt_insert_item = $conn->prepare($sql_insert_item);
                
                $sql_update_stock = "UPDATE brinquedos SET estoque = estoque - ? WHERE codigo = ?";
                $stmt_update_stock = $conn->prepare($sql_update_stock);

                if (!$stmt_insert_item) {
                     $status = "Erro ao preparar a query de inserção de itens: " . $conn->error;
                     $transaction_success = false;
                } elseif (!$stmt_update_stock) {
                     $status = "Erro ao preparar a query de baixa de estoque: " . $conn->error;
                     $transaction_success = false;
                } else {
                    while ($item = $result_cart_items->fetch_assoc()) {
                        $codigo_brinquedo = $item['codigo_brinquedo'];
                        $quantidade_comprada = $item['quantidade'];
                        
                        // CORRIGIDO: bind_param agora tem 3 parâmetros, para id_pedido, codigo_brinquedo e quantidade
                        $stmt_insert_item->bind_param("iii", $pedido_id, $codigo_brinquedo, $quantidade_comprada);
                        if (!$stmt_insert_item->execute()) {
                            $status = "Erro ao executar a inserção do item do pedido: " . $stmt_insert_item->error;
                            $transaction_success = false;
                            break;
                        }
                        
                        $stmt_update_stock->bind_param("ii", $quantidade_comprada, $codigo_brinquedo);
                        if (!$stmt_update_stock->execute()) {
                            $status = "Erro ao executar a baixa no estoque: " . $stmt_update_stock->error;
                            $transaction_success = false;
                            break;
                        }
                    }
                }

                $stmt_cart_items->close();
                if ($stmt_insert_item) $stmt_insert_item->close();
                if ($stmt_update_stock) $stmt_update_stock->close();
            }

            // 3. Limpar o carrinho do usuário, somente se a transação foi bem-sucedida até aqui
            if ($transaction_success) {
                $sql_clear_cart = "DELETE FROM carrinho_usuario WHERE id_usuario = ?";
                $stmt_clear = $conn->prepare($sql_clear_cart);
                if ($stmt_clear) {
                    $stmt_clear->bind_param("i", $user_codigo);
                    if (!$stmt_clear->execute()) {
                        $status = "Erro ao executar a limpeza do carrinho: " . $stmt_clear->error;
                        $transaction_success = false;
                    }
                    $stmt_clear->close();
                } else {
                    $status = "Erro ao preparar a limpeza do carrinho: " . $conn->error;
                    $transaction_success = false;
                }
            } else {
                $status = $status; // mantém o erro específico já capturado
            }

        } else {
            if (empty($status)) {
                $status = "Erro: O pedido não pôde ser criado.";
            }
        }

        if ($transaction_success) {
            $conn->query("COMMIT");
            $status = "<div class='box success-message'>Pagamento APROVADO! Redirecionando para a loja.</div>";
        } else {
            $conn->query("ROLLBACK");
            if (empty($status)) {
                $status = "<div class='box error-message'>Erro no processamento do pedido. Por favor, tente novamente mais tarde.</div>";
            } else {
                 $status = "<div class='box error-message'>" . $status . "</div>";
            }
        }

    } else {
        $status = "<div class='box error-message'>Pagamento RECUSADO. Por favor, verifique os dados ou tente novamente.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Resultado do Pagamento</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="icone.png" type="image/png">
</head>
<body>

<div class="header">
    <img src="icone.png" alt="Logo da Playtopia" class="logo-loja">
    <a href="loja.php" class="back-to-shop-btn">Ir para a Loja</a>
</div>

<div class="cart-container">
    <h2>Resultado do Pagamento</h2>
    <?php echo $status; ?>
</div>

</body>
</html>