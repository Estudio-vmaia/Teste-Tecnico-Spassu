<?php
require_once 'conn.php';

// Variáveis para mensagens
$mensagem = '';
$tipo_mensagem = '';

// Processar formulário de relacionamentos
if ($_POST) {
    $acao = $_POST['acao'] ?? '';
    
    try {
        if ($acao === 'associar_autor') {
            $livro_codl = (int)$_POST['livro_codl'];
            $autor_codau = (int)$_POST['autor_codau'];
            
            // Verificar se já existe a associação
            $sql_check = "SELECT COUNT(*) FROM livro_autor WHERE Livro_Codl = ? AND Autor_CodAu = ?";
            $stmt_check = executarQuery($pdo, $sql_check, [$livro_codl, $autor_codau]);
            if ($stmt_check->fetchColumn() > 0) {
                throw new Exception("Este autor já está associado ao livro selecionado.");
            }
            
            $sql = "INSERT INTO livro_autor (Livro_Codl, Autor_CodAu) VALUES (?, ?)";
            executarQuery($pdo, $sql, [$livro_codl, $autor_codau]);
            
            $mensagem = "Autor associado ao livro com sucesso!";
            $tipo_mensagem = "success";
            
        } elseif ($acao === 'remover_autor') {
            $livro_codl = (int)$_POST['livro_codl'];
            $autor_codau = (int)$_POST['autor_codau'];
            
            $sql = "DELETE FROM livro_autor WHERE Livro_Codl = ? AND Autor_CodAu = ?";
            executarQuery($pdo, $sql, [$livro_codl, $autor_codau]);
            
            $mensagem = "Autor removido do livro com sucesso!";
            $tipo_mensagem = "success";
            
        } elseif ($acao === 'associar_assunto') {
            $livro_codl = (int)$_POST['livro_codl'];
            $assunto_codas = (int)$_POST['assunto_codas'];
            
            // Verificar se já existe a associação
            $sql_check = "SELECT COUNT(*) FROM livro_assunto WHERE Livro_Codl = ? AND Assunto_codAs = ?";
            $stmt_check = executarQuery($pdo, $sql_check, [$livro_codl, $assunto_codas]);
            if ($stmt_check->fetchColumn() > 0) {
                throw new Exception("Este assunto já está associado ao livro selecionado.");
            }
            
            $sql = "INSERT INTO livro_assunto (Livro_Codl, Assunto_codAs) VALUES (?, ?)";
            executarQuery($pdo, $sql, [$livro_codl, $assunto_codas]);
            
            $mensagem = "Assunto associado ao livro com sucesso!";
            $tipo_mensagem = "success";
            
        } elseif ($acao === 'remover_assunto') {
            $livro_codl = (int)$_POST['livro_codl'];
            $assunto_codas = (int)$_POST['assunto_codas'];
            
            $sql = "DELETE FROM livro_assunto WHERE Livro_Codl = ? AND Assunto_codAs = ?";
            executarQuery($pdo, $sql, [$livro_codl, $assunto_codas]);
            
            $mensagem = "Assunto removido do livro com sucesso!";
            $tipo_mensagem = "success";
        }
        
    } catch (Exception $e) {
        $mensagem = $e->getMessage();
        $tipo_mensagem = "danger";
    }
}

// Buscar livros para seleção
$livros = executarQuery($pdo, "SELECT * FROM livro ORDER BY Titulo")->fetchAll();

// Buscar autores e assuntos
$autores = executarQuery($pdo, "SELECT * FROM autor ORDER BY Nome")->fetchAll();
$assuntos = executarQuery($pdo, "SELECT * FROM assunto ORDER BY Descricao")->fetchAll();

// Variáveis para exibição
$livro_selecionado = null;
$autores_livro = [];
$assuntos_livro = [];

if (isset($_GET['livro'])) {
    $codl = (int)$_GET['livro'];
    
    // Buscar dados do livro
    $sql_livro = "SELECT * FROM livro WHERE Codl = ?";
    $stmt_livro = executarQuery($pdo, $sql_livro, [$codl]);
    $livro_selecionado = $stmt_livro->fetch();
    
    if ($livro_selecionado) {
        // Buscar autores do livro
        $sql_autores = "SELECT a.* FROM autor a 
                        INNER JOIN livro_autor la ON a.CodAu = la.Autor_CodAu 
                        WHERE la.Livro_Codl = ? 
                        ORDER BY a.Nome";
        $stmt_autores = executarQuery($pdo, $sql_autores, [$codl]);
        $autores_livro = $stmt_autores->fetchAll();
        
        // Buscar assuntos do livro
        $sql_assuntos = "SELECT s.* FROM assunto s 
                         INNER JOIN livro_assunto las ON s.codAs = las.Assunto_codAs 
                         WHERE las.Livro_Codl = ? 
                         ORDER BY s.Descricao";
        $stmt_assuntos = executarQuery($pdo, $sql_assuntos, [$codl]);
        $assuntos_livro = $stmt_assuntos->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Relacionamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link href="css/layout.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body>
    
    <?php include 'menu.php'; ?>

    <div class="container my-4 main-content">
        <!-- Mensagens -->
        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert" id="mensagem-alert">
                <?= htmlspecialchars($mensagem) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            
            <script>
                <?php include 'functions.js'; ?>
            </script>
            
        <?php endif; ?>

        <div class="row">
            <!-- Seleção de Livro -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-book"></i> Selecionar Livro
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET">
                            <div class="row">
                                <div class="col-md-8">
                                    <select name="livro" class="form-select" onchange="this.form.submit()">
                                        <option value="">Selecione um livro...</option>
                                        <?php foreach ($livros as $livro): ?>
                                            <option value="<?= $livro['Codl'] ?>" 
                                                    <?= ($livro_selecionado && $livro['Codl'] == $livro_selecionado['Codl']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($livro['Titulo']) ?> - <?= htmlspecialchars($livro['Editora']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <a href="relacionamentos.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Limpar Seleção
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php if ($livro_selecionado): ?>
                <!-- Informações do Livro -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle"></i> 
                                <?= htmlspecialchars($livro_selecionado['Titulo']) ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Editora:</strong> <?= htmlspecialchars($livro_selecionado['Editora']) ?></p>
                                    <p><strong>Edição:</strong> <?= $livro_selecionado['Edicao'] ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Ano:</strong> <?= $livro_selecionado['AnoPublicacao'] ?></p>
                                    <p><strong>Valor:</strong> R$ <?= number_format($livro_selecionado['Valor'], 2, ',', '.') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gerenciar Autores -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-user-edit"></i> Autores do Livro
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Adicionar Autor -->
                            <form method="POST" class="mb-3">
                                <input type="hidden" name="acao" value="associar_autor">
                                <input type="hidden" name="livro_codl" value="<?= $livro_selecionado['Codl'] ?>">
                                <div class="row">
                                    <div class="col-8">
                                        <select name="autor_codau" class="form-select" required>
                                            <option value="">Selecione um autor...</option>
                                            <?php foreach ($autores as $autor): ?>
                                                <?php 
                                                $ja_associado = false;
                                                foreach ($autores_livro as $autor_livro) {
                                                    if ($autor_livro['CodAu'] == $autor['CodAu']) {
                                                        $ja_associado = true;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <?php if (!$ja_associado): ?>
                                                    <option value="<?= $autor['CodAu'] ?>">
                                                        <?= htmlspecialchars($autor['Nome']) ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="fas fa-plus"></i> Adicionar
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Lista de Autores -->
                            <?php if (empty($autores_livro)): ?>
                                <p class="text-muted">Nenhum autor associado.</p>
                            <?php else: ?>
                                <?php foreach ($autores_livro as $autor): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                        <span><?= htmlspecialchars($autor['Nome']) ?></span>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="acao" value="remover_autor">
                                            <input type="hidden" name="livro_codl" value="<?= $livro_selecionado['Codl'] ?>">
                                            <input type="hidden" name="autor_codau" value="<?= $autor['CodAu'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                    onclick="return confirm('Remover este autor do livro?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Gerenciar Assuntos -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-tags"></i> Assuntos do Livro
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Adicionar Assunto -->
                            <form method="POST" class="mb-3">
                                <input type="hidden" name="acao" value="associar_assunto">
                                <input type="hidden" name="livro_codl" value="<?= $livro_selecionado['Codl'] ?>">
                                <div class="row">
                                    <div class="col-8">
                                        <select name="assunto_codas" class="form-select" required>
                                            <option value="">Selecione um assunto...</option>
                                            <?php foreach ($assuntos as $assunto): ?>
                                                <?php 
                                                $ja_associado = false;
                                                foreach ($assuntos_livro as $assunto_livro) {
                                                    if ($assunto_livro['codAs'] == $assunto['codAs']) {
                                                        $ja_associado = true;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <?php if (!$ja_associado): ?>
                                                    <option value="<?= $assunto['codAs'] ?>">
                                                        <?= htmlspecialchars($assunto['Descricao']) ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-warning btn-sm w-100">
                                            <i class="fas fa-plus"></i> Adicionar
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Lista de Assuntos -->
                            <?php if (empty($assuntos_livro)): ?>
                                <p class="text-muted">Nenhum assunto associado.</p>
                            <?php else: ?>
                                <?php foreach ($assuntos_livro as $assunto): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                        <span><?= htmlspecialchars($assunto['Descricao']) ?></span>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="acao" value="remover_assunto">
                                            <input type="hidden" name="livro_codl" value="<?= $livro_selecionado['Codl'] ?>">
                                            <input type="hidden" name="assunto_codas" value="<?= $assunto['codAs'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                    onclick="return confirm('Remover este assunto do livro?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-link fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Selecione um livro para gerenciar relacionamentos</h5>
                            <p class="text-muted">Escolha um livro da lista acima para associar autores e assuntos.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
