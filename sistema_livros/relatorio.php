<?php
require_once 'conn.php';

// Buscar dados do relatório usando a view
$sql_relatorio = "SELECT * FROM vw_relatorio_livros_por_autor ORDER BY NomeAutor, Titulo";
$stmt_relatorio = executarQuery($pdo, $sql_relatorio);
$dados_relatorio = $stmt_relatorio->fetchAll();

// Calcular estatísticas gerais
$sql_stats = "SELECT 
              COUNT(DISTINCT CodAu) as TotalAutores,
              COUNT(DISTINCT Codl) as TotalLivros,
              SUM(ValorTotalLivros) as ValorTotalAcervo,
              AVG(Valor) as ValorMedioLivro
              FROM vw_relatorio_livros_por_autor";

$stmt_stats = executarQuery($pdo, $sql_stats);
$estatisticas = $stmt_stats->fetch();

// Agrupar dados por autor para exibição
$relatorio_agrupado = [];
foreach ($dados_relatorio as $linha) {
    
    $autor = $linha['NomeAutor'];
    if (!isset($relatorio_agrupado[$autor])) {
        $relatorio_agrupado[$autor] = [
            'CodAu' => $linha['CodAu'],
            'NomeAutor' => $linha['NomeAutor'],
            'TotalLivros' => 0,
            'ValorTotalLivros' => 0,
            'Livros' => []
        ];
    }
    
    $relatorio_agrupado[$autor]['Livros'][] = $linha;
    $relatorio_agrupado[$autor]['TotalLivros'] += $linha['TotalLivros'];
    $relatorio_agrupado[$autor]['ValorTotalLivros'] += $linha['ValorTotalLivros'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Livros por Autor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link href="css/layout.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .autor-header {
            background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #007bff;
        }
        .print-only {
            display: none;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            .container {
                max-width: none !important;
            }
        }
    </style>
</head>
<body>
    
    <?php include 'menu.php'; ?>

    <div class="container my-4 main-content">
        <!-- Cabeçalho do Relatório -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header stats-card">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h3><?= $estatisticas['TotalAutores'] ?></h3>
                                <p class="mb-0">Autores</p>
                            </div>
                            <div class="col-md-3">
                                <h3><?= $estatisticas['TotalLivros'] ?></h3>
                                <p class="mb-0">Livros</p>
                            </div>
                            <div class="col-md-3">
                                <h3>R$ <?= number_format($estatisticas['ValorTotalAcervo'] ?? 0, 2, ',', '.') ?></h3>
                                <p class="mb-0">Valor Total</p>
                            </div>
                            <div class="col-md-3">
                                <h3>R$ <?= number_format($estatisticas['ValorMedioLivro'] ?? 0, 2, ',', '.') ?></h3>
                                <p class="mb-0">Valor Médio</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controles -->
        <div class="row mb-3 no-print">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-bar"></i> Relatório de Livros Agrupados por Autor
                                </h5>
                                <small class="text-muted">Dados extraídos da view vw_relatorio_livros_por_autor</small>
                            </div>
                            <div class="col-md-4 text-end">
                                <button onclick="window.print()" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-print"></i> Imprimir
                                </button>
                                <button onclick="exportarCSV()" class="btn btn-outline-success">
                                    <i class="fas fa-download"></i> Exportar CSV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cabeçalho para impressão -->
        <div class="print-only mb-4">
            <h2 class="text-center">Relatório de Livros por Autor</h2>
            <p class="text-center text-muted">Gerado em <?= date('d/m/Y H:i') ?></p>
            <hr>
        </div>

        <!-- Relatório Detalhado -->
        <?php if (empty($relatorio_agrupado)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum dado disponível para o relatório</h5>
                            <p class="text-muted">Cadastre livros e autores para visualizar o relatório.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($relatorio_agrupado as $autor): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <!-- Cabeçalho do Autor -->
                            <div class="card-header autor-header">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-0">
                                            <i class="fas fa-user-edit"></i> <?= htmlspecialchars($autor['NomeAutor'] ?? '') ?>
                                        </h5>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge bg-primary fs-6">
                                            <?= $autor['TotalLivros'] ?> livro(s)
                                        </span>
                                        <span class="badge bg-success fs-6 ms-2">
                                            R$ <?= number_format($autor['ValorTotalLivros'] ?? 0, 2, ',', '.') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Livros do Autor -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Editora</th>
                                                <th>Edição</th>
                                                <th>Ano</th>
                                                <th>Valor</th>
                                                <th>Assuntos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($autor['Livros'] as $livro): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($livro['Titulo'] ?? '') ?></strong>
                                                    </td>
                                                    <td><?= htmlspecialchars($livro['Editora'] ?? '') ?></td>
                                                    <td><?= $livro['Edicao'] ?></td>
                                                    <td><?= $livro['AnoPublicacao'] ?></td>
                                                    <td>
                                                        <span class="text-success fw-bold">
                                                            R$ <?= number_format($livro['Valor'] ?? 0, 2, ',', '.') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?= $livro['Assuntos'] ? htmlspecialchars($livro['Assuntos'] ?? '') : 'Sem assuntos' ?>
                                                        </small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Resumo Final -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle"></i> Resumo do Relatório
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Total de Autores:</strong> <?= $estatisticas['TotalAutores'] ?></p>
                                <p><strong>Total de Livros:</strong> <?= $estatisticas['TotalLivros'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Valor Total do Acervo:</strong> R$ <?= number_format($estatisticas['ValorTotalAcervo'] ?? 0, 2, ',', '.') ?></p>
                                <p><strong>Valor Médio por Livro:</strong> R$ <?= number_format($estatisticas['ValorMedioLivro'] ?? 0, 2, ',', '.') ?></p>
                            </div>
                        </div>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-database"></i> 
                            Dados extraídos da view <code>vw_relatorio_livros_por_autor</code> em <?= date('d/m/Y H:i:s') ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportarCSV() {
            // Preparar dados para CSV
            let csvContent = "Autor,Título,Editora,Edição,Ano,Valor,Assuntos\n";
            
            <?php foreach ($dados_relatorio as $linha): ?>
                csvContent += "<?= addslashes($linha['NomeAutor']) ?>,<?= addslashes($linha['Titulo']) ?>,<?= addslashes($linha['Editora']) ?>,<?= $linha['Edicao'] ?>,<?= $linha['AnoPublicacao'] ?>,<?= $linha['Valor'] ?>,<?= addslashes($linha['Assuntos']) ?>\n";
            <?php endforeach; ?>
            
            // Criar e baixar arquivo
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'relatorio_livros_por_autor_<?= date('Y-m-d_H-i-s') ?>.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
