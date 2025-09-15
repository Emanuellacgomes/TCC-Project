<?php
session_start();
require_once "db_connection.php";

$status = "";

// Redireciona para o login se o usuário não estiver logado e o carrinho estiver vazio
if (!isset($_SESSION['user_codigo']) && (!isset($_SESSION['shopping_cart']) || empty($_SESSION['shopping_cart']))) {
    header("Location: login.php");
    exit();
}

// Lógica de remoção de item do carrinho
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'remove_item') {
    $codigo_brinquedo = $_POST['codigo_remove'];

    if (isset($_SESSION['user_codigo'])) {
        $user_codigo = $_SESSION['user_codigo'];
        $sql_delete = "DELETE FROM carrinho_usuario WHERE id_usuario = ? AND codigo_brinquedo = ?";
        $stmt = $conn->prepare($sql_delete);
        $stmt->bind_param("ii", $user_codigo, $codigo_brinquedo);
        $stmt->execute();
        $stmt->close();
    } else {
        // Assegura que $_SESSION['shopping_cart'] é um array antes de tentar remover um item
        if (isset($_SESSION['shopping_cart']) && isset($_SESSION['shopping_cart'][$codigo_brinquedo])) {
            unset($_SESSION['shopping_cart'][$codigo_brinquedo]);
        }
    }
    $status = "<div class='box'>Item removido do carrinho.</div>";
}

// Lógica de atualização de quantidade
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update_quantity') {
    $codigo_brinquedo = $_POST['codigo_update'];
    $quantidade = intval($_POST['quantity']);

    // Verifica se a quantidade é maior que zero e se o produto existe
    if ($quantidade > 0) {
        if (isset($_SESSION['user_codigo'])) {
            $user_codigo = $_SESSION['user_codigo'];
            $sql_update = "UPDATE carrinho_usuario SET quantidade = ? WHERE id_usuario = ? AND codigo_brinquedo = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("iii", $quantidade, $user_codigo, $codigo_brinquedo);
            $stmt->execute();
            $stmt->close();
        } else {
            // Assegura que $_SESSION['shopping_cart'] é um array antes de tentar atualizar
            if (isset($_SESSION['shopping_cart']) && isset($_SESSION['shopping_cart'][$codigo_brinquedo])) {
                $_SESSION['shopping_cart'][$codigo_brinquedo]['quantity'] = $quantidade;
            }
        }
    } else {
        $status = "<div class='box' style='color:red;'>Quantidade deve ser maior que zero.</div>";
    }
}

// Pega os itens do carrinho do banco de dados ou da sessão
$shopping_cart = array();
if (isset($_SESSION['user_codigo'])) {
    $user_codigo = $_SESSION['user_codigo'];
    $sql_cart = "
        SELECT b.codigo, b.nome, b.preco, b.imagem, cu.quantidade 
        FROM carrinho_usuario cu 
        JOIN brinquedos b ON cu.codigo_brinquedo = b.codigo 
        WHERE cu.id_usuario = ?
    ";
    $stmt = $conn->prepare($sql_cart);
    $stmt->bind_param("i", $user_codigo);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $shopping_cart[$row['codigo']] = $row;
    }
    $stmt->close();
} else {
    $shopping_cart = isset($_SESSION["shopping_cart"]) && is_array($_SESSION["shopping_cart"]) ? $_SESSION["shopping_cart"] : array();
}

$total_price = 0;
foreach ($shopping_cart as $item) {
    $total_price += ($item["preco"] * $item["quantidade"]);
}

// Armazena o valor total na sessão para ser acessado na próxima página
$_SESSION['total_carrinho'] = $total_price;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="icone.png" type="image/png">
    <title>Carrinho de Compras</title>
</head>
<body>
<div class="header">
    <img src="icone.png" alt="Logo da Playtopia" class="logo-loja">
    <a href="loja.php" class="back-to-shop-btn">Continuar Comprando</a>
</div>
<div class="cart-container">
    <h2>Seu Carrinho de Compras</h2>
    <?php if (isset($status) && $status != ""): ?>
        <div class="message-box box"><?php echo $status; ?></div>
    <?php endif; ?>
    <?php if (!empty($shopping_cart)): ?>
        <div class="cart-items">
            <?php foreach ($shopping_cart as $item): ?>
                <div class="cart-item">
                    <img src="fotos/<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>">
                    <div class="item-info">
                        <h4><?php echo htmlspecialchars($item['nome']); ?></h4>
                        <p>Preço: R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></p>
                        <form method="post" class="quantity-form">
                            <input type="hidden" name="action" value="update_quantity">
                            <input type="hidden" name="codigo_update" value="<?php echo htmlspecialchars($item['codigo']); ?>">
                            <label for="quantity-<?php echo htmlspecialchars($item['codigo']); ?>">Quantidade:</label>
                            <input type="number" id="quantity-<?php echo htmlspecialchars($item['codigo']); ?>" name="quantity" value="<?php echo htmlspecialchars($item['quantidade']); ?>" min="1" onchange="this.form.submit()">
                        </form>
                        <form method="post" class="remove-form">
                            <input type="hidden" name="action" value="remove_item">
                            <input type="hidden" name="codigo_remove" value="<?php echo htmlspecialchars($item['codigo']); ?>">
                            <button type="submit" class="remove-btn">Remover</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="cart-summary">
            <h3>Total: R$ <?php echo number_format($total_price, 2, ',', '.'); ?></h3>
            <div class="cart-actions-buttons">
                <a href="pagamento.php" class="checkout-btn">Finalizar Compra</a>
            </div>
        </div>
    <?php else: ?>
        <p>Seu carrinho está vazio. <a href="loja.php">Comece a comprar!</a></p>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messageBox = document.querySelector('.message-box');
        if (messageBox) {
            setTimeout(() => {
                messageBox.style.opacity = '0';
                messageBox.style.transition = 'opacity 0.5s ease-out';
                setTimeout(() => messageBox.remove(), 500);
            }, 3000);
        }
    });
</script>
</body>
</html>