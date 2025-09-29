-- ============================================
-- SCRIPT DE LIMPEZA DAS TABELAS
-- ============================================
-- Execute este script quando quiser limpar todos os dados
-- e zerar os índices auto_increment

USE sistema_livros;

-- Desabilitar verificação de foreign keys temporariamente
SET FOREIGN_KEY_CHECKS = 0;

-- Limpar dados das tabelas (respeitando dependências)
DELETE FROM livro_assunto;
DELETE FROM livro_autor;
DELETE FROM livro;
DELETE FROM assunto;
DELETE FROM autor;

-- Reabilitar verificação de foreign keys
SET FOREIGN_KEY_CHECKS = 1;

-- Resetar auto_increment para começar do 1
ALTER TABLE autor AUTO_INCREMENT = 1;
ALTER TABLE assunto AUTO_INCREMENT = 1;
ALTER TABLE livro AUTO_INCREMENT = 1;

-- Verificar se as tabelas estão vazias
SELECT 'Tabelas limpas com sucesso!' as Status;
SELECT COUNT(*) as TotalAutores FROM autor;
SELECT COUNT(*) as TotalAssuntos FROM assunto;
SELECT COUNT(*) as TotalLivros FROM livro;
SELECT COUNT(*) as TotalRelacionamentosAutor FROM livro_autor;
SELECT COUNT(*) as TotalRelacionamentosAssunto FROM livro_assunto;
