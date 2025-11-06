@extends('layouts.app')

@section('title', 'Portal do Aventureiro')

@section('content')
@if(Auth::check())
    {{-- Usuário autenticado --}}
    <div class="text-center my-5">
        <h1 class="fw-bold text-warning mb-3">⚔️ Bem-vindo de volta, {{ Auth::user()->nome }}!</h1>
        <p class="lead mb-5 text-light">
            As tavernas murmuram seu nome, aventureiro. Continue sua jornada e crie novas lendas!
        </p>

        <div class="row justify-content-center text-center">
            <div class="col-md-3 mb-3">
                <a href="{{ route('campanhas.index') }}" class="btn btn-custom w-100 py-3 shadow">
                    🏕️ Minhas Campanhas
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="{{ route('personagens.index') }}" class="btn btn-custom w-100 py-3 shadow">
                    🧝 Meus Personagens
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="{{ route('classes.index') }}" class="btn btn-custom w-100 py-3 shadow">
                    ⚔️ Classes & Habilidades
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="{{ route('missoes.index') }}" class="btn btn-custom w-100 py-3 shadow">
                    🗺️ Missões & Aventuras
                </a>
            </div>
        </div>

        <div class="mt-4">
            {{-- Mostrar botão de criar campanha apenas para Mestre ou Administrador --}}
            @if(Auth::user()->tipo === 'mestre' || Auth::user()->tipo === 'administrador')
                <a href="{{ route('campanhas.create') }}" class="btn btn-outline-warning btn-lg px-5 py-3 shadow">
                    ✨ Criar Nova Campanha
                </a>
            @endif
        </div>
    </div>
@else
    {{-- Visitante não logado --}}
    <div class="text-center my-5">
        <h1 class="fw-bold text-warning mb-4">🌌 Embarque na sua Jornada Épica!</h1>
        <p class="lead text-light mb-4" style="max-width: 700px; margin: 0 auto;">
            O <strong>RPG Manager</strong> é o seu grimório digital para aventuras.
            Crie campanhas lendárias, forje personagens únicos e conquiste mundos — tudo em um só lugar.
        </p>

        <div class="mt-5">
            <a href="{{ route('register') }}" class="btn btn-custom btn-lg mx-2 px-5 py-3 shadow-lg">
                ✨ Crie sua conta agora
            </a>
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg mx-2 px-5 py-3 shadow">
                🔑 Já tenho uma conta
            </a>
        </div>
    </div>

    {{-- Sessão de apresentação da proposta --}}
    <div class="mt-5 text-center text-light">
        <hr class="border-warning my-4" style="opacity: 0.3;">
        <h2 class="fw-bold mb-3 text-warning">🎲 O que é o RPG Manager?</h2>
        <p class="mx-auto mb-4" style="max-width: 800px;">
            O <strong>RPG Manager</strong> é um sistema feito para unir mestres e jogadores em um só reino digital.
            Crie campanhas, gerencie fichas, organize sessões e receba notificações automáticas.
        </p>
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="p-3 border border-warning rounded-4 bg-dark shadow">
                    <h5>🧙 Mestres</h5>
                    <p>Crie e gerencie suas campanhas, defina regras e acompanhe suas sessões.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border border-warning rounded-4 bg-dark shadow">
                    <h5>🎮 Jogadores</h5>
                    <p>Monte fichas, participe de missões e interaja com o grupo em tempo real.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border border-warning rounded-4 bg-dark shadow">
                    <h5>👑 Administradores</h5>
                    <p>Moderam e mantêm o equilíbrio das terras — garantindo que a magia continue fluindo.</p>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
