@extends('layouts.app')

@section('title', $campanha->nome)

@section('content')
<div class="container py-4">

    {{-- 🏰 Cabeçalho Principal --}}
    <div class="p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="fw-bolder">
                <i class="fas fa-dice-d20 me-2"></i> {{ $campanha->nome }}
                <span class="badge bg-secondary ms-2">Campanha</span>
            </h1>
            <div class="d-flex flex-wrap gap-2">
                {{-- Botão de Área do Mestre --}}
                @if(auth()->check() && auth()->id() === $campanha->criador_id)
                    <a href="{{ route('campanhas.mestre', $campanha->id) }}" class="btn btn-warning">
                        <i class="fas fa-hat-wizard"></i> Área do Mestre
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Coluna Principal --}}
        <div class="col-lg-8">

            {{-- 📜 Descrição --}}
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-scroll me-1"></i> Descrição da Campanha
                </div>
                <div class="card-body">
                    <p class="lead mb-0">
                        {{ $campanha->descricao ?? 'A história ainda está sendo escrita pelo Mestre.' }}
                    </p>
                </div>
            </div>

            {{-- 📖 Sessões --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-book-open me-1"></i> Diário de Sessões</h5>
                    @if(auth()->check() && auth()->id() === $campanha->criador_id)
                        <a href="#" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-plus"></i> Nova Sessão
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if(isset($campanha->sessoes) && $campanha->sessoes->count())
                        <ul class="list-group list-group-flush">
                            @foreach($campanha->sessoes as $sessao)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $sessao->titulo }}</h6>
                                            <p class="small mb-1">
                                                <i class="fas fa-calendar-alt"></i>
                                                Data: {{ optional($sessao->data)->format('d/m/Y') ?? 'Sem data definida' }}
                                                | <i class="fas fa-user-edit"></i>
                                                Resumo por: {{ $sessao->criador->nome ?? 'Desconhecido' }}
                                            </p>
                                            <p class="small mb-0">
                                                {{ Str::limit($sessao->resumo ?? 'Sem resumo disponível.', 150) }}
                                            </p>
                                        </div>

                                        {{-- Botão "Ver Sessão" --}}
                                        <div class="ms-3">
                                            <a href="{{ route('sessoes.show', $sessao->id) }}" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Nenhuma sessão registrada ainda.
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🎯 Missões --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-bullseye me-1"></i> Missões da Campanha</h5>
                    <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-list"></i> Ver Todas as Missões
                    </a>
                    @if(auth()->check() && auth()->id() === $campanha->criador_id)
                        <a href="{{ route('missoes.create', $campanha->id) }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-plus"></i> Nova Missão
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($campanha->missoes->count())
                        <ul class="list-group list-group-flush">
                            @foreach($campanha->missoes as $missao)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $missao->titulo }}</h6>
                                            <p class="small mb-1">
                                                <i class="fas fa-calendar-alt"></i>
                                                Data Limite: {{ optional($missao->data_limite)->format('d/m/Y') ?? 'Sem data definida' }}
                                                | <i class="fas fa-user-edit"></i>
                                                Criada por: {{ $missao->criador->nome ?? 'Desconhecido' }}
                                            </p>
                                            <p class="small mb-0">
                                                {{ Str::limit($missao->descricao ?? 'Sem descrição disponível.', 150) }}
                                            </p>
                                        </div>

                                        {{-- Botão "Ver Missão" --}}
                                        <div class="ms-3">
                                            <a href="{{ route('missoes.show', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Nenhuma missão registrada ainda.
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🧝‍♂️ Personagens --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users me-1"></i> Personagens</h5>
                    @if(auth()->check() && $campanha->jogadores->contains('id', auth()->id()))
                        <a href="{{ route('personagens.create', ['campanha' => $campanha->id]) }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-plus"></i> Criar Personagem
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @php
                        $personagensVisiveis = $campanha->personagens->filter(function ($p) use ($campanha) {
                            if ($p->user_id === $campanha->criador_id) {
                                return auth()->id() === $campanha->criador_id || $p->publico;
                            }
                            return true;
                        });
                    @endphp

                    @if($personagensVisiveis->count())
                        <div class="row g-3">
                            @foreach($personagensVisiveis as $personagem)
                                <div class="col-sm-6 col-md-4">
                                    <div class="card bg-light text-dark">
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="fw-bold mb-1">{{ $personagem->nome }}</h6>
                                            <p class="small mb-2">
                                                <i class="fas fa-user-tag me-1"></i>
                                                Jogador: <span class="fw-bold">{{ $personagem->user->nome }}</span>
                                            </p>
                                            <p class="small mb-0">
                                                {{ Str::limit($personagem->descricao, 80) }}
                                            </p>

                                            @if($personagem->user_id === $campanha->criador_id)
                                                <p class="text-danger small mb-2">
                                                    <i class="fas fa-lock me-1"></i>
                                                    Mestre: {{ $personagem->publico ? 'Público' : 'Privado' }}
                                                </p>
                                            @endif

                                            <div class="mt-auto d-flex justify-content-between pt-2">
                                                <a href="{{ route('personagens.show', $personagem->id) }}" class="btn btn-outline-light btn-sm">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                                @if(auth()->check() && (auth()->id() === $personagem->user_id || auth()->id() === $campanha->criador_id))
                                                    <a href="{{ route('personagens.edit', $personagem->id) }}" class="btn btn-outline-warning btn-sm">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-secondary fst-italic mb-0">
                            <i class="fas fa-hourglass-start"></i> Nenhum personagem criado ainda.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- 🎭 Jogadores Participantes --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i> Jogadores Participantes
                </div>
                <div class="card-body p-0">
                    @if($campanha->jogadores->count())
                        <ul class="list-group list-group-flush">
                            @foreach($campanha->jogadores->sortByDesc(fn($j) => $j->id === $campanha->criador_id) as $jogador)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas {{ $jogador->id === $campanha->criador_id ? 'fa-crown text-warning' : 'fa-user text-info' }} me-2"></i>
                                        {{ $jogador->nome }}
                                        @if($jogador->id === $campanha->criador_id)
                                            <span class="badge bg-warning text-dark ms-2">Mestre</span>
                                        @endif
                                    </span>
                                    <span class="badge
                                        {{ $jogador->pivot->status === 'ativo' ? 'bg-success' :
                                           ($jogador->pivot->status === 'pendente' ? 'bg-info' : 'bg-secondary') }}">
                                        {{ ucfirst($jogador->pivot->status) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary fst-italic p-3 mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            Ninguém se juntou à aventura ainda.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
