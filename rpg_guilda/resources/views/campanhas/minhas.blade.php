@extends('layouts.app')

@section('title', 'Minhas Campanhas')

@section('content')

<div class="container py-4 py-md-5 text-light">

    {{-- Cabeçalho Principal --}}
    <div class="mb-5 border-bottom border-secondary pb-3">
        <h1 class="fw-bolder display-5 text-highlight m-0 d-flex align-items-center gap-3">
            <i class="fas fa-scroll"></i> Minhas Aventuras
        </h1>
        <p class="text-secondary mt-2">Campanhas que você mestra ou das quais participa.</p>
    </div>

    {{-- Barra de busca e criar campanha (Otimizada) --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch mb-5 gap-3">

        {{-- Formulário de Pesquisa --}}
        <form action="{{ route('campanhas.minhas') }}" method="GET" class="grow d-flex gap-2">
            <div class="input-group input-group-lg shadow-sm">
                <input type="text" name="search"
                       class="form-control form-control-dark border-secondary bg-dark text-light"
                       placeholder="🔍 Pesquisar por nome ou sistema..."
                       value="{{ request('search') }}">

                <button type="submit" class="btn btn-warning text-dark" title="Pesquisar">
                    <i class="fas fa-search"></i>
                </button>

                @if (request('search'))
                    <a href="{{ route('campanhas.minhas') }}" class="btn btn-outline-secondary" title="Limpar Pesquisa">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Botão Nova Campanha --}}
        <a href="{{ route('campanhas.create') }}" class="btn btn-success fw-bold text-light shadow-sm btn-lg shrink-0 d-flex align-items-center justify-content-center">
            <i class="fas fa-plus me-2"></i> Criar Nova Campanha
        </a>
    </div>

    <hr class="border-secondary my-5">

    {{-- Lista de campanhas --}}
    @if($minhasCampanhas->count())
        <div class="row g-4">
            @foreach($minhasCampanhas as $campanha)
                @php
                    $isMestre = auth()->id() === $campanha->criador_id;
                    $roleBadge = $isMestre
                        ? '<span class="badge bg-primary fw-bold"><i class="fas fa-crown"></i> Mestre</span>'
                        : '<span class="badge bg-info text-dark fw-bold"><i class="fas fa-hat-wizard"></i> Jogador</span>';

                    $statusClass = [
                        'ativa' => 'bg-success',
                        'inativa' => 'bg-secondary',
                        'finalizada' => 'bg-danger'
                    ][$campanha->status] ?? 'bg-warning text-dark';

                    $cardBorderClass = $isMestre ? 'border-primary' : 'border-info';
                @endphp

                <div class="col-sm-12 col-md-6 col-lg-4">
                    {{-- Card com borda colorida e sombra no hover --}}
                    <div class="card bg-dark shadow-lg h-100 campanha-card {{ $cardBorderClass }}">
                        <div class="card-body d-flex flex-column p-4">

                            {{-- Título, Status e Papel --}}
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="fw-bolder text-highlight mb-0 fs-5 me-3">
                                    {{ $campanha->nome }}
                                </h5>

                                <div class="text-nowrap ms-auto">
                                    <span class="badge rounded-pill {{ $statusClass }} py-1 me-1">
                                        {{ ucfirst($campanha->status) }}
                                    </span>
                                    {!! $roleBadge !!}
                                </div>
                            </div>

                            {{-- Informações e Descrição --}}
                            <p class="text-light fw-semibold mb-2">
                                <i class="fas fa-dice-d20 text-secondary me-1"></i> Sistema:
                                <span class="text-light">{{ $campanha->sistema->nome ?? 'N/A' }}</span>
                            </p>

                            <p class="text-secondary small mb-3 grow" style="max-height: 4.5em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                **Descrição:** {{ $campanha->descricao ?: 'Sem descrição disponível.' }}
                            </p>

                            {{-- Métricas e Privacidade --}}
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-secondary">
                                <div class="d-flex gap-3 small text-secondary">
                                    <span>
                                        <i class="fas fa-users"></i> {{ $campanha->jogadores->count() }} Jog.
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar-check"></i> {{ $campanha->sessoes->count() }} Sess.
                                    </span>
                                </div>

                                {{-- Ícone de Privacidade --}}
                                {!! $campanha->privada
                                    ? '<i class="fas fa-lock text-danger" title="Campanha Privada"></i>'
                                    : '<i class="fas fa-globe-americas text-success" title="Campanha Pública"></i>' !!}
                            </div>

                            <hr class="border-secondary my-3">

                            {{-- Ações --}}
                            <div class="d-grid gap-2">
                                <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-warning fw-bold">
                                    <i class="fas fa-eye me-1"></i> Ver Campanha
                                </a>

                                {{-- Acesso à área do Mestre (Apenas se for o criador) --}}
                                @if($isMestre)
                                <a href="{{ route('campanhas.mestre', $campanha->id) }}" class="btn btn-primary fw-bold">
                                    <i class="fas fa-gavel me-1"></i> Área do Mestre
                                </a>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        {{-- Nenhuma campanha encontrada (Melhoria) --}}
        <div class="text-center text-light mt-5 p-5 bg-dark rounded-4 shadow-lg border border-secondary">
            <i class="fas fa-map-signs fa-4x text-highlight mb-4"></i>
            @if(request('search'))
                <h3 class="fs-4 mb-3">Nenhuma aventura encontrada com o termo "{{ request('search') }}".</h3>
                <p class="text-muted">Tente um termo diferente ou limpe sua busca.</p>
                <a href="{{ route('campanhas.minhas') }}" class="btn btn-outline-warning rounded-pill px-4 mt-3">
                    <i class="fas fa-times me-2"></i> Limpar Pesquisa
                </a>
            @else
                <h3 class="fs-4 mb-3">Você ainda não participa de nenhuma campanha.</h3>
                <p class="text-muted">Comece a sua própria jornada ou junte-se a uma aventura!</p>
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="{{ route('campanhas.create') }}" class="btn btn-success fw-bold text-light rounded-pill px-4">
                        <i class="fas fa-plus me-2"></i> Criar Campanha
                    </a>
                    <a href="{{ route('campanhas.todas') }}" class="btn btn-outline-highlight rounded-pill px-4">
                        <i class="fas fa-globe-americas me-2"></i> Explorar Campanhas Públicas
                    </a>
                </div>
            @endif
        </div>
    @endif

</div>

<style>
/* Definição da cor de destaque e estilos Dark Mode */
.text-highlight { color: #ffc107 !important; }
.btn-outline-highlight {
    color: #ffc107;
    border-color: #ffc107;
}
.btn-outline-highlight:hover {
    background-color: #ffc107;
    color: #212529;
}

/* Card Styles */
.campanha-card {
    transition: all 0.3s ease;
    border-radius: 0.75rem;
    border-width: 3px !important; /* Destaque na borda */
}

.campanha-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.7) !important;
}

/* Formulário Dark */
.form-control-dark {
    background-color: #343a40;
    border-color: #495057;
    color: #f8f9fa;
}
.form-control-dark::placeholder {
    color: #adb5bd;
}
.form-control-dark:focus {
    background-color: #343a40;
    border-color: #ffc107;
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
    color: #f8f9fa;
}
</style>
@endsection
