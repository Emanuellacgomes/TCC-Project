-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tempo de Geração: 05/11/2025 às 19h40min
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Extraindo dados da tabela `brinquedos`
--

INSERT INTO `brinquedos` (`codigo`, `nome`, `descricao`, `preco`, `estoque`, `imagem`, `codcategoria`, `codfabricante`) VALUES
(4, 'Certo ou Errado?', 'O jogo "Certo ou Errado" da Estrela é um jogo de tabuleiro que desafia os jogadores a identificar se uma afirmação é verdadeira ou falsa. Ele pode ser jogado por 2 a 5 pessoas e é recomendado para maiores de 7 anos. O objetivo é avançar pelo tabuleiro respondendo corretamente às perguntas, com o jogador que chega ao final primeiro sendo o vencedor. O jogo é uma forma divertida e educativa de aprender curiosidades e fatos interessantes. ', '100.00', 3, '14431b5db842e1372a1b6ed7ae72570awebp', 7, 2),
(5, 'Super Banco Imobiliário', 'O Jogo Banco Imobiliário permite que você invista muito bem o seu dinheiro em novas propriedades! Procura um jogo divertido para jogar com amigos e familiares? Esse modelo é perfeito, garante muito entretenimento e estimula o raciocínio lógico de todos!', '163.99', 33, '717786f0517dccdbb849dab6dd9e2758webp', 7, 2),
(6, 'Pista de Carrinhos', 'Pista de carrinhos com looping e dois carrinhos de metal.', '89.90', 10, 'pista.png', 6, 5),
(7, 'Boneca Clássica', 'Uma boneca com roupas de princesa.', '45.00', 25, 'boneca.png', 1, 4),
(8, 'LEGO Casa na Árvore', 'Kit de construção LEGO com 500 peças para montar uma casa na árvore.', '250.00', 6, 'lego.png', 3, 5),
(9, 'Jogo de Dominó', 'Jogo de dominó clássico com 28 peças de madeira.', '35.00', 50, 'domino.png', 7, 3),
(10, 'Livro Sensorial', 'Livro de pano para bebês, com texturas e cores para estimular os sentidos.', '60.00', 30, 'livro_sensorial.png', 8, 2),
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
(21, 'Ori and The Blind Forest: Estátua de PVC com varia', 'Arte inspirada: captura a essência da arte de capa icônica de Ori e a Floresta Cega, com Naru segurando Ori ternamente em um tronco caído\r\nVariação noturna: apresenta um esquema de cores escuras exclusivo para Naru e o tronco, perfeito para fãs que preferem a estética noturna do jogo\r\nFunções de iluminação LED: o brilho da Ori ganha vida com iluminação LED estática e animada na edição definitiva\r\nBase de resina detalhada: inclui detalhes expandidos como frutas, plantas e raízes de árvores espirituais, pintadas para combinar com o esquema de cores noturno\r\nPerfeito para decoração: adiciona um toque de capricho e calor à casa de qualquer fã, tornando-a uma peça decorativa ideal', '397.84', 6, '9d714d1625de26b13570b4cbcde92564.jpg', 3, 1),
(22, 'Boneca Lego ', 'Uma Boneca feita de pecinhas lego para você montar em casa!', '120.99', 2, '836335a3be0234a9d199cad991bd9ef3.jpg', 0, 6);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Extraindo dados da tabela `fabricante`
--

INSERT INTO `fabricante` (`codigo`, `nome`) VALUES
(8, 'Elka'),
(2, 'Estrela'),
(3, 'Grow'),
(5, 'Hasbro'),
(6, 'Lego'),
(4, 'Mattel'),
(7, 'Minimi'),
(1, 'Moon Studios');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

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
(11, 93, 20, 1),
(12, 93, 18, 2),
(13, 93, 12, 2);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=94 ;

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
(93, 6, '2025-11-05 17:19:03', '379.95', 'pendente', NULL);

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
