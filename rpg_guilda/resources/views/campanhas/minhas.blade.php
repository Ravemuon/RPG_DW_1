@extends('layouts.app')

@section('title', 'Minhas Campanhas')

@section('content')

<div class="container py-5 text-light">

    {{-- Cabeçalho e Ações --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
        <h2 class="fw-bold text-highlight m-0 d-flex align-items-center gap-2">
            <i class="fas fa-scroll"></i> Minhas Campanhas
        </h2>

        <div class="d-flex align-items-center gap-3 w-100 w-md-auto">
            {{-- Formulário de Pesquisa --}}
            <form action="{{ route('campanhas.minhas') }}" method="GET" class="d-flex w-100">
                <input type="text" name="search" class="form-control form-control-dark border-secondary bg-dark text-light"
                       placeholder="Pesquisar por nome ou sistema..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-warning ms-2" title="Pesquisar">
                    <i class="fas fa-search"></i>
                </button>
                @if (request('search'))
                    <a href="{{ route('campanhas.minhas') }}" class="btn btn-outline-secondary ms-1" title="Limpar Pesquisa">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>

            <a href="{{ route('campanhas.create') }}" class="btn btn-warning fw-bold text-dark shadow-sm flex-shrink-0">
                <i class="fas fa-plus"></i> Nova Campanha
            </a>
        </div>
    </div>


    {{-- Lista de campanhas --}}
    @if($minhasCampanhas->count())

        <div class="row g-4">
            @foreach($minhasCampanhas as $campanha)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="card bg-dark border-secondary shadow-lg h-100 transition-all hover:border-warning">
                        <div class="card-body d-flex flex-column">

                            {{-- Título e Status --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="fw-bold text-warning mb-0 fs-5">
                                    {{ $campanha->nome }}
                                </h5>
                                <span class="badge rounded-pill px-3 py-2
                                    bg-{{ $campanha->status === 'ativa' ? 'success' : ($campanha->status === 'inativa' ? 'secondary' : 'warning text-dark') }}">
                                    {{ ucfirst($campanha->status) }}
                                </span>
                            </div>

                            {{-- Tipo (Mestre/Jogador) e Privacidade --}}
                            @php
                                $isMestre = auth()->id() === $campanha->criador_id;
                                $roleBadge = $isMestre
                                    ? '<span class="badge bg-primary me-2"><i class="fas fa-crown"></i> Mestre</span>'
                                    : '<span class="badge bg-info me-2"><i class="fas fa-hat-wizard"></i> Jogador</span>';
                                $privacyIcon = $campanha->privada
                                    ? '<i class="fas fa-lock text-danger" title="Campanha Privada"></i>'
                                    : '<i class="fas fa-globe-americas text-success" title="Campanha Pública"></i>';
                            @endphp
                            <div class="mb-3 d-flex align-items-center">
                                {!! $roleBadge !!}
                                {!! $privacyIcon !!}
                            </div>

                            {{-- Informações --}}
                            <p class="text-secondary small mb-1">
                                Sistema: <strong class="text-light">{{ $campanha->sistema->nome ?? 'N/A' }}</strong>
                            </p>
                            <p class="text-secondary small mb-3 text-truncate-3-lines" style="max-height: 4.5em; overflow: hidden;">
                                Descrição: {{ $campanha->descricao ?: 'Sem descrição disponível.' }}
                            </p>


                            {{-- Ações --}}
                            <div class="mt-auto d-flex flex-column gap-2">
                                <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-warning fw-bold">
                                    <i class="fas fa-eye"></i> Ver Detalhes
                                </a>

                                {{-- Acesso à área do Mestre (Apenas se for o criador) --}}
                                @if($isMestre)
                                <a href="{{ route('campanhas.mestre', $campanha->id) }}" class="btn btn-primary fw-bold">
                                    <i class="fas fa-gavel"></i> Área do Mestre
                                </a>
                                @endif

                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        {{-- Nenhuma campanha encontrada --}}
        <div class="text-center text-light mt-5 p-5 bg-dark rounded-3 shadow-lg">
            <i class="fas fa-frown fa-3x text-secondary mb-3"></i>
            @if(request('search'))
                <p class="fs-4 mb-2">Nenhuma campanha encontrada com o termo "{{ request('search') }}".</p>
                <a href="{{ route('campanhas.minhas') }}" class="btn btn-outline-warning">
                    <i class="fas fa-times"></i> Limpar Pesquisa
                </a>
            @else
                <p class="fs-4 mb-2">Você ainda não participa de nenhuma campanha.</p>
                <p class="text-muted">Comece uma nova ou explore campanhas públicas para se juntar à aventura!</p>
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="{{ route('campanhas.create') }}" class="btn btn-warning fw-bold text-dark">
                        <i class="fas fa-plus"></i> Criar Campanha
                    </a>
                    <a href="{{ route('campanhas.todas') }}" class="btn btn-outline-warning">
                        <i class="fas fa-map-marked-alt"></i> Explorar Campanhas Públicas
                    </a>
                </div>
            @endif
        </div>
    @endif

</div>

<style>
.text-highlight {
    color: var(--btn-bg, #ffc107);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5) !important;
}

/* Estilo para escurecer o controle de formulário */
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
