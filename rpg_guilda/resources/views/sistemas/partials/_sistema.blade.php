@push('styles')
<style>
    /* Card do Sistema */
    .system-card {
        transition: all 0.3s ease;
        border-left: 4px solid #0d6efd;
        overflow: hidden;
    }
    .system-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15) !important;
    }

    /* Badge de Complexidade */
    .complexity-badge {
        padding: 0.35rem 0.75rem;
        font-size: 0.85rem;
        border-radius: 20px;
    }

    /* Ícone Principal do Card */
    .system-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white; 
    }

    /* Tags Informativas */
    .system-tag {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        padding: 0.25rem 0.75rem;
        font-size: 0.8rem;
    }

    /* Emoji Alinhamento */
    .emoji-badge {
        font-size: 1.1rem; 
        margin-right: 0.3rem;
    }

    /* Gradientes para Estatísticas */
    .bg-gradient-primary { background: linear-gradient(90deg, #0d6efd, #004d9c); }
    .bg-gradient-success { background: linear-gradient(90deg, #198754, #0f5132); }
    .bg-gradient-warning { 
        background: linear-gradient(90deg, #ffc107, #cc9a00); 
        color: #212529 !important;
    }
    .bg-gradient-danger { background: linear-gradient(90deg, #dc3545, #a3000f); }
</style>
@endpush

<div class="container-fluid py-3">

    {{-- Estatísticas Rápidas --}}
    <div class="row mb-4">
        @php
            $stats = [
                ['label' => 'TOTAL', 'count' => $sistemas->count(), 'bg' => 'bg-gradient-primary', 'emoji' => '🎯'],
                ['label' => 'BAIXA COMPLEXIDADE', 'count' => $sistemas->filter(fn($s) => str_contains(strtolower($s->complexidade ?? ''), 'baixa'))->count(), 'bg' => 'bg-gradient-success', 'emoji' => '✅'],
                ['label' => 'MÉDIA COMPLEXIDADE', 'count' => $sistemas->filter(fn($s) => str_contains(strtolower($s->complexidade ?? ''), 'média') || str_contains(strtolower($s->complexidade ?? ''), 'media'))->count(), 'bg' => 'bg-gradient-warning', 'emoji' => '⚖️'],
                ['label' => 'ALTA COMPLEXIDADE', 'count' => $sistemas->filter(fn($s) => str_contains(strtolower($s->complexidade ?? ''), 'alta'))->count(), 'bg' => 'bg-gradient-danger', 'emoji' => '⚠️']
            ];
        @endphp

        @foreach($stats as $stat)
            <div class="col-md-3 mb-3">
                <div class="card {{ $stat['bg'] }} text-white border-0 shadow-sm">
                    <div class="card-body py-3 d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-white-50 mb-1">{{ $stat['label'] }}</h6>
                            <h3 class="mb-0">{{ $stat['count'] }}</h3>
                        </div>
                        <span style="font-size: 2rem;">{{ $stat['emoji'] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filtros e Busca --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-0">
                            <span class="emoji-badge">🔍</span>
                        </span>
                        <input type="text" class="form-control border-0 bg-light" placeholder="Buscar sistema por nome..." id="systemSearch">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-lg bg-light border-0" id="complexityFilter">
                        <option value="">📊 Todas complexidades</option>
                        <option value="baixa">✅ Baixa</option>
                        <option value="média">⚖️ Média</option>
                        <option value="alta">⚠️ Alta</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-lg bg-light border-0" id="focusFilter">
                        <option value="">🎯 Todos os focos</option>
                        @foreach($sistemas->pluck('foco')->unique()->filter()->sort() as $foco)
                            <option value="{{ strtolower($foco) }}">{{ $foco }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Conteúdo Principal --}}
    @if($sistemas->isEmpty())
        <div class="text-center py-5">
            <span style="font-size: 4rem;">🎲</span>
            <h3 class="text-muted mt-3">Nenhum Sistema Cadastrado</h3>
            <p class="text-muted mb-4">Comece adicionando seu primeiro sistema de RPG</p>
            <a href="{{ route('sistemas.create') }}" class="btn btn-primary btn-lg">
                <span class="emoji-badge">➕</span> Adicionar Primeiro Sistema
            </a>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="systemsGrid">
            @foreach($sistemas as $sistema)
                @php
                    $complexidade = strtolower($sistema->complexidade ?? '');
                    $complexidadeData = match(true) {
                        str_contains($complexidade, 'baixa') => ['bg' => 'bg-success', 'text' => 'text-white', 'emoji' => '✅', 'nome' => 'Baixa'],
                        str_contains($complexidade, 'média'), str_contains($complexidade, 'media') => ['bg' => 'bg-warning', 'text' => 'text-dark', 'emoji' => '⚖️', 'nome' => 'Média'],
                        str_contains($complexidade, 'alta') => ['bg' => 'bg-danger', 'text' => 'text-white', 'emoji' => '⚠️', 'nome' => 'Alta'],
                        default => ['bg' => 'bg-secondary', 'text' => 'text-white', 'emoji' => '❓', 'nome' => '—']
                    };
                @endphp

                <div class="col" data-name="{{ strtolower($sistema->nome) }}" 
                     data-complexity="{{ $complexidadeData['nome'] }}" 
                     data-focus="{{ strtolower($sistema->foco ?? '') }}">
                    <div class="card system-card h-100 shadow-sm">
                        <div class="card-body">

                            {{-- Cabeçalho --}}
                            <div class="d-flex align-items-start mb-3">
                                <div class="system-icon me-3">🎲</div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <small class="text-muted d-block">🆔 #{{ $sistema->id }}</small>
                                            <h5 class="card-title mb-0 fw-bold">📝 {{ $sistema->nome }}</h5>
                                        </div>
                                        <span class="badge {{ $complexidadeData['bg'] }} {{ $complexidadeData['text'] }} complexity-badge">
                                            <span class="emoji-badge">{{ $complexidadeData['emoji'] }}</span> {{ $sistema->complexidade ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Informações --}}
                            <div class="mb-4">
                                <div class="row g-2">
                                    <div class="col-12 mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="emoji-badge">🎯</span>
                                            <div>
                                                <small class="text-muted d-block">FOCO PRINCIPAL</small>
                                                <span class="fw-semibold">{{ $sistema->foco ?? '—' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-start">
                                            <span class="emoji-badge mt-1">⚙️</span>
                                            <div>
                                                <small class="text-muted d-block">MECÂNICA PRINCIPAL</small>
                                                <p class="mb-0 small">{{ $sistema->mecanica_principal ?? '—' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tags --}}
                            <div class="mb-3">
                                @if($sistema->dados_principais)
                                    <span class="system-tag me-2 mb-2 d-inline-block">🎲 {{ $sistema->dados_principais }}</span>
                                @endif
                                @if($sistema->genero)
                                    <span class="system-tag me-2 mb-2 d-inline-block">🏷️ {{ $sistema->genero }}</span>
                                @endif
                            </div>

                            {{-- Ações --}}
                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route('sistemas.show', $sistema) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center" title="Ver Detalhes">👁️ Ver</a>
                                <a href="{{ route('sistemas.edit', $sistema) }}" class="btn btn-outline-warning btn-sm d-flex align-items-center" title="Editar">✏️ Editar</a>
                                <form action="{{ route('sistemas.destroy', $sistema) }}" method="POST" id="deleteForm-{{ $sistema->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center delete-btn" data-name="{{ $sistema->nome }}" data-form="deleteForm-{{ $sistema->id }}" title="Excluir sistema">🗑️ Excluir</button>
                                </form>
                            </div>

                        </div>

                        {{-- Rodapé --}}
                        <div class="card-footer bg-transparent border-top-0 py-2 d-flex justify-content-between">
                            <small class="text-muted">📅 Criado em: {{ $sistema->created_at->format('d/m/Y') }}</small>
                            <small class="text-muted">👤 {{ $sistema->personagens_count ?? 0 }} personagens</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginação --}}
        @if($sistemas->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $sistemas->links() }}
            </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('systemSearch');
    const complexityFilter = document.getElementById('complexityFilter');
    const focusFilter = document.getElementById('focusFilter');
    const systemsGrid = document.getElementById('systemsGrid');
    const systemCards = systemsGrid ? Array.from(systemsGrid.querySelectorAll('.col')) : [];

    function normalizeString(str) {
        if (!str) return '';
        return str.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "");
    }

    function filterSystems() {
        const searchTerm = normalizeString(searchInput.value);
        const selectedComplexity = normalizeString(complexityFilter.value);
        const selectedFocus = normalizeString(focusFilter.value);

        systemCards.forEach(card => {
            const name = normalizeString(card.dataset.name);
            const complexity = normalizeString(card.dataset.complexity);
            const focus = normalizeString(card.dataset.focus);

            const matchesSearch = name.includes(searchTerm);
            const matchesComplexity = !selectedComplexity || complexity.includes(selectedComplexity);
            const matchesFocus = !selectedFocus || focus.includes(selectedFocus);

            card.style.display = (matchesSearch && matchesComplexity && matchesFocus) ? 'block' : 'none';
        });
    }

    searchInput.addEventListener('input', filterSystems);
    complexityFilter.addEventListener('change', filterSystems);
    focusFilter.addEventListener('change', filterSystems);

    // Exclusão com confirmação
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const systemName = this.dataset.name;
            const formId = this.dataset.form;
            const executeDelete = () => document.getElementById(formId).submit();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Tem certeza?',
                    html: `Você está prestes a excluir o sistema <strong>"${systemName}"</strong>.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    backdrop: true
                }).then((result) => { if (result.isConfirmed) executeDelete(); });
            } else {
                if (confirm(`Tem certeza que deseja excluir "${systemName}"?\nEsta ação é irreversível!`)) executeDelete();
            }
        });
    });
});
</script>
@endpush
