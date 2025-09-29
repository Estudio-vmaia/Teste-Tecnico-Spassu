# 📚 Sistema de Cadastro de Livros

Um sistema web completo para gerenciamento de livros, autores e assuntos, desenvolvido em PHP com interface moderna e responsiva.

## 🚀 Funcionalidades

### 📖 Gerenciamento de Livros
- ✅ Cadastro, edição e exclusão de livros
- ✅ Validação completa de dados (título, editora, edição, ano, valor)
- ✅ Máscaras de entrada para valores monetários e anos
- ✅ Relacionamento com autores e assuntos
- ✅ Listagem com informações detalhadas

### 👥 Gerenciamento de Autores
- ✅ Cadastro, edição e exclusão de autores
- ✅ Validação de nomes únicos
- ✅ Verificação de livros associados antes da exclusão
- ✅ Listagem com contagem de livros por autor

### 🏷️ Gerenciamento de Assuntos
- ✅ Cadastro, edição e exclusão de assuntos
- ✅ Validação de descrições únicas
- ✅ Verificação de livros associados antes da exclusão
- ✅ Listagem com contagem de livros por assunto

### 🔗 Relacionamentos
- ✅ Associação de múltiplos autores a um livro
- ✅ Associação de múltiplos assuntos a um livro
- ✅ Interface intuitiva para gerenciar relacionamentos
- ✅ Remoção segura de associações

### 📊 Relatórios
- ✅ Relatório detalhado por autor
- ✅ Estatísticas gerais do acervo
- ✅ Exportação para CSV
- ✅ Funcionalidade de impressão
- ✅ View otimizada para consultas complexas

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 8.1
- **Banco de Dados**: MySQL 8.0
- **Frontend**: Bootstrap 5, Font Awesome
- **Servidor Web**: Apache 2.4
- **Containerização**: Docker & Docker Compose
- **URLs**: Amigáveis (sem extensão .php)

## 📁 Estrutura do Projeto

```
sistema_livros/
├── 📄 index.php              # Página inicial
├── 📄 livros.php             # Gerenciamento de livros
├── 📄 autores.php            # Gerenciamento de autores
├── 📄 assuntos.php           # Gerenciamento de assuntos
├── 📄 relacionamentos.php    # Gerenciamento de relacionamentos
├── 📄 relatorio.php          # Relatórios e estatísticas
├── 📄 conn.php               # Conexão com banco de dados
├── 📄 menu.php               # Menu de navegação
├── 📄 footer.php             # Rodapé
├── 📄 functions.js           # Funções JavaScript comuns
├── 📄 teste-conexao.php      # Teste de conexão com banco
├── 📄 .htaccess              # Configuração Apache
├── 📄 docker-compose.yml     # Configuração Docker
├── 📄 apache-config.conf     # Configuração adicional Apache
├── 📄 php.ini                # Configurações PHP
├── 📁 css/
│   ├── layout.css            # Estilos de layout global
│   └── sb-admin-2.css        # Tema SB Admin 2
├── 📁 database/
│   └── create_database.sql   # Script de criação do banco
└── 📁 vendor/                # Dependências (Bootstrap, Font Awesome, jQuery)
```

## 🐳 Instalação com Docker

### Pré-requisitos
- Docker
- Docker Compose

### Passos de Instalação

1. **Clone o repositório**:
   ```bash
   git clone <url-do-repositorio>
   cd sistema_livros
   ```

2. **Inicie os containers**:
   ```bash
   docker-compose up -d
   ```

3. **Aguarde a instalação** (primeira execução pode levar alguns minutos)

4. **Teste a conexão**:
   - Acesse: http://localhost:8000/teste-conexao.php

5. **Acesse o sistema**:
   - URL: http://localhost:8000

### URLs Disponíveis

- 🏠 **Início**: http://localhost:8000
- 📚 **Livros**: http://localhost:8000/livros
- 👥 **Autores**: http://localhost:8000/autores
- 🏷️ **Assuntos**: http://localhost:8000/assuntos
- 🔗 **Relacionamentos**: http://localhost:8000/relacionamentos
- 📊 **Relatórios**: http://localhost:8000/relatorio

## 🗄️ Banco de Dados

### Estrutura das Tabelas

- **`livro`**: Informações dos livros (título, editora, edição, ano, valor)
- **`autor`**: Informações dos autores (nome)
- **`assunto`**: Informações dos assuntos (descrição)
- **`livro_autor`**: Relacionamento muitos-para-muitos entre livros e autores
- **`livro_assunto`**: Relacionamento muitos-para-muitos entre livros e assuntos

### View de Relatório

- **`vw_relatorio_livros_por_autor`**: View otimizada para relatórios com agrupamento por autor

### Dados de Exemplo

O sistema inclui dados de exemplo com autores brasileiros clássicos e seus livros.

## ⚙️ Configurações

### Docker Compose

O sistema utiliza dois containers:
- **Web**: Apache + PHP 8.1 (porta 8000)
- **MySQL**: MySQL 8.0 (porta 3306)

### Configurações Automáticas

- **Detecção de Ambiente**: Automática (Docker vs Local)
- **URLs Amigáveis**: Configuradas via .htaccess
- **Validações**: Centralizadas e reutilizáveis
- **Segurança**: Proteção contra XSS e SQL Injection

## 🔧 Funcionalidades Técnicas

### Validações Centralizadas

- **`validarDadosLivro()`**: Validação completa de livros
- **`validarDadosAutor()`**: Validação de autores
- **`validarDadosAssunto()`**: Validação de assuntos

### Recursos de UX/UI

- ✅ Interface responsiva
- ✅ Mensagens de feedback
- ✅ Validação em tempo real
- ✅ Máscaras de entrada
- ✅ Confirmação de exclusão
- ✅ Auto-foco em campos com erro
- ✅ Footer sempre no rodapé

### Segurança

- ✅ Prepared Statements (PDO)
- ✅ Escape de HTML (htmlspecialchars)
- ✅ Validação de entrada
- ✅ Proteção contra SQL Injection
- ✅ Proteção contra XSS

## 🚨 Resolução de Problemas

### Erro de Conexão com Banco

```bash
# Verificar containers
docker-compose ps

# Verificar logs
docker logs sistema_livros_web
docker logs sistema_livros_mysql

# Reiniciar containers
docker-compose restart
```

### Erro "could not find driver"

```bash
# Reinstalar containers (instala drivers MySQL)
docker-compose down
docker-compose up -d
```

### URLs não funcionam

- Verifique se o mod_rewrite está habilitado
- Confirme que o .htaccess está presente
- Teste acessando diretamente com .php

## 📈 Melhorias Implementadas

### Refatoração de Código
- ✅ Eliminação de código duplicado
- ✅ Funções de validação centralizadas
- ✅ Compatibilidade com PHP 8.1+
- ✅ Tratamento de valores null

### Docker e Infraestrutura
- ✅ Containerização completa
- ✅ URLs sem extensão
- ✅ Configuração automática
- ✅ Drivers MySQL incluídos

### Interface e UX
- ✅ Layout responsivo
- ✅ Footer fixo
- ✅ Mensagens de feedback
- ✅ Validação em tempo real

## 🎯 Próximas Melhorias

- [ ] Sistema de autenticação
- [ ] Backup automático do banco
- [ ] API REST
- [ ] Relatórios em PDF
- [ ] Busca avançada
- [ ] Importação em lote

## 📝 Licença

Este projeto foi desenvolvido como teste técnico e está disponível para fins educacionais.

## 👨‍💻 Desenvolvido por

Sistema desenvolvido com foco em boas práticas de desenvolvimento, segurança e usabilidade.

---

**🎉 Sistema totalmente funcional e pronto para uso!**