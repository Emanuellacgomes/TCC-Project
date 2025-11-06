<?php
session_start();
require_once "db_connection.php";

// Verifica se o usuário está logado e se é um gerente
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'gerente') {
    header("Location: loja.php"); // Redireciona para a loja se não for gerente
    exit();
}

$status = ""; // Para mensagens de feedback

// --- Lógica para Adicionar, Atualizar e Excluir Categorias ---

// Adicionar Categoria
if (isset($_POST['adicionar_categoria'])) {
    $nome_categoria = $_POST['nome_nova_categoria'];
    if (!empty($nome_categoria)) {
        $sql = "INSERT INTO categoria (nome) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nome_categoria);
        if ($stmt->execute()) {
            $status = "Categoria '" . htmlspecialchars($nome_categoria) . "' adicionada com sucesso!";
        } else {
            $status = "Erro ao adicionar categoria: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Atualizar Categoria
if (isset($_POST['editar_categoria'])) {
    $codigo_categoria = $_POST['codigo_editar'];
    $novo_nome = $_POST['nome_editar'];
    if (!empty($novo_nome)) {
        $sql = "UPDATE categoria SET nome = ? WHERE codigo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $novo_nome, $codigo_categoria);
        if ($stmt->execute()) {
            $status = "Categoria atualizada com sucesso!";
        } else {
            $status = "Erro ao atualizar categoria: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Excluir Categoria
if (isset($_POST['excluir_categoria'])) {
    $codigo_categoria = $_POST['codigo_excluir'];
    $sql = "DELETE FROM categoria WHERE codigo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codigo_categoria);
    if ($stmt->execute()) {
        $status = "Categoria excluída com sucesso!";
    } else {
        $status = "Erro ao excluir categoria: " . $stmt->error;
    }
    $stmt->close();
}

// --- Lógica para carregar categorias existentes ---
$sql_categorias = "SELECT codigo, nome FROM categoria ORDER BY nome ASC";
$result_categorias = $conn->query($sql_categorias);
$categorias = $result_categorias->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Categorias - Playtopia</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="icone.png" type="image/png">
</head>
<body>

<div class="header">
    <a href="loja.php" id="logo-link">
        <img src="logo.png" alt="Logo da Playtopia" class="logo-loja-adm">
    </a>
    <div class="user-actions">
        <span class="welcome-message">Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
        <a href="administracao.php" class="admin-btn">Voltar para Adm</a>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>
</div>

<div class="container-adm">
    <h1>Gerenciamento de Categorias</h1>

    <?php if ($status): ?>
        <div class="message-box success"><?php echo $status; ?></div>
    <?php endif; ?>

    <div class="card-adm">
        <h3>Adicionar Nova Categoria</h3>
        <form method="post" action="gerenciar_categorias.php" class="form-adm add-form">
            <input type="text" name="nome_nova_categoria" placeholder="Nome da Categoria" required>
            <button type="submit" name="adicionar_categoria" class="btn-adm btn-add">Adicionar Categoria</button>
        </form>
    </div>
    
    <div class="card-adm">
        <h3>Categorias Existentes</h3>
        <?php if (!empty($categorias)): ?>
            <table class="table-adm">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome da Categoria</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($categoria['codigo']); ?></td>
                            <td>
                                <form method="post" action="gerenciar_categorias.php" class="inline-form edit-form">
                                    <input type="hidden" name="codigo_editar" value="<?php echo htmlspecialchars($categoria['codigo']); ?>">
                                    <input type="text" name="nome_editar" value="<?php echo htmlspecialchars($categoria['nome']); ?>" required>
                                    <button type="submit" name="editar_categoria" class="btn-adm btn-edit">Salvar</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="gerenciar_categorias.php" class="inline-form delete-form" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');">
                                    <input type="hidden" name="codigo_excluir" value="<?php echo htmlspecialchars($categoria['codigo']); ?>">
                                    <button type="submit" name="excluir_categoria" class="btn-adm btn-delete">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhuma categoria cadastrada.</p>
        <?php endif; ?>
    </div>
</div>

<div id="preloader" class="preloader-overlay" style="display: none;">
    <div class="spinner-border"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // VARIÁVEIS ESSENCIAIS
    const preloader = document.getElementById('preloader');
    
    // VARIÁVEIS DE AÇÃO
    const logoLink = document.getElementById('logo-link'); 
    const adminBtn = document.querySelector('.admin-btn'); // Voltar para Adm
    const logoutBtn = document.querySelector('.logout-btn'); // Sair
    const containerAdm = document.querySelector('.container-adm'); // Contêiner principal para delegação de formulários

    // 1. FUNÇÕES DO PRELOADER
    function showPreloader() {
        if (preloader) {
            preloader.style.display = 'flex';
        }
    }
    
    function hidePreloader() {
        if (preloader) {
            preloader.style.display = 'none';
        }
    }

    // 2. CORREÇÃO PARA O BOTÃO VOLTAR DO NAVEGADOR (BFCache)
    window.addEventListener('pageshow', function(event) {
        // Se a página foi restaurada do cache (botão Voltar), esconde o preloader
        if (event.persisted) {
            hidePreloader();
        }
    });
    
    // Garante que o preloader comece escondido
    hidePreloader(); 

    // --- LISTENERS DE AÇÃO ---

    // 3. LISTENERS PARA FORMULÁRIOS DE SUBMISSÃO (Adicionar, Salvar, Excluir)
    // Usamos delegação no container principal para capturar todas as submissões
    if (containerAdm) {
        containerAdm.addEventListener('submit', function(e) {
            const form = e.target.closest('form');
            if (form) {
                // A lógica é a mesma para todos os formulários de submissão.
                // Se for o formulário de exclusão, o 'confirm' no HTML garante que showPreloader
                // só será chamado se o usuário clicar em OK.
                showPreloader();
            }
        });
    }

    // 4. LISTENERS DE LINKS/BOTÕES DO HEADER (Redirecionamento)
    if (logoLink) {
        logoLink.addEventListener('click', showPreloader);
    }
    if (adminBtn) {
        adminBtn.addEventListener('click', showPreloader);
    }
    if (logoutBtn) {
        logoutBtn.addEventListener('click', showPreloader);
    }
});
</script>
</body>
</html>