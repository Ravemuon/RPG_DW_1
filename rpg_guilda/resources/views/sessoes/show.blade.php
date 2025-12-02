@extends('layouts.app')

@php
    $user = auth()->user();
    $mestre = $campanha->criador;
    $isMestre = $user && $user->id === optional($mestre)->id;

    $idsConfirmadosSessao = $sessao->presencas->pluck('id')->toArray();
    $jaMarqueiPresenca = $user && in_array($user->id, $idsConfirmadosSessao);

    $podeMarcarPresenca = $user && !$isMestre && !$jaMarqueiPresenca &&
                          $sessao->status === 'agendada';

    $confirmados = [];
    if($mestre) {
        $confirmados[] = ['nome' => $mestre->nome, 'isMestre' => true];
    }

    foreach($sessao->presencas->where('id', '!=', optional($mestre)->id) as $jogador){
        $confirmados[] = ['nome' => $jogador->nome, 'isMestre' => false];
    }

    $outrosJogadores = $campanha->jogadores
        ->filter(fn($jogador) =>
            $jogador->pivot->status === 'ativo' &&
            !in_array($jogador->id, $idsConfirmadosSessao) &&
            $jogador->id !== optional($mestre)->id
        )
        ->sortBy('nome');
@endphp

@section('title', 'Detalhes da Sessão: ' . $sessao->titulo)

@section('content')

{{-- TOASTS --}}
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    @if(session('success'))
    <div class="toast show text-white bg-success border-0">
        <div class="d-flex">
            <div class="toast-body fw-bold">{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="toast show text-white bg-danger border-0">
        <div class="d-flex">
            <div class="toast-body fw-bold">{{ session('error') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
</div>

<div class="container mt-5 text-light">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">

            <div class="card bg-dark border-0 shadow-lg rounded-4">

                {{-- HEADER --}}
                <div class="card-header bg-primary text-white rounded-top-4 p-4">
                    <h1 class="h2 fw-bold mb-1">Sessão: {{ $sessao->titulo }}</h1>
                    <p class="mb-1 opacity-75">
                        Mestre: <strong class="text-warning">{{ optional($mestre)->nome ?? 'Desconhecido' }}</strong>
                    </p>
                    <p class="mb-0 opacity-75">Campanha: {{ $campanha->nome }}</p>
                </div>

                <div class="card-body p-4">

                    {{-- DETALHES --}}
                    <h3 class="h5 text-info mb-3">Detalhes Principais</h3>

                    <div class="bg-secondary-subtle p-3 rounded mb-4">
                        <dl class="row mb-0 small">

                            <dt class="col-sm-4 text-warning">Sistema:</dt>
                            <dd class="col-sm-8 text-light fw-bold">
                                {{ $campanha->sistema->nome ?? 'Desconhecido' }}
                            </dd>

                            <dt class="col-sm-4 text-warning">Status:</dt>
                            <dd class="col-sm-8 text-light fw-bold">
                                {{ ucfirst(str_replace('_', ' ', $sessao->status)) }}
                            </dd>

                            <dt class="col-sm-4 text-warning">Agendada para:</dt>
                            <dd class="col-sm-8 text-light">
                                {{ optional($sessao->data_hora)->format('d/m/Y H:i') ?? 'Não definida' }}
                            </dd>

                            <dt class="col-sm-4 text-warning">Resumo:</dt>
                            <dd class="col-sm-8 text-secondary fst-italic">
                                {{ $sessao->resumo ?: 'Nenhum resumo disponível.' }}
                            </dd>
                        </dl>
                    </div>

                    <hr class="border-secondary">

                    {{-- CONFIRMADOS --}}
                    <h3 class="h5 text-success mb-3">
                        Jogadores Confirmados ({{ count($confirmados) }})
                    </h3>

                    <div class="p-3 border border-success rounded mb-4 d-flex flex-wrap gap-2">

                        @forelse($confirmados as $c)
                            <span class="badge {{ $c['isMestre'] ? 'bg-warning text-dark' : 'bg-success' }}
                                          p-2 fw-bold shadow-sm">
                                {{ $c['nome'] }} @if($c['isMestre']) (Mestre) @endif
                            </span>
                        @empty
                            <p class="text-warning mb-0">Ninguém confirmou ainda.</p>
                        @endforelse

                    </div>

                    <hr class="border-secondary">

                    {{-- OUTROS --}}
                    <h3 class="h5 text-warning mb-3">
                        Jogadores Ativos ({{ $outrosJogadores->count() }})
                    </h3>

                    <p class="text-muted small">Jogadores sem confirmação.</p>

                    <div class="p-3 border border-warning rounded mb-4 d-flex flex-wrap gap-2">
                        @forelse($outrosJogadores as $j)
                            <span class="badge bg-secondary p-2 fw-bold shadow-sm">
                                {{ $j->nome }}
                            </span>
                        @empty
                            <p class="text-info mb-0">Todos já confirmaram.</p>
                        @endforelse
                    </div>

                    <hr class="border-secondary">

                    {{-- NOTAS --}}
                    <h3 class="h5 text-info mb-3">Notas do Mestre</h3>

                    <div class="bg-secondary p-4 rounded text-light shadow-inner" style="min-height: 180px;">
                        {!! nl2br(e($sessao->descricao_detalhada ?? 'Nenhuma anotação.')) !!}
                    </div>

                    <hr class="border-secondary my-4">

                    {{-- GRÁFICO CHARTZ --}}
                    <h3 class="h5 text-primary mb-3">
                        <i class="fa-solid fa-chart-bar"></i> Gráfico de Confirmações
                    </h3>

                    <div id="sessao-presencas-chart" style="height: 260px;"></div>

                    {!! $chart->container() !!}

                </div>

                {{-- FOOTER --}}
                <div class="card-footer bg-dark border-top border-secondary p-3
                            d-flex flex-wrap justify-content-between align-items-center rounded-bottom-4">

                    <small class="text-muted">ID Sessão: {{ $sessao->id }}</small>

                    <div class="d-flex gap-2 flex-wrap">

                        @if($podeMarcarPresenca)
                            <form action="{{ route('sessoes.marcar_presenca', [$campanha->id, $sessao->id]) }}"
                                  method="POST">
                                @csrf
                                <button class="btn btn-success rounded-pill fw-bold px-4 shadow-sm">
                                    Marcar Presença
                                </button>
                            </form>
                        @elseif($jaMarqueiPresenca)
                            <span class="badge bg-success p-2 fw-bold shadow-sm">
                                PRESENÇA CONFIRMADA
                            </span>
                        @endif

                        @if($isMestre)
                            <a href="{{ route('sessoes.edit', [$campanha->id, $sessao->id]) }}"
                               class="btn btn-warning rounded-pill px-4 shadow-sm">
                                Editar Sessão
                            </a>
                        @endif

                        <a href="{{ route('sessoes.index', $campanha->id) }}"
                           class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                            Todas as Sessões
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

{{-- CHARTZ SCRIPTS --}}
<script src="https://unpkg.com/chart.js"></script>
<script src="https://unpkg.com/@chartisan/chartjs"></script>

{!! $chart->script() !!}

<style>
.bg-dark { background-color: #1a1e23 !important; }
.bg-secondary-subtle { background-color: #24292e !important; }
.shadow-inner { box-shadow: inset 0 1px 3px rgba(0,0,0,0.6); }
</style>

@endsection
