# Sistema de Cadastro de Livros

Sistema web desenvolvido em PHP puro para gerenciamento de livros, autores e assuntos, seguindo o modelo de dados fornecido.

## 🚀 Tecnologias Utilizadas

- **Backend**: PHP 8.0+ (funções nativas)
- **Banco de Dados**: MySQL 8.0
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 4
- **Containerização**: Docker
- **Bibliotecas**: jQuery, Ajax

## 📋 Funcionalidades

### CRUD Completo
- ✅ **Livros**: Cadastro com título, editora, edição, ano de publicação e valor (R$)
- ✅ **Autores**: Cadastro de autores com validação de duplicatas
- ✅ **Assuntos**: Cadastro de categorias/assuntos para classificação

### Relacionamentos Many-to-Many
- ✅ **Livro-Autor**: Um livro pode ter múltiplos autores
- ✅ **Livro-Assunto**: Um livro pode ter múltiplos assuntos
- ✅ Interface para gerenciar relacionamentos

### Relatórios
- ✅ **Relatório agrupado por autor** usando view do banco de dados
- ✅ Estatísticas gerais do acervo
- ✅ Exportação para CSV
- ✅ Impressão otimizada

### Validações e Formatações
- ✅ Validação de ano de publicação (não pode ser superior ao atual)
- ✅ Máscara para valores monetários (R$)
- ✅ Validação de campos obrigatórios
- ✅ Prevenção de duplicatas
- ✅ Tratamento de erros específicos

## 🛠️ Instalação e Execução

### Pré-requisitos
- Docker Desktop instalado
- PHP 8.0+ (para desenvolvimento local)

### 1. Clonar/Download do Projeto
```bash
# Navegar para a pasta do projeto
cd "sistema_livros"
```

### 2. Executar com Docker
```bash
# Iniciar o banco MySQL
docker-compose up -d

# Verificar se o container está rodando
docker ps
```

### 3. Acessar o Sistema
O PHP já está configurado para rodar automaticamente junto com o Docker via `docker-compose.yml`. Basta acessar no navegador após subir os containers, não é necessário iniciar manualmente o servidor PHP.

### 4. Acessar no Navegador
- **URL**: http://localhost:8000
- **Banco**: MySQL rodando na porta 3306

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais
- `livro`: Livros com campo valor (R$) adicionado
- `autor`: Autores do sistema
- `assunto`: Categorias/assuntos

### Tabelas de Relacionamento
- `livro_autor`: Relacionamento many-to-many entre livros e autores
- `livro_assunto`: Relacionamento many-to-many entre livros e assuntos

### View para Relatórios
- `vw_relatorio_livros_por_autor`: View que agrupa dados por autor para relatórios

## 📁 Estrutura do Projeto

```
sistema_livros/
├── index.php                 # Página inicial
├── livros.php               # CRUD de livros
├── autores.php              # CRUD de autores
├── assuntos.php             # CRUD de assuntos
├── relacionamentos.php      # Gerenciar relacionamentos
├── relatorio.php            # Relatórios agrupados por autor
├── conn.php                 # Conexão com banco de dados
├── docker-compose.yml       # Configuração Docker
├── database/
│   └── create_database.sql  # Script de criação do banco
└── README.md                # Este arquivo
```

## 🔧 Configurações

### Banco de Dados (conn.php)
```php
$host = 'localhost';
$port = '3306';
$dbname = 'sistema_livros';
$username = 'usuario';
$password = 'senha123';
```

### Docker (docker-compose.yml)
- **MySQL 8.0** na porta 3306
- **Usuário**: usuario
- **Senha**: senha123
- **Banco**: sistema_livros

## 📊 Relatórios

### Relatório por Autor
- Agrupa livros por autor
- Mostra estatísticas por autor
- Calcula valores totais
- Exporta para CSV
- Otimizado para impressão

### Estatísticas Gerais
- Total de autores
- Total de livros
- Valor total do acervo
- Valor médio por livro

## 🎯 Diferenciais Implementados

### Boas Práticas
- ✅ Uso de PDO com prepared statements
- ✅ Tratamento específico de erros (não genérico)
- ✅ Validações robustas
- ✅ Transações para operações críticas
- ✅ Índices no banco para performance

### Interface
- ✅ Bootstrap para responsividade
- ✅ Máscaras de formatação
- ✅ Confirmações para exclusões
- ✅ Mensagens de feedback
- ✅ Interface intuitiva

### Banco de Dados
- ✅ View para relatórios
- ✅ Índices para performance
- ✅ Foreign keys com CASCADE
- ✅ Dados de exemplo para teste

## 🧪 Dados de Exemplo

O sistema já vem com dados de exemplo:
- 5 autores brasileiros famosos
- 8 categorias de assuntos
- 5 livros clássicos da literatura brasileira
- Relacionamentos pré-configurados

## 🔍 Validações Implementadas

### Livros
- Ano não pode ser superior ao atual
- Edição deve ser maior que zero
- Valor não pode ser negativo
- Campos obrigatórios

### Autores/Assuntos
- Prevenção de duplicatas
- Limite de caracteres
- Verificação de relacionamentos antes da exclusão

## 📱 Responsividade

- Interface adaptável para desktop, tablet e mobile
- Bootstrap 4 para layout responsivo
- Componentes otimizados para diferentes telas

---
