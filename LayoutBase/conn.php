<?php
/**
 * Arquivo de conexão com o banco de dados
 * Configurações podem ser alteradas conforme o ambiente
 */

// Configurações do banco de dados
$host = 'localhost';
$port = '3306';
$dbname = 'sistema_livros';
$username = 'usuario';
$password = 'senha123';

// Configurações para diferentes ambientes
if (isset($_SERVER['HTTP_HOST'])) {
    // Ambiente de produção (ajustar conforme necessário)
    if ($_SERVER['HTTP_HOST'] === 'seu-dominio.com') {
        $host = 'seu-host-producao';
        $username = 'usuario-producao';
        $password = 'senha-producao';
    }
}

try {
    // Conexão PDO
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // Definir timezone
    $pdo->exec("SET time_zone = '-03:00'");
    
} catch (PDOException $e) {
    // Log do erro (em produção, usar sistema de log adequado)
    error_log("Erro de conexão com banco de dados: " . $e->getMessage());
    
    // Mensagem amigável para o usuário
    die("Erro de conexão com o banco de dados. Tente novamente mais tarde.");
}

// Função para executar queries com tratamento de erro
function executarQuery($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Erro na query: " . $e->getMessage() . " SQL: " . $sql);
        throw new Exception("Erro na operação do banco de dados.");
    }
}

// Função para obter último ID inserido
function obterUltimoId($pdo) {
    return $pdo->lastInsertId();
}

// Função para iniciar transação
function iniciarTransacao($pdo) {
    return $pdo->beginTransaction();
}

// Função para confirmar transação
function confirmarTransacao($pdo) {
    return $pdo->commit();
}

// Função para reverter transação
function reverterTransacao($pdo) {
    return $pdo->rollBack();
}
?>
