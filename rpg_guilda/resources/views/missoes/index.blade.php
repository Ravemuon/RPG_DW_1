@extends('layouts.app')

@section('title', "Missões da Campanha - {$campanha->nome}")

@section('content')

@php
    $search = $search ?? '';
    $prioridade = $prioridade ?? '';
    $status = $status ?? '';

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
    <div class="header-section mb-5 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('campanhas.index') }}" class="text-muted">Campanhas</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('campanhas.show', $campanha->id) }}" class="text-muted">{{ Str::limit($campanha->nome, 20) }}</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page">Missões</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon bg-primary"><i class="fas fa-crosshairs"></i></div>
                <div>
                    <h1 class="fw-bold mb-1">Missões</h1>
                    <p class="text-muted mb-0">{{ $campanha->nome }}</p>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-secondary px-4"><i class="fas fa-arrow-left me-2"></i> Voltar</a>
            @can('update', $campanha)
            <a href="{{ route('missoes.create', $campanha->id) }}" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-plus-circle me-2"></i> Nova Missão</a>
            @endcan
        </div>
    </div>

    {{-- FILTROS --}}
    <section class="card border-0 shadow-sm mb-5">
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar missão..." class="form-control">
                </div>
                <div class="col-md-3">
                    <select name="prioridade" class="form-select">
                        <option value="">Todas as prioridades</option>
                        <option value="alta" @selected($prioridade=='alta')>Alta</option>
                        <option value="media" @selected($prioridade=='media')>Média</option>
                        <option value="baixa" @selected($prioridade=='baixa')>Baixa</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Todos os status</option>
                        @foreach(['pendente','em_andamento','concluida','cancelada'] as $s)
                            @php $info = get_status_badge_fa($s); @endphp
                            <option value="{{ $s }}" @selected($status==$s)>{{ $info['texto'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary"><i class="fas fa-filter me-1"></i> Aplicar</button>
                </div>
            </form>
        </div>
    </section>

    {{-- GRÁFICOS --}}
    @if(isset($statusChart) && isset($prioridadeChart))
    <section class="mb-5">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5><i class="fas fa-chart-pie me-2 text-info"></i>Distribuição por Status</h5>
                    </div>
                    <div class="card-body">
                        {!! $statusChart->container() !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5><i class="fas fa-chart-bar me-2 text-warning"></i>Distribuição por Prioridade</h5>
                    </div>
                    <div class="card-body">
                        {!! $prioridadeChart->container() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- LISTAGEM --}}
    @if($missoes->count())
    <div class="row g-4">
        @foreach($missoes as $missao)
        @php
            $statusInfo = get_status_badge_fa($missao->status);
            $prioInfo = get_prioridade_badge_fa($missao->prioridade);
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100" style="border-left:4px solid {{ $prioInfo['corHex'] }};">
                <div class="card-body">
                    <h5>{{ $missao->titulo }}</h5>
                    <p>{{ Str::limit($missao->descricao, 120) }}</p>
                    <div class="mb-2">
                        <span class="badge" style="background: {{ $prioInfo['corHex'] }}20; color: {{ $prioInfo['corHex'] }};">
                            <i class="fas fa-{{ $prioInfo['icone'] }}"></i> {{ $prioInfo['texto'] }}
                        </span>
                        <span class="badge" style="background: {{ $statusInfo['corHex'] }}20; color: {{ $statusInfo['corHex'] }};">
                            <i class="fas fa-{{ $statusInfo['icone'] }}"></i> {{ $statusInfo['texto'] }}
                        </span>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('missoes.show', [$campanha->id, $missao->id]) }}" class="btn btn-outline-primary btn-sm flex-fill"><i class="fas fa-eye"></i> Detalhes</a>
                        @can('update', $campanha)
                        <a href="{{ route('missoes.edit', [$campanha->id, $missao->id]) }}" class="btn btn-outline-warning btn-sm flex-fill"><i class="fas fa-edit"></i> Editar</a>
                        @endcan
                        <a href="{{ route('missoes.exportarPdf', [$campanha->id, $missao->id]) }}" class="btn btn-outline-info btn-sm flex-fill"><i class="fas fa-file-pdf"></i> PDF</a>
                        @can('delete', $campanha)
                        <button type="button" onclick="confirmDelete('{{ addslashes($missao->titulo) }}', this)" class="btn btn-outline-danger btn-sm flex-fill" data-url="{{ route('missoes.destroy', [$campanha->id, $missao->id]) }}"><i class="fas fa-trash"></i> Excluir</button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $missoes->withQueryString()->links('pagination::bootstrap-5') }}</div>
    @else
    <div class="text-center py-5">
        <h4>Nenhuma missão encontrada</h4>
        @can('update', $campanha)
        <a href="{{ route('missoes.create', $campanha->id) }}" class="btn btn-primary">Criar Missão</a>
        @endcan
    </div>
    @endif
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if(isset($statusChart)) {!! $statusChart->script() !!} @endif
@if(isset($prioridadeChart)) {!! $prioridadeChart->script() !!} @endif

<script>
function confirmDelete(title, button){
    const url = button.dataset.url;
    if(confirm(`Deseja realmente excluir a missão "${title}"?`)){
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

@endsection
