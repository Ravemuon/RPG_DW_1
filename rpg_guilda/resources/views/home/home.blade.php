@extends('layouts.app')

@section('title', 'Portal do Aventureiro')

@section('content')
<div class="container py-5 text-light">

    @auth
        <div class="text-center mb-5 p-4 rounded-4 shadow-lg"
             style="background: linear-gradient(135deg, rgba(0,0,0,0.8), rgba(20,20,20,0.9)); border: 1px solid var(--btn-bg);">

            <h1 class="fw-bold mb-2 display-4 text-warning" style="text-shadow: 0 0 10px rgba(255,193,7,0.5);">
                <i class="fas fa-hand-spock me-2"></i> Bem-vindo de volta, {{ Auth::user()->nome }}!
            </h1>
            <p class="lead mb-0 text-muted">
                Que sua jornada seja longa e cheia de aventuras!
            </p>
        </div>

        <h2 class="fw-bold mb-4 text-center text-primary border-bottom pb-2" style="border-color: rgba(255, 255, 255, 0.1) !important;">
            Ações Rápidas
        </h2>

        <div class="row g-4 justify-content-center mb-5">
            @php
                $menuItems = [
                    ['route' => 'campanhas.minhas', 'icon' => 'fas fa-shield-alt', 'label' => 'Minhas Campanhas', 'color' => 'btn-primary', 'emoji' => '🛡️'],
                    ['route' => 'campanhas.create', 'icon' => 'fas fa-plus-circle', 'label' => 'Criar Campanha', 'color' => 'btn-success', 'emoji' => '✨'],
                    ['route' => 'sistemas.index', 'icon' => 'fas fa-book', 'label' => 'Ver Sistemas', 'color' => 'btn-info', 'emoji' => '📚'],
                    ['route' => 'amizades.amigos', 'icon' => 'fas fa-handshake', 'label' => 'Meus Amigos', 'color' => 'btn-warning', 'emoji' => '🤝']
                ];
            @endphp

            @foreach($menuItems as $item)
                <div class="col-6 col-sm-4 col-md-3 col-lg-3">
                    <a href="{{ route($item['route']) }}"
                       class="card text-center text-decoration-none h-100 shadow-lg {{ $item['color'] }} p-3 link-light border-0 transition-hover"
                       style="--bs-bg-opacity: .9; transform: scale(1); transition: transform 0.2s; background-color: var(--card-bg) !important;">

                        <div class="card-body">
                            <span class="display-5 mb-2 text-{{ explode('-', $item['color'])[1] }} d-block">{{ $item['emoji'] }}</span>
                            <p class="fw-bold mb-0 mt-2 small text-light">{{ $item['label'] }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-5 pt-4 border-top" style="border-color: rgba(255, 255, 255, 0.1) !important;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0 text-highlight">
                    <i class="fas fa-fire me-2"></i> Campanhas Disponíveis
                </h3>
                <a href="{{ route('campanhas.todas') }}" class="btn btn-outline-info rounded-pill btn-sm">
                    Ver Todas <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>

            @if(isset($campanhas) && $campanhas->isNotEmpty())
                <div id="carouselCampanhasPublicas" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner pb-5">
                        @foreach($campanhas->chunk(3) as $index => $chunk)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <div class="row g-4 justify-content-center">
                                    @foreach($chunk as $campanha)
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="card h-100 shadow-lg border-primary border-3"
                                                 style="background-color: #2b3035;">
                                                <div class="card-body d-flex flex-column">

                                                    <h5 class="card-title fw-bolder mb-3 text-warning">
                                                        {{ $campanha->nome }}
                                                    </h5>

                                                    <p class="mb-2 small text-secondary">
                                                        <i class="fas fa-hat-wizard me-1"></i> Mestre: <strong class="text-light">{{ $campanha->criador->nome ?? 'Desconhecido' }}</strong>
                                                    </p>
                                                    <p class="mb-2 small text-secondary">
                                                        <i class="fas fa-dice-d20 me-1"></i> Sistema: <strong class="text-info">{{ $campanha->sistemaRPG }}</strong>
                                                    </p>

                                                    <p class="mb-3 mt-auto">
                                                        <strong class="text-muted">Status:</strong>
                                                        @if($campanha->status === 'ativa')
                                                            <span class="badge bg-success ms-1">Ativa</span>
                                                        @elseif($campanha->status === 'pausada')
                                                            <span class="badge bg-warning text-dark ms-1">Pausada</span>
                                                        @else
                                                            <span class="badge bg-secondary ms-1">Encerrada</span>
                                                        @endif

                                                        @if($campanha->privada)
                                                            <span class="badge bg-danger ms-1"><i class="fas fa-lock me-1"></i> Privada</span>
                                                        @else
                                                            <span class="badge bg-primary ms-1"><i class="fas fa-globe-americas me-1"></i> Pública</span>
                                                        @endif
                                                    </p>

                                                    <a href="{{ route('campanhas.show', $campanha->id) }}"
                                                       class="btn btn-outline-warning mt-2 w-100">
                                                        <i class="fas fa-search me-1"></i> Ver Detalhes
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($campanhas->count() > 3)
                        <button class="carousel-control-prev" type="button"
                                data-bs-target="#carouselCampanhasPublicas" data-bs-slide="prev"
                                style="width: 5%; color: var(--btn-bg);">
                            <span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button"
                                data-bs-target="#carouselCampanhasPublicas" data-bs-slide="next"
                                style="width: 5%; color: var(--btn-bg);">
                            <span class="carousel-control-next-icon bg-dark rounded-circle p-3"></span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                    @endif
                </div>
            @else
                <div class="text-center py-5 bg-dark rounded-3 shadow-sm border border-secondary">
                    <i class="fas fa-dizzy display-4 text-danger mb-3"></i>
                    <p class="fst-italic fs-5 mb-3 text-light">Nenhuma campanha pública disponível no momento.</p>
                    <p class="text-muted">⚔️ Crie a sua própria aventura ou junte-se a um amigo!</p>
                    <a href="{{ route('campanhas.create') }}" class="btn btn-success mt-3 rounded-pill">
                        <i class="fas fa-plus me-2"></i> Criar Nova Campanha
                    </a>
                </div>
            @endif
        </div>
    @endauth

</div>

<style>
    .transition-hover:hover {
        transform: scale(1.05) !important;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4) !important;
    }
    .text-highlight {
        color: var(--btn-bg);
        text-shadow: 0 0 6px rgba(0,0,0,0.5);
    }
</style>
@endsection
