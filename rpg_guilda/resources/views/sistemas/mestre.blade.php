@extends('layouts.app')

@section('title', 'Área do Mestre - ' . $campanha->nome)

@section('content')
<div class="container py-5">
    @php
        $isMestre = auth()->check() && auth()->user()->id === $campanha->criador_id;
        $proximaSessao = $campanha->sessoes->where('data', '>=', now())->sortBy('data')->first();
        $npcs = $campanha->personagens->where('npc', true);
        $amigosDisponiveis = $amigos->reject(fn($amigo) => $campanha->jogadores->contains($amigo->id));

        // Redireciona se não for o mestre
        if (!$isMestre) {
            return redirect()->route('campanhas.show', $campanha->id)->with('error', 'Acesso negado. Você não é o mestre desta campanha.');
        }

        $jogadoresAtivos = $campanha->jogadores->where('pivot.status', 'ativo')->pluck('nome')->implode(', ');
    @endphp

    <div class="text-center mb-4">
        <h1 class="fw-bold text-danger mb-2">⚙️ Área do Mestre</h1>
        <h2 class="fw-bold text-warning mb-0">{{ $campanha->nome }}</h2>
        <p class="text-light mb-4 small">Mestre: **{{ $campanha->criador->nome ?? 'Mestre Desconhecido' }}**</p>

        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-light btn-sm">⬅️ Voltar para a Campanha</a>
            <a href="{{ route('campanhas.edit', $campanha->id) }}" class="btn btn-outline-success btn-sm">✏️ Editar Campanha</a>
            <a href="{{ route('campanhas.chat', $campanha->id) }}" class="btn btn-warning btn-sm">💬 Chat</a>
        </div>
    </div>

    <hr class="text-light">

    {{-- Abas: Jogadores (Com Gerenciamento) / NPCs / Sessões --}}
    <ul class="nav nav-tabs mb-3" id="mestreTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="jogadores-mestre-tab" data-bs-toggle="tab" data-bs-target="#jogadores-mestre" type="button" role="tab" aria-controls="jogadores-mestre" aria-selected="true">
                👥 Gerenciar Jogadores
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="npcs-tab" data-bs-toggle="tab" data-bs-target="#npcs" type="button" role="tab" aria-controls="npcs" aria-selected="false">
                🗡️ NPCs
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sessoes-mestre-tab" data-bs-toggle="tab" data-bs-target="#sessoes-mestre" type="button" role="tab" aria-controls="sessoes-mestre" aria-selected="false">
                🗺️ Gerenciar Sessões
            </button>
        </li>
    </ul>

    <div class="tab-content" id="mestreTabContent">
        {{-- Gerenciar Jogadores (Inclui Ativos, Pendentes e Rejeitados) --}}
        <div class="tab-pane fade show active" id="jogadores-mestre" role="tabpanel" aria-labelledby="jogadores-mestre-tab">
            @include('campanhas.partials.gerenciar_jogadores', ['campanha' => $campanha, 'isMestre' => true])

            {{-- Adicionar amigos (Apenas mestre) --}}
            <div class="mb-5 mt-5">
                <h4 class="fw-bold text-warning mb-3">➕ Enviar Convite a Amigos</h4>
                <form action="{{ route('campanhas.usuarios.adicionar', $campanha->id) }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <select name="user_id" class="form-select" required>
                            <option value="">Selecione um amigo</option>
                            @foreach($amigosDisponiveis as $amigo)
                                <option value="{{ $amigo->id }}">{{ $amigo->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-success w-100">Enviar convite</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- NPCs (Apenas mestre) --}}
        <div class="tab-pane fade" id="npcs" role="tabpanel" aria-labelledby="npcs-tab">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('personagens.create', ['campanha_id' => $campanha->id, 'npc' => true]) }}" class="btn btn-outline-warning btn-sm">➕ Criar NPC</a>
            </div>

            @if($npcs->count())
                <div class="row g-3">
                    @foreach($npcs as $npc)
                        {{-- Inclui o card para NPC --}}
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

        {{-- Gerenciar Sessões --}}
        <div class="tab-pane fade" id="sessoes-mestre" role="tabpanel" aria-labelledby="sessoes-mestre-tab">
            <a href="{{ route('sessoes.create', ['campanha' => $campanha->id]) }}" class="btn btn-outline-warning btn-sm mb-3">➕ Criar Nova Sessão</a>

            <h4 class="fw-bold text-light mt-4 mb-3">Lista Completa de Sessões</h4>

            @if($campanha->sessoes->count())
                <ul class="list-group list-group-flush mt-3">
                    @foreach($campanha->sessoes->sortByDesc('data') as $sessao)
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-dark text-light border-secondary">
                            <div>
                                <strong class="{{ $sessao->data->isFuture() ? 'text-info' : 'text-light' }}">
                                    {{ $sessao->titulo }}
                                    @if($sessao->data->isFuture())
                                        <span class="badge bg-info">Próxima</span>
                                    @endif
                                </strong>
                                <span class="text-secondary">— {{ $sessao->data?->format('d/m/Y H:i') ?? 'Sem data' }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('sessoes.show', $sessao->id) }}" class="btn btn-outline-info btn-sm">🔍 Ver</a>
                                <a href="{{ route('sessoes.edit', $sessao->id) }}" class="btn btn-outline-success btn-sm">✏️ Editar</a>
                                <form action="{{ route('sessoes.destroy', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta sessão?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">❌ Remover</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-secondary fst-italic mt-3">Nenhuma sessão criada ainda. Crie a primeira!</p>
            @endif
        </div>
    </div>
</div>
@endsection
