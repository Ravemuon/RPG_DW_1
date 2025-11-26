@extends('layouts.app')

@section('title', 'Todas as Campanhas')

@section('content')
<div class="container py-5 text-light">

    {{-- Cabeçalho Principal --}}
    <div class="text-center mb-5">
        <h1 class="fw-bolder display-4 text-highlight">
            <i class="bi bi-compass me-2"></i> Explorar Aventuras
        </h1>
        <p class="text-secondary fs-6">
            Descubra mundos, comece a sua jornada ou crie uma nova saga.
        </p>
    </div>

    {{-- Barra de busca e criar campanha (Otimizada) --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-4 mb-5">

        {{-- Busca --}}
        <form action="{{ route('campanhas.todas') }}" method="GET" class="grow me-md-4">
            <div class="input-group input-group-lg shadow-sm">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="🔍 Buscar campanhas por nome ou sistema..."
                       class="form-control bg-dark text-light border-highlight rounded-start-pill border-end-0">
                <button type="submit" class="btn btn-highlight rounded-end-pill px-4 fw-bold">
                    Buscar
                </button>
            </div>
        </form>

        {{-- Criar Campanha --}}
        @auth
            <a href="{{ route('campanhas.create') }}" class="btn btn-success btn-lg fw-bold rounded-pill px-5 shadow">
                <i class="bi bi-plus-circle-fill me-2"></i> Criar Campanha
            </a>
        @endauth
    </div>

    <hr class="border-secondary mb-5">

    {{-- Lista de campanhas --}}
    <div class="row g-5">
        @forelse($todasCampanhas as $campanha)
            @php
                $user = auth()->user();
                $isMestre = $user && $user->id === $campanha->criador_id;

                // Pega a informação de participação (ativo, pendente, recusado)
                $jogador = $campanha->jogadores->where('id', auth()->id())->first();
                $pivot = $jogador?->pivot;

                // Define a classe de destaque no card
                $cardHighlightClass = $isMestre ? 'border-highlight-mestre' : ($pivot && $pivot->status === 'ativo' ? 'border-highlight-ativo' : 'border-highlight');
            @endphp

            <div class="col-sm-6 col-lg-4">
                <div class="card campanha-card shadow-lg bg-dark h-100 {{ $cardHighlightClass }}">
                    <div class="card-body d-flex flex-column text-center p-4">

                        {{-- Avatar e Mestre --}}
                        <div class="mb-3">
                            <img src="{{ $campanha->criador->avatar_url ?? 'https://via.placeholder.com/85/343a40/ffffff?text=GM' }}"
                                 alt="Avatar do mestre"
                                 class="rounded-circle border shadow-sm avatar-mestre"
                                 style="width: 85px; height: 85px; object-fit: cover;">
                        </div>

                        {{-- Título e Mestre --}}
                        <h4 class="fw-bolder text-highlight mb-1">{{ $campanha->nome }}</h4>
                        <p class="text-secondary small mb-3">
                            <i class="bi bi-crown-fill me-1"></i> Mestre: **{{ $campanha->criador->nome ?? 'Desconhecido' }}**
                        </p>

                        {{-- Descrição curta --}}
                        <p class="text-light small mb-4 grow">
                            {{ Str::limit($campanha->descricao ?? 'Sem descrição disponível.', 90, '...') }}
                        </p>

                        {{-- Info básicas (Badges) --}}
                        <div class="d-flex justify-content-center gap-2 flex-wrap mb-4">
                            <span class="badge rounded-pill bg-info text-dark fw-bold">
                                <i class="bi bi-dice-5-fill me-1"></i> {{ $campanha->sistema->nome ?? 'Desconhecido' }}
                            </span>
                            <span class="badge rounded-pill bg-{{ $campanha->status === 'ativa' ? 'success' : 'secondary' }} fw-bold">
                                <i class="bi bi-activity me-1"></i> {{ ucfirst($campanha->status) }}
                            </span>
                            <span class="badge rounded-pill bg-{{ $campanha->privada ? 'warning text-dark' : 'primary' }} fw-bold">
                                <i class="bi bi-{{ $campanha->privada ? 'lock-fill' : 'globe2' }} me-1"></i> {{ $campanha->privada ? 'Privada' : 'Pública' }}
                            </span>
                        </div>

                        {{-- Ações --}}
                        <div class="mt-auto d-grid gap-2">

                            {{-- Ver Detalhes (Sempre Primário) --}}
                            <a href="{{ route('campanhas.show', $campanha->id) }}"
                               class="btn btn-outline-light rounded-pill fw-bold">
                                <i class="bi bi-eye-fill me-1"></i> Ver Detalhes
                            </a>

                            @auth
                                @if($isMestre)
                                    {{-- Mestre --}}
                                    <a href="{{ route('campanhas.mestre', $campanha->id) }}" class="btn btn-highlight rounded-pill fw-bold">
                                        <i class="bi bi-tools me-1"></i> Área do Mestre
                                    </a>
                                @elseif($pivot && $pivot->status === 'ativo')
                                    {{-- Participa --}}
                                    <button class="btn btn-success rounded-pill fw-bold" disabled>
                                        <i class="bi bi-check-circle-fill me-1"></i> Você Participa
                                    </button>
                                @elseif($pivot && $pivot->status === 'pendente')
                                    {{-- Pendente --}}
                                    <button class="btn btn-warning text-dark rounded-pill fw-bold" disabled>
                                        <i class="bi bi-hourglass-split me-1"></i> Solicitação Pendente
                                    </button>
                                @elseif(!$pivot)
                                    {{-- Solicitar entrada (se não for mestre e não estiver no pivot) --}}
                                    <form action="{{ route('campanhas.solicitar', $campanha->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary rounded-pill fw-bold w-100">
                                            <i class="bi bi-person-plus-fill me-1"></i> Solicitar Participação
                                        </button>
                                    </form>
                                @endif

                                {{-- Ações Administrativas (Excluir/Editar) --}}
                                @if(auth()->id() === $campanha->criador_id || auth()->user()->tipo === 'administrador')
                                    <div class="d-flex justify-content-center gap-2 mt-2">
                                        <a href="{{ route('campanhas.edit', $campanha->id) }}"
                                           class="btn btn-sm btn-outline-info rounded-pill fw-bold flex-fill">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </a>
                                        <form action="{{ route('campanhas.destroy', $campanha->id) }}" method="POST" class="d-inline flex-fill">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill fw-bold w-100"
                                                    onclick="return confirm('Deseja realmente excluir esta campanha?')">
                                                <i class="bi bi-trash-fill"></i> Excluir
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div> {{-- Fim Ações --}}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <h3 class="text-secondary mb-3">Nenhuma campanha encontrada.</h3>
                <p class="text-muted fst-italic">Tente ajustar sua busca ou crie uma nova aventura!</p>
                @auth
                    <a href="{{ route('campanhas.create') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                        <i class="bi bi-plus-circle-fill me-2"></i> Criar Campanha Agora
                    </a>
                @endauth
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div class="mt-5 d-flex justify-content-center">
        {{ $todasCampanhas->withQueryString()->links() }}
    </div>
</div>

<style>
/* Estilos para Dark Mode */
.bg-dark { background-color: #212529 !important; }
.text-highlight { color: #ffc107 !important; /* Amarelo/Dourado */ }
.btn-highlight {
    background-color: #ffc107 !important;
    color: #212529 !important;
    border-color: #ffc107 !important;
}
.border-highlight { border: 2px solid #ffc107; }
.border-highlight-mestre { border: 3px solid #ffc107; }
.border-highlight-ativo { border: 3px solid #198754; /* Verde do Success */ }

/* Card e Avatar */
.campanha-card {
    transition: all .3s ease-in-out;
    border-radius: 0.75rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.4);
    border: 1px solid #495057; /* Borda padrão mais escura */
}
.campanha-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.7);
}
.avatar-mestre {
    border-color: #ffc107 !important;
}
</style>
@endsection
