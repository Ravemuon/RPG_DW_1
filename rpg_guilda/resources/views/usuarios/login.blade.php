@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            {{-- Seção temática RPG --}}
            <div class="text-center mb-4">
                <h1 class="fw-bold text-warning">🌌 Portal do Aventureiro</h1>
                <p class="text-light mb-0">Entre no reino do <strong>RPG Manager</strong> e viva aventuras épicas.</p>
                <small class="text-muted">Não tem conta? Crie a sua e comece a jornada!</small>
            </div>

            <div class="card p-4 shadow bg-dark border-warning">

                <h3 class="text-center mb-4 text-warning">Login</h3>

                {{-- Mensagens de sessão --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                {{-- Formulário de login --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label text-light">E-mail</label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Senha --}}
                    <div class="mb-3">
                        <label for="password" class="form-label text-light">Senha</label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password"
                               required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Botão --}}
                    <button type="submit" class="btn btn-warning w-100 mb-3">Entrar</button>
                </form>

                {{-- Link para registro --}}
                <div class="text-center">
                    <span class="text-light">Novo aventureiro? </span>
                    <a href="{{ route('register') }}" class="text-warning fw-bold">Criar conta</a>
                </div>

            </div>

            {{-- Extras temáticos --}}
            <div class="mt-4 text-center text-light">
                <p>Descubra um mundo de campanhas, missões e personagens únicos!</p>
                <div class="row justify-content-center">
                    <div class="col-md-4 p-2">
                        <div class="p-3 border border-warning rounded-4 shadow bg-secondary">
                            <h6>🧙 Mestres</h6>
                            <p class="mb-0">Gerencie suas campanhas e aventureiros.</p>
                        </div>
                    </div>
                    <div class="col-md-4 p-2">
                        <div class="p-3 border border-warning rounded-4 shadow bg-secondary">
                            <h6>🎮 Jogadores</h6>
                            <p class="mb-0">Crie fichas e participe de missões em tempo real.</p>
                        </div>
                    </div>
                    <div class="col-md-4 p-2">
                        <div class="p-3 border border-warning rounded-4 shadow bg-secondary">
                            <h6>👑 Administradores</h6>
                            <p class="mb-0">Mantenha o equilíbrio do reino e a magia fluindo.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
