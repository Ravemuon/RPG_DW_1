@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="container py-5">

    <h2 class="fw-bold text-warning mb-4">✏️ Editar Perfil</h2>

    {{-- Formulário de edição --}}
    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Nome -->
        <div class="mb-3">
            <label for="nome" class="form-label text-light">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $usuario->nome) }}" required>
        </div>

        <!-- E-mail -->
        <div class="mb-3">
            <label for="email" class="form-label text-light">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
        </div>

        <!-- Biografia -->
        <div class="mb-3">
            <label for="biografia" class="form-label text-light">Biografia</label>
            <textarea class="form-control" id="biografia" name="biografia" rows="4" placeholder="Escreva algo sobre você">{{ old('biografia', $usuario->biografia) }}</textarea>
        </div>

        <!-- Tema -->
        <div class="mb-3">
            <label for="tema" class="form-label text-light">Tema</label>
            <select class="form-select" id="tema" name="tema" required>
                <option value="medieval" {{ $usuario->tema === 'medieval' ? 'selected' : '' }}>Medieval</option>
                <option value="sobrenatural" {{ $usuario->tema === 'sobrenatural' ? 'selected' : '' }}>Sobrenatural</option>
                <option value="cyberpunk" {{ $usuario->tema === 'cyberpunk' ? 'selected' : '' }}>Cyberpunk</option>
            </select>
        </div>

        <!-- Senha Atual -->
        <div class="mb-3">
            <label for="current_password" class="form-label text-light">Senha Atual <small class="text-danger">*</small></label>
            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Digite sua senha atual" required>
        </div>

        <!-- Nova Senha -->
        <div class="mb-3">
            <label for="new_password" class="form-label text-light">Nova Senha</label>
            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Digite uma nova senha (opcional)">
        </div>

        <!-- Confirmar Nova Senha -->
        <div class="mb-3">
            <label for="new_password_confirmation" class="form-label text-light">Confirmar Nova Senha</label>
            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Repita a nova senha">
        </div>

        <button type="submit" class="btn btn-warning mt-3">Salvar Alterações</button>
        <a href="{{ route('usuarios.perfil') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>

    {{-- Upload de Avatar e Banner --}}
    <div class="mt-5">
        <h4 class="text-warning mb-3">📸 Alterar Avatar e Banner</h4>
        <div class="row g-4">
            <div class="col-md-6 text-center">
                {{-- Avatar --}}
                <div class="position-relative d-inline-block">
                    <img src="{{ $usuario->avatar_url }}" alt="Avatar de {{ $usuario->nome }}"
                        class="rounded-circle border border-warning shadow"
                        style="width: 150px; height: 150px; object-fit: cover;">

                    <label for="avatarUpload" class="position-absolute bottom-0 end-0 bg-light rounded-circle p-2 shadow" style="cursor:pointer;">
                        <i class="bi bi-camera-fill text-dark"></i>
                    </label>

                    <form action="{{ route('usuarios.uploadImagem', ['usuario' => $usuario->id, 'tipo' => 'avatar']) }}"
                        method="POST" enctype="multipart/form-data" class="d-none">
                        @csrf
                        <input type="file" name="arquivo" id="avatarUpload" accept="image/*" onchange="this.form.submit()">
                    </form>
                </div>
            </div>

            <div class="col-md-6 text-center">
                {{-- Banner --}}
                <div class="position-relative rounded overflow-hidden" style="height: 200px;">
                    <div class="w-100 h-100" style="
                        background-image: url('{{ $usuario->banner_url }}');
                        background-size: cover;
                        background-position: center;
                    "></div>

                    <label for="bannerUpload" class="position-absolute top-0 end-0 m-3 btn btn-outline-light btn-sm shadow" style="cursor:pointer;">
                        <i class="bi bi-camera-fill"></i>
                    </label>

                    <form action="{{ route('usuarios.uploadImagem', ['usuario' => $usuario->id, 'tipo' => 'banner']) }}"
                        method="POST" enctype="multipart/form-data" class="d-none">
                        @csrf
                        <input type="file" name="arquivo" id="bannerUpload" accept="image/*" onchange="this.form.submit()">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
