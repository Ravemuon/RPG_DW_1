@extends('layouts.app')

@section('title', "Missões da Campanha - {$campanha->nome}")

@section('content')

@php
    $search = $search ?? '';
    $prioridade = $prioridade ?? '';

    // Funções auxiliares para badges - Usando Font Awesome para padronizar com o primeiro arquivo
    function get_status_badge_fa($status) {
        return [
            'pendente'     => ['cor' => 'secondary', 'icone' => 'hourglass-half', 'texto' => 'Pendente'],
            'em_andamento' => ['cor' => 'info', 'icone' => 'sync-alt', 'texto' => 'Em Andamento'],
            'concluida'    => ['cor' => 'success', 'icone' => 'check-circle', 'texto' => 'Concluída'],
            'cancelada'    => ['cor' => 'danger', 'icone' => 'times-circle', 'texto' => 'Cancelada'],
        ][$status] ?? ['cor' => 'light', 'icone' => 'question-circle', 'texto' => 'Desconhecido'];
    }

    function get_prioridade_badge_fa($prioridade) {
        return [
            'baixa' => ['cor' => 'success', 'icone' => 'arrow-down', 'texto' => 'Baixa'],
            'media' => ['cor' => 'warning text-dark', 'icone' => 'exclamation-triangle', 'texto' => 'Média'],
            'alta'  => ['cor' => 'danger', 'icone' => 'fire', 'texto' => 'Alta'],
        ][$prioridade] ?? ['cor' => 'dark', 'icone' => 'question', 'texto' => 'Não Definida'];
    }
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container py-5 text-light">

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom border-secondary pb-3">
        <h1 class="display-5 fw-bolder text-primary">
            🎯 Missões de {{ $campanha->nome }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('campanhas.show', $campanha->id) }}"
               class="btn btn-outline-secondary rounded-pill px-4 shadow-sm d-none d-md-inline-flex align-items-center">
               <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>

            @can('update', $campanha)
            <a href="{{ route('missoes.create', $campanha->id) }}"
               class="btn btn-success rounded-pill px-4 shadow-sm">
               <i class="fas fa-plus me-1"></i> Nova Missão
            </a>
            @endcan
        </div>
    </div>

    {{-- FILTROS E BUSCA (Design unificado) --}}
    <div class="card bg-secondary-subtle shadow-lg mb-5 p-4 rounded-4 border-0">
        <h4 class="text-info mb-3 fw-bold"><i class="fas fa-filter me-2"></i>Filtrar Missões</h4>
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-7">
                <label for="search" class="form-label text-muted small">Título/Descrição</label>
                <input type="text"
                        name="search"
                        id="search"
                        class="form-control bg-dark border-secondary text-light rounded-pill py-2 px-4"
                        placeholder="Buscar por título ou descrição..."
                        value="{{ $search }}">
            </div>

            <div class="col-md-3">
                <label for="prioridade" class="form-label text-muted small">Prioridade</label>
                <select name="prioridade"
                        id="prioridade"
                        class="form-select bg-dark border-secondary text-light rounded-pill py-2"
                        onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <option value="alta"  @selected($prioridade == 'alta')>Alta</option>
                    <option value="media" @selected($prioridade == 'media')>Média</option>
                    <option value="baixa" @selected($prioridade == 'baixa')>Baixa</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary rounded-pill flex-fill">
                    <i class="fas fa-search"></i>
                </button>
                @if($search || $prioridade)
                    <a href="{{ route('missoes.index', $campanha->id) }}"
                       class="btn btn-danger rounded-pill flex-fill" title="Limpar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ÁREA DOS GRÁFICOS --}}
    @if(isset($statusChart) && isset($prioridadeChart) && $missoes->count() > 0)
    <h2 class="h4 text-warning mb-3"><i class="fas fa-chart-pie me-2"></i>Estatísticas de Missões</h2>
    <div class="row mb-5 g-4">
        <div class="col-lg-6">
            <div class="card bg-secondary border-0 rounded-4 shadow-lg h-100">
                <div class="card-header bg-secondary border-bottom border-dark pt-4 px-4">
                    <h5 class="fw-bold text-light"><i class="fas fa-tasks me-2"></i>Status das Missões</h5>
                </div>
                <div class="card-body p-4">
                    <div style="position: relative; height:300px; width:100%">
                        {!! $statusChart->container() !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card bg-secondary border-0 rounded-4 shadow-lg h-100">
                <div class="card-header bg-secondary border-bottom border-dark pt-4 px-4">
                    <h5 class="fw-bold text-light"><i class="fas fa-fire-alt me-2"></i>Prioridade das Missões</h5>
                </div>
                <div class="card-body p-4">
                    <div style="position: relative; height:300px; width:100%">
                        {!! $prioridadeChart->container() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- LISTA DE MISSÕES --}}
    <h3 class="fw-bolder text-primary mb-4 mt-5"><i class="fas fa-list-alt me-2"></i>Lista de Missões ({{ $missoes->count() }})</h3>

    @if($missoes->count())
        <div class="row g-4">
            @foreach($missoes as $missao)
                @php
                    $statusInfo = get_status_badge_fa($missao->status);
                    $prioInfo = get_prioridade_badge_fa($missao->prioridade);
                @endphp

                <div class="col-xl-4 col-md-6">
                    <div class="card bg-dark border-secondary border rounded-4 shadow-lg h-100 p-3 transition-hover">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-{{ $prioInfo['cor'] }} px-3 py-2 text-uppercase fw-bold">
                                <i class="fas fa-{{ $prioInfo['icone'] }}"></i> {{ $prioInfo['texto'] }}
                            </span>

                            <span class="badge bg-{{ $statusInfo['cor'] }} px-3 py-2 text-uppercase fw-bold">
                                <i class="fas fa-{{ $statusInfo['icone'] }}"></i> {{ $statusInfo['texto'] }}
                            </span>
                        </div>

                        <h4 class="fw-bold text-light text-truncate" title="{{ $missao->titulo }}">{{ $missao->titulo }}</h4>

                        <p class="text-muted small grow mb-4">
                            {{ Str::limit($missao->descricao, 120) }}
                        </p>

                        <div class="d-flex gap-2 pt-3 border-top border-secondary mt-auto">
                            <a href="{{ route('missoes.show', [$campanha->id, $missao->id]) }}"
                               class="btn btn-outline-info btn-sm rounded-pill flex-fill shadow-sm">
                               <i class="fas fa-eye"></i> Ver
                            </a>

                            @can('update', $campanha)
                            <a href="{{ route('missoes.edit', [$campanha->id, $missao->id]) }}"
                               class="btn btn-outline-warning btn-sm rounded-pill flex-fill shadow-sm">
                               <i class="fas fa-edit"></i> Editar
                            </a>
                            @endcan

                            <a href="{{ route('missoes.exportarPdf', [$campanha->id, $missao->id]) }}"
                               class="btn btn-outline-primary btn-sm rounded-pill flex-fill shadow-sm">
                               <i class="fas fa-file-pdf"></i> PDF
                            </a>

                            @can('delete', $campanha)
                            <form action="{{ route('missoes.destroy', [$campanha->id, $missao->id]) }}"
                                  method="POST" class="flex-fill">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm rounded-pill w-100 shadow-sm"
                                        onclick="return confirm('Tem certeza que deseja excluir esta missão? Esta ação não pode ser desfeita.')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info bg-secondary text-center rounded-4 shadow-lg p-5" role="alert">
            <h4 class="alert-heading text-info">📭 Nenhuma missão encontrada!</h4>
            <p class="text-light">Não há missões cadastradas com os filtros atuais.</p>
            @can('update', $campanha)
            <a href="{{ route('missoes.create', $campanha->id) }}"
               class="btn btn-primary rounded-pill mt-3 px-4 py-2">
               <i class="fas fa-plus"></i> Criar Primeira Missão
            </a>
            @endcan
        </div>
    @endif

</div>

{{-- SCRIPTS DOS GRÁFICOS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

{{-- Renderização dos Scripts dos Gráficos --}}
@if(isset($statusChart) && isset($prioridadeChart) && $missoes->count() > 0)
    {!! $statusChart->script() !!}
    {!! $prioridadeChart->script() !!}
@endif

<style>
    /* Estilos customizados para o Dark Theme (Replicados para padronizar) */
    body { background-color: #1a1e23; }
    .bg-dark { background-color: #1a1e23 !important; }
    .bg-secondary-subtle { background-color: #24292e !important; }
    .bg-secondary { background-color: #2d3748 !important; }

    /* Cores de destaque padronizadas */
    .text-primary { color: #81e6d9 !important; } /* Ciano/Aqua */
    .text-info { color: #63b3ed !important; } /* Azul Claro */
    .text-warning { color: #f6ad55 !important; } /* Laranja */

    /* Sombras e Bordas */
    .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.2) !important; }

    /* Botões Primários */
    .btn-primary {
        background-color: #81e6d9;
        border-color: #81e6d9;
        color: #1a1e23;
        font-weight: bold;
    }
    .btn-primary:hover {
        background-color: #63b3ed;
        border-color: #63b3ed;
        color: #1a1e23;
    }

    /* Campos de Formulário */
    .form-control, .form-select {
        color: #fff !important;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(129, 230, 217, 0.25); /* Sombra baseada na cor primária */
        background-color: #1a1e23; /* Darker para o input */
        border-color: #81e6d9 !important;
        color: #fff;
    }

    /* Efeito de hover nas cards */
    .transition-hover {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .transition-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px -5px rgba(0, 0, 0, 0.6), 0 6px 10px -3px rgba(0, 0, 0, 0.3) !important;
    }
</style>
@endsection
