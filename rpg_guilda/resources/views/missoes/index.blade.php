@extends('layouts.app')

@section('title', "Missões da Campanha - {$campanha->nome}")

@section('content')

@php
    $search = $search ?? '';
    $prioridade = $prioridade ?? '';

    // Funções auxiliares para badges
    function get_status_badge_fa($status) {
        return [
            'pendente'     => ['cor' => 'secondary', 'icone' => 'hourglass-half', 'texto' => 'Pendente', 'corHex' => '#6c757d'],
            'em_andamento' => ['cor' => 'info', 'icone' => 'sync-alt', 'texto' => 'Em Andamento', 'corHex' => '#0dcaf0'],
            'concluida'    => ['cor' => 'success', 'icone' => 'check-circle', 'texto' => 'Concluída', 'corHex' => '#198754'],
            'cancelada'    => ['cor' => 'danger', 'icone' => 'times-circle', 'texto' => 'Cancelada', 'corHex' => '#dc3545'],
        ][$status] ?? ['cor' => 'light', 'icone' => 'question-circle', 'texto' => 'Desconhecido', 'corHex' => '#f8f9fa'];
    }

    function get_prioridade_badge_fa($prioridade) {
        return [
            'baixa' => ['cor' => 'success', 'icone' => 'arrow-down', 'texto' => 'Baixa', 'corHex' => '#198754'],
            'media' => ['cor' => 'warning', 'icone' => 'exclamation-triangle', 'texto' => 'Média', 'corHex' => '#ffc107'],
            'alta'  => ['cor' => 'danger', 'icone' => 'fire', 'texto' => 'Alta', 'corHex' => '#dc3545'],
        ][$prioridade] ?? ['cor' => 'dark', 'icone' => 'question', 'texto' => 'Não Definida', 'corHex' => '#212529'];
    }
@endphp

<div class="container-fluid py-4 px-lg-5">

    {{-- HEADER --}}
    <div class="header-section mb-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('campanhas.index') }}" class="text-muted">Campanhas</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('campanhas.show', $campanha->id) }}" class="text-muted">{{ Str::limit($campanha->nome, 20) }}</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Missões</li>
                    </ol>
                </nav>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon bg-primary">
                        <i class="fas fa-crosshairs"></i>
                    </div>
                    <div>
                        <h1 class="fw-bold mb-1">Missões</h1>
                        <p class="text-muted mb-0">{{ $campanha->nome }}</p>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-arrow-left me-2"></i> Voltar
                </a>
                @can('update', $campanha)
                <a href="{{ route('missoes.create', $campanha->id) }}" class="btn btn-primary px-4 shadow-sm">
                    <i class="fas fa-plus-circle me-2"></i> Nova Missão
                </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- ESTATÍSTICAS RÁPIDAS --}}
    @if($missoes->count() > 0)
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h5 text-muted fw-semibold mb-0">
                <i class="fas fa-chart-simple me-2"></i>Visão Geral
            </h2>
            <span class="badge bg-light text-dark">
                Total: {{ $missoes->count() }} missões
            </span>
        </div>

        <div class="row g-3">
            @php
                $contagemStatus = [
                    'concluida' => $missoes->where('status', 'concluida')->count(),
                    'em_andamento' => $missoes->where('status', 'em_andamento')->count(),
                    'pendente' => $missoes->where('status', 'pendente')->count(),
                    'cancelada' => $missoes->where('status', 'cancelada')->count(),
                ];
            @endphp

            @foreach($contagemStatus as $status => $quantidade)
                @php $statusInfo = get_status_badge_fa($status); @endphp
                <div class="col-xl-3 col-lg-6">
                    <div class="stats-card card border-0 shadow-sm h-100">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="stats-icon" style="background: {{ $statusInfo['corHex'] }}20; color: {{ $statusInfo['corHex'] }};">
                                    <i class="fas fa-{{ $statusInfo['icone'] }}"></i>
                                </div>
                                <span class="badge bg-light text-dark small">{{ $statusInfo['texto'] }}</span>
                            </div>
                            <h3 class="fw-bold mb-0" style="color: {{ $statusInfo['corHex'] }};">{{ $quantidade }}</h3>
                            <p class="text-muted small mb-0">missões</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- FILTROS --}}
    <section class="card border-0 shadow-sm mb-5">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
                <div>
                    <h4 class="fw-bold mb-1"><i class="fas fa-filter me-2 text-primary"></i>Filtrar Missões</h4>
                    <p class="text-muted small mb-0">Busque missões específicas por critérios</p>
                </div>
                
                <div class="d-flex gap-2">
                    @if($search || $prioridade)
                    <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-outline-danger">
                        <i class="fas fa-times me-1"></i> Limpar
                    </a>
                    @endif
                </div>
            </div>

            <form method="GET" class="row g-3">
                <div class="col-xl-5 col-lg-6 col-md-12">
                    <label class="form-label small text-muted">Buscar missão</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search }}" 
                               placeholder="Digite título ou descrição..." 
                               class="form-control border-start-0">
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6">
                    <label class="form-label small text-muted">Prioridade</label>
                    <select name="prioridade" class="form-select">
                        <option value="">Todas as prioridades</option>
                        <option value="alta" @selected($prioridade == 'alta')>Alta</option>
                        <option value="media" @selected($prioridade == 'media')>Média</option>
                        <option value="baixa" @selected($prioridade == 'baixa')>Baixa</option>
                    </select>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-6">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos os status</option>
                        @foreach(['pendente', 'em_andamento', 'concluida', 'cancelada'] as $s)
                            @php $info = get_status_badge_fa($s); @endphp
                            <option value="{{ $s }}" @selected(request('status') == $s)>
                                {{ $info['texto'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-6 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Aplicar
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- GRÁFICOS --}}
    @if(isset($statusChart) && isset($prioridadeChart) && $missoes->count() > 0)
    <section class="mb-5">
        <h3 class="fw-bold mb-4">Análise de Dados</h3>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-chart-pie me-2 text-info"></i>
                            Distribuição por Status
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div style="position: relative; height: 280px; width:100%">
                            {!! $statusChart->container() !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-chart-bar me-2 text-warning"></i>
                            Distribuição por Prioridade
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div style="position: relative; height: 280px; width:100%">
                            {!! $prioridadeChart->container() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- LISTAGEM DE MISSÕES --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Todas as Missões</h3>
            <p class="text-muted mb-0">
                {{ $missoes->count() }} missões encontradas
                @if($search)
                    <span class="text-primary"> • Busca: "{{ $search }}"</span>
                @endif
                @if($prioridade)
                    @php $prioInfo = get_prioridade_badge_fa($prioridade); @endphp
                    <span class="text-primary"> • Prioridade: {{ $prioInfo['texto'] }}</span>
                @endif
            </p>
        </div>
        
        @if($missoes->count())
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-sort me-2"></i>Ordenar
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?sort=prioridade"><i class="fas fa-fire me-2"></i>Prioridade</a></li>
                <li><a class="dropdown-item" href="?sort=status"><i class="fas fa-tasks me-2"></i>Status</a></li>
                <li><a class="dropdown-item" href="?sort=titulo"><i class="fas fa-font me-2"></i>Título (A-Z)</a></li>
            </ul>
        </div>
        @endif
    </div>

    @if($missoes->count())
    <div class="row g-4">
        @foreach($missoes as $missao)
        @php
            $statusInfo = get_status_badge_fa($missao->status);
            $prioInfo = get_prioridade_badge_fa($missao->prioridade);
            $borderColor = $prioInfo['corHex'];
        @endphp

        <div class="col-xl-4 col-lg-6 col-md-12">
            <div class="card h-100 shadow-sm hover-shadow mission-card" 
                 style="border-left: 4px solid {{ $borderColor }};">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge" style="background: {{ $prioInfo['corHex'] }}20; color: {{ $prioInfo['corHex'] }};">
                                <i class="fas fa-{{ $prioInfo['icone'] }} me-1"></i> {{ $prioInfo['texto'] }}
                            </span>
                            <span class="badge" style="background: {{ $statusInfo['corHex'] }}20; color: {{ $statusInfo['corHex'] }};">
                                <i class="fas fa-{{ $statusInfo['icone'] }} me-1"></i> {{ $statusInfo['texto'] }}
                            </span>
                        </div>
                        
                        @if($missao->updated_at->diffInDays(now()) < 7)
                        <span class="badge bg-info">
                            <i class="fas fa-clock me-1"></i> Recente
                        </span>
                        @endif
                    </div>

                    <h5 class="card-title fw-bold mb-3">{{ $missao->titulo }}</h5>
                    
                    <p class="card-text text-muted mb-4">
                        {{ Str::limit($missao->descricao, 140) }}
                    </p>

                    <div class="mission-meta d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div class="text-muted small">
                            <i class="fas fa-calendar me-1"></i> 
                            {{ $missao->created_at->format('d/m/Y') }}
                        </div>
                        @if($missao->data_conclusao)
                        <div class="text-muted small">
                            <i class="fas fa-flag-checkered me-1"></i>
                            {{ \Carbon\Carbon::parse($missao->data_conclusao)->format('d/m/Y') }}
                        </div>
                        @endif
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('missoes.show', [$campanha->id, $missao->id]) }}" 
                           class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="fas fa-eye me-1"></i> Detalhes
                        </a>
                        
                        @can('update', $campanha)
                        <a href="{{ route('missoes.edit', [$campanha->id, $missao->id]) }}" 
                           class="btn btn-outline-warning btn-sm flex-fill">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        @endcan
                        
                        <a href="{{ route('missoes.exportarPdf', [$campanha->id, $missao->id]) }}" 
                           class="btn btn-outline-info btn-sm flex-fill">
                            <i class="fas fa-file-pdf me-1"></i> Exportar
                        </a>
                        
                        @can('delete', $campanha)
                        <button type="button" 
                                onclick="confirmDelete('{{ addslashes($missao->titulo) }}', this)"
                                class="btn btn-outline-danger btn-sm flex-fill"
                                data-url="{{ route('missoes.destroy', [$campanha->id, $missao->id]) }}">
                            <i class="fas fa-trash me-1"></i> Excluir
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-5">
        {{ $missoes->appends(['search' => $search, 'prioridade' => $prioridade])->links('pagination::bootstrap-5') }}
    </div>

    @else
    <div class="text-center py-5">
        <div class="empty-state">
            <div class="empty-state-icon mb-4">
                <i class="fas fa-crosshairs fa-3x text-muted"></i>
            </div>
            <h4 class="fw-bold mb-3">Nenhuma missão encontrada</h4>
            <p class="text-muted mb-4">
                @if($search || $prioridade)
                Não encontramos missões com os filtros atuais. Tente ajustar sua busca.
                @else
                Esta campanha ainda não possui missões cadastradas.
                @endif
            </p>
            <div class="d-flex justify-content-center gap-3">
                @if($search || $prioridade)
                <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Limpar filtros
                </a>
                @endif
                @can('update', $campanha)
                <a href="{{ route('missoes.create', $campanha->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Criar Primeira Missão
                </a>
                @endcan
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    :root {
        --primary-color: #4361ee;
        --success-color: #198754;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --info-color: #0dcaf0;
    }

    .header-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 2rem;
        border-radius: 1rem;
        margin-top: -1rem;
    }

    .header-icon {
        width: 60px;
        height: 60px;
        background: var(--primary-color);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .stats-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .mission-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }

    .mission-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        border-color: var(--primary-color);
    }

    .mission-meta {
        font-size: 0.875rem;
    }

    .empty-state {
        max-width: 400px;
        margin: 0 auto;
    }

    .empty-state-icon {
        opacity: 0.5;
    }

    .hover-shadow:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .header-section {
            padding: 1.5rem;
        }
        
        .stats-card .card-body {
            padding: 1rem !important;
        }
        
        .mission-card .card-body {
            padding: 1.5rem !important;
        }
        
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if(isset($statusChart) && isset($prioridadeChart) && $missoes->count() > 0)
    {!! $statusChart->script() !!}
    {!! $prioridadeChart->script() !!}
@endif

<script>
function confirmDelete(title, button) {
    const url = button.getAttribute('data-url');
    
    Swal.fire({
        title: 'Excluir Missão?',
        html: `A missão <strong>"${title}"</strong> será permanentemente removida.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Adicionar tooltips aos botões
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

@endsection