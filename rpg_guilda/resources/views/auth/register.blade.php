@extends('layouts.app')

@section('title', 'Registrar')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <h1 class="fw-bold text-warning">🌌 Portal do Aventureiro</h1>
                <p class="text-light mb-0">
                    Crie sua conta no <strong>RPG Manager</strong> e comece a aventura.
                </p>
                <small class="text-muted">
                    Já tem conta? <a href="{{ route('login') }}" class="text-warning fw-bold">Entrar</a>
                </small>
            </div>

            <div class="card p-4 shadow-lg" style="background-color: var(--card-bg); border: 2px solid var(--card-border);">
                <h3 class="text-center mb-4 text-warning">Registro</h3>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('register.perform') }}">
                    @csrf

                    {{-- Nome --}}
                    <div class="mb-3">
                        <label for="nome" class="form-label text-light">Nome completo</label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome') }}"
                               class="form-control @error('nome') is-invalid @enderror" required>
                        @error('nome')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- E-mail --}}
                    <div class="mb-3">
                        <label for="email" class="form-label text-light">E-mail</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Senha --}}
                    <div class="mb-3">
                        <label for="password" class="form-label text-light">Senha</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Confirmar senha --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label text-light">Confirmar Senha</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-control" required>
                    </div>

                    {{-- Tema --}}
                    <div class="mb-3">
                        <label for="tema" class="form-label text-light">Escolha seu tema</label>
                        <select id="tema" name="tema" class="form-select" onchange="document.documentElement.setAttribute('data-theme', this.value)">
                            @foreach($temas as $tema)
                                <option value="{{ $tema }}" {{ old('tema') == $tema ? 'selected' : '' }}>
                                    {{ ucfirst($tema) }}
                                </option>
                            @endforeach
                        </select>
                        @error('tema')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-warning w-100 mb-3">Registrar</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
