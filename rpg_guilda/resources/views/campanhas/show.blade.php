@extends('layouts.app')

@section('title', $campanha->nome)

@section('content')
<div class="container py-5">

    {{-- Título da campanha --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold text-warning mb-2">{{ $campanha->nome }}</h2>
        <p class="text-light mb-1"><strong>Sistema:</strong> {{ $campanha->sistemaRPG }}</p>
        <p class="text-light mb-3">{{ $campanha->descricao ?? 'Sem descrição.' }}</p>
        <p class="text-light mb-3">
            <strong>Status:</strong>
            @if($campanha->status ?? 'ativa' === 'ativa')
                <span class="badge bg-success">Ativa</span>
            @elseif($campanha->status === 'pausada')
                <span class="badge bg-warning text-dark">Pausada</span>
            @else
                <span class="badge bg-secondary">Encerrada</span>
            @endif
        </p>

        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="{{ route('campanhas.index') }}" class="btn btn-outline-light btn-sm">⬅️ Voltar</a>
            @if(auth()->user()->id === $campanha->criador_id || auth()->user()->tipo === 'administrador')
                <a href="{{ route('campanhas.edit', $campanha->id) }}" class="btn btn-outline-success btn-sm">✏️ Editar</a>
                <a href="{{ route('campanhas.chat', $campanha->id) }}" class="btn btn-warning btn-sm">💬 Abrir Chat</a>
            @endif
        </div>
    </div>

    {{-- Jogadores --}}
    <div class="mb-5">
        <h4 class="fw-bold text-warning mb-3">🧝 Jogadores</h4>

        @if($campanha->jogadores->count())
            <div class="row g-3">
                @foreach($campanha->jogadores as $jogador)
                    <div class="col-12 col-md-4">
                        <div class="card bg-dark text-light border-warning shadow-sm p-3">
                            <h5>{{ $jogador->nome }}</h5>
                            <p class="mb-1">
                                <strong>Status:</strong>
                                @php $status = $jogador->pivot->status; @endphp
                                @if($status === 'mestre')
                                    <span class="badge bg-success">Mestre</span>
                                @elseif($status === 'player')
                                    <span class="badge bg-info text-dark">Jogador</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($status) }}</span>
                                @endif
                            </p>

                            @if(auth()->user()->id === $campanha->criador_id || auth()->user()->tipo === 'administrador')
                                @if(auth()->user()->id !== $jogador->id)
                                    <form action="{{ route('campanhas.usuarios.remove', $campanha->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="usuario" value="{{ $jogador->id }}">
                                        <button class="btn btn-outline-danger btn-sm w-100 mt-2">❌ Remover</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-secondary fst-italic">Ainda não há jogadores nesta campanha.</p>
        @endif
    </div>

    {{-- Personagens --}}
    <div class="mb-5">
        <h4 class="fw-bold text-warning mb-3">🗡️ Personagens</h4>

        @if($campanha->personagens->count())
            <div class="row g-3">
                @foreach($campanha->personagens as $personagem)
                    <div class="col-12 col-md-4">
                        <div class="card bg-dark text-light border-info shadow-sm p-3">
                            <h5>{{ $personagem->nome }}</h5>
                            <p class="mb-1"><strong>Classe:</strong> {{ $personagem->classe->nome ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Nível:</strong> {{ $personagem->nivel ?? 1 }}</p>
                            <a href="{{ route('personagens.show', $personagem->id) }}" class="btn btn-outline-info btn-sm w-100 mt-2">🔍 Ver</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-secondary fst-italic">Nenhum personagem criado nesta campanha.</p>
        @endif

        @auth
            @if($campanha->jogadores->contains(auth()->user()->id) || auth()->user()->id === $campanha->criador_id)
                <a href="{{ route('personagens.create', ['campanha_id' => $campanha->id]) }}" class="btn btn-outline-warning btn-sm mt-3">➕ Criar Personagem</a>
            @endif
        @endauth
    </div>

    {{-- Sessões --}}
    <div class="mb-5">
        <h4 class="fw-bold text-warning mb-3">🗺️ Sessões</h4>

        @if($campanha->sessoes->count())
            <ul class="list-group">
                @foreach($campanha->sessoes as $sessao)
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-dark text-light">
                        {{ $sessao->titulo }} - {{ $sessao->data ? $sessao->data->format('d/m/Y') : 'Sem data' }}
                        @if(auth()->user()->id === $campanha->criador_id || auth()->user()->tipo === 'administrador')
                            <form action="{{ route('sessoes.destroy', $sessao->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm">❌ Remover</button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-secondary fst-italic">Nenhuma sessão criada ainda.</p>
        @endif

        @if(auth()->user()->id === $campanha->criador_id || auth()->user()->tipo === 'administrador')
            <a href="{{ route('sessoes.create', ['campanha' => $campanha->id]) }}" class="btn btn-outline-warning btn-sm mt-3">➕ Criar Sessão</a>
        @endif
    </div>

</div>
@endsection
