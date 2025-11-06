@extends('layouts.app')

@section('title', 'Editar Perfil – RPG Manager')

@section('content')
<div class="container py-5 text-light">

    {{-- Cabeçalho --}}
    <div class="text-center mb-5">
        <h1 class="text-warning fw-bold display-4">⚙️ Editar Perfil</h1>
        <p class="lead text-secondary">Atualize suas informações de usuário e preferências de tema.</p>
    </div>

    {{-- Card de Edição --}}
    <div class="card bg-dark border-warning shadow-lg mx-auto" style="max-width: 600px;">
        <div class="card-header text-center text-warning">
            <h3>Informações do Usuário</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Nome --}}
                <div class="mb-3">
                    <label for="nome" class="form-label text-warning">Nome</label>
                    <input type="text" name="nome" id="nome" class="form-control bg-secondary text-light border-warning"
                           value="{{ old('nome', $usuario->nome) }}" required>
                    @error('nome') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Email (somente leitura) --}}
                <div class="mb-3">
                    <label for="email" class="form-label text-warning">Email</label>
                    <input type="email" id="email" class="form-control bg-dark text-light border-secondary"
                           value="{{ $usuario->email }}" readonly>
                </div>

                {{-- Tipo de Usuário --}}
                <div class="mb-3">
                    <label class="form-label text-warning">Tipo de Usuário</label>
                    <input type="text" class="form-control bg-dark text-light border-secondary"
                           value="@if($usuario->is_admin ?? false) Administrador
                                  @elseif($usuario->tipo === 'mestre') Mestre
                                  @else Jogador @endif" readonly>
                </div>

                {{-- Tema --}}
                <div class="mb-3">
                    <label for="tema" class="form-label text-warning">Tema</label>
                    <select name="tema" id="tema" class="form-select bg-secondary text-light border-warning" required>
                        <option value="medieval" {{ $usuario->tema === 'medieval' ? 'selected' : '' }}>Medieval</option>
                        <option value="sobrenatural" {{ $usuario->tema === 'sobrenatural' ? 'selected' : '' }}>Sobrenatural</option>
                        <option value="cyberpunk" {{ $usuario->tema === 'cyberpunk' ? 'selected' : '' }}>Cyberpunk</option>
                    </select>
                    @error('tema') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Senha Atual --}}
                <div class="mb-3">
                    <label for="current_password" class="form-label text-warning">Senha Atual</label>
                    <input type="password" name="current_password" id="current_password" class="form-control bg-secondary text-light border-warning" required>
                    @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Nova Senha --}}
                <div class="mb-3">
                    <label for="new_password" class="form-label text-warning">Nova Senha (opcional)</label>
                    <input type="password" name="new_password" id="new_password" class="form-control bg-secondary text-light border-warning">
                    @error('new_password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Confirmar Nova Senha --}}
                <div class="mb-3">
                    <label for="new_password_confirmation" class="form-label text-warning">Confirmar Nova Senha</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control bg-secondary text-light border-warning">
                </div>

                <button type="submit" class="btn btn-warning btn-custom w-100 mt-3">Salvar Alterações</button>
            </form>
        </div>
    </div>

    {{-- Link de Voltar --}}
    <div class="text-center mt-4">
        <a href="{{ route('perfil') }}" class="btn btn-outline-warning">← Voltar ao Perfil</a>
    </div>
</div>
@endsection
