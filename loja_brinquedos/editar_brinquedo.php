<?php
session_start();
require_once "db_connection.php";

// Verifica se o usuário é um gerente, caso contrário, redireciona
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'gerente') {
    header("Location: loja.php");
    exit();
}

$status = "";
$brinquedo = null;

// Lógica para carregar os dados do brinquedo para edição
if (isset($_POST['codigo_brinquedo'])) {
    $codigo = $_POST['codigo_brinquedo'];
    $sql_brinquedo = "SELECT * FROM brinquedos WHERE codigo = ?";
    $stmt = $conn->prepare($sql_brinquedo);
    $stmt->bind_param("i", $codigo);
    $stmt->execute();
    $result_brinquedo = $stmt->get_result();
    $brinquedo = $result_brinquedo->fetch_assoc();
    $stmt->close();
    
    if (!$brinquedo) {
        $status = "Brinquedo não encontrado.";
    }
}

// Lógica para processar a edição (atualização)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'editar') {
    $codigo = $_POST['codigo'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $codcategoria = $_POST['codcategoria'];
    $codfabricante = $_POST['codfabricante'];

    // Lógica para a imagem
    $novo_nome_imagem = $_POST['imagem_atual']; // Mantém a imagem atual por padrão
    if (isset($_FILES['imagem_nova']) && $_FILES['imagem_nova']['error'] == 0) {
        $diretorio = "fotos/";
        $extensao = strtolower(substr($_FILES['imagem_nova']['name'], -4));
        $novo_nome_imagem = md5(time()) . $extensao;
        
        // Move o novo arquivo para a pasta
        if (!move_uploaded_file($_FILES['imagem_nova']['tmp_name'], $diretorio . $novo_nome_imagem)) {
            $status = "Erro ao fazer upload da nova imagem.";
            $novo_nome_imagem = $_POST['imagem_atual']; // Reverte para a imagem antiga em caso de erro
        }
    }

    $sql = "UPDATE brinquedos SET nome = ?, descricao = ?, preco = ?, estoque = ?, imagem = ?, codcategoria = ?, codfabricante = ? WHERE codigo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdissii", $nome, $descricao, $preco, $estoque, $novo_nome_imagem, $codcategoria, $codfabricante, $codigo);
    
    if ($stmt->execute()) {
        $status = "Brinquedo atualizado com sucesso!";
        // Recarrega os dados para exibir a atualização
        $sql_brinquedo = "SELECT * FROM brinquedos WHERE codigo = ?";
        $stmt_reload = $conn->prepare($sql_brinquedo);
        $stmt_reload->bind_param("i", $codigo);
        $stmt_reload->execute();
        $result_reload = $stmt_reload->get_result();
        $brinquedo = $result_reload->fetch_assoc();
        $stmt_reload->close();
    } else {
        $status = "Erro ao atualizar brinquedo: " . $stmt->error;
    }
    $stmt->close();
}

// Carrega categorias e fabricantes para os selects
$sql_categorias = "SELECT codigo, nome FROM categoria ORDER BY nome ASC";
$result_categorias = $conn->query($sql_categorias);
$sql_fabricantes = "SELECT codigo, nome FROM fabricante ORDER BY nome ASC";
$result_fabricantes = $conn->query($sql_fabricantes);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="icone.png" type="image/png">
    <title>Editar Brinquedo - Painel do Gerente</title>
</head>
<body>

<div class="header">
    <a href="loja.php" id="logo-link">
        <img src="logo.png" alt="Logo da Loja de Brinquedos" class="logo-loja-adm">
    </a>
    <div class="user-actions">
        <span class="welcome-message">Olá, Gerente!</span>
        <a href="administracao.php" class="admin-btn">Painel</a>
        <a href="gerenciar_brinquedos.php" class="home-btn">Voltar</a>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>
</div>

<div class="admin-container">
    <h2>Editar Brinquedo</h2>
    
    <?php if ($status): ?>
        <div class="box status-message"><?php echo $status; ?></div>
    <?php endif; ?>

    <?php if ($brinquedo): ?>
        <div class="admin-section">
            <form action="editar_brinquedo.php" method="post" enctype="multipart/form-data" class="edit-form">
                <input type="hidden" name="action" value="editar">
                <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($brinquedo['codigo']); ?>">
                <input type="hidden" name="imagem_atual" value="<?php echo htmlspecialchars($brinquedo['imagem']); ?>">

                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($brinquedo['nome']); ?>" required>
                
                <label for="descricao">Descrição:</label>
                <textarea name="descricao" id="descricao" required><?php echo htmlspecialchars($brinquedo['descricao']); ?></textarea>
                
                <label for="preco">Preço:</label>
                <input type="number" name="preco" id="preco" step="0.01" min="0" value="<?php echo htmlspecialchars($brinquedo['preco']); ?>" required>
                
                <label for="estoque">Estoque:</label>
                <input type="number" name="estoque" id="estoque" min="0" value="<?php echo htmlspecialchars($brinquedo['estoque']); ?>" required>
                
                <label>Imagem Atual:</label>
                <img src="fotos/<?php echo htmlspecialchars($brinquedo['imagem']); ?>" alt="Imagem do Brinquedo" style="width: 150px; display: block; margin-bottom: 10px;">
                <label for="imagem_nova">Substituir Imagem:</label>
                <input type="file" name="imagem_nova" id="imagem_nova">
                
                <label for="codcategoria">Categoria:</label>
                <select name="codcategoria" id="codcategoria" required>
                    <?php $result_categorias->data_seek(0); ?>
                    <?php while($cat = $result_categorias->fetch_assoc()): ?>
                        <option value="<?php echo $cat['codigo']; ?>" <?php echo ($cat['codigo'] == $brinquedo['codcategoria']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nome']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label for="codfabricante">Fabricante:</label>
                <select name="codfabricante" id="codfabricante" required>
                    <?php $result_fabricantes->data_seek(0); ?>
                    <?php while($fab = $result_fabricantes->fetch_assoc()): ?>
                        <option value="<?php echo $fab['codigo']; ?>" <?php echo ($fab['codigo'] == $brinquedo['codfabricante']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($fab['nome']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit" class="submit-btn">Atualizar Brinquedo</button>
            </form>
        </div>
    <?php else: ?>
        <p>Por favor, selecione um brinquedo na página de gerenciamento para editar.</p>
    <?php endif; ?>
</div>

<div id="preloader" class="preloader-overlay" style="display: none;">
    <div class="spinner-border"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // VARIÁVEIS ESSENCIAIS
    const preloader = document.getElementById('preloader');
    
    // VARIÁVEIS DE AÇÃO
    const editForm = document.querySelector('.edit-form');
    const logoLink = document.getElementById('logo-link'); 
    const adminBtn = document.querySelector('.admin-btn'); 
    const homeBtn = document.querySelector('.home-btn'); 
    const logoutBtn = document.querySelector('.logout-btn'); 

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
    
    // Garante que o preloader comece escondido (boa prática)
    hidePreloader(); 

    // --- LISTENERS DE AÇÃO ---

    // 3. LISTENER DE FORMULÁRIO (Atualizar Brinquedo)
    if (editForm) {
        editForm.addEventListener('submit', showPreloader);
    }

    // 4. LISTENERS DE LINKS/BOTÕES DO HEADER (Redirecionamento)
    if (logoLink) {
        logoLink.addEventListener('click', showPreloader);
    }
    if (adminBtn) {
        adminBtn.addEventListener('click', showPreloader);
    }
    if (homeBtn) {
        homeBtn.addEventListener('click', showPreloader);
    }
    if (logoutBtn) {
        logoutBtn.addEventListener('click', showPreloader);
    }
});
</script>
</body>
</html>