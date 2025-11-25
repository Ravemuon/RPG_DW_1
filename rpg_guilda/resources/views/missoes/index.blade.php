@extends('layouts.app')

@section('title', "Missões da Campanha - {$campanha->nome}")

@section('content')

@php
    $search = $search ?? '';
    $prioridade = $prioridade ?? '';

    function get_status_badge($status) {
        return [
            'pendente'     => ['cor' => 'secondary', 'icone' => 'hourglass-split', 'texto' => 'Pendente'],
            'em_andamento' => ['cor' => 'info', 'icone' => 'arrow-repeat', 'texto' => 'Em Andamento'],
            'concluida'    => ['cor' => 'success', 'icone' => 'check-circle-fill', 'texto' => 'Concluída'],
            'cancelada'    => ['cor' => 'danger', 'icone' => 'x-circle-fill', 'texto' => 'Cancelada'],
        ][$status] ?? ['cor' => 'light', 'icone' => 'question-circle-fill', 'texto' => 'Desconhecido'];
    }

    function get_prioridade_badge($prioridade) {
        return [
            'baixa' => ['cor' => 'success', 'icone' => 'arrow-down-circle', 'texto' => 'Baixa'],
            'media' => ['cor' => 'warning text-dark', 'icone' => 'exclamation-circle', 'texto' => 'Média'],
            'alta'  => ['cor' => 'danger', 'icone' => 'fire', 'texto' => 'Alta'],
        ][$prioridade] ?? ['cor' => 'dark', 'icone' => 'question-lg', 'texto' => 'Não Definida'];
    }
@endphp

<div class="container py-5">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <div class="row align-items-center mb-5">
        <div class="col-md-8">
            <h1 class="display-5 fw-bolder text-primary">
                🎯 Missões de {{ $campanha->nome }}
            </h1>
            <p class="text-muted lead">Gerencie todas as tarefas desta campanha.</p>
        </div>

        <div class="col-md-4 text-md-end">
            <a href="{{ route('campanhas.show', $campanha->id) }}"
               class="btn btn-outline-secondary rounded-pill me-2">
               <i class="bi bi-arrow-left"></i> Voltar
            </a>

            @can('update', $campanha)
            <a href="{{ route('missoes.create', $campanha->id) }}"
               class="btn btn-primary rounded-pill">
                <i class="bi bi-plus-circle"></i> Nova Missão
            </a>
            @endcan
        </div>
    </div>

    <hr class="mb-4">

    {{-- FILTROS --}}
    <div class="card shadow-sm mb-4 p-4 rounded-4 border-0">
        <form method="GET" class="row g-3">

            <div class="col-md-6">
                <input type="text"
                       name="search"
                       class="form-control form-control-lg rounded-pill"
                       placeholder="Buscar por título ou descrição..."
                       value="{{ $search }}">
            </div>

            <div class="col-md-4">
                <select name="prioridade"
                        class="form-select form-select-lg rounded-pill"
                        onchange="this.form.submit()">
                    <option value="">Todas as Prioridades</option>
                    <option value="alta"  @selected($prioridade == 'alta')>Alta</option>
                    <option value="media" @selected($prioridade == 'media')>Média</option>
                    <option value="baixa" @selected($prioridade == 'baixa')>Baixa</option>
                </select>
            </div>

            <div class="col-md-2 text-md-end">
                <button class="btn btn-primary btn-lg rounded-pill w-100">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
            </div>

            @if($search || $prioridade)
            <div class="col-md-12 text-end">
                <a href="{{ route('missoes.index', $campanha->id) }}"
                   class="btn btn-outline-danger rounded-pill">
                    <i class="bi bi-x-circle"></i> Limpar
                </a>
            </div>
            @endif

        </form>
    </div>

    {{-- GRÁFICO --}}
    <div class="card shadow-sm p-4 mb-5 rounded-4 border-0">
        <h5 class="fw-bold mb-3 text-primary">
            <i class="bi bi-graph-up"></i> Visão Geral de Status
        </h5>
        <canvas id="chartStatus" style="max-height: 260px;"></canvas>
    </div>

    {{-- LISTA --}}
    <h3 class="fw-bold mb-4">Lista de Missões ({{ $missoes->count() }})</h3>

    @if($missoes->count())
        <div class="row g-4">
            @foreach($missoes as $missao)
                @php
                    $statusInfo = get_status_badge($missao->status);
                    $prioInfo = get_prioridade_badge($missao->prioridade);
                @endphp

                <div class="col-xl-4 col-md-6">
                    <div class="card shadow-sm rounded-4 h-100 p-3">

                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-{{ $prioInfo['cor'] }} px-3 py-2">
                                <i class="bi bi-{{ $prioInfo['icone'] }}"></i> {{ $prioInfo['texto'] }}
                            </span>

                            <span class="badge bg-{{ $statusInfo['cor'] }} px-3 py-2">
                                <i class="bi bi-{{ $statusInfo['icone'] }}"></i> {{ $statusInfo['texto'] }}
                            </span>
                        </div>

                        <h4 class="fw-bold text-truncate">{{ $missao->titulo }}</h4>

                        <p class="text-muted small flex-grow-1">
                            {{ Str::limit($missao->descricao, 120) }}
                        </p>

                        <div class="d-flex gap-2 pt-3 border-top">

                            <a href="{{ route('missoes.show', [$campanha->id, $missao->id]) }}"
                               class="btn btn-primary btn-sm rounded-pill flex-fill">
                               <i class="bi bi-eye"></i> Ver
                            </a>

                            @can('update', $campanha)
                            <a href="{{ route('missoes.edit', [$campanha->id, $missao->id]) }}"
                               class="btn btn-warning btn-sm rounded-pill flex-fill">
                               <i class="bi bi-pencil"></i> Editar
                            </a>
                            @endcan

                            <a href="{{ route('missoes.exportarPdf', [$campanha->id, $missao->id]) }}"
                               class="btn btn-info btn-sm rounded-pill flex-fill">
                               <i class="bi bi-filetype-pdf"></i> PDF
                            </a>

                            @can('delete', $campanha)
                            <form action="{{ route('missoes.destroy', [$campanha->id, $missao->id]) }}"
                                  method="POST" class="flex-fill">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm rounded-pill w-100"
                                        onclick="return confirm('Excluir missão?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan

                        </div>

                    </div>
                </div>

            @endforeach
        </div>
    @else
        <div class="alert alert-warning text-center rounded-4 p-5">
            <h4>📭 Nenhuma missão encontrada.</h4>
            @can('update', $campanha)
            <a href="{{ route('missoes.create', $campanha->id) }}"
               class="btn btn-primary rounded-pill mt-3">
               ➕ Criar Primeira Missão
            </a>
            @endcan
        </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(
        document.getElementById('chartStatus'),
        {
            type: 'doughnut',
            data: {
                labels: ['Pendentes', 'Em Andamento', 'Concluídas', 'Canceladas'],
                datasets: [{
                    data: [
                        {{ $dashboard['pendentes'] }},
                        {{ $dashboard['andamento'] }},
                        {{ $dashboard['concluidas'] }},
                        {{ $dashboard['canceladas'] }},
                    ]
                }]
            },
            options: { responsive: true }
        }
    );
</script>

@endsection
