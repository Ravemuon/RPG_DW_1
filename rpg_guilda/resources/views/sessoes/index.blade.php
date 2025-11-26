@extends('layouts.app')

@section('title', "Sessões - {$campanha->nome}")

@section('content')

@php
    $user = auth()->user();
    $isMestre = $user && $user->id === $campanha->criador_id;
@endphp

<div class="container py-5 text-light">

    {{-- Header --}}
    <header class="text-center mb-5 border-bottom border-secondary pb-3">
        <h1 class="display-4 fw-bold text-success">Gerenciamento de Sessões</h1>
        <p class="lead text-muted fst-italic">{{ $campanha->nome }}</p>
    </header>

    {{-- Feedback --}}
    @if(session('success') || session('error'))
        <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} bg-{{ session('success') ? 'success' : 'danger' }} text-light border-0 shadow-lg mb-4">
            {{ session('success') ?? session('error') }}
        </div>
    @endif

    {{-- Ações e Busca --}}
    <div class="d-flex flex-wrap gap-3 mb-5 justify-content-center justify-content-md-between align-items-center">

        <div class="d-flex flex-wrap gap-3">
            @if($isMestre)
                <a href="{{ route('sessoes.create', $campanha->id) }}"
                   class="btn btn-success btn-lg rounded-pill shadow-lg px-4 fw-bold d-flex align-items-center">
                    <i class="bi bi-plus-circle me-2"></i> Criar Nova Sessão
                </a>
            @endif

            <a href="{{ route('campanhas.mestre', $campanha->id) }}"
               class="btn btn-outline-secondary btn-lg rounded-pill px-4 fw-bold d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Voltar ao Painel
            </a>
        </div>

        {{-- Campo de busca --}}
        <form action="{{ route('sessoes.index', $campanha->id) }}" method="GET" class="d-flex align-items-center">
            <input type="text" name="busca" class="form-control me-2 rounded-pill" placeholder="🔍 Buscar sessões..." value="{{ request('busca') }}">
            <button type="submit" class="btn btn-primary rounded-pill">
                <i class="bi bi-search"></i> Buscar
            </button>
        </form>
    </div>

    @if($sessoes->count())
        <div class="row g-4">
            @foreach($sessoes->sortByDesc('data_hora') as $sessao)
                @php
                    $statusMapping = [
                        'agendada'     => 'primary',
                        'em_andamento' => 'warning',
                        'concluida'    => 'success',
                        'cancelada'    => 'danger',
                    ];
                    $statusColor = $statusMapping[$sessao->status] ?? 'secondary';
                @endphp

                {{-- Card --}}
                <div class="col-12 col-lg-6">
                    <div class="card bg-dark-card h-100 shadow-xl border-{{ $statusColor }} border-3 transition-hover">
                        <div class="card-body d-flex flex-column">

                            {{-- Título e status --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="card-title fw-bolder text-light text-truncate me-3">
                                    {{ $sessao->titulo }}
                                </h4>
                                <span class="badge bg-{{ $statusColor }} text-uppercase fw-bold p-2">
                                    {{ ucfirst(str_replace('_', ' ', $sessao->status)) }}
                                </span>
                            </div>

                            {{-- Data --}}
                            <div class="text-muted small mb-3 border-bottom border-dark pb-2">
                                <p class="mb-0">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    Data:
                                    <span class="fw-bold text-light">
                                        {{ optional($sessao->data_hora)->format('d/m/Y H:i') ?? 'Não Agendada' }}
                                    </span>
                                </p>
                            </div>

                            {{-- Resumo --}}
                            <p class="card-text text-secondary mb-4 grow">
                                {{ Str::limit($sessao->resumo ?? 'Sem resumo disponível. Clique em detalhes para adicionar.', 120) }}
                            </p>

                            {{-- Botões --}}
                            <div class="mt-auto d-flex flex-wrap gap-2 pt-3 border-top border-dark">
                                <a href="{{ route('sessoes.show', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}"
                                   class="btn btn-sm btn-info rounded-pill fw-bold">
                                    <i class="bi bi-eye me-1"></i> Detalhes
                                </a>

                                @if($isMestre)
                                    <a href="{{ route('sessoes.edit', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}"
                                       class="btn btn-sm btn-warning rounded-pill">
                                        <i class="bi bi-pencil-square me-1"></i> Editar
                                    </a>

                                    <form action="{{ route('sessoes.destroy', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}"
                                          method="POST" class="d-inline ms-auto">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-pill"
                                                onclick="return confirm('Tem certeza que deseja deletar a sessão: {{ $sessao->titulo }}?')">
                                            <i class="bi bi-trash me-1"></i> Deletar
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('sessoes.exportar-pdf', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}"
                                   class="btn btn-sm btn-light rounded-pill">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        <div class="card bg-dark-card border-info text-center p-5 shadow-lg">
            <div class="card-body">
                <p class="fs-4 fw-bold text-info">Nenhuma sessão encontrada</p>
                <p class="text-secondary mb-4">Esta campanha ainda não possui sessões registradas.</p>
                @if($isMestre)
                    <a href="{{ route('sessoes.create', $campanha->id) }}"
                       class="btn btn-info btn-lg rounded-pill fw-bold">
                        <i class="bi bi-plus-circle me-2"></i> Criar Primeira Sessão
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
.bg-dark-card { background-color: #212529 !important; }
.transition-hover { transition: transform .2s ease, box-shadow .2s ease; }
.transition-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,.5) !important; }
.card-title { color: #f8f9fa !important; }
</style>

@endsection
