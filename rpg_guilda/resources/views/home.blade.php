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
            <div class="row g-3 justify-content-center text-center">
                @php
                    $botaoClass = 'btn btn-custom w-100 py-3 shadow-lg fw-bold';
                @endphp
                <div class="col-6 col-md-2">
                    <a href="{{ route('campanhas.index') }}" class="{{ $botaoClass }}">🏕️ Campanhas</a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="{{ route('personagens.index') }}" class="{{ $botaoClass }}">🧝 Personagens</a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="{{ route('classes.index') }}" class="{{ $botaoClass }}">⚔️ Classes</a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="{{ route('missoes.index') }}" class="{{ $botaoClass }}">🗺️ Missões</a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="{{ route('sistemas.index') }}" class="{{ $botaoClass }}">📜 Sistemas</a>
                </div>
            </div>

            {{-- Criar campanha apenas para mestre/administrador --}}
            @if(in_array(Auth::user()->tipo, ['mestre', 'administrador']))
                <div class="mt-4">
                    <a href="{{ route('campanhas.create') }}" class="btn btn-outline-warning btn-lg px-5 py-3 shadow-lg"
                       style="text-shadow: 0 0 6px var(--btn-bg);">
                        ✨ Criar Nova Campanha
                    </a>
                </div>
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

            {{-- Botões registro/login --}}
            <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-custom btn-lg px-5 py-3 shadow-lg">
                    ✨ Crie sua conta
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-5 py-3 shadow">
                    🔑 Já tenho uma conta
                </a>
            </div>

            {{-- Sobre o site --}}
            <div class="mt-5 text-center text-light">
                <hr class="border-warning my-4 opacity-25">
                <h2 class="fw-bold mb-3 text-warning" style="text-shadow: 0 0 6px var(--btn-bg);">
                    🎲 O que é o RPG Manager?
                </h2>
                <p class="mx-auto mb-5" style="max-width: 850px; text-shadow: 0 0 2px #000;">
                    Plataforma completa para mestres, jogadores e administradores, reunindo todas as ferramentas de RPG em um único portal.
                </p>

                <div class="row g-4 justify-content-center">
                    @foreach(['Mestres'=>'🧙 Gerencie campanhas e sessões.',
                              'Jogadores'=>'🎮 Monte fichas e participe de missões.',
                              'Administradores'=>'👑 Moderam o sistema e mantêm a magia.'] as $role => $desc)
                        <div class="col-12 col-md-3">
                            <div class="p-4 border border-warning rounded-4 bg-dark shadow-lg h-100"
                                 style="text-shadow: 0 0 4px #000;">
                                <h5>{{ $role }}</h5>
                                <p>{{ $desc }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Recursos adicionais --}}
                <div class="mt-5">
                    <h3 class="fw-bold text-warning mb-3" style="text-shadow: 0 0 6px var(--btn-bg);">✨ Recursos</h3>
                    <div class="row g-4 justify-content-center">
                        @foreach([
                            'Sistemas Personalizados'=>'📜 Escolha ou crie sistemas de regras.',
                            'Notificações'=>'🔔 Receba alertas em tempo real.',
                            'Upload de Arquivos'=>'📂 Adicione mapas e fichas facilmente.'
                        ] as $title => $desc)
                        <div class="col-12 col-md-3">
                            <div class="p-4 border border-warning rounded-4 bg-dark shadow-lg h-100"
                                 style="text-shadow: 0 0 3px #000;">
                                <h5>{{ $title }}</h5>
                                <p>{{ $desc }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- CTA final --}}
                <div class="mt-5 text-center">
                    <a href="{{ route('register') }}" class="btn btn-custom btn-lg px-5 py-3 shadow-lg" style="text-shadow: 0 0 6px var(--btn-bg);">
                        Comece sua aventura agora ✨
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
