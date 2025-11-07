@extends('layouts.app')

@section('title', $campanha->nome)

@section('content')
<div class="container py-5">

    {{-- Cabeçalho da campanha --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold text-warning mb-2">{{ $campanha->nome }}</h2>
        <p class="text-light mb-1"><strong>Sistema:</strong> {{ $campanha->sistema->nome ?? 'Desconhecido' }}</p>
        <p class="text-light mb-3">{{ $campanha->descricao ?? 'Sem descrição.' }}</p>
        <p class="text-light mb-3">
            <strong>Status:</strong>
            @php
                $statusBadge = match($campanha->status ?? 'ativa') {
                    'ativa' => '<span class="badge bg-success">Ativa</span>',
                    'pausada' => '<span class="badge bg-warning text-dark">Pausada</span>',
                    'encerrada' => '<span class="badge bg-secondary">Encerrada</span>',
                    default => '<span class="badge bg-secondary">Desconhecido</span>'
                };
            @endphp
            {!! $statusBadge !!}
        </p>

        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="{{ route('campanhas.todas') }}" class="btn btn-outline-light btn-sm">⬅️ Voltar</a>
            @auth
                @if(auth()->user()->id === $campanha->criador_id || auth()->user()->tipo === 'administrador')
                    <a href="{{ route('campanhas.edit', $campanha->id) }}" class="btn btn-outline-success btn-sm">✏️ Editar</a>
                    <a href="{{ route('campanhas.chat', $campanha->id) }}" class="btn btn-warning btn-sm">💬 Chat</a>
                @endif
            @endauth
        </div>
    </div>

    {{-- Status do usuário logado --}}
    @auth
        @php
            $isMestre = auth()->user()->id === $campanha->criador_id;

            if($isMestre) {
                $userStatus = 'mestre';
            } else {
                $userPivot = $campanha->jogadores->firstWhere('id', auth()->user()->id)?->pivot;
                $userStatus = $userPivot->status ?? null;
            }
        @endphp

        <div class="mb-4">
            @if($userStatus === 'pendente')
                <div class="alert alert-warning text-center">⏳ Solicitação de entrada enviada, aguardando aprovação.</div>
            @elseif($userStatus === 'ativo')
                <div class="alert alert-success text-center">✅ Você é jogador desta campanha.</div>
            @elseif($userStatus === 'mestre')
                <div class="alert alert-success text-center">✅ Você é o mestre desta campanha.</div>
            @endif
        </div>
    @endauth

    {{-- Abas: Jogadores / NPCs --}}
    <ul class="nav nav-tabs mb-3" id="campanhaTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="jogadores-tab" data-bs-toggle="tab" data-bs-target="#jogadores" type="button" role="tab" aria-controls="jogadores" aria-selected="true">
                👥 Jogadores
            </button>
        </li>
        @if($isMestre)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="npcs-tab" data-bs-toggle="tab" data-bs-target="#npcs" type="button" role="tab" aria-controls="npcs" aria-selected="false">
                🗡️ NPCs
            </button>
        </li>
        @endif
    </ul>

    <div class="tab-content" id="campanhaTabContent">
        {{-- Jogadores --}}
        <div class="tab-pane fade show active" id="jogadores" role="tabpanel" aria-labelledby="jogadores-tab">
            @include('campanhas.partials.jogadores', ['campanha' => $campanha, 'isMestre' => $isMestre])
        </div>

        {{-- NPCs (apenas mestre) --}}
        @if($isMestre)
        <div class="tab-pane fade" id="npcs" role="tabpanel" aria-labelledby="npcs-tab">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('personagens.create', ['campanha_id' => $campanha->id, 'npc' => true]) }}" class="btn btn-outline-warning btn-sm">➕ Criar NPC</a>
            </div>

            @php
                $npcs = $campanha->personagens->where('npc', true);
            @endphp

            @if($npcs->count())
                <div class="row g-3">
                    @foreach($npcs as $npc)
                        @include('campanhas.partials.card', [
                            'titulo' => $npc->nome,
                            'descricao' => $npc->descricao ?? 'Sem descrição',
                            'borda' => 'border-secondary',
                            'acoes' => [
                                [
                                    'tipo' => 'link',
                                    'rota' => route('personagens.edit', $npc->id),
                                    'classe' => 'btn-outline-success btn-sm',
                                    'texto' => '✏️ Editar'
                                ],
                                [
                                    'tipo' => 'form',
                                    'rota' => route('personagens.destroy', $npc->id),
                                    'classe' => 'btn-outline-danger btn-sm',
                                    'texto' => '❌ Remover',
                                    'metodo' => 'DELETE',
                                    'confirm' => 'Tem certeza que deseja excluir este NPC?'
                                ]
                            ]
                        ])
                    @endforeach
                </div>
            @else
                <p class="text-secondary fst-italic mt-3">Nenhum NPC criado ainda.</p>
            @endif
        </div>
        @endif
    </div>

    {{-- Adicionar amigos (apenas mestre) --}}
    @auth
        @if($isMestre)
            <div class="mb-5 mt-5">
                <h4 class="fw-bold text-warning mb-3">➕ Adicionar amigos à campanha</h4>
                <form action="{{ route('campanhas.usuarios.adicionar', $campanha->id) }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <select name="user_id" class="form-select" required>
                            <option value="">Selecione um amigo</option>
                            @foreach($amigos as $amigo)
                                @if(!$campanha->jogadores->contains($amigo->id))
                                    <option value="{{ $amigo->id }}">{{ $amigo->nome }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-success w-100">Enviar convite</button>
                    </div>
                </form>
            </div>
        @endif
    @endauth

    {{-- Sessões --}}
    <div class="mb-5 mt-5">
        <h4 class="fw-bold text-warning mb-3">🗺️ Sessões</h4>
        @php
            $proximaSessao = $campanha->sessoes->where('data', '>=', now())->sortBy('data')->first();
            $ultimaSessao = $campanha->sessoes->where('data', '<', now())->sortByDesc('data')->first();
        @endphp

        <div class="row g-3">
            @if($proximaSessao)
                @include('campanhas.partials.card', [
                    'titulo' => "📅 Próxima Sessão: {$proximaSessao->titulo}",
                    'descricao' => "Data: {$proximaSessao->data->format('d/m/Y H:i')}",
                    'borda' => 'border-warning'
                ])
            @endif
            @if($ultimaSessao)
                @include('campanhas.partials.card', [
                    'titulo' => "🕒 Última Sessão: {$ultimaSessao->titulo}",
                    'descricao' => "Data: {$ultimaSessao->data->format('d/m/Y H:i')}",
                    'borda' => 'border-info'
                ])
            @endif
        </div>

        @if($campanha->sessoes->count())
            <ul class="list-group list-group-flush mt-3">
                @foreach($campanha->sessoes->sortBy('data') as $sessao)
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-dark text-light">
                        <div>
                            <strong>{{ $sessao->titulo }}</strong>
                            <span class="text-secondary">— {{ $sessao->data?->format('d/m/Y') ?? 'Sem data' }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            @if(in_array($userStatus, ['mestre','ativo']))
                                <a href="{{ route('sessoes.show', $sessao->id) }}" class="btn btn-outline-info btn-sm">🔍 Ver</a>
                            @endif
                            @auth
                                @if($userStatus === 'mestre')
                                    <a href="{{ route('sessoes.edit', $sessao->id) }}" class="btn btn-outline-success btn-sm">✏️ Editar</a>
                                    <form action="{{ route('sessoes.destroy', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta sessão?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">❌ Remover</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-secondary fst-italic mt-3">Nenhuma sessão criada ainda.</p>
        @endif

        @auth
            @if($userStatus === 'mestre')
                <a href="{{ route('sessoes.create', ['campanha' => $campanha->id]) }}" class="btn btn-outline-warning btn-sm mt-3">➕ Criar Sessão</a>
            @endif
        @endauth
    </div>
</div>
@endsection
