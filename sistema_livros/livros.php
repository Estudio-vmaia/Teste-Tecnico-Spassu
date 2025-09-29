<?php
require_once 'conn.php';

$mensagem = '';
$tipo_mensagem = '';
$campo_com_erro = ''; 

$dados_formulario = [
    'titulo' => '',
    'editora' => '',
    'edicao' => '1',
    'ano_publicacao' => '',
    'valor' => ''
];

function validarDadosLivro($dados, $retornar_primeiro_erro = false) {

    $titulo             = trim($dados['titulo']);
    $editora            = trim($dados['editora']);
    $edicao             = (int)$dados['edicao'];
    $ano_publicacao     = trim($dados['ano_publicacao']);
    $valor              = str_replace(',', '.', str_replace('.', '', $dados['valor']));
    
    $validacoes = [
        [
            'condicao' => empty($titulo),
            'campo'    => 'titulo',
            'mensagem' => "Todos os campos obrigatórios devem ser preenchidos."
        ],
        [
            'condicao' => empty($editora),
            'campo'    => 'editora',
            'mensagem' => "Todos os campos obrigatórios devem ser preenchidos."
        ],
        [
            'condicao' => empty($ano_publicacao),
            'campo'    => 'ano_publicacao',
            'mensagem' => "Todos os campos obrigatórios devem ser preenchidos."
        ],
        [
            'condicao' => $edicao < 1,
            'campo'    => 'edicao',
            'mensagem' => "A edição deve ser maior que zero."
        ],
        [
            'condicao' => !preg_match('/^\d{4}$/', $ano_publicacao),
            'campo'    => 'ano_publicacao',
            'mensagem' => "O ano de publicação deve ter 4 dígitos."
        ],
        [
            'condicao' => $ano_publicacao > date('Y'),
            'campo'    => 'ano_publicacao',
            'mensagem' => "O ano de publicação não pode ser superior ao ano atual."
        ],
        [
            'condicao' => $valor < 0,
            'campo'    => 'valor',
            'mensagem' => "O valor não pode ser negativo."
        ],
    ];
    
    $erros = [];
    foreach ($validacoes as $validacao) {
        if ($validacao['condicao']) {
            $erros[] = [
                'campo' => $validacao['campo'],
                'mensagem' => $validacao['mensagem']
            ];
            
            if ($retornar_primeiro_erro) {
                return $erros;
            }
        }
    }
    
    return $erros;
}

// Processar formulário
if ($_POST) {
    $acao = $_POST['acao'] ?? ''; // Inserir, Editar, Excluir
    
    // Capturar dados do formulário para persistência
    $dados_formulario = [
        'titulo'            => $_POST['titulo'] ?? '',
        'editora'           => $_POST['editora'] ?? '',
        'edicao'            => $_POST['edicao'] ?? '1',
        'ano_publicacao'    => $_POST['ano_publicacao'] ?? '',
        'valor'             => $_POST['valor'] ?? ''
    ];
    
    try {

        $titulo         = trim($dados_formulario['titulo']);
        $editora        = trim($dados_formulario['editora']);
        $edicao         = (int)$dados_formulario['edicao'];
        $ano_publicacao = trim($dados_formulario['ano_publicacao']);
        $valor          = str_replace(',', '.', str_replace('.', '', $dados_formulario['valor']));

        if ($acao === 'inserir') {
            // Validar dados usando a função reutilizável
            $erros_validacao = validarDadosLivro($dados_formulario, true);
            
            if (!empty($erros_validacao)) {
                $campo_com_erro = $erros_validacao[0]['campo'];
                throw new Exception($erros_validacao[0]['mensagem']);
            }
        
            $sql = "INSERT INTO livro (Titulo, Editora, Edicao, AnoPublicacao, Valor) VALUES (?, ?, ?, ?, ?)";
            executarQuery($pdo, $sql, [$titulo, $editora, $edicao, $ano_publicacao, $valor]);
            
            $mensagem = "Livro cadastrado com sucesso!";
            $tipo_mensagem = "success";
            
        } elseif ($acao === 'editar') {

            $codl = (int)$_POST['codl'];
            
            // Validar dados usando a função reutilizável
            $erros_validacao = validarDadosLivro($dados_formulario, false);
            
            if (!empty($erros_validacao)) {
                // Concatenar todas as mensagens de erro
                $mensagens_erro = array_map(function($erro) {
                    return $erro['mensagem'];
                }, $erros_validacao);
                throw new Exception(implode("<br>", $mensagens_erro));
            }
            
            $sql = "UPDATE livro SET Titulo = ?, Editora = ?, Edicao = ?, AnoPublicacao = ?, Valor = ? WHERE Codl = ?";
            executarQuery($pdo, $sql, [$titulo, $editora, $edicao, $ano_publicacao, $valor, $codl]);
            
            $mensagem = "Livro atualizado com sucesso!";
            $tipo_mensagem = "success";
            
        } elseif ($acao === 'excluir') {
            
            $codl = (int)$_POST['codl'];
            
            iniciarTransacao($pdo);
            
            // Remover relacionamentos primeiro
            executarQuery($pdo, "DELETE FROM livro_autor WHERE Livro_Codl = ?", [$codl]);
            executarQuery($pdo, "DELETE FROM livro_assunto WHERE Livro_Codl = ?", [$codl]);
            
            // Remover livro
            executarQuery($pdo, "DELETE FROM livro WHERE Codl = ?", [$codl]);
            
            confirmarTransacao($pdo);
            
            $mensagem = "Livro excluído com sucesso!";
            $tipo_mensagem = "success";
        }
        
    } catch (Exception $e) {
        $mensagem = $e->getMessage();
        $tipo_mensagem = "danger";
    }
}

// Buscar livros para listagem
$sql_livros = "SELECT l.*, 
               GROUP_CONCAT(DISTINCT a.Nome ORDER BY a.Nome SEPARATOR ', ') as Autores,
               GROUP_CONCAT(DISTINCT s.Descricao ORDER BY s.Descricao SEPARATOR ', ') as Assuntos
               FROM livro l
               LEFT JOIN livro_autor la ON l.Codl = la.Livro_Codl
               LEFT JOIN autor a ON la.Autor_CodAu = a.CodAu
               LEFT JOIN livro_assunto las ON l.Codl = las.Livro_Codl
               LEFT JOIN assunto s ON las.Assunto_codAs = s.codAs
               GROUP BY l.Codl
               ORDER BY l.Titulo";

$stmt_livros = executarQuery($pdo, $sql_livros);
$livros = $stmt_livros->fetchAll();

// Buscar autores e assuntos para os selects
$autores = executarQuery($pdo, "SELECT * FROM autor ORDER BY Nome")->fetchAll();
$assuntos = executarQuery($pdo, "SELECT * FROM assunto ORDER BY Descricao")->fetchAll();

$livro_editando = null;

if (isset($_GET['editar'])) {
    $codl = (int)$_GET['editar'];
    $sql_edit = "SELECT * FROM livro WHERE Codl = ?";
    $stmt_edit = executarQuery($pdo, $sql_edit, [$codl]);
    $livro_editando = $stmt_edit->fetch();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Livros</title>
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
                <?= htmlspecialchars($mensagem ?? '') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>

                <?php include 'functions.js'; ?>
                
                // Foco automático no campo com erro
                document.addEventListener('DOMContentLoaded', function() {
                    <?php if ($campo_com_erro): ?>
                        const campoErro = document.getElementById('<?= $campo_com_erro ?>');
                        if (campoErro) {
                            campoErro.focus();
                            campoErro.classList.add('is-invalid');
                        }
                    <?php else: ?>
                        // Se não há campo específico com erro, foca no primeiro campo vazio
                        const requiredFields = ['titulo', 'editora', 'edicao', 'ano_publicacao', 'valor'];
                        
                        for (const fieldId of requiredFields) {
                            const field = document.getElementById(fieldId);
                            if (field && !field.value.trim()) {
                                field.focus();
                                field.classList.add('is-invalid');
                                break;
                            }
                        }
                    <?php endif; ?>
                });
            </script>
        <?php endif; ?>

        <div class="row">
            <!-- Formulário -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-<?= $livro_editando ? 'edit' : 'plus' ?>"></i>
                            <?= $livro_editando ? 'Editar Livro' : 'Novo Livro' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="acao" value="<?= $livro_editando ? 'editar' : 'inserir' ?>">
                            <?php if ($livro_editando): ?>
                                <input type="hidden" name="codl" value="<?= $livro_editando['Codl'] ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título *</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" 
                                       value="<?= htmlspecialchars(($livro_editando['Titulo'] ?? $dados_formulario['titulo']) ?? '') ?>" 
                                       maxlength="40" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editora" class="form-label">Editora *</label>
                                <input type="text" class="form-control" id="editora" name="editora" 
                                       value="<?= htmlspecialchars(($livro_editando['Editora'] ?? $dados_formulario['editora']) ?? '') ?>" 
                                       maxlength="40" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edicao" class="form-label">Edição *</label>
                                <input type="number" class="form-control" id="edicao" name="edicao" 
                                       value="<?= $livro_editando['Edicao'] ?? $dados_formulario['edicao'] ?>" 
                                       min="1" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="ano_publicacao" class="form-label">Ano de Publicação *</label>
                                <input type="text" class="form-control" id="ano_publicacao" name="ano_publicacao" 
                                       value="<?= htmlspecialchars(($livro_editando['AnoPublicacao'] ?? $dados_formulario['ano_publicacao']) ?? '') ?>" 
                                       pattern="\d{4}" maxlength="4" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="valor" class="form-label">Valor (R$) *</label>
                                <input type="text" class="form-control valor-input" id="valor" name="valor" 
                                       value="<?= $livro_editando ? number_format($livro_editando['Valor'] ?? 0, 2, ',', '.') : $dados_formulario['valor'] ?>" 
                                       required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?= $livro_editando ? 'Atualizar' : 'Cadastrar' ?>
                                </button>
                                <?php if ($livro_editando): ?>
                                    <a href="livros.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Lista de Livros -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> Lista de Livros (<?= count($livros) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($livros)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Nenhum livro cadastrado ainda.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Editora</th>
                                            <th>Edição</th>
                                            <th>Ano</th>
                                            <th>Valor</th>
                                            <th>Autores</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($livros as $livro): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($livro['Titulo'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($livro['Editora'] ?? '') ?></td>
                                                <td><?= $livro['Edicao'] ?></td>
                                                <td><?= $livro['AnoPublicacao'] ?></td>
                                                <td>R$ <?= number_format($livro['Valor'] ?? 0, 2, ',', '.') ?></td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= $livro['Autores'] ? htmlspecialchars($livro['Autores'] ?? '') : 'Sem autores' ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="livros.php?editar=<?= $livro['Codl'] ?>" 
                                                           class="btn btn-outline-primary" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="confirmarExclusao(<?= $livro['Codl'] ?>, '<?= htmlspecialchars($livro['Titulo'] ?? '') ?>')" 
                                                                title="Excluir">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
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
                    <p>Tem certeza que deseja excluir o livro <strong id="tituloLivro"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita e removerá todos os relacionamentos.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="codl" id="codlExcluir">
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
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
                    <p>Tem certeza que deseja excluir o livro <strong id="tituloLivro"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita e removerá todos os relacionamentos.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="codl" id="codlExcluir">
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>

        // Máscara para valor monetário
        document.getElementById('valor').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (value/100).toFixed(2) + '';
            value = value.replace(".", ",");
            value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
            value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
            e.target.value = value;
        });

        // Máscara para ano
        document.getElementById('ano_publicacao').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
        });

        function confirmarExclusao(codl, titulo) {
            document.getElementById('codlExcluir').value = codl;
            document.getElementById('tituloLivro').textContent = titulo;
            new bootstrap.Modal(document.getElementById('modalExcluir')).show();
        }
    </script>
</body>
</html>
