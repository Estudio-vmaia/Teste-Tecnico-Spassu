<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Cadastro de Livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }
        .card-hover {
            transition: transform 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book"></i> Sistema de Livros
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="relacionamentos.php">
                    <i class="fas fa-link"></i> Relacionamentos
                </a>
                <a class="nav-link" href="relatorio.php">
                    <i class="fas fa-chart-bar"></i> Relatórios
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 mb-4">Sistema de Cadastro de Livros</h1>
            <p class="lead">Gerencie seu acervo de livros, autores e assuntos de forma simples e eficiente</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <!-- Livros -->
            <div class="col-lg-4 mb-4">
                <div class="card card-hover h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-book fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Livros</h5>
                        <p class="card-text">Cadastre e gerencie seus livros com informações completas incluindo título, editora, edição, ano e valor.</p>
                        <a href="livros.php" class="btn btn-primary">
                            <i class="fas fa-list"></i> Gerenciar Livros
                        </a>
                    </div>
                </div>
            </div>

            <!-- Autores -->
            <div class="col-lg-4 mb-4">
                <div class="card card-hover h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-edit fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Autores</h5>
                        <p class="card-text">Mantenha o cadastro de autores e associe-os aos livros do seu acervo.</p>
                        <a href="autores.php" class="btn btn-success">
                            <i class="fas fa-users"></i> Gerenciar Autores
                        </a>
                    </div>
                </div>
            </div>

            <!-- Assuntos -->
            <div class="col-lg-4 mb-4">
                <div class="card card-hover h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-tags fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Assuntos</h5>
                        <p class="card-text">Organize seus livros por categorias e assuntos para facilitar a busca.</p>
                        <a href="assuntos.php" class="btn btn-warning">
                            <i class="fas fa-tag"></i> Gerenciar Assuntos
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Relacionamentos e Relatórios -->
        <div class="row mt-5">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-link"></i> Relacionamentos
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Associe autores e assuntos aos seus livros, criando relacionamentos many-to-many.</p>
                        <a href="relacionamentos.php" class="btn btn-secondary">
                            <i class="fas fa-link"></i> Gerenciar Relacionamentos
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar"></i> Relatórios
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Visualize relatórios detalhados do seu acervo, agrupados por autor com informações completas.</p>
                        <a href="relatorio.php" class="btn btn-info">
                            <i class="fas fa-file-alt"></i> Ver Relatório Completo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2025 Sistema de Cadastro de Livros - Desenvolvido com PHP</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
