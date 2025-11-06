<?php
session_start();
require_once "db_connection.php";

// Verifica se o usuário é um gerente, caso contrário, redireciona
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'gerente') {
    header("Location: loja.php");
    exit();
}

$status = "";

// Lógica para Adicionar um novo brinquedo (Gravar)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'adicionar') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $codcategoria = $_POST['codcategoria'];
    $codfabricante = $_POST['codfabricante'];
    
    // Processamento da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $diretorio = "fotos/";
        $extensao = strtolower(substr($_FILES['imagem']['name'], -4)); // Pega os últimos 4 caracteres para a extensão
        $novo_nome_imagem = md5(time()) . $extensao;
        
        // Move o arquivo para a pasta de destino
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio . $novo_nome_imagem)) {
            $sql = "INSERT INTO brinquedos (nome, descricao, preco, estoque, imagem, codcategoria, codfabricante) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdissi", $nome, $descricao, $preco, $estoque, $novo_nome_imagem, $codcategoria, $codfabricante);
            
            if ($stmt->execute()) {
                $status = "Brinquedo adicionado com sucesso!";
            } else {
                $status = "Erro ao adicionar brinquedo: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $status = "Erro ao fazer upload da imagem.";
        }
    } else {
        $status = "Por favor, selecione uma imagem para o brinquedo.";
    }
}

// Lógica para Excluir um brinquedo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'excluir') {
    $codigo = $_POST['codigo'];
    $sql = "DELETE FROM brinquedos WHERE codigo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codigo);
    if ($stmt->execute()) {
        $status = "Brinquedo excluído com sucesso!";
    } else {
        $status = "Erro ao excluir brinquedo: " . $stmt->error;
    }
    $stmt->close();
}

// Lógica para pesquisar e listar brinquedos
$searchTerm = '';
$whereClause = '';
$params = array();
$types = '';

if (isset($_GET['busca']) && !empty($_GET['busca'])) {
    $searchTerm = '%' . $_GET['busca'] . '%';
    $whereClause .= " WHERE b.nome LIKE ?";
    $params[] = &$searchTerm;
    $types .= 's';
}

$sql = "SELECT b.*, c.nome AS categoria_nome, f.nome AS fabricante_nome FROM brinquedos b 
        JOIN categoria c ON b.codcategoria = c.codigo 
        JOIN fabricante f ON b.codfabricante = f.codigo" . $whereClause . " ORDER BY b.nome ASC";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    array_unshift($params, $types);
    call_user_func_array(array($stmt, 'bind_param'), $params);
}
$stmt->execute();
$result_brinquedos = $stmt->get_result();

// Carrega categorias e fabricantes para os selects dos formulários
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
    <title>Gerenciar Brinquedos - Painel do Gerente</title>
</head>
<body>

<div class="header">
    <a href="loja.php">
        <img src="logo.png" alt="Logo da Loja de Brinquedos" class="logo-loja-adm">
    </a>
    <div class="user-actions">
        <span class="welcome-message">Olá, Gerente!</span>
        <a href="administracao.php" class="admin-btn">Painel</a>
        <a href="loja.php" class="home-btn">Voltar para a Loja</a>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>
</div>

<div class="admin-container">
    <h2>Gerenciar Brinquedos</h2>
    
    <?php if ($status): ?>
        <div class="box status-message"><?php echo $status; ?></div>
    <?php endif; ?>

    <div class="admin-section">
        <h3>Adicionar Novo Brinquedo</h3>
        <form action="gerenciar_brinquedos.php" method="post" enctype="multipart/form-data" class="add-form">
            <input type="hidden" name="action" value="adicionar">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required>
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" required></textarea>
            <label for="preco">Preço:</label>
            <input type="number" name="preco" id="preco" step="0.01" min="0" required>
            <label for="estoque">Estoque:</label>
            <input type="number" name="estoque" id="estoque" min="0" required>
            <label for="imagem">Imagem:</label>
            <input type="file" name="imagem" id="imagem" required>
            <label for="codcategoria">Categoria:</label>
            <select name="codcategoria" id="codcategoria" required>
                <?php $result_categorias->data_seek(0); ?>
                <?php while($cat = $result_categorias->fetch_assoc()): ?>
                    <option value="<?php echo $cat['codigo']; ?>"><?php echo htmlspecialchars($cat['nome']); ?></option>
                <?php endwhile; ?>
            </select>
            <label for="codfabricante">Fabricante:</label>
            <select name="codfabricante" id="codfabricante" required>
                <?php $result_fabricantes->data_seek(0); ?>
                <?php while($fab = $result_fabricantes->fetch_assoc()): ?>
                    <option value="<?php echo $fab['codigo']; ?>"><?php echo htmlspecialchars($fab['nome']); ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="submit-btn">Adicionar Brinquedo</button>
        </form>
    </div>

    <div class="admin-section">
        <h3>Brinquedos Existentes</h3>
        <form action="gerenciar_brinquedos.php" method="get" class="search-form">
            <input type="text" name="busca" placeholder="Pesquisar por nome...">
            <button class="search-button" type="submit">
                <img src="lupa.png" alt="Buscar" height="20" width="20">
            </button>
        </form>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Categoria</th>
                    <th>Fabricante</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_brinquedos->num_rows > 0): ?>
                    <?php while($brinquedo = $result_brinquedos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($brinquedo['codigo']); ?></td>
                        <td><img src="fotos/<?php echo htmlspecialchars($brinquedo['imagem']); ?>" alt="Imagem do Brinquedo" style="width: 50px;"></td>
                        <td><?php echo htmlspecialchars($brinquedo['nome']); ?></td>
                        <td>R$ <?php echo number_format($brinquedo['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($brinquedo['estoque']); ?></td>
                        <td><?php echo htmlspecialchars($brinquedo['categoria_nome']); ?></td>
                        <td><?php echo htmlspecialchars($brinquedo['fabricante_nome']); ?></td>
                        <td class="actions">
                            <form method="post" action="editar_brinquedo.php" style="display:inline;">
                                <input type="hidden" name="codigo_brinquedo" value="<?php echo htmlspecialchars($brinquedo['codigo']); ?>">
                                <button type="submit" class="edit-btn">Editar</button>
                            </form>
                            <form method="post" action="gerenciar_brinquedos.php" style="display:inline;">
                                <input type="hidden" name="action" value="excluir">
                                <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($brinquedo['codigo']); ?>">
                                <button type="submit" class="delete-btn">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Nenhum brinquedo encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div id="preloader" class="preloader-overlay" style="display: none;">
    <div class="spinner-border"></div>
</div>
</body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // VARIÁVEIS ESSENCIAIS
    const preloader = document.getElementById('preloader');
    
    // VARIÁVEIS PARA LISTENERS DE REDIRECIONAMENTO E SUBMISSÃO
    const addForm = document.querySelector('.add-form');
    const searchForm = document.querySelector('.search-form');
    
    // Links/Botões do Header
    const adminBtn = document.querySelector('.admin-btn');
    const homeBtn = document.querySelector('.home-btn');
    const logoutBtn = document.querySelector('.logout-btn');
    
    // Tabela de Dados (Contêiner para delegação de eventos)
    const dataTable = document.querySelector('.data-table');

    // 1. FUNÇÃO PARA MOSTRAR O PRELOADER
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
    // 2. CORREÇÃO PARA O BOTÃO VOLTAR DO NAVEGADOR
    // O evento pageshow é disparado quando a página é carregada (incluindo BFCache)
    window.addEventListener('pageshow', function(event) {
        // Se a propriedade persisted for true, a página foi restaurada do cache.
        if (event.persisted) {
            hidePreloader();
        }
    });
    
    // Garante que o preloader esteja escondido por padrão ao carregar
    hidePreloader();

    // --- LISTENERS DE AÇÃO ---

    // 2. LISTENERS DE FORMULÁRIO (Adicionar e Pesquisar)
    if (addForm) {
        addForm.addEventListener('submit', showPreloader);
    }
    if (searchForm) {
        searchForm.addEventListener('submit', showPreloader);
    }

    // 3. LISTENERS DE LINKS/BOTÕES DO HEADER
    if (adminBtn) {
        adminBtn.addEventListener('click', showPreloader);
    }
    if (homeBtn) {
        homeBtn.addEventListener('click', showPreloader);
    }
    if (logoutBtn) {
        logoutBtn.addEventListener('click', showPreloader);
    }

    // 4. LISTENERS DA TABELA (Editar e Excluir)
    // Usa delegação de eventos para capturar submissões de formulários
    if (dataTable) {
        dataTable.addEventListener('submit', function(e) {
            // Verifica se a submissão veio de um dos formulários de Editar ou Excluir
            if (e.target.tagName === 'FORM') {
                // Antes de enviar, verifica se a confirmação de exclusão passou
                if (e.target.querySelector('input[name="action"][value="excluir"]')) {
                    // O formulário de exclusão já tem um 'onsubmit' no HTML,
                    // mas podemos garantir o preloader aqui:
                    if (confirm('Tem certeza que deseja excluir este item?')) {
                        showPreloader();
                    } else {
                        e.preventDefault(); // Impede o envio se o usuário cancelar
                    }
                } else {
                    // É o formulário de Editar (que redireciona)
                    showPreloader();
                }
            }
        });
        
        // Listener para o botão de editar, caso a lógica de submit no form não seja suficiente.
        // O listener de 'submit' acima já cobre os dois casos de formulário.
    }
});
</script>
</html>