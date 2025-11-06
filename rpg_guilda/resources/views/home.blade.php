@extends('layouts.app')

@section('title', 'Portal do Aventureiro')

@section('content')
<div class="container py-5">

    @if(Auth::check())
        {{-- Usuário logado --}}
        <div class="text-center mb-5">
            <h1 class="fw-bold text-warning mb-3 display-5" style="text-shadow: 0 0 8px var(--btn-bg);">
                ⚔️ Bem-vindo de volta, {{ Auth::user()->nome }}!
            </h1>
            <p class="lead mb-5 text-light" style="text-shadow: 0 0 4px #000;">
                Continue sua jornada e descubra novos horizontes!
            </p>

            {{-- Botões principais --}}
            <div class="row g-3 justify-content-center text-center mb-4">
                @php $botaoClass = 'btn btn-custom w-100 py-3 shadow-lg fw-bold'; @endphp
                <div class="col-6 col-md-2"><a href="{{ route('campanhas.index') }}" class="{{ $botaoClass }}">🏕️ Campanhas</a></div>
                <div class="col-6 col-md-2"><a href="{{ route('personagens.index') }}" class="{{ $botaoClass }}">🧝 Personagens</a></div>
                <div class="col-6 col-md-2"><a href="{{ route('classes.index') }}" class="{{ $botaoClass }}">⚔️ Classes</a></div>
                <div class="col-6 col-md-2"><a href="{{ route('missoes.index') }}" class="{{ $botaoClass }}">🗺️ Missões</a></div>
                <div class="col-6 col-md-2"><a href="{{ route('sistemas.index') }}" class="{{ $botaoClass }}">📜 Sistemas</a></div>
                {{-- Botão Adicionar Amigos --}}
                <div class="col-6 col-md-2"><a href="{{ route('usuarios.index') }}" class="{{ $botaoClass }}">➕ Amigos</a></div>
            </div>

            {{-- Criar campanha --}}
            @if(in_array(Auth::user()->tipo, ['mestre', 'administrador']))
                <div class="mb-5">
                    <a href="{{ route('campanhas.create') }}" class="btn btn-outline-warning btn-lg px-5 py-3 shadow-lg" style="text-shadow: 0 0 6px var(--btn-bg);">
                        ✨ Criar Nova Campanha
                    </a>
                </div>
            @endif
        </div>

        {{-- Campanhas do usuário --}}
        <div class="mb-5">
            <h3 class="fw-bold text-warning mb-3" style="text-shadow: 0 0 6px var(--btn-bg);">🔥 Minhas Campanhas</h3>

            @if(!empty($campanhasUsuario) && $campanhasUsuario->count() > 0)
                <div id="carouselCampanhas" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($campanhasUsuario as $index => $campanha)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <div class="card p-3 shadow-lg bg-dark border-warning mx-auto" style="max-width: 400px;">
                                    <h5 class="fw-bold text-warning">{{ $campanha->nome }}</h5>
                                    <p class="mb-2 text-light"><strong>Sistema:</strong> {{ $campanha->sistemaRPG }}</p>
                                    <p class="text-light">
                                        <strong>Status:</strong>
                                        @if($campanha->status === 'ativa')
                                            <span class="badge bg-success">Ativa</span>
                                        @elseif($campanha->status === 'pausada')
                                            <span class="badge bg-warning text-dark">Pausada</span>
                                        @else
                                            <span class="badge bg-secondary">Encerrada</span>
                                        @endif
                                    </p>
                                    <div class="d-flex justify-content-between mt-3">
                                        <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-warning btn-sm flex-fill me-2">🔍 Ver</a>
                                        <a href="{{ route('campanhas.chat', $campanha->id) }}" class="btn btn-warning btn-sm flex-fill ms-2">💬 Abrir Chat</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselCampanhas" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselCampanhas" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                </div>
            @else
                <p class="text-center text-secondary fst-italic">Você ainda não participa de nenhuma campanha. ⚔️</p>
            @endif
        </div>

    @else
        {{-- Visitante --}}
        <div class="text-center mb-5">
            <h1 class="fw-bold text-warning mb-4 display-5" style="text-shadow: 0 0 8px var(--btn-bg);">
                🌌 Embarque na sua Jornada Épica!
            </h1>
            <p class="lead text-light mb-4 mx-auto" style="max-width: 750px; text-shadow: 0 0 4px #000;">
                O <strong>RPG Manager</strong> é o seu portal digital para aventuras.
                Crie campanhas, gerencie fichas, organize sessões e receba notificações automáticas.
            </p>
            <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-custom btn-lg px-5 py-3 shadow-lg">✨ Crie sua conta</a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-5 py-3 shadow">🔑 Já tenho uma conta</a>
            </div>
        </div>
    @endif

</div>
@endsection
