# Sistema de Cadastro de Livros - Docker Setup

## Configuração do Docker com Apache + PHP + MySQL

Este projeto agora inclui uma configuração completa do Docker com Apache, PHP e MySQL para suportar URLs sem extensão `.php`.

### Arquivos de Configuração

- `.htaccess` - Configuração do Apache para URLs sem extensão
- `apache-config.conf` - Configuração adicional do Apache
- `docker-compose.yml` - Configuração dos containers Docker

### Como Usar

1. **Parar containers existentes** (se houver):
   ```bash
   docker-compose down
   ```

2. **Iniciar os novos containers**:
   ```bash
   docker-compose up -d
   ```

3. **Acessar o sistema**:
   - URL: http://localhost:8000
   - URLs sem extensão funcionarão:
     - http://localhost:8000/livros
     - http://localhost:8000/autores
     - http://localhost:8000/assuntos
     - http://localhost:8000/relatorio
     - http://localhost:8000/relacionamentos

### Serviços Incluídos

- **Web Server**: Apache 2.4 com PHP 8.1
- **Database**: MySQL 8.0
- **Portas**:
  - Web: 8000 (http://localhost:8000)
  - MySQL: 3306 (localhost:3306)

### Funcionalidades do .htaccess

- ✅ URLs sem extensão `.php`
- ✅ Redirecionamento automático de URLs com `.php` para sem extensão
- ✅ Configurações de segurança
- ✅ Cache e compressão para melhor performance

### Troubleshooting

Se houver problemas:

1. **Verificar logs do Apache**:
   ```bash
   docker logs sistema_livros_web
   ```

2. **Verificar logs do MySQL**:
   ```bash
   docker logs sistema_livros_mysql
   ```

3. **Reiniciar containers**:
   ```bash
   docker-compose restart
   ```

### Migração de Setup Anterior

Se você estava usando apenas MySQL no Docker:

1. Pare o container MySQL atual
2. Execute `docker-compose up -d` para iniciar a nova configuração
3. Os dados do banco serão preservados no volume `mysql_data`
