@extends('layouts.app')

{{-- Corrigido: Usando a coluna 'nome' em vez de 'name' --}}
@php
    $user = auth()->user();
    $mestre = $campanha->criador; // Facilitando o acesso ao objeto Mestre
    $isMestre = $user && $user->id === optional($mestre)->id; // Usando optional() por segurança

    // Simplificado o acesso a presenças
    $idsConfirmadosSessao = $sessao->presencas->pluck('id')->toArray();
    $jaMarqueiPresenca = $user && in_array($user->id, $idsConfirmadosSessao);

    // Lógica para marcar presença
    $podeMarcarPresenca = $user && !$isMestre && !$jaMarqueiPresenca &&
                          $sessao->status === 'agendada'; // Presença só pode ser marcada se agendada

    // Lista de jogadores confirmados para exibição
    $confirmados = [];
    if($mestre) {
        // Mestre sempre listado primeiro e como Mestre
        $confirmados[] = ['nome' => $mestre->nome, 'isMestre' => true];
    }

    // Adiciona jogadores que confirmaram presença (e não são o Mestre)
    foreach($sessao->presencas->where('id', '!=', optional($mestre)->id) as $jogador){
        // Usando $jogador->nome
        $confirmados[] = ['nome' => $jogador->nome, 'isMestre' => false];
    }

    // Outros jogadores ativos da campanha que AINDA NÃO confirmaram
    $outrosJogadores = $campanha->jogadores
        // Filtra apenas jogadores 'ativos' e que não estão na lista de confirmados
        ->filter(fn($jogador) =>
            $jogador->pivot->status === 'ativo' &&
            !in_array($jogador->id, $idsConfirmadosSessao) &&
            $jogador->id !== optional($mestre)->id // Garante que o mestre não apareça aqui
        )
        ->sortBy('nome'); // Ordena pelo nome
@endphp

@section('title', 'Detalhes da Sessão: ' . $sessao->titulo)

@section('content')
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    {{-- Bloco de Toasts (Mantido) --}}
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

                {{-- Cabeçalho --}}
                <div class="card-header bg-primary text-white border-bottom-0 rounded-top-4 p-4">
                    <h1 class="h2 mb-1 fw-bold">Sessão: {{ $sessao->titulo }}</h1>
                    {{-- CORRIGIDO: Usando ->nome --}}
                    <p class="mb-1 opacity-75">
                        <i class="fas fa-hat-wizard me-1"></i> Mestre: <strong class="text-warning">{{ optional($mestre)->nome ?? 'Mestre Desconhecido' }}</strong>
                    </p>
                    <p class="mb-0 opacity-75"><i class="fas fa-scroll me-1"></i> Campanha: {{ $campanha->nome }}</p>
                </div>

                {{-- Corpo --}}
                <div class="card-body p-4">

                    {{-- Detalhes Principais --}}
                    <h3 class="h5 text-info mb-3"><i class="fas fa-info-circle me-2"></i> Detalhes Principais</h3>
                    <div class="bg-secondary-subtle p-3 rounded mb-4">
                        <dl class="row mb-0 small">
                            <dt class="col-sm-4 text-warning">Sistema de Regras:</dt>
                            <dd class="col-sm-8 text-light fw-bold">
                                {{ $campanha->sistema->nome ?? 'Sistema Desconhecido' }}
                            </dd>

                            <dt class="col-sm-4 text-warning">Status Atual:</dt>
                            <dd class="col-sm-8 text-light fw-bold">{{ ucfirst(str_replace('_', ' ', $sessao->status ?? 'Não definido')) }}</dd>

                            <dt class="col-sm-4 text-warning">Agendada para:</dt>
                            <dd class="col-sm-8 text-light">{{ optional($sessao->data_hora)->format('d/m/Y H:i') ?? 'Data não agendada' }}</dd>

                            <dt class="col-sm-4 text-warning">Resumo:</dt>
                            <dd class="col-sm-8 text-secondary fst-italic">{{ $sessao->resumo ?? 'Nenhum resumo inicial.' }}</dd>
                        </dl>
                    </div>

                    <hr class="border-secondary my-4">

                    {{-- Jogadores Confirmados --}}
                    <h3 class="h5 text-success mb-3">
                        <i class="fas fa-user-check me-2"></i> Jogadores Confirmados na Sessão ({{ count($confirmados) }})
                    </h3>
                    <div class="mb-4 p-3 border border-success rounded d-flex flex-wrap gap-2">
                        @forelse($confirmados as $confirmado)
                            {{-- CORRIGIDO: Usando $confirmado['nome'] --}}
                            <span class="badge {{ $confirmado['isMestre'] ? 'bg-warning text-dark' : 'bg-success' }} p-2 fw-bold shadow-sm">
                                <i class="fas {{ $confirmado['isMestre'] ? 'fa-hat-wizard' : 'fa-user-check' }} me-1"></i>
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

                    {{-- Outros Jogadores da Campanha --}}
                    <h3 class="h5 text-warning mb-3">
                        <i class="fas fa-users me-2"></i> Outros Jogadores Ativos da Campanha ({{ $outrosJogadores->count() }})
                    </h3>
                    <p class="text-muted small">Jogadores da campanha que **ainda não** confirmaram presença nesta sessão.</p>
                    <div class="mb-4 p-3 border border-warning rounded d-flex flex-wrap gap-2">
                        @forelse($outrosJogadores as $jogador)
                            {{-- CORRIGIDO: Usando $jogador->nome --}}
                            <span class="badge bg-secondary p-2 fw-bold shadow-sm">
                                <i class="fas fa-user me-1"></i> {{ $jogador->nome }}
                            </span>
                        @empty
                            <p class="text-info mb-0">Todos os jogadores ativos já confirmaram presença nesta sessão.</p>
                        @endforelse
                    </div>

                    <hr class="border-secondary my-4">

                    {{-- Notas do Mestre --}}
                    <h3 class="h5 text-info mb-3"><i class="fas fa-book-open me-2"></i> Notas do Mestre / Detalhes da História</h3>
                    <div class="bg-secondary p-4 rounded text-light shadow-inner" style="min-height: 180px;">
                        <p class="mb-0">
                            @if(isset($sessao->descricao_detalhada))
                                {!! nl2br(e($sessao->descricao_detalhada)) !!}
                            @else
                                <em class="opacity-75">*Nenhuma descrição detalhada disponível para esta sessão. Aguarde atualizações do Mestre.*</em>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Rodapé com ações --}}
                <div class="card-footer bg-dark border-top border-secondary p-3 d-flex flex-wrap justify-content-between align-items-center rounded-bottom-4">
                    <small class="text-muted">ID da Sessão: {{ $sessao->id }}</small>
                    <div class="d-flex gap-2 flex-wrap mt-2 mt-md-0">

                        {{-- Botão de Marcar Presença --}}
                        @if($podeMarcarPresenca)
                            <form action="{{ route('sessoes.marcar_presenca', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-pill fw-bold px-4 shadow-sm">
                                    ✅ Marcar Presença
                                </button>
                            </form>
                        @elseif($jaMarqueiPresenca)
                            <span class="badge bg-success p-2 fw-bold shadow-sm">PRESENÇA CONFIRMADA!</span>
                        @endif

                        {{-- Botão de Edição (Somente Mestre) --}}
                        @if($isMestre)
                            <a href="{{ route('sessoes.edit', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-warning rounded-pill px-4 shadow-sm">
                                ✏️ Editar Sessão
                            </a>
                        @endif

                        <a href="{{ route('sessoes.index', $campanha->id) }}" class="btn btn-outline-secondary rounded-pill px-4">
                            ← Todas as Sessões
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Dependências --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
.bg-dark { background-color: #1a1e23 !important; }
.bg-secondary-subtle { background-color: #24292e !important; }
.text-info { color: #63b3ed !important; }
.text-warning { color: #f6ad55 !important; }
.shadow-inner { box-shadow: inset 0 1px 3px rgba(0,0,0,0.6); }
</style>
@endsection
