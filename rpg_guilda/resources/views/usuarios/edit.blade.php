@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center fw-bold text-warning">⚙️ Editar Perfil</h2>

    <div class="card p-4 mx-auto shadow-lg" style="max-width: 500px;">
        <form action="{{ route('usuarios.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nome de Usuário</label>
                <input type="text" name="name" id="name" class="form-control bg-dark text-light border-warning"
                       value="{{ old('name', $usuario->name) }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="current_password" class="form-label">Senha Atual</label>
                <input type="password" name="current_password" id="current_password" class="form-control bg-dark text-light border-warning" required>
                @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="new_password" class="form-label">Nova Senha (opcional)</label>
                <input type="password" name="new_password" id="new_password" class="form-control bg-dark text-light border-warning">
                @error('new_password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="new_password_confirmation" class="form-label">Confirmar Nova Senha</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control bg-dark text-light border-warning">
            </div>

            <button type="submit" class="btn btn-custom w-100">Salvar Alterações</button>
        </form>
    </div>
</div>
@endsection
