@extends('layouts.app')

@section('title', "Sessões da Campanha - {$campanha->nome}")

@section('content')

@php
    use Illuminate\Support\Str;

    $search = $search ?? '';
    $dateSearch = $dateSearch ?? '';
    $statusFilter = $statusFilter ?? request('status', 'todas');

    $dashboard = $dashboardData ?? [
        'total' => 0,
        'concluidas' => 0,
        'agendadas' => 0
    ];

    function get_status_badge_fa($status) {
        return [
            'agendada'     => ['cor' => 'info', 'icone' => 'calendar-alt', 'texto' => 'Agendada'],
            'em_andamento' => ['cor' => 'warning text-dark', 'icone' => 'spinner', 'texto' => 'Em Andamento'],
            'concluida'    => ['cor' => 'success', 'icone' => 'check-double', 'texto' => 'Concluída'],
            'cancelada'    => ['cor' => 'danger', 'icone' => 'times-circle', 'texto' => 'Cancelada'],
        ][$status] ?? ['cor' => 'light', 'icone' => 'question-circle', 'texto' => 'Desconhecido'];
    }
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container py-5 text-light">

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom border-secondary pb-3">
        <h1 class="display-5 fw-bolder text-primary">
            📅 Sessões de {{ $campanha->nome }}
        </h1>

        <div class="d-flex gap-2">
            <a href="{{ route('campanhas.show', $campanha->id) }}"
               class="btn btn-outline-secondary rounded-pill px-4 shadow-sm d-none d-md-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>

            @can('update', $campanha)
            <a href="{{ route('sessoes.create', $campanha->id) }}"
               class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-calendar-plus me-1"></i> Nova Sessão
            </a>
            @endcan
        </div>
    </div>

    {{-- DASHBOARD --}}
    @if($dashboard['total'] > 0)
    <h2 class="h4 text-warning mb-3"><i class="fas fa-tachometer-alt me-2"></i>Resumo Rápido</h2>

    <div class="row mb-5 g-4">

        <div class="col-lg-4 col-md-6">
            <div class="card bg-secondary border-0 rounded-4 shadow-lg p-3 text-center transition-hover">
                <h5 class="text-light fw-bold mb-1 display-6">{{ $dashboard['total'] }}</h5>
                <p class="text-muted small mb-0"><i class="fas fa-list-ul me-1"></i> Total de Sessões</p>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card bg-secondary border-0 rounded-4 shadow-lg p-3 text-center transition-hover">
                <h5 class="text-success fw-bold mb-1 display-6">{{ $dashboard['concluidas'] }}</h5>
                <p class="text-muted small mb-0"><i class="fas fa-check-double me-1"></i> Sessões Concluídas</p>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card bg-secondary border-0 rounded-4 shadow-lg p-3 text-center transition-hover">
                <h5 class="text-info fw-bold mb-1 display-6">{{ $dashboard['agendadas'] }}</h5>
                <p class="text-muted small mb-0"><i class="fas fa-calendar-alt me-1"></i> Sessões Agendadas</p>
            </div>
        </div>
    </div>
    @endif

    {{-- FILTROS --}}
    <div class="card bg-secondary-subtle shadow-lg mb-5 p-4 rounded-4 border-0">
        <h4 class="text-info mb-3 fw-bold"><i class="fas fa-filter me-2"></i>Filtrar Sessões</h4>

        <form method="GET" class="row g-3 align-items-end">

            <div class="col-md-4">
                <label for="search" class="form-label text-muted small">Título (Busca Exata)</label>
                <input type="text" name="search" id="search"
                       class="form-control bg-dark border-secondary text-light rounded-pill py-2 px-4"
                       placeholder="Buscar por título..."
                       value="{{ $search }}">
            </div>

            <div class="col-md-3">
                <label for="date_search" class="form-label text-muted small">Data ou Ano</label>
                <input type="text" name="date_search" id="date_search"
                       class="form-control bg-dark border-secondary text-light rounded-pill py-2 px-4"
                       placeholder="2025-12-01 ou 2025"
                       value="{{ $dateSearch }}">
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted small">Status</label>
                <select name="status" id="statusFilter"
                        class="form-select bg-dark border-secondary text-light rounded-pill py-2">
                    <option value="todas" @selected($statusFilter=='todas')>Todas</option>
                    <option value="agendada" @selected($statusFilter=='agendada')>Agendada</option>
                    <option value="em_andamento" @selected($statusFilter=='em_andamento')>Em Andamento</option>
                    <option value="concluida" @selected($statusFilter=='concluida')>Concluída</option>
                    <option value="cancelada" @selected($statusFilter=='cancelada')>Cancelada</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary rounded-pill flex-fill" type="submit">
                    <i class="fas fa-search"></i>
                </button>

                @if($search || $dateSearch || $statusFilter !== 'todas')
                    <a href="{{ route('sessoes.index', $campanha->id) }}"
                       class="btn btn-danger rounded-pill flex-fill">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- GRÁFICO --}}
    @if(isset($presencasChart) && $dashboard['concluidas'] > 0)
    <h2 class="h4 text-warning mb-3"><i class="fas fa-chart-line me-2"></i>Estatísticas de Engajamento</h2>

    <div class="row mb-5 g-4">
        <div class="col-12">
            <div class="card bg-secondary border-0 rounded-4 shadow-lg">
                <div class="card-header bg-secondary border-bottom border-dark pt-4 px-4">
                    <h5 class="fw-bold text-light"><i class="fas fa-users me-2"></i>Taxa de Presença por Sessão</h5>
                </div>

                <div class="card-body p-4">
                    <div style="height:350px">
                        {!! $presencasChart->container() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- LISTA PRINCIPAL --}}
    <h3 class="fw-bolder text-primary mb-4 mt-5">
        <i class="fas fa-list-alt me-2"></i>Lista de Sessões ({{ $sessoes->total() }})
    </h3>

    @if($sessoes->count())
    <div class="row g-4">

        @foreach($sessoes as $sessao)
        @php
            $statusInfo = get_status_badge_fa($sessao->status);
            $dataHora = \Carbon\Carbon::parse($sessao->data_hora);
        @endphp

        <div class="col-xl-4 col-md-6">
            <div class="card bg-dark border-secondary rounded-4 shadow-lg h-100 p-3 transition-hover">

                <div class="d-flex justify-content-between mb-3">
                    <span class="badge bg-secondary px-3 py-2 fw-bold text-muted">
                        <i class="fas fa-clock me-1"></i> {{ $dataHora->format('d/m/Y H:i') }}
                    </span>

                    <span class="badge bg-{{ $statusInfo['cor'] }} px-3 py-2 fw-bold">
                        <i class="fas fa-{{ $statusInfo['icone'] }}"></i> {{ $statusInfo['texto'] }}
                    </span>
                </div>

                <h4 class="fw-bold text-light text-truncate">{{ $sessao->titulo }}</h4>

                <p class="text-muted small mb-4">
                    {{ Str::limit($sessao->resumo ?? 'Sem resumo.', 120) }}
                </p>

                <div class="d-flex gap-2 pt-3 border-top border-secondary mt-auto">

                    <a href="{{ route('sessoes.show', [$campanha->id, $sessao->id]) }}"
                       class="btn btn-outline-info btn-sm rounded-pill flex-fill">
                        <i class="fas fa-eye"></i> Detalhes
                    </a>

                    @can('update', $campanha)
                    <a href="{{ route('sessoes.edit', [$campanha->id, $sessao->id]) }}"
                       class="btn btn-outline-warning btn-sm rounded-pill flex-fill">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    @endcan

                    @if($sessao->status == 'concluida')
                    <a href="{{ route('sessoes.exportarPdf', [$campanha->id, $sessao->id]) }}"
                       class="btn btn-outline-primary btn-sm rounded-pill flex-fill">
                        <i class="fas fa-file-pdf"></i> Relatório
                    </a>
                    @endif

                    @can('delete', $campanha)
                    <form action="{{ route('sessoes.destroy', [$campanha->id, $sessao->id]) }}"
                          method="POST" class="flex-fill">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm rounded-pill w-100"
                                onclick="return confirm('Excluir definitivamente esta sessão?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endcan
                </div>

            </div>
        </div>

        @endforeach

    </div>

    <div class="d-flex justify-content-center mt-5">
        {{ $sessoes->appends([
            'search'=>$search,
            'date_search'=>$dateSearch,
            'status'=>$statusFilter
        ])->links('pagination::bootstrap-5') }}
    </div>

    @else
    <div class="alert alert-info bg-secondary text-center rounded-4 shadow-lg p-5">
        <h4 class="text-info">📭 Nenhuma sessão encontrada!</h4>
        <p class="text-light">Tente alterar os filtros.</p>

        @can('update', $campanha)
        <a href="{{ route('sessoes.create', $campanha->id) }}"
           class="btn btn-primary rounded-pill px-4 py-2 mt-3">
            <i class="fas fa-calendar-plus"></i> Agendar Sessão
        </a>
        @endcan
    </div>
    @endif

    <hr class="border-secondary my-5">

{{-- CHARTS --}}
@if(isset($presencasChart) && $dashboard['concluidas'] > 0)
    {!! $presencasChart->script() !!}
@endif

<script>
function filterList() {
    const input = document.getElementById('search-quick').value.toLowerCase();
    const list = document.getElementById('quick-session-list');
    const items = list.getElementsByClassName('quick-list-item');
    const noResults = document.getElementById('no-results');

    let found = false;

    Array.from(items).forEach(item => {
        const title = item.getAttribute('data-title');
        if (title.includes(input)) {
            item.style.display = 'flex';
            found = true;
        } else {
            item.style.display = 'none';
        }
    });

    noResults.style.display = found ? 'none' : 'block';
}
</script>

<style>
    body { background-color: #1a1e23; }
    .bg-secondary-subtle { background-color: #24292e !important; }
    .bg-dark { background-color: #1a1e23 !important; }
    .text-primary { color: #81e6d9 !important; }
    .text-info { color: #63b3ed !important; }
    .text-warning { color: #f6ad55 !important; }
    .transition-hover { transition: .2s ease-in-out; }
    .transition-hover:hover { transform: translateY(-4px); }
    .form-control, .form-select { color:#fff !important; }
</style>

@endsection
