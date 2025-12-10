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
    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-4 mb-5">

        {{-- Busca - Ocupa o máximo de espaço em telas médias/grandes --}}
        <form action="{{ route('campanhas.todas') }}" method="GET" class="flex-grow-1 w-100 w-md-auto">
            <div class="input-group input-group-lg shadow-lg">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="🔍 Buscar campanhas por nome, sistema ou mestre..."
                       class="form-control bg-dark text-light border-highlight rounded-start-pill border-end-0">
                <button type="submit" class="btn btn-highlight rounded-end-pill px-4 fw-bold">
                    <i class="bi bi-search d-md-none"></i> <span class="d-none d-md-inline">Buscar</span>
                </button>
            </div>
        </form>

        {{-- Criar Campanha --}}
        @auth
            <a href="{{ route('campanhas.create') }}" class="btn btn-success btn-lg fw-bold rounded-pill px-5 shadow-lg flex-shrink-0">
                <i class="bi bi-plus-circle-fill me-2"></i> Criar Campanha
            </a>
        @endauth
    </div>

    <hr class="border-secondary mb-5 opacity-25">

    {{-- Lista de campanhas --}}
    <div class="row g-5">
        @forelse($todasCampanhas as $campanha)
            @php
                $user = auth()->user();
                $isMestre = $user && $user->id === $campanha->criador_id;

                // Pega a informação de participação (ativo, pendente, recusado)
                $participacao = $campanha->jogadores()->where('user_id', auth()->id())->first()?->pivot;

                // Define a classe de destaque no card
                if ($isMestre) {
                    $cardHighlightClass = 'border-highlight-mestre';
                    $actionBadge = 'Mestre';
                } elseif ($participacao) {
                    $status = $participacao->status;
                    if ($status === 'ativo') {
                        $cardHighlightClass = 'border-highlight-ativo';
                        $actionBadge = 'Jogador Ativo';
                    } elseif ($status === 'pendente') {
                        $cardHighlightClass = 'border-highlight-pendente';
                        $actionBadge = 'Solicitação Pendente';
                    } else {
                         // Recusado/Outro
                        $cardHighlightClass = 'border-highlight';
                        $actionBadge = '';
                    }
                } else {
                    $cardHighlightClass = 'border-highlight';
                    $actionBadge = '';
                }

                $avatarUrl = $campanha->criador->avatar_url ?? 'https://via.placeholder.com/85/343a40/ffffff?text=GM';
                $mestreNome = $campanha->criador->nome ?? 'Mestre Desconhecido';
            @endphp

            <div class="col-sm-6 col-lg-4 d-flex"> {{-- d-flex e h-100 para garantir a mesma altura --}}
                <div class="card campanha-card shadow-lg bg-dark h-100 {{ $cardHighlightClass }}">
                    <div class="card-body d-flex flex-column text-center p-4">

                        {{-- Avatar e Mestre --}}
                        <div class="mb-3">
                            <img src="{{ $avatarUrl }}"
                                 alt="Avatar do mestre: {{ $mestreNome }}"
                                 data-bs-toggle="tooltip" data-bs-placement="top" title="Mestre: {{ $mestreNome }}"
                                 class="rounded-circle border shadow-sm avatar-mestre"
                                 style="width: 85px; height: 85px; object-fit: cover;">
                        </div>

                        {{-- Título e Mestre --}}
                        <h4 class="fw-bolder text-highlight mb-1">{{ $campanha->nome }}</h4>
                        <p class="text-secondary small mb-3">
                            <i class="bi bi-crown-fill me-1"></i> Mestre: **{{ $mestreNome }}**
                        </p>

                        {{-- Descrição curta --}}
                        <p class="text-light small mb-4 flex-grow-1 overflow-hidden text-truncate-3-lines">
                            {{ Str::limit($campanha->descricao ?? 'Sem descrição disponível.', 90, '...') }}
                        </p>

                        {{-- Info básicas (Badges) --}}
                        <div class="d-flex justify-content-center gap-2 flex-wrap mb-4">
                            <span class="badge rounded-pill bg-info text-dark fw-bold"
                                  data-bs-toggle="tooltip" data-bs-placement="bottom" title="Sistema de RPG">
                                <i class="bi bi-dice-5-fill me-1"></i> {{ $campanha->sistema->nome ?? 'Desconhecido' }}
                            </span>
                            <span class="badge rounded-pill bg-{{ $campanha->status === 'ativa' ? 'success' : 'secondary' }} fw-bold"
                                  data-bs-toggle="tooltip" data-bs-placement="bottom" title="Status da Campanha">
                                <i class="bi bi-activity me-1"></i> {{ ucfirst($campanha->status) }}
                            </span>
                            <span class="badge rounded-pill bg-{{ $campanha->privada ? 'danger' : 'primary' }} fw-bold border border-light"
                                  data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $campanha->privada ? 'Campanha Privada' : 'Campanha Pública' }}">
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
                                @elseif($participacao && $participacao->status === 'ativo')
                                    {{-- Participa --}}
                                    <button class="btn btn-success rounded-pill fw-bold" disabled>
                                        <i class="bi bi-check-circle-fill me-1"></i> Você Participa
                                    </button>
                                @elseif($participacao && $participacao->status === 'pendente')
                                    {{-- Pendente --}}
                                    <button class="btn btn-warning text-dark rounded-pill fw-bold" disabled>
                                        <i class="bi bi-hourglass-split me-1"></i> Solicitação Pendente
                                    </button>
                                @elseif(!$participacao)
                                    {{-- Solicitar entrada (se não for mestre e não houver participação) --}}
                                    <form action="{{ route('campanhas.solicitar', $campanha->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary rounded-pill fw-bold w-100">
                                            <i class="bi bi-person-plus-fill me-1"></i> Solicitar Participação
                                        </button>
                                    </form>
                                @endif

                                {{-- Ações Administrativas (Excluir/Editar) - Menor e mais discreto --}}
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
                    <a href="{{ route('campanhas.create') }}" class="btn btn-highlight rounded-pill px-4 mt-3 fw-bold">
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
/* Estilos Dark Mode (Melhorados) */
.bg-dark { background-color: #212529 !important; }
.text-highlight { color: #ffc107 !important; /* Amarelo/Dourado */ }
.btn-highlight {
    background-color: #ffc107 !important;
    color: #212529 !important;
    border-color: #ffc107 !important;
}

/* Bordas de Destaque */
.border-highlight { border: 1px solid #495057; } /* Borda Padrão */
.border-highlight-mestre { border: 3px solid #ffc107; box-shadow: 0 0 15px rgba(255, 193, 7, 0.5); } /* Mestre */
.border-highlight-ativo { border: 3px solid #198754; } /* Jogador Ativo */
.border-highlight-pendente { border: 3px solid #ffc107; border-style: dashed; } /* Pendente - Novo Estilo */

/* Card e Avatar */
.campanha-card {
    transition: all .4s cubic-bezier(0.25, 0.8, 0.25, 1); /* Transição mais suave */
    border-radius: 1rem; /* Borda mais arredondada */
    box-shadow: 0 8px 17px rgba(0,0,0,0.4);
}
.campanha-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.8);
}
.avatar-mestre {
    border: 3px solid #ffc107 !important;
}

/* Utilitário para limitar texto em linhas */
.text-truncate-3-lines {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 3.5em; /* Garante altura mínima para 3 linhas */
}

/* Classe para o input de busca para garantir a ocupação de espaço no d-flex */
.flex-grow-1 {
    flex-grow: 1 !important;
}

/* Correção para o Paginação em Dark Mode */
.pagination .page-item .page-link {
    background-color: #212529; /* Cor de fundo Dark */
    color: #f8f9fa; /* Cor do texto claro */
    border: 1px solid #495057; /* Borda mais suave */
}
.pagination .page-item.active .page-link {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #212529 !important;
}
.pagination .page-item:not(.active):hover .page-link {
    background-color: #343a40;
    border-color: #ffc107;
}
</style>

{{-- Para que os tooltips funcionem, você precisa ter o JS do Bootstrap importado --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection