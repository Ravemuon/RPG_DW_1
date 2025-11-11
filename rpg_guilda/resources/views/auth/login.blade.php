@extends('layouts.app')

@section('title', 'Portal do Aventureiro')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="text-center mb-5">
                <div class="p-4 rounded shadow-lg" style="background-color: rgba(0,0,0,0.4);">
                    <h1 class="fw-bold mb-3 display-5" style="color: var(--btn-bg); text-shadow: 0 0 10px rgba(0,0,0,0.8);">
                        🌌 Portal do Aventureiro
                    </h1>
                    <p class="lead mb-4 mx-auto" style="max-width: 700px; color: var(--bs-body-color); text-shadow: 0 0 4px rgba(0,0,0,0.6);">
                        Acesse o <strong style="color: var(--btn-bg);">RPG Manager</strong> e embarque em jornadas épicas.
                        Crie campanhas, gerencie fichas e explore mundos infinitos!
                    </p>
                </div>
            </div>

            {{-- Card de Login --}}
            <div class="card p-4 shadow-lg border-0" style="background-color: var(--card-bg); border: 2px solid var(--card-border);">
                <h3 class="text-center mb-4 fw-bold" style="color: var(--btn-bg);">🔑 Entrar</h3>

                {{-- Mensagens de sessão --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @error('email')
                    <div class="alert alert-danger" role="alert">
                        As credenciais fornecidas não correspondem aos nossos registros.
                    </div>
                @enderror

                {{-- Formulário --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- E-mail --}}
                    <div class="mb-3">
                        <label for="email" class="form-label text-light">E-mail</label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required
                               autofocus>
                    </div>

                    {{-- Senha --}}
                    <div class="mb-4">
                        <label for="password" class="form-label text-light">Senha</label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Botão --}}
                    <button type="submit" class="btn w-100 fw-bold py-2" style="background-color: var(--btn-bg); color: var(--btn-text);">
                        Entrar
                    </button>

                    {{-- Link para Registro --}}
                    <p class="text-center mt-3 mb-0">
                        <a href="{{ route('register') }}" class="text-warning fw-bold">✨ Criar uma conta</a>
                    </p>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
