@extends('layouts.app')

@section('title', 'Portal do Aventureiro')

@section('content')
<div class="container py-4">

    {{-- Usuário logado --}}
    @auth
        {{-- Cabeçalho --}}
        <div class="text-center mb-5">
            <div class="p-4 rounded mb-4" style="background-color: rgba(0,0,0,0.4);">
                <h1 class="fw-bold mb-2 display-5" style="color: var(--btn-bg); text-shadow: 0 0 8px rgba(0,0,0,0.8);">
                    ⚔️ Bem-vindo de volta, {{ Auth::user()->nome }}!
                </h1>
                <p class="lead mb-0" style="color: var(--bs-body-color); text-shadow: 0 0 4px rgba(0,0,0,0.6);">
                    Continue sua jornada e descubra novos horizontes!
                </p>
            </div>
        </div>

        {{-- Menu principal --}}
        <div class="row g-3 justify-content-center mb-5">
            @php
                $menuItems = [
                    ['route' => 'campanhas.minhas', 'icon' => '🏕️', 'label' => 'Minhas Campanhas'],
                    ['route' => 'sistemas.index', 'icon' => '📜', 'label' => 'Ver Sistemas'],
                    ['route' => 'amizades.amigos', 'icon' => '🤝', 'label' => 'Meus Amigos']
                ];
            @endphp

            @foreach($menuItems as $item)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <a href="{{ route($item['route']) }}"
                       class="btn d-flex flex-column align-items-center justify-content-center p-3 shadow-lg h-100
                              {{ request()->routeIs($item['route'] . '.*') ? 'active' : '' }}"
                       style="background-color: var(--btn-bg); color: var(--btn-text);">
                        <span class="fs-2 mb-2">{{ $item['icon'] }}</span>
                        <span class="fw-bold small">{{ $item['label'] }}</span>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Campanhas disponíveis --}}
        <div class="mt-5 pt-4 border-top" style="border-color: var(--btn-bg) !important;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0" style="color: var(--btn-bg); text-shadow: 0 0 6px rgba(0,0,0,0.8);">
                    🔥 Campanhas Disponíveis
                </h3>
                <a href="{{ route('campanhas.todas') }}"
                   class="btn btn-outline-light btn-sm"
                   style="border-color: var(--btn-bg); color: var(--btn-bg);">
                    Ver Todas
                </a>
            </div>

            @if(isset($campanhas) && $campanhas->isNotEmpty())
                <div id="carouselCampanhasPublicas" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($campanhas->chunk(3) as $index => $chunk)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <div class="row g-4 justify-content-center">
                                    @foreach($chunk as $campanha)
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="card h-100 shadow-lg"
                                                 style="background-color: var(--card-bg); border-color: var(--card-border);">
                                                <div class="card-body">
                                                    <h5 class="card-title fw-bold mb-3" style="color: var(--btn-bg);">
                                                        {{ $campanha->nome }}
                                                    </h5>
                                                    <p class="mb-2"><strong>Sistema:</strong>
                                                        <span style="color: var(--btn-bg);">{{ $campanha->sistemaRPG }}</span>
                                                    </p>
                                                    <p class="mb-2"><strong>Mestre:</strong>
                                                        <span style="color: var(--btn-text);">{{ $campanha->criador->nome ?? 'Desconhecido' }}</span>
                                                    </p>
                                                    <p class="mb-3">
                                                        <strong>Status:</strong>
                                                        @if($campanha->status === 'ativa')
                                                            <span class="badge bg-success">Ativa</span>
                                                        @elseif($campanha->status === 'pausada')
                                                            <span class="badge bg-warning text-dark">Pausada</span>
                                                        @else
                                                            <span class="badge bg-secondary">Encerrada</span>
                                                        @endif
                                                    </p>
                                                    <a href="{{ route('campanhas.show', $campanha->id) }}"
                                                       class="btn btn-outline-light btn-sm w-100"
                                                       style="border-color: var(--btn-bg); color: var(--btn-bg);">
                                                        🔍 Ver Detalhes
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
                                data-bs-target="#carouselCampanhasPublicas" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button"
                                data-bs-target="#carouselCampanhasPublicas" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                    @endif
                </div>
            @else
                <div class="text-center py-5">
                    <p class="fst-italic fs-5 mb-3" style="color: var(--bs-body-color);">Nenhuma campanha disponível no momento.</p>
                    <p class="text-secondary">⚔️ Seja o primeiro a criar uma aventura!</p>
                </div>
            @endif
        </div>
    @endauth

</div>
@endsection
