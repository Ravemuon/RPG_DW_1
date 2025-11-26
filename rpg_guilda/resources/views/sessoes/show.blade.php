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
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    @if(session('success'))
    <div class="toast show align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body fw-bold">{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="toast show align-items-center text-white bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body fw-bold">{{ session('error') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
</div>

<div class="container mt-5 text-light">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card bg-dark border-0 shadow-lg rounded-4">

                <div class="card-header bg-primary text-white border-bottom-0 rounded-top-4 p-4">
                    <h1 class="h2 mb-1 fw-bold">Sessão: {{ $sessao->titulo }}</h1>
                    <p class="mb-1 opacity-75">
                        Mestre: <strong class="text-warning">{{ optional($mestre)->nome ?? 'Mestre Desconhecido' }}</strong>
                    </p>
                    <p class="mb-0 opacity-75">Campanha: {{ $campanha->nome }}</p>
                </div>

                <div class="card-body p-4">

                    <h3 class="h5 text-info mb-3">Detalhes Principais</h3>
                    <div class="bg-secondary-subtle p-3 rounded mb-4">
                        <dl class="row mb-0 small">
                            <dt class="col-sm-4 text-warning">Sistema de Regras:</dt>
                            <dd class="col-sm-8 text-light fw-bold">
                                {{ $campanha->sistema->nome ?? 'Sistema Desconhecido' }}
                            </dd>

                            <dt class="col-sm-4 text-warning">Status Atual:</dt>
                            <dd class="col-sm-8 text-light fw-bold">{{ ucfirst(str_replace('_', ' ', $sessao->status ?? 'Não definido')) }}</dd>

                            <dt class="col-sm-4 text-warning">Agendada para:</dt>
                            <dd class="col-sm-8 text-light">
                                {{ optional($sessao->data_hora)->format('d/m/Y H:i') ?? 'Data não agendada' }}
                            </dd>

                            <dt class="col-sm-4 text-warning">Resumo:</dt>
                            <dd class="col-sm-8 text-secondary fst-italic">{{ $sessao->resumo ?? 'Nenhum resumo inicial.' }}</dd>
                        </dl>
                    </div>

                    <hr class="border-secondary my-4">

                    <h3 class="h5 text-success mb-3">Jogadores Confirmados na Sessão ({{ count($confirmados) }})</h3>
                    <div class="mb-4 p-3 border border-success rounded d-flex flex-wrap gap-2">
                        @forelse($confirmados as $confirmado)
                            <span class="badge {{ $confirmado['isMestre'] ? 'bg-warning text-dark' : 'bg-success' }} p-2 fw-bold shadow-sm">
                                {{ $confirmado['nome'] }}
                                @if($confirmado['isMestre'])
                                    (Mestre)
                                @endif
                            </span>
                        @empty
                            <p class="text-warning mb-0">Nenhum jogador confirmou presença ainda.</p>
                        @endforelse
                    </div>

                    <hr class="border-secondary my-4">

                    <h3 class="h5 text-warning mb-3">Outros Jogadores Ativos da Campanha ({{ $outrosJogadores->count() }})</h3>
                    <p class="text-muted small">Jogadores que ainda não confirmaram presença.</p>
                    <div class="mb-4 p-3 border border-warning rounded d-flex flex-wrap gap-2">
                        @forelse($outrosJogadores as $jogador)
                            <span class="badge bg-secondary p-2 fw-bold shadow-sm">
                                {{ $jogador->nome }}
                            </span>
                        @empty
                            <p class="text-info mb-0">Todos os jogadores ativos já confirmaram presença nesta sessão.</p>
                        @endforelse
                    </div>

                    <hr class="border-secondary my-4">

                    <h3 class="h5 text-info mb-3">Notas do Mestre / Detalhes da História</h3>
                    <div class="bg-secondary p-4 rounded text-light shadow-inner" style="min-height: 180px;">
                        <p class="mb-0">
                            @if(isset($sessao->descricao_detalhada))
                                {!! nl2br(e($sessao->descricao_detalhada)) !!}
                            @else
                                <em class="opacity-75">Nenhuma descrição detalhada disponível.</em>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="card-footer bg-dark border-top border-secondary p-3 d-flex flex-wrap justify-content-between align-items-center rounded-bottom-4">
                    <small class="text-muted">ID da Sessão: {{ $sessao->id }}</small>
                    <div class="d-flex gap-2 flex-wrap mt-2 mt-md-0">

                        @if($podeMarcarPresenca)
                            <form action="{{ route('sessoes.marcar_presenca', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-pill fw-bold px-4 shadow-sm">
                                    Marcar Presença
                                </button>
                            </form>
                        @elseif($jaMarqueiPresenca)
                            <span class="badge bg-success p-2 fw-bold shadow-sm">PRESENÇA CONFIRMADA</span>
                        @endif

                        @if($isMestre)
                            <a href="{{ route('sessoes.edit', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-warning rounded-pill px-4 shadow-sm">
                                Editar Sessão
                            </a>
                        @endif

                        <a href="{{ route('sessoes.index', $campanha->id) }}" class="btn btn-outline-secondary rounded-pill px-4">
                            Todas as Sessões
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
.bg-dark { background-color: #1a1e23 !important; }
.bg-secondary-subtle { background-color: #24292e !important; }
.text-info { color: #63b3ed !important; }
.text-warning { color: #f6ad55 !important; }
.shadow-inner { box-shadow: inset 0 1px 3px rgba(0,0,0,0.6); }
</style>
@endsection
