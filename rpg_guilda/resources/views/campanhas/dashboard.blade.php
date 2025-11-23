@extends('layouts.app')

@section('title', "Área do Mestre - {$campanha->nome}")

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-warning mb-4">🎩 Área do Mestre — {{ $campanha->nome }}</h2>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Botões rápidos --}}
    <div class="mb-4 d-flex gap-3 flex-wrap">
        <a href="{{ route('campanhas.edit', $campanha->id) }}" class="btn btn-warning rounded-pill">✏️ Editar Campanha</a>
        <a href="{{ route('missoes.create', $campanha->id) }}" class="btn btn-primary rounded-pill">➕ Criar Missão</a>
        <a href="{{ route('sessoes.create', $campanha->id) }}" class="btn btn-success rounded-pill">➕ Criar Sessão</a>
        <a href="{{ route('personagens.create') }}" class="btn btn-info rounded-pill">➕ Criar Personagem</a>
    </div>

    <div class="row">
        {{-- Gráfico de Status das Missões --}}
        <div class="col-lg-6 mb-4">
            <div class="card bg-dark border-primary shadow-sm h-100 p-3">
                <h5 class="text-primary fw-bold">📊 Status das Missões</h5>
                <canvas id="statusMissaoChart"></canvas>
            </div>
        </div>

        {{-- Gráfico de Prioridade das Missões --}}
        <div class="col-lg-6 mb-4">
            <div class="card bg-dark border-info shadow-sm h-100 p-3">
                <h5 class="text-info fw-bold">📊 Prioridade das Missões</h5>
                <canvas id="prioridadeMissaoChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Listagem Missões --}}
    <div class="card bg-dark border-primary mb-4 shadow-sm">
        <div class="card-header text-primary fw-bold">🎯 Missões da Campanha</div>
        <div class="card-body p-0">
            @if($campanha->missoes->count())
                <ul class="list-group list-group-flush">
                    @foreach($campanha->missoes->sortByDesc('created_at') as $missao)
                        <li class="list-group-item bg-dark text-light border-secondary d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $missao->titulo }}</strong>
                                <small>({{ ucfirst($missao->prioridade) }})</small>
                                <br>
                                <small>{{ Str::limit($missao->descricao, 60) }}</small>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('missoes.show', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-outline-primary btn-sm rounded-pill">🔍 Ver</a>
                                <a href="{{ route('missoes.edit', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-outline-warning btn-sm rounded-pill">✏️ Editar</a>
                                <form action="{{ route('missoes.destroy', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm rounded-pill">🗑️ Deletar</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-secondary fst-italic p-3 mb-0 text-center">Nenhuma missão criada.</p>
            @endif
        </div>
    </div>

    <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-secondary rounded-pill mt-3">⬅️ Voltar à Campanha</a>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const statusData = @json($campanha->missoes->groupBy('status')->map->count());
    const prioridadeData = @json($campanha->missoes->groupBy('prioridade')->map->count());

    const statusLabels = Object.keys(statusData);
    const statusCounts = Object.values(statusData);

    const prioridadeLabels = Object.keys(prioridadeData);
    const prioridadeCounts = Object.values(prioridadeData);

    const ctxStatus = document.getElementById('statusMissaoChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                label: 'Missões',
                data: statusCounts,
                backgroundColor: ['#ffc107', '#0d6efd', '#198754', '#6c757d'],
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { color: 'white' } } }
        }
    });

    const ctxPrioridade = document.getElementById('prioridadeMissaoChart').getContext('2d');
    new Chart(ctxPrioridade, {
        type: 'bar',
        data: {
            labels: prioridadeLabels,
            datasets: [{
                label: 'Quantidade',
                data: prioridadeCounts,
                backgroundColor: ['#0d6efd', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: 'white' }, grid: { color: '#444' } },
                y: { ticks: { color: 'white' }, grid: { color: '#444' }, beginAtZero: true }
            }
        }
    });
</script>
@endsection
