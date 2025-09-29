<?php
require_once 'conn.php';

// Variáveis para mensagens
$mensagem = '';
$tipo_mensagem = '';

function validarDadosAutor($nome, $pdo, $codau_excluir = null) {
    $nome = trim($nome);
    $erros = [];
    
    // Validações básicas
    if (empty($nome)) {
        $erros[] = "O nome do autor é obrigatório.";
    }
    
    if (strlen(trim($nome)) < 2) {
        $erros[] = "Nome do autor deve ter pelo menos 2 caracteres.";
    }
    
    if (strlen($nome) > 40) {
        $erros[] = "O nome do autor deve ter no máximo 40 caracteres.";
    }
    
    // Verificar duplicação apenas se não há erros básicos
    if (empty($erros)) {
        if ($codau_excluir !== null) {

            // Excluir o próprio registro da verificação
            $sql_check = "SELECT COUNT(*) FROM autor WHERE Nome = ? AND CodAu != ?";
            $stmt_check = executarQuery($pdo, $sql_check, [$nome, $codau_excluir]);
        } else {

            // Verificar se já existe
            $sql_check = "SELECT COUNT(*) FROM autor WHERE Nome = ?";
            $stmt_check = executarQuery($pdo, $sql_check, [$nome]);
        }
        
        if ($stmt_check->fetchColumn() > 0) {
            $erros[] = "Já existe um autor com este nome.";
        }
    }
    
    return $erros;
}

// Processar formulário
if ($_POST) {
    $acao = $_POST['acao'] ?? '';
    
    try {
        if ($acao === 'inserir') {
            $nome = trim($_POST['nome']);
            
            // Validar dados usando a função reutilizável
            $erros_validacao = validarDadosAutor($nome, $pdo);
            
            if (!empty($erros_validacao)) {
                throw new Exception(implode("<br>", $erros_validacao));
            }
            
            $sql = "INSERT INTO autor (Nome) VALUES (?)";
            executarQuery($pdo, $sql, [$nome]);
            
            $mensagem = "Autor cadastrado com sucesso!";
            $tipo_mensagem = "success";
            
        } elseif ($acao === 'editar') {
            $codau = (int)$_POST['codau'];
            $nome = trim($_POST['nome']);
            
            // Validar dados usando a função reutilizável (passando o ID para excluir da verificação de duplicação)
            $erros_validacao = validarDadosAutor($nome, $pdo, $codau);
            
            if (!empty($erros_validacao)) {
                throw new Exception(implode("<br>", $erros_validacao));
            }
            
            $sql = "UPDATE autor SET Nome = ? WHERE CodAu = ?";
            executarQuery($pdo, $sql, [$nome, $codau]);
            
            $mensagem = "Autor atualizado com sucesso!";
            $tipo_mensagem = "success";
            
        } elseif ($acao === 'excluir') {

            $codau = (int)$_POST['codau'];
            
            // Verificar se o autor tem livros associados
            $sql_check = "SELECT COUNT(*) FROM livro_autor WHERE Autor_CodAu = ?";
            $stmt_check = executarQuery($pdo, $sql_check, [$codau]);
            if ($stmt_check->fetchColumn() > 0) {
                throw new Exception("Não é possível excluir este autor pois ele possui livros associados.");
            }
            
            $sql = "DELETE FROM autor WHERE CodAu = ?";
            executarQuery($pdo, $sql, [$codau]);
            
            $mensagem = "Autor excluído com sucesso!";
            $tipo_mensagem = "success";
        }
        
    } catch (Exception $e) {
        $mensagem = $e->getMessage();
        $tipo_mensagem = "danger";
    }
}

// Buscar autores para listagem
$sql_autores = "SELECT a.*, 
                COUNT(la.Livro_Codl) as TotalLivros,
                GROUP_CONCAT(DISTINCT l.Titulo ORDER BY l.Titulo SEPARATOR ', ') as Livros
                FROM autor a
                LEFT JOIN livro_autor la ON a.CodAu = la.Autor_CodAu
                LEFT JOIN livro l ON la.Livro_Codl = l.Codl
                GROUP BY a.CodAu, a.Nome
                ORDER BY a.Nome";

$stmt_autores = executarQuery($pdo, $sql_autores);
$autores = $stmt_autores->fetchAll();

// Variáveis para edição
$autor_editando = null;
if (isset($_GET['editar'])) {
    $codau = (int)$_GET['editar'];
    $sql_edit = "SELECT * FROM autor WHERE CodAu = ?";
    $stmt_edit = executarQuery($pdo, $sql_edit, [$codau]);
    $autor_editando = $stmt_edit->fetch();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Autores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book"></i> Sistema de Livros
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home"></i> Início
                </a>
                <a class="nav-link" href="livros.php">
                    <i class="fas fa-book"></i> Livros
                </a>
                <a class="nav-link" href="assuntos.php">
                    <i class="fas fa-tags"></i> Assuntos
                </a>
                <a class="nav-link" href="relatorio.php">
                    <i class="fas fa-chart-bar"></i> Relatórios
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <!-- Mensagens -->
        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert" id="mensagem-alert">
                <?= htmlspecialchars($mensagem) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>                
                setTimeout(function() {
                    const alert = document.getElementById('mensagem-alert');
                    if (alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            </script>
        <?php endif; ?>

        <div class="row">
            <!-- Formulário -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-<?= $autor_editando ? 'edit' : 'plus' ?>"></i>
                            <?= $autor_editando ? 'Editar Autor' : 'Novo Autor' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="acao" value="<?= $autor_editando ? 'editar' : 'inserir' ?>">
                            <?php if ($autor_editando): ?>
                                <input type="hidden" name="codau" value="<?= $autor_editando['CodAu'] ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome do Autor *</label>
                                <input type="text" class="form-control" id="nome" name="nome" 
                                       value="<?= htmlspecialchars($autor_editando['Nome'] ?? '') ?>" 
                                       maxlength="40" required>
                                <div class="form-text">Máximo 40 caracteres</div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> <?= $autor_editando ? 'Atualizar' : 'Cadastrar' ?>
                                </button>
                                <?php if ($autor_editando): ?>
                                    <a href="autores.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Lista de Autores -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> Lista de Autores (<?= count($autores) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($autores)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-user-edit fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Nenhum autor cadastrado ainda.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Total de Livros</th>
                                            <th>Livros</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($autores as $autor): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($autor['Nome']) ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary"><?= $autor['TotalLivros'] ?></span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= $autor['Livros'] ? htmlspecialchars($autor['Livros']) : 'Sem livros' ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="autores.php?editar=<?= $autor['CodAu'] ?>" 
                                                           class="btn btn-outline-success" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if ($autor['TotalLivros'] == 0): ?>
                                                            <button type="button" class="btn btn-outline-danger" 
                                                                    onclick="confirmarExclusao(<?= $autor['CodAu'] ?>, '<?= htmlspecialchars($autor['Nome']) ?>')" 
                                                                    title="Excluir">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-outline-secondary" 
                                                                    title="Não pode excluir - possui livros associados" disabled>
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="modalExcluir" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o autor <strong id="nomeAutor"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="codau" id="codauExcluir">
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarExclusao(codau, nome) {
            document.getElementById('codauExcluir').value = codau;
            document.getElementById('nomeAutor').textContent = nome;
            new bootstrap.Modal(document.getElementById('modalExcluir')).show();
        }
    </script>
</body>
</html>
