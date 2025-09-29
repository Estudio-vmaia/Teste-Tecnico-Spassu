<?php
require_once 'conn.php';

echo "<h2>Teste de Conexão com Banco de Dados</h2>";

// Informações do ambiente
echo "<h3>Informações do Ambiente:</h3>";
echo "<p><strong>Docker detectado:</strong> " . (getenv('DOCKER_CONTAINER') ? 'Sim' : 'Não') . "</p>";
echo "<p><strong>Arquivo /.dockerenv existe:</strong> " . (file_exists('/.dockerenv') ? 'Sim' : 'Não') . "</p>";
echo "<p><strong>Host usado:</strong> " . $host . "</p>";

// Teste de conexão
echo "<h3>Teste de Conexão:</h3>";
try {
    $sql = "SELECT VERSION() as version, NOW() as current_time";
    $stmt = executarQuery($pdo, $sql);
    $result = $stmt->fetch();
    
    echo "<p style='color: green;'><strong>✅ Conexão bem-sucedida!</strong></p>";
    echo "<p><strong>Versão do MySQL:</strong> " . $result['version'] . "</p>";
    echo "<p><strong>Data/Hora atual:</strong> " . $result['current_time'] . "</p>";
    
    // Teste de tabelas
    echo "<h3>Verificação de Tabelas:</h3>";
    $sql = "SHOW TABLES";
    $stmt = executarQuery($pdo, $sql);
    $tables = $stmt->fetchAll();
    
    if (empty($tables)) {
        echo "<p style='color: orange;'>⚠️ Nenhuma tabela encontrada. Execute o script de criação do banco.</p>";
    } else {
        echo "<p style='color: green;'>✅ Tabelas encontradas:</p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table[0] . "</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ Erro de conexão:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Voltar para a página inicial</a></p>";
?>
