<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG Systems Dashboard | Home</title>

    <!-- Carrega Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <!-- Carrega Ícones Bootstrap (opcional, mas altamente recomendado) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Estilos customizados mínimos para um visual limpo */
        body {
            background-color: #f0f2f5; /* Um cinza muito claro para o fundo */
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
        /* Ajuste para as pílulas de navegação */
        .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
            background-color: #0d6efd; /* Cor primária do Bootstrap */
            color: #fff;
            font-weight: 600;
        }
        .nav-pills .nav-link {
            color: #343a40;
        }
        .navbar-brand {
            font-weight: 700;
        }
    </style>
</head>
<body>

<!-- Navbar (Cabeçalho Fixo) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="bi bi-dice-5-fill me-2"></i>
            RPG Vault
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="alertNotImplemented('Página de Documentação')">Documentação</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="alertNotImplemented('Página de Perfil')">Perfil</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section (Destaque Principal) -->
<header class="py-5 bg-white border-bottom shadow-sm">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-10 mx-auto text-center">
                <h1 class="display-5 fw-bold text-dark">Gerenciador de Sistemas de RPG</h1>
                <p class="lead text-muted mb-4">
                    Visualize, organize e explore a lista de sistemas de RPG de mesa, alternando facilmente entre o Dashboard Admin e a Visualização Pública.
                </p>
                <!-- Seletor de Modo de Visualização (Bootstrap Pills) - Centralizado e visualmente aprimorado -->
                <div class="d-flex justify-content-center">
                    <ul class="nav nav-pills p-2 bg-light rounded-pill shadow-sm" id="view-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-4" id="btn-admin" data-bs-toggle="pill" data-bs-target="#admin-view" type="button" role="tab" aria-selected="true">
                                <i class="bi bi-shield-lock me-1"></i> Admin
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4" id="btn-public" data-bs-toggle="pill" data-bs-target="#public-view" type="button" role="tab" aria-selected="false">
                                <i class="bi bi-globe me-1"></i> Público
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>


<main class="container mt-5 mb-5">

    <!-- Mensagem de Sucesso (Bootstrap Alert) - Posicionado no topo do container principal -->
    <div id="success-message" class="alert alert-success d-none rounded-3 shadow-sm" role="alert">
        <p class="mb-0"><strong><i class="bi bi-check-circle-fill me-1"></i> Sucesso!</strong> <span id="success-text"></span></p>
    </div>

    <!-- Conteúdo das Vistas -->
    <div class="tab-content" id="view-content-tabs">

        <!-- Dashboard Admin (Lista CRUD) -->
        <div id="admin-view" class="tab-pane fade show active" role="tabpanel" aria-labelledby="btn-admin">

            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h2 class="h3 text-dark fw-bold">Dashboard Admin <i class="bi bi-table"></i></h2>
                <button onclick="alertNotImplemented('Criar Novo Sistema')" class="btn btn-primary rounded-pill shadow-sm">
                    <i class="bi bi-plus-circle-fill me-1"></i> Criar Novo Sistema
                </button>
            </div>

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col" class="text-center">ID</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col" class="d-none d-sm-table-cell">Foco</th>
                                    <th scope="col" class="d-none d-md-table-cell">Complexidade</th>
                                    <th scope="col" class="d-none d-lg-table-cell">Criado em</th>
                                    <th scope="col" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="admin-table-body">
                                <!-- Linhas serão preenchidas por JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Simulação de Paginação -->
                <div class="card-footer bg-light d-flex justify-content-between align-items-center text-sm text-muted border-top rounded-bottom-3">
                    <span class="text-muted small">Exibindo 1 a <span id="total-sistemas"></span> sistemas</span>
                    <nav aria-label="Navegação da Tabela">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#" onclick="alertNotImplemented('Próxima Página')">2</a></li>
                            <li class="page-item"><a class="page-link" href="#" onclick="alertNotImplemented('Próxima Página')">Próximo</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Visualização Pública (Descrição) -->
        <div id="public-view" class="tab-pane fade" role="tabpanel" aria-labelledby="btn-public">

            <h2 class="h3 text-dark fw-bold mb-4 border-bottom pb-3">Catálogo de Sistemas de RPG <i class="bi bi-book"></i></h2>

            <div id="public-cards-container" class="row g-4">
                <!-- Cards serão preenchidos por JS -->
            </div>
            <p id="no-systems-public" class="text-center text-muted fs-5 mt-5 d-none">
                <i class="bi bi-search me-1"></i> Nenhum sistema cadastrado para visualização.
            </p>
        </div>

    </div>
</main>

<!-- Footer -->
<footer class="bg-dark text-white-50 py-4 mt-5">
    <div class="container text-center">
        <p class="mb-0 small">&copy; 2025 RPG Vault. Todos os direitos reservados. | Desenvolvido com Bootstrap 5.</p>
    </div>
</footer>

<!-- Modal de Confirmação de Exclusão (Bootstrap Modal) -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-danger border-3">
            <div class="modal-header bg-danger text-white rounded-top-3">
                <h5 class="modal-title" id="deleteModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-dark fs-5">Tem certeza que deseja excluir o sistema <strong><span id="system-name-to-delete" class="text-danger"></span></strong>?</p>
                <p class="text-muted small">Essa ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancel-delete">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Excluir Permanentemente</button>
            </div>
        </div>
    </div>
</div>

<!-- Carrega Bootstrap 5 JS e dependências -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<script>
    // --- DADOS MOCKADOS ---
    const mockSistemas = [
        { id: 1, nome: "Dungeons & Dragons 5e", foco: "Fantasia Medieval", complexidade: "Média", created_at: "20/10/2023", descricao: "O RPG de mesa mais famoso, focado em aventuras épicas, exploração e interpretação de personagens. Ideal para iniciantes.", slug: 'dd5e', icon: 'dungeon' },
        { id: 2, nome: "Chamado de Cthulhu 7E", foco: "Horror Cósmico", complexidade: "Baixa/Média", created_at: "15/05/2023", descricao: "Focado em investigação e na fragilidade da sanidade humana contra os horrores Lovecraftianos. É um jogo de terror e mistério.", slug: 'cthulhu7e', icon: 'mask' },
        { id: 3, nome: "Tormenta 20", foco: "Fantasia Brasileira", complexidade: "Alta", created_at: "01/01/2024", descricao: "Sistema próprio de fantasia heroica com mecânicas robustas e mundo detalhado de Arton. Perfeito para quem gosta de regras detalhadas.", slug: 't20', icon: 'shield' },
        { id: 4, nome: "Shadowrun 6ª Edição", foco: "Cyberpunk/Fantasia", complexidade: "Alta", created_at: "10/02/2023", descricao: "Uma mistura de magia, tecnologia futurista e corporações sombrias. Complexo, mas muito recompensador.", slug: 'shadowrun6e', icon: 'lightning' },
        { id: 5, nome: "Monster of the Week", foco: "Séries de TV (Buffy)", complexidade: "Baixa", created_at: "05/11/2024", descricao: "Um sistema 'Powered by the Apocalypse' focado em caçar monstros semanais em um estilo televisivo.", slug: 'motw', icon: 'bug' },
    ];

    let sistemaToDelete = null;
    let deleteModalInstance;

    // --- FUNÇÕES DE RENDERIZAÇÃO ---

    /**
     * Retorna um ícone Bootstrap baseado no tema do RPG (simulação).
     */
    function getSystemIcon(iconSlug) {
        const iconMap = {
            dungeon: 'bi-castle-fill',
            mask: 'bi-moon-stars-fill',
            shield: 'bi-gem',
            lightning: 'bi-cpu-fill',
            bug: 'bi-person-x-fill'
        };
        return iconMap[iconSlug] || 'bi-book-half';
    }


    /**
     * Preenche a tabela do Admin (CRUD)
     */
    function renderAdminTable() {
        const tbody = document.getElementById('admin-table-body');
        tbody.innerHTML = '';

        if (mockSistemas.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Nenhum sistema encontrado.</td></tr>`;
            document.getElementById('total-sistemas').textContent = '0';
            return;
        }

        mockSistemas.forEach(sistema => {
            const row = `
                <tr>
                    <td scope="row" class="text-center">${sistema.id}</td>
                    <td>${sistema.nome}</td>
                    <td class="d-none d-sm-table-cell"><span class="badge text-bg-secondary">${sistema.foco ?? 'N/A'}</span></td>
                    <td class="d-none d-md-table-cell">${sistema.complexidade ?? 'N/A'}</td>
                    <td class="d-none d-lg-table-cell">${sistema.created_at}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center flex-wrap gap-1">
                            <button onclick="alertNotImplemented('Ver ${sistema.nome}')" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button onclick="alertNotImplemented('Editar ${sistema.nome}')" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" onclick="showDeleteModal(${sistema.id}, '${sistema.nome}')" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });

        document.getElementById('total-sistemas').textContent = mockSistemas.length;
    }

    /**
     * Preenche a visualização Pública (Cards de Detalhes)
     */
    function renderPublicCards() {
        const container = document.getElementById('public-cards-container');
        container.innerHTML = '';

        if (mockSistemas.length === 0) {
             document.getElementById('no-systems-public').classList.remove('d-none');
             return;
        } else {
             document.getElementById('no-systems-public').classList.add('d-none');
        }

        mockSistemas.forEach(sistema => {
            const card = `
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 border-top border-primary border-4 rounded-4 transition-shadow hover-shadow-lg" style="transition: all 0.3s ease-in-out;">
                        <div class="card-body d-flex flex-column justify-content-between p-4">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi ${getSystemIcon(sistema.icon)} fs-3 text-primary me-3"></i>
                                    <h5 class="card-title fw-bold text-dark mb-0">${sistema.nome}</h5>
                                </div>
                                <div class="mb-3">
                                    <span class="badge text-bg-primary me-2 rounded-pill">${sistema.foco}</span>
                                    <span class="badge text-bg-info text-dark rounded-pill">Comp.: ${sistema.complexidade}</span>
                                </div>
                                <p class="card-text text-muted small">${sistema.descricao}</p>
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <button onclick="alertNotImplemented('Acessar Detalhes de ${sistema.nome}')" class="btn btn-sm btn-outline-primary rounded-pill">
                                    Ver Mais Detalhes &rarr;
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', card);
        });
    }

    // --- LÓGICA DE AÇÕES (CRUD/Simulação) ---

    /**
     * Exibe o modal de confirmação de exclusão (usando a API do Bootstrap).
     */
    function showDeleteModal(id, nome) {
        sistemaToDelete = { id: id, nome: nome };
        document.getElementById('system-name-to-delete').textContent = nome;

        if (!deleteModalInstance) {
             const deleteModalElement = document.getElementById('deleteModal');
             deleteModalInstance = new bootstrap.Modal(deleteModalElement);
        }
        deleteModalInstance.show();
    }

    /**
     * Oculta o modal de confirmação de exclusão.
     */
    function hideDeleteModal() {
        if (deleteModalInstance) {
            deleteModalInstance.hide();
        }
        sistemaToDelete = null;
    }

    /**
     * Simula a exclusão.
     */
    function confirmDelete() {
        if (sistemaToDelete) {
            const index = mockSistemas.findIndex(s => s.id === sistemaToDelete.id);
            if (index !== -1) {
                const nomeExcluido = mockSistemas[index].nome;
                mockSistemas.splice(index, 1);

                // Exibe a mensagem de sucesso
                const successAlert = document.getElementById('success-message');
                successAlert.classList.remove('d-none', 'alert-info', 'alert-danger');
                successAlert.classList.add('alert-success');
                document.getElementById('success-text').innerHTML = `Sistema <strong>${nomeExcluido}</strong> excluído com sucesso.`;

                setTimeout(() => {
                    successAlert.classList.add('d-none');
                }, 3000);

                renderAdminTable(); // Recarrega a tabela
            }
        }
        hideDeleteModal();
    }

    /**
     * Função substituta para alert() - usa o alerta Bootstrap
     */
    function alertNotImplemented(action) {
        const successAlert = document.getElementById('success-message');
        successAlert.classList.remove('d-none', 'alert-success', 'alert-danger');
        successAlert.classList.add('alert-info');
        document.getElementById('success-text').innerHTML = `Ação de <strong>"${action}"</strong> simulada. Em um ambiente real, esta ação levaria a uma nova rota.`;

        setTimeout(() => {
            successAlert.classList.add('d-none');
        }, 3000);
    }

    // --- INICIALIZAÇÃO E LISTENERS ---

    document.addEventListener('DOMContentLoaded', () => {
        // Inicializa a tabela admin por padrão
        renderAdminTable();

        // Inicializa o modal (necessário para a API de JS)
        const deleteModalElement = document.getElementById('deleteModal');
        deleteModalInstance = new bootstrap.Modal(deleteModalElement);

        // Listener para alternar a visualização (chama a renderização pública quando o tab é ativado)
        document.getElementById('btn-public').addEventListener('click', renderPublicCards);

        // Listeners para o Modal de Exclusão
        document.getElementById('confirm-delete').addEventListener('click', confirmDelete);

    });

</script>
</body>
</html>
