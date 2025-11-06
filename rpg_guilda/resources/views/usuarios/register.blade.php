@extends('layouts.app')

@section('title', 'Registrar')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4 shadow">
                <h3 class="text-center mb-4">Registrar</h3>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" name="nome" value="{{ old('nome') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <label for="tema" class="form-label">Tema</label>
                        <select class="form-select" name="tema">
                            <option value="medieval">Medieval</option>
                            <option value="sobrenatural">Sobrenatural</option>
                            <option value="cyberpunk">Cyberpunk</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Registrar</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="{{ route('login') }}">Já tenho uma conta</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
