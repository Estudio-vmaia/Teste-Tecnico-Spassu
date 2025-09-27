# 🚀 Instruções de Execução - Sistema de Livros

## ⚡ Execução Rápida

### 1. Iniciar o Banco de Dados
```bash
# Na pasta LayoutBase, execute:
docker-compose up -d

# Verificar se está rodando:
docker ps
```

### 2. Iniciar o Servidor PHP
```bash
# Na pasta LayoutBase, execute:
php -S localhost:8000
```

### 3. Acessar o Sistema
- **URL**: http://localhost:8000
- **Usuário do banco**: usuario
- **Senha do banco**: senha123

## 📋 Checklist de Funcionalidades

### ✅ CRUD Completo
- [ ] **Livros**: Cadastrar, editar, excluir livros
- [ ] **Autores**: Cadastrar, editar, excluir autores
- [ ] **Assuntos**: Cadastrar, editar, excluir assuntos

### ✅ Relacionamentos
- [ ] **Livro-Autor**: Associar múltiplos autores a um livro
- [ ] **Livro-Assunto**: Associar múltiplos assuntos a um livro
- [ ] **Remoção**: Remover relacionamentos existentes

### ✅ Relatórios
- [ ] **Por Autor**: Visualizar relatório agrupado por autor
- [ ] **Estatísticas**: Ver totais e valores médios
- [ ] **Exportação**: Baixar relatório em CSV
- [ ] **Impressão**: Imprimir relatório formatado

### ✅ Validações
- [ ] **Ano**: Não permite ano superior ao atual
- [ ] **Valor**: Máscara monetária (R$)
- [ ] **Duplicatas**: Previne cadastros duplicados
- [ ] **Relacionamentos**: Impede exclusão com dependências

## 🎯 Pontos para Demonstração

### 1. Estrutura do Banco
- Mostrar tabelas criadas
- Explicar view `vw_relatorio_livros_por_autor`
- Demonstrar relacionamentos many-to-many

### 2. CRUD de Livros
- Cadastrar livro com validações
- Editar informações
- Excluir com confirmação
- Mostrar formatação de valor

### 3. Relacionamentos
- Associar múltiplos autores a um livro
- Associar múltiplos assuntos a um livro
- Mostrar interface de gerenciamento

### 4. Relatório por Autor
- Exibir agrupamento por autor
- Mostrar estatísticas gerais
- Testar exportação CSV
- Demonstrar impressão

### 5. Validações
- Tentar cadastrar ano futuro
- Testar valores negativos
- Verificar prevenção de duplicatas

## 🔧 Comandos Úteis

### Docker
```bash
# Parar containers
docker-compose down

# Ver logs
docker-compose logs mysql

# Acessar MySQL diretamente
docker exec -it sistema_livros_mysql mysql -u usuario -p sistema_livros
```

### PHP
```bash
# Verificar versão
php -v

# Servidor em porta diferente
php -S localhost:3000

# Com logs detalhados
php -S localhost:8000 -t . -d display_errors=1
```

## 🐛 Solução de Problemas

### Banco não conecta
1. Verificar se Docker está rodando
2. Verificar porta 3306 disponível
3. Testar conexão: `docker exec -it sistema_livros_mysql mysql -u usuario -p`

### Erro de permissão
1. Verificar se pasta tem permissão de escrita
2. Verificar se PHP tem acesso ao banco

### Página não carrega
1. Verificar se servidor PHP está rodando
2. Verificar URL correta (localhost:8000)
3. Verificar logs do PHP

## 📊 Dados de Teste

O sistema já vem com dados de exemplo:
- **5 Autores**: Machado de Assis, Clarice Lispector, etc.
- **8 Assuntos**: Romance, Poesia, Contos, etc.
- **5 Livros**: Dom Casmurro, A Hora da Estrela, etc.
- **Relacionamentos**: Pré-configurados para demonstração

## 🎨 Interface

- **Bootstrap 4**: Layout responsivo
- **FontAwesome**: Ícones profissionais
- **Cores**: Esquema de cores consistente
- **Responsivo**: Funciona em mobile/tablet

## 📈 Performance

- **Índices**: Criados para otimizar consultas
- **PDO**: Prepared statements para segurança
- **View**: Relatório otimizado no banco
- **Cache**: Bootstrap/jQuery via CDN local

---

**Sistema pronto para apresentação! 🚀**
