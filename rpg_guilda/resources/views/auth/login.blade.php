@extends('layouts.app')

@section('title', 'Entrar')

@section('content')

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-md-6">

        {{-- Seção temática RPG --}}
        <div class="text-center mb-4">
            <h1 class="fw-bold text-warning">🌌 Portal do Aventureiro</h1>
            <p class="text-light mb-0">
                Acesse sua conta no <strong>RPG Manager</strong> para continuar a jornada.
            </p>
            {{-- O link agora aponta para o registro --}}
            <small class="text-muted"><a href="{{ route('register') }}" class="text-warning fw-bold">Não tem conta? Criar Conta</a></small>
        </div>

        <div class="card p-4 shadow-lg" style="background-color: var(--card-bg); border: 2px solid var(--card-border);">

            <h3 class="text-center mb-4 text-warning">Login</h3>

            {{-- Mensagens de sessão (incluindo erro de credenciais) --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            {{-- O controller de Login retorna erros via $errors, não session('error') --}}
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
                    {{-- Nota: O erro de 'email' é usado pelo controller para indicar falha na credencial. --}}
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

                {{-- Lembrar-me --}}
                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label class="form-check-label text-light" for="remember">Lembrar-me</label>
                </div>

                {{-- Botão --}}
                <button type="submit" class="btn btn-warning w-100">Entrar</button>

                {{-- Opcional: Link para Esqueci Senha (Não definido nas rotas, mas comum) --}}
                {{-- <p class="text-center mt-3 mb-0"><a href="#" class="text-muted small">Esqueci minha senha</a></p> --}}
            </form>

        </div>

    </div>
</div>


</div>
@endsection
