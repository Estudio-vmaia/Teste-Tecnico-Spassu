<?php
require_once 'conn.php';

// Variáveis para mensagens
$mensagem = '';
$tipo_mensagem = '';

// Processar formulário
if ($_POST) {
    $acao = $_POST['acao'] ?? '';
    
    try {
        if ($acao === 'inserir') {
            $descricao = trim($_POST['descricao']);
            
            // Validações
            if (empty($descricao)) {
                throw new Exception("A descrição do assunto é obrigatória.");
            }
            
            if (strlen($descricao) > 20) {
                throw new Exception("A descrição do assunto deve ter no máximo 20 caracteres.");
            }
            
            // Verificar se já existe
            $sql_check = "SELECT COUNT(*) FROM assunto WHERE Descricao = ?";
            $stmt_check = executarQuery($pdo, $sql_check, [$descricao]);
            if ($stmt_check->fetchColumn() > 0) {
                throw new Exception("Já existe um assunto com esta descrição.");
            }
            
            $sql = "INSERT INTO assunto (Descricao) VALUES (?)";
            executarQuery($pdo, $sql, [$descricao]);
            
            $mensagem = "Assunto cadastrado com sucesso!";
            $tipo_mensagem = "success";
            
        } elseif ($acao === 'editar') {
            $codas = (int)$_POST['codas'];
            $descricao = trim($_POST['descricao']);
            
            // Validações (mesmas do inserir)
            if (empty($descricao)) {
                throw new Exception("A descrição do assunto é obrigatória.");
            }
            
            if (strlen($descricao) > 20) {
                throw new Exception("A descrição do assunto deve ter no máximo 20 caracteres.");
            }
            
            // Verificar se já existe (exceto o próprio registro)
            $sql_check = "SELECT COUNT(*) FROM assunto WHERE Descricao = ? AND codAs != ?";
            $stmt_check = executarQuery($pdo, $sql_check, [$descricao, $codas]);
            if ($stmt_check->fetchColumn() > 0) {
                throw new Exception("Já existe um assunto com esta descrição.");
            }
            
            $sql = "UPDATE assunto SET Descricao = ? WHERE codAs = ?";
            executarQuery($pdo, $sql, [$descricao, $codas]);
            
            $mensagem = "Assunto atualizado com sucesso!";
            $tipo_mensagem = "success";
            
        } elseif ($acao === 'excluir') {
            $codas = (int)$_POST['codas'];
            
            // Verificar se o assunto tem livros associados
            $sql_check = "SELECT COUNT(*) FROM livro_assunto WHERE Assunto_codAs = ?";
            $stmt_check = executarQuery($pdo, $sql_check, [$codas]);
            if ($stmt_check->fetchColumn() > 0) {
                throw new Exception("Não é possível excluir este assunto pois ele possui livros associados.");
            }
            
            $sql = "DELETE FROM assunto WHERE codAs = ?";
            executarQuery($pdo, $sql, [$codas]);
            
            $mensagem = "Assunto excluído com sucesso!";
            $tipo_mensagem = "success";
        }
        
    } catch (Exception $e) {
        $mensagem = $e->getMessage();
        $tipo_mensagem = "danger";
    }
}

// Buscar assuntos para listagem
$sql_assuntos = "SELECT s.*, 
                 COUNT(las.Livro_Codl) as TotalLivros,
                 GROUP_CONCAT(DISTINCT l.Titulo ORDER BY l.Titulo SEPARATOR ', ') as Livros
                 FROM assunto s
                 LEFT JOIN livro_assunto las ON s.codAs = las.Assunto_codAs
                 LEFT JOIN livro l ON las.Livro_Codl = l.Codl
                 GROUP BY s.codAs, s.Descricao
                 ORDER BY s.Descricao";

$stmt_assuntos = executarQuery($pdo, $sql_assuntos);
$assuntos = $stmt_assuntos->fetchAll();

// Variáveis para edição
$assunto_editando = null;
if (isset($_GET['editar'])) {
    $codas = (int)$_GET['editar'];
    $sql_edit = "SELECT * FROM assunto WHERE codAs = ?";
    $stmt_edit = executarQuery($pdo, $sql_edit, [$codas]);
    $assunto_editando = $stmt_edit->fetch();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Assuntos</title>
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
                <a class="nav-link" href="autores.php">
                    <i class="fas fa-user-edit"></i> Autores
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
            <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mensagem) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Formulário -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-<?= $assunto_editando ? 'edit' : 'plus' ?>"></i>
                            <?= $assunto_editando ? 'Editar Assunto' : 'Novo Assunto' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="acao" value="<?= $assunto_editando ? 'editar' : 'inserir' ?>">
                            <?php if ($assunto_editando): ?>
                                <input type="hidden" name="codas" value="<?= $assunto_editando['codAs'] ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição do Assunto *</label>
                                <input type="text" class="form-control" id="descricao" name="descricao" 
                                       value="<?= htmlspecialchars($assunto_editando['Descricao'] ?? '') ?>" 
                                       maxlength="20" required>
                                <div class="form-text">Máximo 20 caracteres</div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> <?= $assunto_editando ? 'Atualizar' : 'Cadastrar' ?>
                                </button>
                                <?php if ($assunto_editando): ?>
                                    <a href="assuntos.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Lista de Assuntos -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> Lista de Assuntos (<?= count($assuntos) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($assuntos)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Nenhum assunto cadastrado ainda.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Descrição</th>
                                            <th>Total de Livros</th>
                                            <th>Livros</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($assuntos as $assunto): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($assunto['Descricao']) ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning"><?= $assunto['TotalLivros'] ?></span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= $assunto['Livros'] ? htmlspecialchars($assunto['Livros']) : 'Sem livros' ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="assuntos.php?editar=<?= $assunto['codAs'] ?>" 
                                                           class="btn btn-outline-warning" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if ($assunto['TotalLivros'] == 0): ?>
                                                            <button type="button" class="btn btn-outline-danger" 
                                                                    onclick="confirmarExclusao(<?= $assunto['codAs'] ?>, '<?= htmlspecialchars($assunto['Descricao']) ?>')" 
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
                    <p>Tem certeza que deseja excluir o assunto <strong id="descricaoAssunto"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="codas" id="codasExcluir">
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
        function confirmarExclusao(codas, descricao) {
            document.getElementById('codasExcluir').value = codas;
            document.getElementById('descricaoAssunto').textContent = descricao;
            new bootstrap.Modal(document.getElementById('modalExcluir')).show();
        }
    </script>
</body>
</html>
