<?php
session_start();
require_once "db_connection.php";

// Verifica se o usuário está logado e se é um gerente
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'gerente') {
    header("Location: loja.php"); // Redireciona para a loja se não for gerente
    exit();
}

$status = ""; // Para mensagens de feedback

// --- Lógica para Adicionar, Atualizar e Excluir Fabricantes ---

// Adicionar Fabricante
if (isset($_POST['adicionar_fabricante'])) {
    $nome_fabricante = $_POST['nome_novo_fabricante'];
    if (!empty($nome_fabricante)) {
        $sql = "INSERT INTO fabricante (nome) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nome_fabricante);
        if ($stmt->execute()) {
            $status = "Fabricante '" . htmlspecialchars($nome_fabricante) . "' adicionado com sucesso!";
        } else {
            $status = "Erro ao adicionar fabricante: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Atualizar Fabricante
if (isset($_POST['editar_fabricante'])) {
    $codigo_fabricante = $_POST['codigo_editar'];
    $novo_nome = $_POST['nome_editar'];
    if (!empty($novo_nome)) {
        $sql = "UPDATE fabricante SET nome = ? WHERE codigo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $novo_nome, $codigo_fabricante);
        if ($stmt->execute()) {
            $status = "Fabricante atualizado com sucesso!";
        } else {
            $status = "Erro ao atualizar fabricante: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Excluir Fabricante
if (isset($_POST['excluir_fabricante'])) {
    $codigo_fabricante = $_POST['codigo_excluir'];
    $sql = "DELETE FROM fabricante WHERE codigo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codigo_fabricante);
    if ($stmt->execute()) {
        $status = "Fabricante excluído com sucesso!";
    } else {
        $status = "Erro ao excluir fabricante: " . $stmt->error;
    }
    $stmt->close();
}

// --- Lógica para carregar fabricantes existentes ---
$sql_fabricantes = "SELECT codigo, nome FROM fabricante ORDER BY nome ASC";
$result_fabricantes = $conn->query($sql_fabricantes);
$fabricantes = $result_fabricantes->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Fabricantes - Playtopia</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="icone.png" type="image/png">
</head>
<body>

<div class="header">
    <a href="loja.php">
        <img src="logo.png" alt="Logo da Playtopia" class="logo-loja-adm">
    </a>
    <div class="user-actions">
        <span class="welcome-message">Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
        <a href="administracao.php" class="admin-btn">Voltar para Adm</a>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>
</div>

<div class="container-adm">
    <h1>Gerenciamento de Fabricantes</h1>

    <?php if ($status): ?>
        <div class="message-box success"><?php echo $status; ?></div>
    <?php endif; ?>

    <div class="card-adm">
        <h3>Adicionar Novo Fabricante</h3>
        <form method="post" action="gerenciar_fabricantes.php" class="form-adm">
            <input type="text" name="nome_novo_fabricante" placeholder="Nome do Fabricante" required>
            <button type="submit" name="adicionar_fabricante" class="btn-adm btn-add">Adicionar Fabricante</button>
        </form>
    </div>
    
    <div class="card-adm">
        <h3>Fabricantes Existentes</h3>
        <?php if (!empty($fabricantes)): ?>
            <table class="table-adm">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome do Fabricante</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fabricantes as $fabricante): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fabricante['codigo']); ?></td>
                            <td>
                                <form method="post" action="gerenciar_fabricantes.php" class="inline-form">
                                    <input type="hidden" name="codigo_editar" value="<?php echo htmlspecialchars($fabricante['codigo']); ?>">
                                    <input type="text" name="nome_editar" value="<?php echo htmlspecialchars($fabricante['nome']); ?>" required>
                                    <button type="submit" name="editar_fabricante" class="btn-adm btn-edit">Salvar</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="gerenciar_fabricantes.php" class="inline-form" onsubmit="return confirm('Tem certeza que deseja excluir este fabricante?');">
                                    <input type="hidden" name="codigo_excluir" value="<?php echo htmlspecialchars($fabricante['codigo']); ?>">
                                    <button type="submit" name="excluir_fabricante" class="btn-adm btn-delete">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum fabricante cadastrado.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>