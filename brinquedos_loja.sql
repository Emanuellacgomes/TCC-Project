-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tempo de Geração: 06/11/2025 às 21h08min
-- Versão do Servidor: 5.5.20
-- Versão do PHP: 5.3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de Dados: `brinquedos_loja`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `brinquedos`
--

CREATE TABLE IF NOT EXISTS `brinquedos` (
  `codigo` int(5) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descricao` text NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `estoque` int(3) NOT NULL DEFAULT '0',
  `imagem` varchar(100) NOT NULL,
  `codcategoria` int(5) NOT NULL,
  `codfabricante` int(5) NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `codcategoria` (`codcategoria`),
  KEY `codfabricante` (`codfabricante`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=42 ;

--
-- Extraindo dados da tabela `brinquedos`
--

INSERT INTO `brinquedos` (`codigo`, `nome`, `descricao`, `preco`, `estoque`, `imagem`, `codcategoria`, `codfabricante`) VALUES
(4, 'Certo ou Errado?', 'O jogo "Certo ou Errado" da Estrela é um jogo de tabuleiro que desafia os jogadores a identificar se uma afirmação é verdadeira ou falsa. Ele pode ser jogado por 2 a 5 pessoas e é recomendado para maiores de 7 anos. O objetivo é avançar pelo tabuleiro respondendo corretamente às perguntas, com o jogador que chega ao final primeiro sendo o vencedor. O jogo é uma forma divertida e educativa de aprender curiosidades e fatos interessantes. ', '100.00', 3, '14431b5db842e1372a1b6ed7ae72570awebp', 7, 2),
(5, 'Super Banco Imobiliário', 'O Jogo Banco Imobiliário permite que você invista muito bem o seu dinheiro em novas propriedades! Procura um jogo divertido para jogar com amigos e familiares? Esse modelo é perfeito, garante muito entretenimento e estimula o raciocínio lógico de todos!', '163.99', 33, '717786f0517dccdbb849dab6dd9e2758webp', 7, 2),
(6, 'Pista de Carrinhos', 'Pista de carrinhos com looping e dois carrinhos de metal.', '89.90', 10, 'pista', 6, 5),
(7, 'Boneca Clássica', 'Uma boneca com roupas de princesa.', '45.00', 25, 'boneca', 1, 4),
(8, 'LEGO Casa na Árvore', 'Kit de construção LEGO com 500 peças para montar uma casa na árvore.', '250.00', 6, 'lego', 3, 5),
(9, 'Jogo de Dominó', 'Jogo de dominó clássico com 28 peças de madeira.', '35.00', 50, 'domino', 7, 3),
(10, 'Livro Sensorial', 'Livro de pano para bebês, com texturas e cores para estimular os sentidos.', '60.00', 30, 'livro_sensorial', 8, 2),
(11, 'Pelúcia Ori', 'Uma pelúcia do amado personagem Ori da Franquia Ori and the Blind Forest perfeita para abraços!', '899.99', 0, 'da98027752910c871c7a398149ba806dwebp', 4, 1),
(12, 'Colecionável Exclusivo - Angel - Lilo & Stich', 'Leve para casa toda a diversão e travessuras do Stitch com essa Miniatura Colecionável Oficial! Inspirada no adorável experimento 626 da Disney, essa peça traz detalhes incríveis que capturam sua personalidade única. - Design autêntico: Expressão fiel e acabamento impecável para um visual encantador. - Tamanho ideal: Compacta e perfeita para decorar sua estante, mesa ou coleção. - Presente perfeito: Para fãs de Lilo & Stitch, colecionadores da Disney ou qualquer um apaixonado por esse alienígena travesso! Adote o Stitch e leve um toque de aventura e fofura para o seu dia a dia!', '39.99', 27, '170b47ee710e8b491f727804803bd67fwebp', 5, 3),
(13, 'Boneca Articulada e Acessórios - Wicked - Elphaba', 'Não há ninguém como Elphaba! Inspirada em Wicked da Universal Pictures, a boneca fashion Elphaba captura o coração da irmandade do filme. Vestida com sua moda removível fiel ao filme, mochila escolar e chapéu característico, Elphaba pode fazer poses dinâmicas com flexibilidade no tronco, cotovelos, pulsos e joelhos. Elphaba também apresenta seu visual icônico com detalhes fiéis ao filme, como seu cabelo longo e trançado e pele verde. Os fãs vão querer colecionar todas as bonecas da terra de Oz! Cada uma vendida separadamente, sujeita à disponibilidade. As bonecas não ficam em pé sozinhas. As cores e decorações podem variar.', '314.99', 0, 'af4e967fba984becfec5588eae176e28webp', 2, 4),
(14, 'Jogo Eu Sou? - Novas Cartas e App - Estrela', 'Descrição do produto\r\nJogo Eu Sou? da Estrela! Eu sou... verde? Eu sou... um animal? Descubra qual é a imagem da sua carta fazendo perguntas para os outros jogadores! Eu Sou? é um jogo em que cada jogador sorteia um carta que possui uma imagem, e deve fazer perguntas aos outros jogadores para tentar descobrir qual é a imagem mostrada na carta. A carta de cada jogador é colocada sobre sua cabeça, presa a um acessório que permite que somente os outros jogadores vejam qual é a imagem mostrada. Eu Sou? é um jogo rápido, divertido e interativo, que diverte as crianças e também os adultos que jogam com elas! Eu sou? é um jogo para 2 a 4 jogadores, recomendado a partir de 6 anos e com duração média de 30 minutos. Contém na embalagem: 1 ampulheta, 30 cartas, 4 cintas para cartas e 1 manual de instruções. As imagens são meramente ilustrativas, podendo sofrer alterações de cores na embalagem', '99.99', 14, '331ead5e2183221ef98fffbb540acc00webp', 7, 2),
(15, 'Pista De Brinquedo - Hot Wheels - Sprint - Mattel', 'Você pode simular emoção de uma corrida de Fórmula 1 com o conjunto pista de corridas Hot Wheels Racing Sprint. Ele inclui três carros de Fórmula 1 die-cast em escala 1:64 com decorações que combinam com as equipes favoritas dos fãs - McLaren Formula 1 Team, BWT Alpine F1 Team e Visa Cash App RB Formula One Team - e duas maneiras de brincar. No modo corrida, você pode carregar os veículos no lançador e acionar a alavanca para colocá-los em alta velocidade na pista. Será que haverá uma incrível mudança de liderança quando os carros forem desviados para a pista de ultrapassagem? No modo infinito, você pode pressionar a alavanca da bomba para manter os carros correndo ao redor do conjunto, evitando os desviadores. As peças da pista são compatíveis com a pista Speed Snap para corridas e acrobacias ampliadas. Pista e conjuntos adicionais vendidos separadamente. As cores e as decorações podem variar.', '699.99', 34, 'f59c43f453d1b0f356b526a675e159a2webp', 2, 4),
(16, 'Chocalho - Descobrindo com Diversão - Carangueijo ', 'Pequeno no tamanho, gigante na diversão! O Caranguejo Chocalho da MINIMI é o companheiro perfeito para entreter e estimular os sentidos do seu bebê. Com cores vibrantes, texturas divertidas e um som suave de chocalho, ele desperta a curiosidade e incentiva a coordenação motora desde os primeiros meses de vida. Suas patinhas flexíveis são ideais para os pequenos explorarem com as mãos, enquanto o chocalho interno produz um som delicado a cada movimento, ajudando no desenvolvimento auditivo. Leve e de fácil pegada, o Caranguejo Chocalho foi projetado para as mãozinhas curiosas que adoram agarrar, sacudir e descobrir novas sensações. Seguro, resistente e feito com materiais de alta qualidade, esse brinquedo é ideal para bebês em fase de exploração, tornando cada momento uma aventura cheia de cores, sons e descobertas! Características principais: - Formato de caranguejo com design divertido e cores vibrantes - Chocalho interno com som suave para estímulo auditivo - Patinhas flexíveis e texturizadas para explorar o tato - Leve e fácil de segurar, perfeito para mãos pequenas - Material seguro e resistente, ideal para bebês Deixe o seu bebê mergulhar nessa experiência sensorial com o Caranguejo Chocalho da MINIMI – um brinquedo encantador que diverte enquanto estimula o desenvolvimento!', '29.99', 56, 'e6192757a743f626fc0515764f8df3e0webp', 1, 7),
(17, 'Boneca Barbie Fashionista com Closet de Roupas e A', 'O playset Barbie Ultimate Closet inclui bonecas, roupas e acessórios Barbie e tem estilo por dentro e por fora\r\nO armário tem um exterior roxo com um interior rosa e está decorado com portas duplas claras para um vislumbre do guarda roupa da boneca Barbie\r\nUm rack dobrável é para pendurar roupas e é útil para mudanças no estilo de vestir. Seis cabides também estão incluídos.\r\nA boneca Barbie usa um vestido listrado e tem dois vestidos adicionais para trocar de roupa.\r\nTrês pares de sapatos, dois colares e duas bolsas criam olhares diferentes em um instante\r\nUma alça de transporte facilita a zenagem ou a viagem\r\nMisture e combine para jogar moda e diversão de estilo', '1003.49', 12, '6b6cb8be7642de446ed119825054845dwebp', 0, 4),
(18, 'Boneca E Acessórios - Baby Alive - Swimmer - Roxo ', 'AVENTURAS AQUÁTICAS: Com a boneca Baby Alive Sunny Swimmer para meninos e meninas de 3 anos, as crianças vão se divertir com aventuras na água; ÓCULOS E NADADEIRA: A boneca inclui óculos e 2 nadadeiras para muita diversão na água; BRINCAR DENTRO E FORA D''ÁGUA: Linda boneca para brincadeiras divertidas na água para meninos e meninas a partir dos 3 anos: Boneca articulada com uma linda roupa de banho com estampas de criaturas marinhas para brincadeiras dentro e fora d''água; EXCELENTE PRESENTE PARA MENINOS E MENINAS: Estas bonecas aquáticas Baby Alive com acessórios são excelentes presentes de fim de ano ou de aniversário para crianças (Vendidos separadamente, sujeitos à disponibilidade.) A boneca Baby Alive Sunny Swimmer está pronta para muitas aventuras aquáticas. Esta boneca pra brincar na água é perfeita para meninos e meninas. Vem com óculos e 2 nadadeiras. A boneca articulada vem com uma linda roupa de banho com estampas de criaturas marinhas. É uma boneca divertida feita para brincar na água. Ideal para meninos e meninas a partir dos 3 anos. Estas bonecas aquáticas Baby Alive com acessórios são excelentes presentes de fim de ano ou de aniversário para crianças. (Vendidos separadamente, sujeitos à disponibilidade.)', '99.99', 134, '74066cbaa93e44ccbf74dc7cd64c30bawebp', 0, 5),
(19, 'Boneca Minnie Patinadora com Sons - Elka - Disney', 'A Minnie adora patinar e passear com o Mickey! Aperte a estrelinha e ela fala frases. A Minnie tem patins e capacete para brincar e se divertir!', '149.99', 19, 'c63bae5ebdbe823bcd363d775f361e7dwebp', 0, 8),
(20, 'LEGO Creator - Unicórnio Mágico 3 em 1 - 31140', 'Viaje para uma terra mítica distante para horas de diversão com estas criaturas lindas. Assista ao Unicórnio Mágico com seu chifre dourado cavalgar pela paisagem de arco-íris. Para ainda mais diversão encantadora, você pode reconstruir o modelo em um cavalo marinho nadando no mar ou transformá-lo em um pavão fofinho com uma cauda cheia de penas coloridas. A escolha é sua com este conjunto 3 em 1.', '99.99', 43, 'ad02ff8cde1be99ff346a501fbc66db5webp', 3, 6),
(21, 'Ori and The Blind Forest: Estátua de PVC', 'Arte inspirada: captura a essência da arte de capa icônica de Ori e a Floresta Cega, com Naru segurando Ori ternamente em um tronco caído\r\nVariação noturna: apresenta um esquema de cores escuras exclusivo para Naru e o tronco, perfeito para fãs que preferem a estética noturna do jogo\r\nFunções de iluminação LED: o brilho da Ori ganha vida com iluminação LED estática e animada na edição definitiva\r\nBase de resina detalhada: inclui detalhes expandidos como frutas, plantas e raízes de árvores espirituais, pintadas para combinar com o esquema de cores noturno\r\nPerfeito para decoração: adiciona um toque de capricho e calor à casa de qualquer fã, tornando-a uma peça decorativa ideal', '397.84', 6, '9d714d1625de26b13570b4cbcde92564', 3, 1),
(22, 'Boneca Lego ', 'Uma Boneca feita de pecinhas lego para você montar em casa!', '120.99', 2, '836335a3be0234a9d199cad991bd9ef3', 0, 6),
(23, 'Hora do Banho - Disney Baby - 14 Peças - Toyster', 'A linha BDA foi desenvolvida pensando no desenvolvimento e crescimento do seu bebê. São quatorze peças de espuma macia que, quando estão molhadas, grudam no azulejo. É uma forma divertida de conhecer os personagens da Disney na Hora do Banho.', '79.99', 34, 'a99af41c13f7e2018b7497c10cd69a96webp', 8, 9),
(24, 'Jogo Educativo Pega Pompom Disney Pixar', 'As crianças amam pompons coloridos!! E não há nada mais divertido do que desenvolver habilidades brincado!! Atividade que desenvolve a coordenação motora fina, facilitando a prática da escrita, desenho e outras tarefas. Trabalha a concentração, reconhecimento de cores e criatividade.\r\nContém:56 pompons coloridos, 02 pinças plásticas e 04 placas de madeira dos personagens queridinhos dos filmes Disney Pixar.', '78.90', 12, '82f8477429d62757499988f1b76f0b8ewebp', 8, 9),
(25, '  Piano e Xilofone - Disney Baby - Mickey e Amigos', 'O Piano Xilofone acompanha 2 baquetas para auxiliar na descoberta de novos sons • Acompanha alça na parte inferior para facilitar no transporte, fácil de carregar • As teclas do piano são macias e reproduzem sons vibrantes, atraindo a atenção do bebê • Auxilia no desenvolvimento de habilidades motoras, visuais, auditivas e imaginativa', '62.99', 0, '2c3e4088a8d96deece5c11c644d18885webp', 8, 9),
(26, 'Telefone Sonoro - Disney - Minnie - Elka', 'A alegria estará presente na sua brincadeira com o Telefone Sonoro - Disney - Minnie da Elka! Esse incrível telefone sonoro conta com frases da Minnie que vão deixar sua brincadeira muito mais divertida. Brinque com o fone, aperte as teclas e o botão com frases da sua personagem predileta da Disney e divirta. As meninas vão adorar brincar com o Telefone Sonoro - Disney - Minnie da Elka! Fabricado em plástico atóxico com a qualidade Elka, e é alimentado por 3 baterias LR 41 (inclusas para teste).', '119.99', 29, '9f76b5acaa9bcc25204409c96ede3b17webp', 1, 8),
(27, 'Brinquedo Educativo - Meu primeiro Smartphone - So', 'Chegou o telefone perfeito para os pequenos exploradores! Com o Descobrindo com Diversão - Meu Primeiro Smartphone da MINIMI, as crianças podem brincar, ouvir sons divertidos e interagir como se tivessem um celular de verdade. Com botões interativos, luzes coloridas e efeitos sonoros animados, este brinquedo estimula a curiosidade e o desenvolvimento sensorial, ajudando os pequenos a descobrirem novas cores, sons e até mesmo os primeiros números. Cada toque é uma nova descoberta, tornando o aprendizado mais envolvente e cheio de diversão! Compacto, seguro e fácil de segurar, o Meu Primeiro Smartphone é ideal para estimular a imaginação das crianças, incentivando o faz de conta e momentos de brincadeira criativa. Características principais: - Design colorido e interativo, perfeito para mãos pequenas - Sons divertidos que incentivam a exploração e o aprendizado - Seguro e resistente para horas de diversão sem preocupações Seu pequeno vai adorar ter o próprio smartphone para brincar e aprender! Cada embalagem contém produtos que podem variar a cor e o modelo. Atenção: O produto é sortido, as cores e os modelos podem variar de acordo com estoque! Não é possível escolher a cor ou modelo do produto. EMBALAGEM UNITÁRIA. IMPORTANTE: Ao Realizar a compra no site e retirar o produto na loja, será entregue o item que estiver disponível no estoque.', '109.99', 12, '96754cc6a3f1c5189a32512ce1d5b895webp', 1, 7),
(28, 'Girafa com Blocos Fisher-Price', 'Girafa com Blocos da Fisher-Price.\r\nesta Girafa Vem com Quatro Blocos, Cada Qual com Uma Animação Diferente.\r\na Brincadeira Começa Quando o Bebê Coloca Os Blocos Na Cabeça da Girafa e Eles Vão Descendo Até o Final.\r\nna Medida em Que Os Blocos Vão Passando Pelas "janelas", o Bebê Ouve Sons e Efeitos Divertidos.\r\no Produto Desenvolve a Percepção Visual, Auditiva Musical, Tátil, Relação de Causa-Efeito e Criatividade.\r\ntodos Podem Ser Guardados Dentro da Girafa Quando a Brincadeira Acabar.', '199.99', 4, '7cc0a36a6246ad7c0ce6f7b601d111c4webp', 1, 9),
(29, 'Playset - Escola Super Hero High - DC Super Hero G', 'Você está preparada para entrar no universo de DC Super Hero Girls e viver grandes aventuras em DC Super Hero High? Com o Playset - Escola Super Hero High - DC Super Hero Girls da Mattel vai ser possível criar histórias incríveis, participar de treinamentos, e recriar cenas do seriado dessas heroínas que são um arrazo! O Playset conta com mecanismos de voo, gancho e corda. Os acessórios temáticos aumento as oportunidades de brincadeira no ambiente clássico do colégio. Vem acompanhado de uma boneca de aproximadamente 15 centímetros de altura, de uma super-heroína de DC Super Hero Girls. E também é compatível com as demais bonecas desse tamanho (vendidas separadamente).', '479.99', 6, 'aea3e2d79e525c7cae0e27c8435ed650webp', 6, 4),
(30, 'Avião Musical - Descobrindo com Diversão - Minimi', 'Prepare-se para uma viagem incrível pelo mundo da imaginação com o Avião Musical Minimi! Criado para estimular os sentidos e o aprendizado dos pequenos, esse aviãozinho encantador combina luzes, sons e movimento para tornar cada brincadeira uma nova descoberta. Com um design colorido e botões interativos, o Avião Musical Minimi ajuda no desenvolvimento da coordenação motora e da percepção auditiva, enquanto as melodias animadas tornam a experiência ainda mais divertida. Basta um toque para ouvir músicas alegres e ver o avião ganhar vida, incentivando a curiosidade e a criatividade das crianças. Perfeito para os primeiros anos de vida, esse brinquedo educativo transforma cada brincadeira em um momento de aprendizado. Seu pequeno piloto vai adorar explorar novas aventuras com o Avião Musical Minimi! Características principais: - Design interativo e colorido - Músicas e sons envolventes para estimular a audição - Luzes vibrantes que despertam a atenção dos pequenos - Botões fáceis de apertar, desenvolvendo a coordenação motora Decole rumo à diversão e ao aprendizado com o Avião Musical Minimi – onde cada brincadeira é uma nova aventura!', '249.99', 8, '8b52ccec62cf210c22332dd592d85041webp', 2, 7),
(31, 'Skate de Dedo Tech Deck - Vert Wall 2.0 - Sunny', 'Skate de Dedo Tech Deck - Vert Wall 2.0 da Sunny.\r\naperfeiçoe Suas Habilidades de Skate com o Conjunto de Rampa Tech Deck Vert Wall 2.0 X-Connect Park Creator. Isso Inclui Tudo o Que Você Precisa Para Construir Um Incrível Parque Personalizado. Configure Diferentes Combinações com As Rampas Vert, Wedge Ramp e Sub-Rail Para Aprender e Melhorar Suas Manobras.\r\nesculpe a Parede Vertical, Deslize o Coping, Esmerilhe o Trilho ou Configure e Personalize o Local da Maneira Que Desejar. Ande de Skate No Parque com o Exclusivo Braço Sk8mafia Skateboards Pro Incluído e Adicione à Sua Coleção de Mini Skates. Combine o Parque com Outras Rampas Compatíveis com X-Connect Para Construir Os Obstáculos Finais Para Você Destruir.', '249.99', 5, '830ba0af67790b5a2b2830addfd77338webp', 6, 10),
(32, 'Skate de Dedo Tech Deck Pack com 6 Sortido - Sunny', 'Tech deck - kit 3 skate de dedo com acessórios coleção baker - sunny 2892O tech deck traz para você o verdadeiro negócio com autênticos fingerboards de 96mm de verdadeiras empresas de skateCada escala de 96 mm inclui uma prancha com um incrível design de skate de uma marca de skate icônica.Há uma tonelada de decks para colecionarContém 3 skate de 96mm, 3 shape bonus mais acessórios.', '299.90', 1, 'f040ac0eeeb5de6ea5c04b000a8c5d9dwebp', 6, 10),
(33, 'Flat Ball', 'Flat Ball é um super disco high-tech que desliza com a força do ar! Jogar bola de mesa ou de chão dentro de casa sem quebrar nada é possível. Flat Ball desliza coma força do ar no piso frio, madeira ou carpete! O material macio utilizado na lateral do disco protege móveis e paredes. Invente o seu jogo, você pode jogar futebol, boliche, hockey e muito mais! Alimentado por 4 pilhas AA não inclusas.', '129.99', 9, '68ee561fe7a661601bc3e7fc8ca3d7f7webp', 6, 10),
(34, 'Skate Dedo Com Obstáculo e Card Disorder Tech Deck', 'Skate Dedo Com Obstáculo e Card Disorder Tech Deck - Sunny   Informações do Produto: O skate de dedo com obstáculo é ideal para os amantes do skate e aqueles que procuram um desafio único, o skate de dedo tech deck é ideal para competições e brincadeiras. Mostre suas habilidades sem ter que sair de casa. Crie manobras incríveis enquanto desenvolve controle e agilidade. Recomendado para crianças maiores de 6 anos de idade.   Itens inclusos: 2 skates, 4 cards, 1 obstáculo. Dimensões aproximadas do produto: 9,5cm x 2,8cm. Recomendado: Não recomendado para menores de 6 anos de idade. Alimentação: Não necessita. Material composição: Plástico. Certificação: Registro inmetro 006839/2021. Ocp 0006. Garantia do Fabricante: 03 meses contra defeitos de fabricação.   Aviso: Todas as informações divulgadas são de responsabilidade exclusiva do fabricante/fornecedor. As cores dos tecidos ou dos produtos podem variar entre as imagens mostradas acima ou fotos da embalagem. Imagens meramente ilustrativas.', '169.90', 92, '7cb15982b206094f9c03cee0f77af84cwebp', 6, 10),
(35, 'Skate Barbie - Sem Acessórios - Sortido', 'As meninas radicais se divertirão muito com o Skate da Barbie, onde farão manobras iradas. O estiloso Skate é ideal para crianças radicais e que adoram aventuras. Este item não contém acessórios. Utilizar com equipamento de proteção. Recomenda-se a compra de acessórios de segurança para uso profissional ou adequados. Não utilizar o produto em vias públicas. É recomendada a supervisão de um adulto responsável. Produto projetado para suportar a carga máxima de 50 kg. Composição: Madeira, Base de alumínio e rodas de PVC. Para evitar o perigo de asfixia, manter a bag longe do alcance das crianças. Cada embalagem contém apenas 1 skate (unitário) que pode variar a cor e o modelo. Atenção: O produto é sortido, as cores e os modelos podem variar de acordo com estoque! Não é possível escolher a cor ou modelo do produto. EMBALAGEM UNITÁRIA. IMPORTANTE: Ao Realizar a compra no site e retirar o produto na loja, será entregue o item que estiver disponível no estoque.', '279.99', 43, '8037bb81f0c5aa80711f7d6ec3c58642webp', 6, 4),
(36, 'Brinquedo Clássico - Pogobol - Amarelo e Laranja -', 'Descrição do produto\r\nMais um Clássico da Estrela está de volta, o divertido Pogobol! Você vai se divertir e de quebra gastar uma boa dose de energia brincando! Para brincar, é só apoiar os pés sobre o disco, empurrar o disco para baixo com os pés e começar a pular. O Pogobol pode ser usado por crianças acima de 05 anos, pesando até 70 kg.', '229.99', 21, '885fb4f5617ba1f8dba2ec73e38ecdfdwebp', 6, 2),
(37, 'Brinquedo Clássico - Pogobol - Roxo e Verde - Estr', 'Descrição do produto\r\nMais um Clássico da Estrela está de volta, o divertido Pogobol! Você vai se divertir e de quebra gastar uma boa dose de energia brincando! Para brincar, é só apoiar os pés sobre o disco, empurrar o disco para baixo com os pés e começar a pular. O Pogobol pode ser usado por crianças acima de 05 anos, pesando até 70 kg.', '229.99', 32, '10d6cc52957eefade7793209b5b2a2eewebp', 6, 2),
(38, 'Pogobol - Roxo e Amarelo - Estrela', '\r\nO divertido Pogobol da Estrela voltou! Você vai se divertir e de quebra gastar uma boa dose de energia brincando! Você vai pular muito com esse brinquedo!', '229.99', 0, 'a5638a07d4e14c81274b31971ea40cf1webp', 6, 2),
(39, 'Toalha Social - Barbie Esportes - Mattel', 'A diversão é garantida em todas as ideias com esse Toalha Social!', '249.99', 0, '6ccef81d641ee420067774dc7e36bab9webp', 6, 4),
(40, 'Hot Wheels Patinete Max Turbo Com Fumaça - Fun Div', 'Patinete com Fumaça da Fun Divirta-se é tudo isso e muito mais! À primeira vista parece um patinete comum com 3 rodas, desenhado para conferir mais estabilidade e equilíbrio ao passeio das crianças, mas não se deixe enganar! Aperte seus pequenos botões localizados na robusta prancha e veja o que acontece! Radicais faróis frontais se acendem, conferindo ao patinete um estilo único. Aperte mais um botão para acionar o irado modo turbo e suas turbinas traseiras vão soltar fumaça e piscar em diferentes cores. A fumaça é feita com a adição de água em um compartimento escondido. E não fica por ai! Dê impulso e suas rodas também se acenderão com luzes piscantes que param só quando o patinete está imóvel. Incrível, não é?! É o seu passeio mais radical! O guidão ainda tem regulagem de altura e pode ser dobrado para caber em qualquer lugar. Recarregável via USB. Idade recomendada: acima de 5 anos. Selo INMETRO: INNAC 02115-000.', '299.99', 0, '76fcc981d856984673fa37a4439119b5webp', 6, 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `carrinho_usuario`
--

CREATE TABLE IF NOT EXISTS `carrinho_usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `codigo_brinquedo` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_adicao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_usuario` (`id_usuario`,`codigo_brinquedo`),
  KEY `codigo_brinquedo` (`codigo_brinquedo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoria`
--

CREATE TABLE IF NOT EXISTS `categoria` (
  `codigo` int(5) NOT NULL,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `categoria`
--

INSERT INTO `categoria` (`codigo`, `nome`) VALUES
(0, 'Bonecas'),
(1, 'Presente por idade'),
(2, 'Novidades'),
(3, 'Colecionáveis'),
(4, 'Pelúcias'),
(5, 'Exclusivos'),
(6, 'Esportes'),
(7, 'Jogos de Tabuleiro'),
(8, 'Brinquedos para Bebês');

-- --------------------------------------------------------

--
-- Estrutura da tabela `descontos_cep`
--

CREATE TABLE IF NOT EXISTS `descontos_cep` (
  `cep_prefixo` varchar(9) NOT NULL,
  `porcentagem_desconto` decimal(5,2) NOT NULL,
  PRIMARY KEY (`cep_prefixo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `descontos_cep`
--

INSERT INTO `descontos_cep` (`cep_prefixo`, `porcentagem_desconto`) VALUES
('88805-085', '25.00'),
('88805-120', '25.00'),
('88806-000', '25.00'),
('88807-260', '50.00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `fabricante`
--

CREATE TABLE IF NOT EXISTS `fabricante` (
  `codigo` int(5) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`codigo`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Extraindo dados da tabela `fabricante`
--

INSERT INTO `fabricante` (`codigo`, `nome`) VALUES
(9, 'Disney'),
(8, 'Elka'),
(2, 'Estrela'),
(3, 'Grow'),
(5, 'Hasbro'),
(6, 'Lego'),
(4, 'Mattel'),
(7, 'Minimi'),
(1, 'Moon Studios'),
(10, 'Sunny');

-- --------------------------------------------------------

--
-- Estrutura da tabela `itens_pedido`
--

CREATE TABLE IF NOT EXISTS `itens_pedido` (
  `id_item_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `codigo_brinquedo` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  PRIMARY KEY (`id_item_pedido`),
  KEY `id_pedido` (`id_pedido`),
  KEY `codigo_brinquedo` (`codigo_brinquedo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Extraindo dados da tabela `itens_pedido`
--

INSERT INTO `itens_pedido` (`id_item_pedido`, `id_pedido`, `codigo_brinquedo`, `quantidade`) VALUES
(1, 87, 12, 1),
(2, 88, 4, 1),
(3, 88, 8, 1),
(4, 89, 10, 1),
(5, 90, 9, 1),
(6, 91, 21, 1),
(7, 91, 17, 1),
(8, 91, 16, 1),
(9, 92, 12, 1),
(10, 92, 22, 1),
(14, 94, 36, 1),
(15, 94, 37, 1),
(16, 95, 33, 1),
(17, 95, 37, 1),
(18, 96, 21, 1),
(19, 97, 19, 1),
(25, 102, 18, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos`
--

CREATE TABLE IF NOT EXISTS `pedidos` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `data_pedido` datetime NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `status_pagamento` varchar(50) NOT NULL,
  `id_transacao_mp` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=103 ;

--
-- Extraindo dados da tabela `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `data_pedido`, `valor_total`, `status_pagamento`, `id_transacao_mp`) VALUES
(87, 3, '2025-10-29 19:07:38', '29.99', 'aprovado', '131136596793'),
(88, 3, '2025-10-29 19:09:32', '262.50', 'aprovado', '131729365846'),
(89, 3, '2025-10-29 19:15:56', '60.00', 'cancelado', '131730085494'),
(90, 3, '2025-10-29 19:17:17', '26.25', 'pendente', '131137411495'),
(91, 3, '2025-11-03 20:24:34', '1073.49', 'aprovado', '132350964452'),
(92, 3, '2025-11-03 20:52:39', '120.74', 'pendente', '131755647237'),
(94, 3, '2025-11-06 18:53:07', '344.99', 'aprovado', '132154295639'),
(95, 3, '2025-11-06 19:18:56', '269.99', 'cancelado', '132758952882'),
(96, 6, '2025-11-06 19:32:15', '298.38', 'aprovado', '132158695673'),
(97, 3, '2025-11-06 19:38:37', '112.49', 'aprovado', '132763217784'),
(102, 6, '2025-11-06 20:26:01', '74.99', 'pendente', '132767561252');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `codigo` int(5) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `senha` varchar(12) NOT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'cliente',
  PRIMARY KEY (`codigo`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`codigo`, `email`, `nome`, `senha`, `tipo`) VALUES
(3, 'gerente@loja.com', 'Fábio da Costa', 'senha123', 'gerente'),
(6, 'emanuel@gmail.com', 'Emanuel de Lacerda Gomes', 'leuname', 'cliente'),
(7, 'cris@gmail.com', 'Cristiane Pavei Fernades', 'senha', 'cliente'),
(8, 'mari@gmail.com', 'Mariane', 'melhorprof', 'cliente');

--
-- Restrições para as tabelas dumpadas
--

--
-- Restrições para a tabela `brinquedos`
--
ALTER TABLE `brinquedos`
  ADD CONSTRAINT `brinquedos_ibfk_1` FOREIGN KEY (`codcategoria`) REFERENCES `categoria` (`codigo`),
  ADD CONSTRAINT `brinquedos_ibfk_2` FOREIGN KEY (`codfabricante`) REFERENCES `fabricante` (`codigo`);

--
-- Restrições para a tabela `carrinho_usuario`
--
ALTER TABLE `carrinho_usuario`
  ADD CONSTRAINT `carrinho_usuario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`codigo`),
  ADD CONSTRAINT `carrinho_usuario_ibfk_2` FOREIGN KEY (`codigo_brinquedo`) REFERENCES `brinquedos` (`codigo`);

--
-- Restrições para a tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`codigo_brinquedo`) REFERENCES `brinquedos` (`codigo`);

--
-- Restrições para a tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`codigo`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
