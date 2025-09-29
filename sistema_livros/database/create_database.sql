-- Script para criação do banco de dados do sistema de livros
-- Execute este script no MySQL para criar a estrutura completa

CREATE DATABASE IF NOT EXISTS sistema_livros CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sistema_livros;

-- Tabela de Autores
DROP TABLE IF EXISTS autor;
CREATE TABLE autor (
    CodAu INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(40) NOT NULL,
    INDEX idx_autor_nome (Nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Assuntos
DROP TABLE IF EXISTS assunto;
CREATE TABLE assunto (
    codAs INT AUTO_INCREMENT PRIMARY KEY,
    Descricao VARCHAR(50) NOT NULL,
    INDEX idx_assunto_descricao (Descricao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Livros (com campo valor adicionado conforme solicitado)
DROP TABLE IF EXISTS livro;
CREATE TABLE livro (
    Codl INT AUTO_INCREMENT PRIMARY KEY,
    Titulo VARCHAR(40) NOT NULL,
    Editora VARCHAR(40) NOT NULL,
    Edicao INT NOT NULL DEFAULT 1,
    AnoPublicacao VARCHAR(4) NOT NULL,
    Valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    INDEX idx_livro_titulo (Titulo),
    INDEX idx_livro_editora (Editora),
    INDEX idx_livro_ano (AnoPublicacao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de relacionamento Livro-Autor (Many-to-Many)
DROP TABLE IF EXISTS livro_autor;
CREATE TABLE livro_autor (
    Livro_Codl INT NOT NULL,
    Autor_CodAu INT NOT NULL,
    PRIMARY KEY (Livro_Codl, Autor_CodAu),
    FOREIGN KEY (Livro_Codl) REFERENCES livro(Codl) ON DELETE CASCADE,
    FOREIGN KEY (Autor_CodAu) REFERENCES autor(CodAu) ON DELETE CASCADE,
    INDEX Livro_Autor_FKIndex1 (Livro_Codl),
    INDEX Livro_Autor_FKIndex2 (Autor_CodAu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de relacionamento Livro-Assunto (Many-to-Many)
DROP TABLE IF EXISTS livro_assunto;
CREATE TABLE livro_assunto (
    Livro_Codl INT NOT NULL,
    Assunto_codAs INT NOT NULL,
    PRIMARY KEY (Livro_Codl, Assunto_codAs),
    FOREIGN KEY (Livro_Codl) REFERENCES livro(Codl) ON DELETE CASCADE,
    FOREIGN KEY (Assunto_codAs) REFERENCES assunto(codAs) ON DELETE CASCADE,
    INDEX Livro_Assunto_FKIndex1 (Livro_Codl),
    INDEX Livro_Assunto_FKIndex2 (Assunto_codAs)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- View para relatório agrupado por autor
DROP VIEW IF EXISTS vw_relatorio_livros_por_autor;
CREATE VIEW vw_relatorio_livros_por_autor AS
SELECT 
    a.CodAu,
    a.Nome as NomeAutor,
    l.Codl,
    l.Titulo,
    l.Editora,
    l.Edicao,
    l.AnoPublicacao,
    l.Valor,
    GROUP_CONCAT(DISTINCT s.Descricao ORDER BY s.Descricao SEPARATOR ', ') as Assuntos,
    COUNT(DISTINCT l.Codl) as TotalLivros,
    SUM(l.Valor) as ValorTotalLivros
FROM autor a
LEFT JOIN livro_autor la ON a.CodAu = la.Autor_CodAu
LEFT JOIN livro l ON la.Livro_Codl = l.Codl
LEFT JOIN livro_assunto las ON l.Codl = las.Livro_Codl
LEFT JOIN assunto s ON las.Assunto_codAs = s.codAs
GROUP BY a.CodAu, a.Nome, l.Codl, l.Titulo, l.Editora, l.Edicao, l.AnoPublicacao, l.Valor
ORDER BY a.Nome, l.Titulo;

-- ============================================
-- LIMPEZA DAS TABELAS (OPCIONAL)
-- ============================================
-- Descomente as linhas abaixo se quiser limpar os dados antes de inserir novos

-- Desabilitar verificação de foreign keys temporariamente
-- SET FOREIGN_KEY_CHECKS = 0;

-- Limpar dados das tabelas (respeitando dependências)
-- DELETE FROM livro_assunto;
-- DELETE FROM livro_autor;
-- DELETE FROM livro;
-- DELETE FROM assunto;
-- DELETE FROM autor;

-- Reabilitar verificação de foreign keys
-- SET FOREIGN_KEY_CHECKS = 1;

-- Resetar auto_increment para começar do 1
-- ALTER TABLE autor AUTO_INCREMENT = 1;
-- ALTER TABLE assunto AUTO_INCREMENT = 1;
-- ALTER TABLE livro AUTO_INCREMENT = 1;

-- ============================================
-- DADOS DE EXEMPLO PARA O SISTEMA
-- ============================================

-- 1. Inserir autores (5 autores brasileiros famosos)
INSERT INTO autor (Nome) VALUES 
('Machado de Assis'),
('Clarice Lispector'),
('Jorge Amado'),
('Cecília Meireles'),
('Carlos Drummond de Andrade');

-- 2. Inserir assuntos (8 categorias de assuntos)
INSERT INTO assunto (Descricao) VALUES 
('Romance'),
('Poesia'),
('Contos'),
('Literatura Brasileira'),
('Ficção'),
('Biografia'),
('História'),
('Filosofia');

-- 3. Inserir livros (5 livros clássicos da literatura brasileira)
INSERT INTO livro (Titulo, Editora, Edicao, AnoPublicacao, Valor) VALUES 
('Dom Casmurro', 'Editora Globo', 1, '1899', 29.90),
('A Hora da Estrela', 'Rocco', 1, '1977', 35.50),
('Gabriela, Cravo e Canela', 'Companhia das Letras', 1, '1958', 42.00),
('Romanceiro da Inconfidência', 'Nova Fronteira', 1, '1953', 38.90),
('A Rosa do Povo', 'Record', 1, '1945', 31.75);

-- 4. Relacionar livros com autores (relacionamentos many-to-many)
INSERT INTO livro_autor (Livro_Codl, Autor_CodAu) VALUES 
(1, 1), -- Dom Casmurro - Machado de Assis
(2, 2), -- A Hora da Estrela - Clarice Lispector
(3, 3), -- Gabriela, Cravo e Canela - Jorge Amado
(4, 4), -- Romanceiro da Inconfidência - Cecília Meireles
(5, 5); -- A Rosa do Povo - Carlos Drummond de Andrade

-- 5. Relacionar livros com assuntos (relacionamentos many-to-many)
INSERT INTO livro_assunto (Livro_Codl, Assunto_codAs) VALUES 
(1, 1), (1, 4), -- Dom Casmurro - Romance, Literatura Brasileira
(2, 1), (2, 4), -- A Hora da Estrela - Romance, Literatura Brasileira
(3, 1), (3, 4), -- Gabriela, Cravo e Canela - Romance, Literatura Brasileira
(4, 2), (4, 4), -- Romanceiro da Inconfidência - Poesia, Literatura Brasileira
(5, 2), (5, 4); -- A Rosa do Povo - Poesia, Literatura Brasileira

-- ============================================
-- DADOS INSERIDOS COM SUCESSO!
-- ============================================
