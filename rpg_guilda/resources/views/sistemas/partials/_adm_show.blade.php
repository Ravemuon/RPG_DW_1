@extends('layouts.app')

@section('title', 'Detalhes do Sistema: ' . $sistema->nome)

@section('content')
<div class="container py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('sistemas.index') }}">Sistemas</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $sistema->nome }}</li>
        </ol>
    </nav>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="sistema-icon">
                    <i class="bi bi-dice-6-fill fs-1 text-primary"></i>
                </div>
                <div>
                    <h1 class="fw-bold mb-1">{{ $sistema->nome }}</h1>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-tag me-1"></i>{{ $sistema->foco ?: 'Geral' }}
                        </span>
                        <span class="text-muted">
                            <i class="bi bi-calendar3 me-1"></i>Atualizado {{ $sistema->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button onclick="window.history.back()" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </button>
            
            @if(auth()->user()?->is_admin)
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-gear me-1"></i>Ações
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('sistemas.edit', $sistema->id) }}">
                            <i class="bi bi-pencil me-2"></i>Editar Sistema
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('sistemas.create') }}">
                            <i class="bi bi-plus-lg me-2"></i>Novo Sistema
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('sistemas.destroy', $sistema->id) }}"
                              id="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="dropdown-item text-danger" 
                                    onclick="confirmDelete()">
                                <i class="bi bi-trash me-2"></i>Excluir
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </div>

    {{-- Grid Principal --}}
    <div class="row">
        {{-- Coluna Esquerda: Informações Gerais --}}
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary bg-gradient text-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Detalhes do Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Descrição</h6>
                        <p class="card-text">
                            @if($sistema->descricao)
                                {{ $sistema->descricao }}
                            @else
                                <span class="text-muted fst-italic">Sem descrição disponível</span>
                            @endif
                        </p>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <h6 class="text-uppercase text-muted small fw-bold mb-2">
                                <i class="bi bi-bullseye me-1"></i>Foco
                            </h6>
                            <span class="d-block fw-semibold">
                                {{ $sistema->foco ?: 'Não especificado' }}
                            </span>
                        </div>
                        
                        <div class="col-6 mb-3">
                            <h6 class="text-uppercase text-muted small fw-bold mb-2">
                                <i class="bi bi-gear me-1"></i>Mecânica
                            </h6>
                            <span class="d-block fw-semibold">
                                {{ $sistema->mecanica_principal ?: 'Não definida' }}
                            </span>
                        </div>
                    </div>

                    {{-- Complexidade --}}
                    <div class="mb-3">
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">
                            <i class="bi bi-speedometer2 me-1"></i>Complexidade
                        </h6>
                        @php
                            $niveis = [
                                'Baixa' => ['icon' => 'bi-check-circle', 'color' => 'success', 'bg' => 'bg-success-light'],
                                'Média' => ['icon' => 'bi-exclamation-triangle', 'color' => 'warning', 'bg' => 'bg-warning-light'],
                                'Alta' => ['icon' => 'bi-fire', 'color' => 'danger', 'bg' => 'bg-danger-light']
                            ];
                            
                            $nivel = $sistema->complexidade ?? 'Não definida';
                            $config = $niveis[$nivel] ?? ['icon' => 'bi-question-circle', 'color' => 'secondary', 'bg' => 'bg-secondary-light'];
                        @endphp
                        
                        <div class="complexidade-indicator {{ $config['bg'] }} p-3 rounded">
                            <div class="d-flex align-items-center">
                                <i class="bi {{ $config['icon'] }} fs-4 text-{{ $config['color'] }} me-3"></i>
                                <div>
                                    <div class="fw-bold fs-5 text-{{ $config['color'] }}">{{ $nivel }}</div>
                                    <div class="progress mt-2" style="height: 6px;">
                                        @php
                                            $progress = [
                                                'Baixa' => 25,
                                                'Média' => 50,
                                                'Alta' => 85
                                            ];
                                        @endphp
                                        <div class="progress-bar bg-{{ $config['color'] }}" 
                                             style="width: {{ $progress[$nivel] ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Links --}}
                    @if($sistema->pagina)
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">
                            <i class="bi bi-link-45deg me-1"></i>Links Externos
                        </h6>
                        <a href="{{ $sistema->pagina }}" target="_blank" 
                           class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center">
                            <i class="bi bi-box-arrow-up-right me-2"></i>
                            Visitar Página Oficial
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Coluna Direita: Conteúdo do Sistema --}}
        <div class="col-lg-8">
            {{-- Regras Opcionais --}}
            @if($sistema->regras_opcionais)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info bg-gradient text-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-stars me-2"></i>Regras Opcionais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="regras-content">
                        {!! nl2br(e($sistema->regras_opcionais)) !!}
                    </div>
                </div>
            </div>
            @endif

            {{-- Atributos --}}
            @if(method_exists($sistema, 'getAtributos') && count($sistema->getAtributos()))
            @include('sistemas.partials.show-section', [
                'title' => 'Atributos',
                'icon' => 'bi-bar-chart',
                'items' => $sistema->getAtributos(),
                'color' => 'purple',
                'route' => null
            ])
            @endif

            {{-- Cards Grid para seções --}}
            <div class="row g-4">
                <div class="col-md-6">
                    @include('sistemas.partials.show-section-card', [
                        'title' => 'Raças',
                        'icon' => 'bi-people',
                        'items' => $sistema->racas,
                        'color' => 'success',
                        'route' => route('sistemas.racas.index', $sistema->id)
                    ])
                </div>
                
                <div class="col-md-6">
                    @include('sistemas.partials.show-section-card', [
                        'title' => 'Origens',
                        'icon' => 'bi-house-door',
                        'items' => $sistema->origens,
                        'color' => 'warning',
                        'route' => route('sistemas.origens.index', $sistema->id)
                    ])
                </div>
                
                <div class="col-md-6">
                    @include('sistemas.partials.show-section-card', [
                        'title' => 'Classes',
                        'icon' => 'bi-person-badge',
                        'items' => $sistema->classes,
                        'color' => 'primary',
                        'route' => route('sistemas.classes.index', $sistema->id)
                    ])
                </div>
                
                <div class="col-md-6">
                    @include('sistemas.partials.show-section-card', [
                        'title' => 'Perícias',
                        'icon' => 'bi-award',
                        'items' => $sistema->pericias,
                        'color' => 'info',
                        'route' => route('sistemas.pericias.index', $sistema->id)
                    ])
                </div>
            </div>

            {{-- Estatísticas --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>Estatísticas do Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="stat-number text-primary fw-bold fs-3">
                                {{ $sistema->racas->count() }}
                            </div>
                            <div class="text-muted small">Raças</div>
                        </div>
                        <div class="col-3">
                            <div class="stat-number text-warning fw-bold fs-3">
                                {{ $sistema->origens->count() }}
                            </div>
                            <div class="text-muted small">Origens</div>
                        </div>
                        <div class="col-3">
                            <div class="stat-number text-info fw-bold fs-3">
                                {{ $sistema->classes->count() }}
                            </div>
                            <div class="text-muted small">Classes</div>
                        </div>
                        <div class="col-3">
                            <div class="stat-number text-success fw-bold fs-3">
                                {{ $sistema->pericias->count() }}
                            </div>
                            <div class="text-muted small">Perícias</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmação --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o sistema <strong>"{{ $sistema->nome }}"</strong>?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita. Todas as raças, classes, origens e perícias associadas também serão removidas.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="delete-form" class="btn btn-danger">Sim, excluir</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .sistema-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .complexidade-indicator {
        transition: transform 0.2s;
    }
    
    .complexidade-indicator:hover {
        transform: translateY(-2px);
    }
    
    .bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
    .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
    .bg-secondary-light { background-color: rgba(108, 117, 125, 0.1); }
    
    .stat-number {
        font-family: 'Segoe UI', system-ui, sans-serif;
    }
    
    .regras-content {
        line-height: 1.8;
    }
    
    .regras-content ul {
        padding-left: 1.5rem;
    }
    
    .regras-content li {
        margin-bottom: 0.5rem;
    }
    
    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
</style>
@endpush

@push('scripts')
<script>
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection