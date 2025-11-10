<?php
session_start();
require_once "db_connection.php";
$show_lgpd_popup = !isset($_COOKIE['consent_lgpd']); 
$status = "";

// Lógica para definir o CEP do usuário no cookie
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cep'])) {
    $origem = isset($_POST['origem']) && $_POST['origem'] === 'auto' ? 'auto' : 'manual';
    setcookie('cep_usuario', $_POST['cep'], time() + (86400 * 30), "/"); 
    setcookie('cep_origem', $origem, time() + (86400 * 30), "/");
    header("Location: loja.php");
    exit();
}


// Lógica para buscar detalhes do produto via AJAX
if (isset($_GET['action']) && $_GET['action'] === 'get_product_details' && isset($_GET['codigo'])) {
    header('Content-Type: application/json');
    $codigo_brinquedo = $conn->real_escape_string($_GET['codigo']);

    $sql_brinquedo = "SELECT codigo, nome, descricao, preco, imagem, estoque, codcategoria FROM brinquedos WHERE codigo = '$codigo_brinquedo'";
    $result_brinquedo = $conn->query($sql_brinquedo);
    $dados_brinquedo = $result_brinquedo->fetch_assoc();

    if ($dados_brinquedo) {
        $cep_usuario = isset($_COOKIE['cep_usuario']) ? $_COOKIE['cep_usuario'] : null;
        $dados_brinquedo['frete_status'] = 'Informe seu CEP para calcular o frete.';
        $dados_brinquedo['desconto_porcentagem'] = null;

        $cep_origem = isset($_COOKIE['cep_origem']) ? $_COOKIE['cep_origem'] : 'manual';

        if ($cep_usuario && $cep_origem === 'auto') {

            $dados_brinquedo['frete_status'] = 'Entrega para o CEP ' . $cep_usuario;

            $cep_limpo = str_replace('-', '', $cep_usuario);
            
            $sql_desconto = "SELECT porcentagem_desconto FROM descontos_cep WHERE REPLACE(cep_prefixo, '-', '') = ?";
            $stmt_desconto = $conn->prepare($sql_desconto);
            
            if ($stmt_desconto) {
                $stmt_desconto->bind_param("s", $cep_limpo);
                $stmt_desconto->execute();
                $result_desconto = $stmt_desconto->get_result();
                $desconto_row = $result_desconto->fetch_assoc();
                $stmt_desconto->close();

                if ($desconto_row) {
                    $porcentagem_desconto = floatval($desconto_row['porcentagem_desconto']);
                    $dados_brinquedo['preco_original'] = $dados_brinquedo['preco'];
                    $dados_brinquedo['preco_desconto'] = $dados_brinquedo['preco'] * (1 - ($porcentagem_desconto / 100));
                    $dados_brinquedo['desconto_porcentagem'] = $porcentagem_desconto;
                }
            } else {
                 error_log("Erro ao preparar a consulta de desconto: " . $conn->error);
            }
        }
        echo json_encode($dados_brinquedo);
    } else {
        echo json_encode(array('error' => 'Brinquedo não encontrado.'));
    }
    exit();
}

// Lógica de adicionar ao carrinho
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['codigo_add_cart'])) {
    if (!isset($_SESSION['user_codigo'])) {
        $status = "<div class='box' style='color:red;'>Você precisa estar logado para adicionar itens ao carrinho.</div>";
    } else {
        $codigo_brinquedo = $conn->real_escape_string($_POST['codigo_add_cart']);
        $sql_brinquedo = "SELECT codigo, nome, preco, imagem FROM brinquedos WHERE codigo = '$codigo_brinquedo'";
        $result_brinquedo = $conn->query($sql_brinquedo);
        $dados_brinquedo = $result_brinquedo->fetch_assoc();
        if ($dados_brinquedo) {
            $user_codigo = $_SESSION['user_codigo'];
            $quantity = 1;
            $sql_insert_or_update = "
                INSERT INTO carrinho_usuario (id_usuario, codigo_brinquedo, quantidade)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE quantidade = quantidade + ?
            ";
            $stmt_cart = $conn->prepare($sql_insert_or_update);
            if ($stmt_cart) {
                $stmt_cart->bind_param("iiii", $user_codigo, $dados_brinquedo['codigo'], $quantity, $quantity);
                if ($stmt_cart->execute()) {
                    $status = "<div class='box'>Brinquedo adicionado ao carrinho!</div>";
                } else {
                    $status = "<div class='box' style='color:red;'>Erro ao adicionar brinquedo ao carrinho.</div>";
                }
                $stmt_cart->close();
            } else {
                $status = "<div class='box' style='color:red;'>Erro ao preparar a consulta de carrinho.</div>";
                 error_log("Erro ao preparar a consulta de carrinho: " . $conn->error);
            }
        } else {
            $status = "<div class='box' style='color:red;'>Erro: Brinquedo não encontrado.</div>";
        }
    }
}

// Lógica de busca e filtro de categoria
$searchTerm = '';
$categoryFilter = null;
$whereClause = '';
$params = array();
$paramTypes = '';

if (isset($_GET['busca']) && !empty($_GET['busca'])) {
    $searchTerm = '%' . $_GET['busca'] . '%';
    $whereClause .= "nome LIKE ?";
    $params[] = &$searchTerm;
    $paramTypes .= 's';
}

if (isset($_GET['categoria_filtro']) && is_numeric($_GET['categoria_filtro'])) {
    $categoryFilter = $_GET['categoria_filtro'];
    if (!empty($whereClause)) {
        $whereClause .= " AND ";
    }
    $whereClause .= "codcategoria = ?";
    $params[] = &$categoryFilter;
    $paramTypes .= 'i';
}

if (!empty($whereClause)) {
    $whereClause = "WHERE " . $whereClause;
}

// Lógica de Paginação
$limit = 12;
$currentPage = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($currentPage - 1) * $limit;

// Contar o total de brinquedos para a paginação
$sql_count = "SELECT COUNT(*) AS total FROM brinquedos " . $whereClause;
$stmt_count = $conn->prepare($sql_count);
if ($stmt_count) {
    if (!empty($params)) {
        call_user_func_array(array($stmt_count, 'bind_param'), array_merge(array($paramTypes), $params));
    }
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $row_count = $result_count->fetch_assoc();
    $totalBrinquedos = $row_count['total'];
    $totalPaginas = ceil($totalBrinquedos / $limit);
    $stmt_count->close();
} else {
     $totalPaginas = 1;
     $totalBrinquedos = 0;
     error_log("Erro ao preparar a consulta de contagem: " . $conn->error);
}

// Carrega as categorias para os botões de filtro
$sql_categorias = "SELECT codigo, nome FROM categoria ORDER BY nome ASC";
$result_categorias = $conn->query($sql_categorias);
$categorias = $result_categorias->fetch_all(MYSQLI_ASSOC);

// Contagem de itens no carrinho
$cart_count = 0;
if (isset($_SESSION['user_codigo'])) {
    $user_codigo = $_SESSION['user_codigo'];
    $sql_cart_count = "SELECT SUM(quantidade) AS total_itens FROM carrinho_usuario WHERE id_usuario = ?";
    $stmt_cart_count = $conn->prepare($sql_cart_count);
    if ($stmt_cart_count) {
        $stmt_cart_count->bind_param("i", $user_codigo);
        $stmt_cart_count->execute();
        $result_cart_count = $stmt_cart_count->get_result();
        $row_cart_count = $result_cart_count->fetch_assoc();
        if ($row_cart_count && isset($row_cart_count['total_itens'])) {
            $cart_count = $row_cart_count['total_itens'];
        } else {
            $cart_count = 0;
        }
        $stmt_cart_count->close();
    } else {
        error_log("Erro ao preparar a consulta de contagem de carrinho: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="icone.png" type="image/png">
    <title>Playtopia</title>
</head>
<body>

<div class="header">
    <img src="logo.png" alt="Logo da Playtopia" class="logo-loja">
    
    <button id="cep-trigger" class="cep-trigger">
        <img src="cep.png" alt="CEP" height="20" width="20"/> 
        CEP: Não definido
    </button>

    <form method="GET" action="loja.php" class="search-form">
        <input type="text" name="busca" placeholder="Pesquisar brinquedos..." value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>">
        <button type="submit" class="search-button">
            <img src="lupa.png" alt="Buscar" height="20" width="20">
        </button>
    </form>

    <div class="user-actions">
    <?php if (isset($_SESSION['user_name'])): ?>
        <span class="welcome-message">Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
        
        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'gerente'): ?>
            <a href="administracao.php" class="admin-btn"><img class ="admin-button" src="admin.png"></a>
        <?php endif; ?>
        
        <a href="logout.php" class="logout-btn">Sair</a>
    <?php else: ?>
        <a href="login.php" class="login-btn">Login</a>
    <?php endif; ?>

    <div class="cart_div">
        <a href="#" id="cart-link">
            <img src="carrinho.png" alt="Carrinho" height="30" width="30"/> Carrinho
            <?php if ($cart_count > 0): ?>
                <span><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>
    </div>
  </div>
</div>
<div class="cabecalho-background">
    <div class="slideshow">
        <img src="slide1.jpg" alt="Slide 1" class="slide active">
        <img src="slide2.jpg" alt="Slide 2" class="slide">
        <img src="slide3.jpg" alt="Slide 3" class="slide">
        <img src="slide4.jpg" alt="Slide 4" class="slide">
        <img src="slide5.jpg" alt="Slide 5" class="slide">
    </div>
</div>
<div class="category-filters">
    <a href="loja.php" class="category-btn <?php echo (!isset($_GET['categoria_filtro'])) ? 'active' : ''; ?>">Todos</a>
    <?php foreach ($categorias as $categoria): ?>
        <a href="loja.php?categoria_filtro=<?php echo htmlspecialchars($categoria['codigo']); ?>" 
           class="category-btn <?php echo (isset($_GET['categoria_filtro']) && $_GET['categoria_filtro'] == $categoria['codigo']) ? 'active' : ''; ?>">
            <?php echo htmlspecialchars($categoria['nome']); ?>
        </a>
    <?php endforeach; ?>
</div>

    <?php if (isset($status) && $status != "") { ?>
        <div class="message-box box">
            <?php echo $status; ?>
        </div>
    <?php } ?>

    <div class="product-grid">
        <?php
        $sql = "SELECT * FROM brinquedos " . $whereClause . " ORDER BY codigo DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $newParamTypes = $paramTypes . 'ii';
            $params_to_bind = array();
            $params_to_bind[] = &$newParamTypes;
            if (!empty($params)) {
                foreach ($params as $param_ref) {
                    $params_to_bind[] = &$param_ref;
                }
            }
            $params_to_bind[] = &$limit;
            $params_to_bind[] = &$offset;

            call_user_func_array(array($stmt, 'bind_param'), $params_to_bind);
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            // NOVO: Pega o CEP do usuário para checar o desconto
            $cep_usuario_logado = isset($_COOKIE['cep_usuario']) ? $_COOKIE['cep_usuario'] : null;
            $cep_origem = isset($_COOKIE['cep_origem']) ? $_COOKIE['cep_origem'] : 'manual';

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
        
                        $porcentagem_desconto = 0;

                    // ✅ Só aplica desconto se o CEP for obtido automaticamente
                if ($cep_usuario_logado && $cep_origem === 'auto') {
                    $cep_limpo = str_replace('-', '', $cep_usuario_logado);
                    $sql_desconto = "SELECT porcentagem_desconto FROM descontos_cep WHERE REPLACE(cep_prefixo, '-', '') = ?";
                    $stmt_desconto = $conn->prepare($sql_desconto);
            
                if ($stmt_desconto) {
                    $stmt_desconto->bind_param("s", $cep_limpo);
                    $stmt_desconto->execute();
                    $result_desconto = $stmt_desconto->get_result();
                    $desconto_row = $result_desconto->fetch_assoc();
                    $stmt_desconto->close();
                
                if ($desconto_row) {
                    $porcentagem_desconto = floatval($desconto_row['porcentagem_desconto']);
                }
            }
        }


                    $preco_original = $row['preco'];
                    $preco_exibido = $preco_original;
                    $is_desconto_aplicado = $porcentagem_desconto > 0;
                    if ($is_desconto_aplicado) {
                        $preco_exibido = $preco_original * (1 - ($porcentagem_desconto / 100));
                    }
        ?>
        <div class="product_wrapper" data-codigo="<?php echo htmlspecialchars($row['codigo']); ?>">
            <div class="product-card-content">
                <div class="image-wrapper">
                    <img src='fotos/<?php echo htmlspecialchars($row['imagem']); ?>' alt="<?php echo htmlspecialchars($row['nome']); ?>" class="image"/>
                </div>
                <h3><?php echo htmlspecialchars($row['nome']); ?></h3>
                <p class="description"><?php echo htmlspecialchars($row['descricao']); ?></p>
                <div class="price-section">
                    <?php if ($is_desconto_aplicado): ?>
                        <span class="discount-percent-tag"><?php echo number_format($porcentagem_desconto, 0); ?>% OFF</span>
                        <span class="price-original">R$ <?php echo number_format($preco_original, 2, ',', '.'); ?></span>
                        <span class="price-discounted">R$ <?php echo number_format($preco_exibido, 2, ',', '.'); ?></span>
                    <?php else: ?>
                        <span class="price">R$ <?php echo number_format($preco_exibido, 2, ',', '.'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($row['estoque'] > 0): ?>
                <button class="open-details-btn">Ver Detalhes</button>
            <?php else: ?>
                <button class="out-of-stock-btn" disabled>Esgotado</button>
            <?php endif; ?>
        </div>
        <?php
                }
            } else {
                echo "<p style='text-align: center; width: 100%;'>Nenhum brinquedo encontrado.</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='text-align: center; width: 100%;'>Erro ao carregar produtos. " . $conn->error . "</p>";
        }
        ?>
    </div>

    <div class="pagination">
        <?php
        $url_params = $_GET;
        
        // --- NOVO CÓDIGO PARA LIMITAR OS LINKS DE PÁGINA ---
        $limite_links = 3; 
        
        // 1. Calcula o ponto de partida do loop (garante que a página atual fique no meio)
        // max(1, ...) impede que o loop comece em 0 ou negativo.
        $inicio_loop = max(1, $currentPage - floor($limite_links / 2));
        
        // 2. Calcula o ponto final do loop
        // min($totalPaginas, ...) impede que o loop ultrapasse o total de páginas.
        $fim_loop = min($totalPaginas, $inicio_loop + $limite_links - 1);
        
        // 3. Ajuste final: se chegou ao fim (totalPaginas), recua o início para manter o limite
        if ($fim_loop == $totalPaginas) {
            $inicio_loop = max(1, $fim_loop - $limite_links + 1);
        }
        // --- FIM DO NOVO CÓDIGO ---

        // Link ANTERIOR
        if ($currentPage > 1) {
            $url_params['pagina'] = $currentPage - 1;
            echo "<a href='loja.php?" . http_build_query($url_params) . "' class='pagination-link'>Anterior</a>";
        }
        
        // Exibe "..." se o início não for a página 1
        if ($inicio_loop > 1) {
             echo "<span class='pagination-dots'>...</span>";
        }
        
        // Links de PÁGINA (agora limitados)
        for ($i = $inicio_loop; $i <= $fim_loop; $i++) {
            $url_params['pagina'] = $i;
            $class = ($i == $currentPage) ? 'pagination-link active' : 'pagination-link';
            echo "<a href='loja.php?" . http_build_query($url_params) . "' class='$class'>$i</a>";
        }
        
        // Exibe "..." se o fim não for a última página
        if ($fim_loop < $totalPaginas) {
             echo "<span class='pagination-dots'>...</span>";
        }

        // Link PRÓXIMA
        if ($currentPage < $totalPaginas) {
            $url_params['pagina'] = $currentPage + 1;
            echo "<a href='loja.php?" . http_build_query($url_params) . "' class='pagination-link'>Próxima</a>";
        }
        ?>
    </div>
    <?php if ($show_lgpd_popup): ?>
<?php endif; ?>

<script>
    // Função helper para definir cookies
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    // Botão: Aceitar Tudo (Full Consentimento)
    document.getElementById('accept-lgpd-btn').addEventListener('click', function() {
        // Verifica se o checkbox de dados opcionais foi marcado
        var optional_consent = document.getElementById('optional-data-consent').checked ? 'full' : 'basic';
        
        // 1. Seta o cookie principal (para não mostrar mais o popup)
        setCookie('consent_lgpd', 'accepted', 365); 
        
        // 2. Seta o cookie para o consentimento opcional
        setCookie('consent_optional_data', optional_consent, 365); 
        
        document.getElementById('lgpd-banner').style.display = 'none';
    });

    // Botão: Apenas Essenciais
    document.getElementById('close-lgpd-btn').addEventListener('click', function() {
        // Aceita apenas os cookies essenciais
        setCookie('consent_lgpd', 'accepted_basic', 365);
        setCookie('consent_optional_data', 'basic', 365); // Nega o uso de dados opcionais
        document.getElementById('lgpd-banner').style.display = 'none';
    });
</script>
    <div id="product-details-modal" class="product-details-modal">
        <div class="modal-content-details">
            <button class="close-details-btn">&times;</button>
            <div id="product-details-content">
            </div>
        </div>
    </div>
    
    <div id="login-required-modal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="close-login-popup">&times;</span>
            <h2>É necessário fazer login</h2>
            <div class="modal-body">
                <p>Para adicionar produtos ao carrinho ou acessá-lo, você precisa estar logado.</p>
                <a href="login.php" class="login-btn-modal">Fazer Login</a>
            </div>
        </div>
    </div>

    <div id="cep-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Calcular Frete</h2>
            <div class="modal-body">
                <p>Por favor, informe seu CEP para calcular o frete e verificar a disponibilidade.</p>

                <div class="option-box">
                    <h4>Digitar CEP</h4>
                    <div class="cep-manual">
                        <input type="text" id="cep-input" placeholder="Ex: 00000-000" maxlength="9">
                        <button id="buscar-cep-btn" class="button-modal">Buscar</button>
                    </div>
                    <div id="cep-manual-status" class="status-message"></div>
                    <br>
                    <div id="find-cep">
                        <a href="https://buscacepinter.correios.com.br/app/endereco/index.php?t">Não sei meu CEP.</a>
                    </div>
                </div>

                <div class="option-separator">
                    <hr>
                    <span>ou</span>
                    <hr>
                </div>

                <div class="option-box">
                    <h4>Usar Localização</h4>
                    <button id="detectar-cep-btn" class="button-modal secondary">Detectar Localização</button>
                    <div id="cep-auto-status" class="status-message"></div>
                </div>
                
                <button id="close-modal-btn" class="button-modal cancel">Não agora</button>

            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>Navegação</h4>
                <ul>
                    <li><a href="loja.php">Início</a></li>
                    <li><a href="carrinho.php">Carrinho</a></li>
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'gerente'): ?>
                        <li><a href="administracao.php">Administração</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Sobre Nós</h4>
                <p>A Playtopia é a sua loja de brinquedos favorita! Oferecemos os melhores brinquedos para todas as idades, com diversão e segurança garantidas.</p>
            </div>
            <div class="footer-section">
                <h4>Redes Sociais</h4>
                <ul class="social-links">
                    <li><a href="#"><img src="instagram.png" alt="Instagram"></a></li>
                    <li><a href="#"><img src="facebook.png" alt="Facebook"></a></li>
                    <li><a href="#"><img src="twitter.png" alt="Twitter"></a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> Playtopia. Todos os direitos reservados.</p>
        </div>
    </footer>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // VARIÁVEIS DO PRELOADER E LOGIN
    const preloader = document.getElementById('preloader');
    const isLoggedIn = <?php echo isset($_SESSION['user_codigo']) ? 'true' : 'false'; ?>; 
    
    // Variáveis para os Listeners
    const detailsContent = document.getElementById('product-details-content');
    const paginationContainer = document.querySelector('.pagination'); // Para Paginação
    const filterContainer = document.querySelector('.filter-bar'); // Seleciona o contêiner da barra de filtros
    const adminLink = document.getElementById('admin-link'); // Para o Admin
    const cartLink = document.getElementById('cart-link'); // Já existia

    // Função global para mostrar o preloader
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
    // --- LISTENERS DE AÇÃO QUE CAUSAM REDIRECIONAMENTO ---
    
    // LISTENER 1: Formulário de Busca (Estático)
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', showPreloader);
    }
    
    // LISTENER 2: Adicionar ao Carrinho (DELEGAÇÃO para formulário DINÂMICO no Modal)
    detailsContent.addEventListener('submit', function(e) {
        if (e.target.closest('form')) {
            showPreloader();
        }
    });

    // LISTENER 3: Ativar ao clicar no link de Sair (logout.php)
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', showPreloader);
    }
    
    // LISTENER 4: Ativar ao clicar no link do Carrinho (se estiver logado)
    if (cartLink && isLoggedIn) {
        cartLink.addEventListener('click', showPreloader);
    }

    // LISTENER 5 (NOVO): Ativar ao clicar nos links de Paginação
    if (paginationContainer) {
        paginationContainer.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' && e.target.classList.contains('pagination-link')) {
                showPreloader();
            }
        });
    }

    // LISTENER 6 (CORRIGIDO): Ativar ao clicar nos Filtros/Categorias
    // Seleciona TODOS os elementos com a classe .category-btn e aplica o listener a cada um
    const filterButtons = document.querySelectorAll('.category-btn'); 
    filterButtons.forEach(btn => {
        btn.addEventListener('click', showPreloader);
    });

    // LISTENER 7 (NOVO): Ativar ao clicar no link do Painel de Admin
    const adminBtn = document.querySelector('.admin-btn');
    if (adminBtn) {
        adminBtn.addEventListener('click', showPreloader);
    }

    const login2Btn = document.querySelector('.login-btn-modal');
    if (login2Btn) {
        login2Btn.addEventListener('click', showPreloader);
    }

    // LISTENER 8: Ativar ao clicar no link de Logar (login.php)
    const loginBtn = document.querySelector('.login-btn');
    if (loginBtn) {
        loginBtn.addEventListener('click', showPreloader);
    }

    // FIM DO BLOCO PRELOADER. O RESTANTE DO SEU CÓDIGO CONTINUA ABAIXO.

    // Scripts do CEP (Suas variáveis originais, agora funcionais)
    const cepTrigger = document.getElementById('cep-trigger');
    const cepModal = document.getElementById('cep-modal');
    const closeCepModalBtn = cepModal.querySelector('.close-button');
    const closeSecondaryBtn = document.getElementById('close-modal-btn');
    const cepInput = document.getElementById('cep-input');
    const buscarCepBtn = document.getElementById('buscar-cep-btn');
    const detectarCepBtn = document.getElementById('detectar-cep-btn');
    const cepManualStatus = document.getElementById('cep-manual-status');
    const cepAutoStatus = document.getElementById('cep-auto-status');
    
    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    const savedCep = getCookie('cep_usuario');
    if (savedCep) {
        cepTrigger.innerHTML = '<img src="cep.png" alt="CEP" height="20" width="20"/> CEP: ' + savedCep;
    } else {
        cepTrigger.innerHTML = '<img src="cep.png" alt="CEP" height="20" width="20"/> CEP: Não definido';
    }


    function openCepModal() {
        cepModal.style.display = 'flex';
        cepInput.value = '';
        cepManualStatus.style.display = 'none';
        cepAutoStatus.style.display = 'none';
    }

    function closeCepModal() {
        cepModal.style.display = 'none';
    }

    function setCepAndReload(cep, origem = 'manual') {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'loja.php';

        const inputCep = document.createElement('input');
        inputCep.type = 'hidden';
        inputCep.name = 'cep';
        inputCep.value = cep;
        form.appendChild(inputCep);

        const inputOrigem = document.createElement('input');
        inputOrigem.type = 'hidden';
        inputOrigem.name = 'origem';
        inputOrigem.value = origem;
        form.appendChild(inputOrigem);

        // A chamada correta: ativa o preloader antes do envio!
        showPreloader(); 

        document.body.appendChild(form);
        form.submit();
    }

    // AQUI ESTÃO OS LISTENERS DO CEP E MODAIS (AGORA FUNCIONAIS)
    cepTrigger.addEventListener('click', openCepModal);
    closeCepModalBtn.addEventListener('click', closeCepModal);
    closeSecondaryBtn.addEventListener('click', closeCepModal);
    window.addEventListener('click', function(event) {
        if (event.target === cepModal) {
            closeCepModal();
        }
    });

    cepInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 5) {
            value = value.substring(0, 5) + '-' + value.substring(5, 8);
        }
        e.target.value = value;
    });

    function buscarCepPorInput(cep) {
        const cleanCep = cep.replace(/\D/g, '');
        if (cleanCep.length !== 8) {
            exibirStatus(cepManualStatus, 'Por favor, digite um CEP válido com 8 dígitos.', 'error');
            return;
        }
        exibirStatus(cepManualStatus, 'Buscando CEP...', 'info');
        fetch('https://viacep.com.br/ws/' + cleanCep + '/json/')
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    exibirStatus(cepManualStatus, 'CEP não encontrado. Por favor, tente novamente.', 'error');
                } else {
                    const endereco = data.logradouro + ', ' + data.bairro + ', ' + data.localidade + ' - ' + data.uf;
                    exibirStatus(cepManualStatus, 'Endereço: ' + endereco, 'success');
                    setCepAndReload(data.cep);
                }
            })
            .catch(error => {
                exibirStatus(cepManualStatus, 'Erro ao buscar o CEP. Tente novamente mais tarde.', 'error');
                console.error('Erro na requisição da ViaCEP:', error);
            });
    }

    function buscarEnderecoPorLatLong(latitude, longitude) {
        exibirStatus(cepAutoStatus, 'Detectando sua localização...', 'info');
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + latitude + '&lon=' + longitude + '&zoom=18&addressdetails=1')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na requisição da API Nominatim');
                }
                return response.json();
            })
            .then(data => {
                const address = data.address;
                if (address && address.postcode) {
                    const cepEncontrado = address.postcode;
                    const cidade = address.city;
                    const estado = address.state;
                    exibirStatus(cepAutoStatus, 'Sua localização foi detectada! Cidade: ' + cidade + ', Estado: ' + estado + '. CEP: ' + cepEncontrado, 'success');
                    setCepAndReload(cepEncontrado, 'auto');
                } else {
                    exibirStatus(cepAutoStatus, 'Não foi possível encontrar um CEP para sua localização. Por favor, digite manualmente.', 'error');
                }
            })
            .catch(error => {
                exibirStatus(cepAutoStatus, 'Erro ao buscar a localização. Tente novamente mais tarde.', 'error');
                console.error('Erro na requisição da Nominatim:', error);
            });
    }

    function exibirStatus(elemento, mensagem, tipo) {
        elemento.innerHTML = mensagem;
        elemento.style.display = 'block';
        elemento.className = 'status-message ' + tipo;
    }

    buscarCepBtn.addEventListener('click', function() {
        buscarCepPorInput(cepInput.value);
    });

    cepInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            buscarCepPorInput(cepInput.value);
        }
    });

    detectarCepBtn.addEventListener('click', function() {
        if (navigator.geolocation) {
            exibirStatus(cepAutoStatus, 'Detectando sua localização...', 'info');
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                buscarEnderecoPorLatLong(latitude, longitude);
            }, function(error) {
                exibirStatus(cepAutoStatus, 'Não foi possível detectar sua localização. Permissão negada ou erro.', 'error');
                console.error('Erro de geolocalização:', error);
            });
        } else {
            exibirStatus(cepAutoStatus, 'A geolocalização não é suportada por este navegador.', 'error');
        }
    });

    // -------- CÓDIGO DA JANELA DE DETALHES --------
    const productGrid = document.querySelector('.product-grid');
    const detailsModal = document.getElementById('product-details-modal');
    // detailsContent foi definido no topo
    const closeDetailsBtn = document.querySelector('.close-details-btn');
    const loginRequiredModal = document.getElementById('login-required-modal');
    const closeLoginPopupBtn = document.getElementById('close-login-popup');
    

    productGrid.addEventListener('click', function(e) {
        const detailsButton = e.target.closest('.open-details-btn');

        if (detailsButton) {
            e.preventDefault(); 
            const productWrapper = detailsButton.closest('.product_wrapper');
            const codigo = productWrapper.getAttribute('data-codigo');
            
            detailsContent.innerHTML = '<div class="loading-placeholder">Carregando detalhes...</div>';
            detailsModal.style.display = 'flex';

            fetch('loja.php?action=get_product_details&codigo=' + codigo)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        detailsContent.innerHTML = '<div class="error-placeholder">Erro ao carregar detalhes.</div>';
                        console.error('Erro ao buscar detalhes:', data.error);
                        return;
                    }
                    
                    let priceHtml = '';
                    if (data.desconto_porcentagem) {
                        priceHtml = 
                            '<div class="price-with-discount">' +
                                '<span class="price-original">R$ ' + parseFloat(data.preco_original).toFixed(2).replace('.', ',') + '</span>' +
                                '<span class="price-discounted">R$ ' + parseFloat(data.preco_desconto).toFixed(2).replace('.', ',') + '</span>' +
                                '<div class="discount-info">' +
                                    '<p>Você tem <strong>' + data.desconto_porcentagem + '% de desconto</strong> para este CEP!</p>' +
                                '</div>' +
                            '</div>';
                    } else {
                        priceHtml = '<div class="details-price">R$ ' + parseFloat(data.preco).toFixed(2).replace('.', ',') + '</div>';
                    }
                    
                    // O form de Adicionar ao Carrinho agora ativará o Preloader via listener de delegação (feito no topo)
                    const addToCartButtonHtml = isLoggedIn 
                        ? '<form method="post" action="loja.php">' +
                            '<input type="hidden" name="codigo_add_cart" value="' + data.codigo + '">' +
                            '<button type="submit" class="add-to-cart-details">Adicionar ao Carrinho</button>' +
                          '</form>'
                        : '<button type="button" class="add-to-cart-details login-required-btn">Adicionar ao Carrinho</button>';

                    detailsContent.innerHTML = 
                        '<div class="details-content-wrapper">' +
                            '<div class="details-image-container">' +
                                '<img src="fotos/' + data.imagem + '" alt="' + data.nome + '" class="details-image">' +
                            '</div>' +
                            '<div class="details-info">' +
                                '<h3>' + data.nome + '</h3>' +
                                '<p class="details-description">' + data.descricao + '</p>' +
                                '<div class="details-availability">' +
                                    '<p><strong>Estoque:</strong> ' + (data.estoque > 0 ? 'Disponível' : 'Esgotado') + '</p>' +
                                    '<p class="details-frete"><strong>Frete:</strong> ' + data.frete_status + '</p>' +
                                '</div>' +
                                priceHtml +
                                addToCartButtonHtml +
                            '</div>' +
                        '</div>';

                    const newLoginRequiredBtn = detailsContent.querySelector('.login-required-btn');
                    if (newLoginRequiredBtn) {
                        newLoginRequiredBtn.addEventListener('click', function() {
                            detailsModal.style.display = 'none';
                            loginRequiredModal.style.display = 'flex';
                        });
                    }
                })
                .catch(error => {
                    detailsContent.innerHTML = '<div class="error-placeholder">Erro ao carregar detalhes.</div>';
                    console.error('Erro na requisição AJAX:', error);
                });
        }
    });

    closeDetailsBtn.addEventListener('click', function() {
        detailsModal.style.display = 'none';
    });

    closeLoginPopupBtn.addEventListener('click', function() {
        loginRequiredModal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === detailsModal) {
            detailsModal.style.display = 'none';
        }
        if (event.target === loginRequiredModal) {
            loginRequiredModal.style.display = 'none';
        }
    });
    
    // CORREÇÃO: Clique no carrinho (apenas garante que o preloader não foi chamado duas vezes)
    cartLink.addEventListener('click', function(e) {
        if (!isLoggedIn) {
            e.preventDefault();
            loginRequiredModal.style.display = 'flex';
        } else {
            window.location.href = 'carrinho.php';
        }
    });
    
    // -------- CÓDIGO DO SLIDESHOW --------
    const slides = document.querySelectorAll('.slideshow .slide');
    let currentSlide = 0;
    
    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
        });
        slides[index].classList.add('active');
    }
    
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }
    
    setInterval(nextSlide, 4500); // Troca de slide a cada 4,5 segundos
    
    showSlide(currentSlide); // Inicia o slideshow
});
</script>
<div id="preloader" class="preloader-overlay" style="display: none;">
    <div class="spinner-border"></div>
</div>
</body>
</html>