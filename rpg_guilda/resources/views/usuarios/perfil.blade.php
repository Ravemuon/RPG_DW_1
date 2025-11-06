@extends('layouts.app')

@section('title', 'Livro do Aventureiro – Perfil')

@section('content')
<div class="container py-5">
    {{-- Banner --}}
    <div class="position-relative mb-5" style="height: 350px; border-radius: .25rem; overflow: hidden;">
        <div class="w-100 h-100" style="
            background-image: url('{{ $usuario->banner?->caminho ? Storage::url($usuario->banner->caminho) : asset('images/default-banner.jpg') }}');
            background-size: cover;
            background-position: center;
        "></div>

        {{-- Botão de upload do banner --}}
        <label for="bannerUpload" class="position-absolute top-0 end-0 m-3 btn btn-outline-light btn-sm" style="cursor:pointer; z-index: 10;">
            <i class="bi bi-camera-fill"></i>
        </label>
        <form action="{{ route('usuarios.uploadBanner', $usuario->id) }}" method="POST" enctype="multipart/form-data" class="d-none">
            @csrf
            <input type="file" name="banner_arquivo" id="bannerUpload" accept="image/*" onchange="this.form.submit()">
        </form>
    </div>

    {{-- Avatar, nome e ID --}}
    <div class="text-center mb-5" style="margin-top: -75px; z-index: 10; position: relative;">
        {{-- Avatar --}}
        <div class="position-relative d-inline-block">
            <img src="{{ $usuario->perfil?->caminho ? Storage::url($usuario->perfil->caminho) : asset('images/default-avatar.png') }}"
                 alt="Avatar de {{ $usuario->nome }}"
                 class="rounded-circle border border-{{ $usuario->tema == 'cyberpunk' ? 'info' : ($usuario->tema == 'sobrenatural' ? 'purple' : 'warning') }} shadow-lg"
                 style="width: 150px; height: 150px; object-fit: cover; cursor:pointer;">

            {{-- Upload avatar --}}
            <label for="perfilUpload" class="position-absolute bottom-0 end-0 bg-light rounded-circle p-2 shadow" style="cursor:pointer;">
                <i class="bi bi-camera-fill text-dark"></i>
            </label>
            <form action="{{ route('usuarios.uploadPerfil', $usuario->id) }}" method="POST" enctype="multipart/form-data" class="d-none">
                @csrf
                <input type="file" name="perfil_arquivo" id="perfilUpload" accept="image/*" onchange="this.form.submit()">
            </form>
        </div>

        {{-- Nome e ID --}}
        <div class="mt-3 text-light">
            <h3 class="text-{{ $usuario->tema == 'cyberpunk' ? 'info' : ($usuario->tema == 'sobrenatural' ? 'purple' : 'warning') }}">
                {{ $usuario->nome }}
            </h3>
            <p class="mb-0">ID: {{ $usuario->id }}</p>
            <p class="mb-0">
                @if($usuario->is_admin ?? false)
                    👑 Administrador
                @elseif($usuario->tipo === 'mestre')
                    🧙 Mestre
                @else
                    🎮 Jogador
                @endif
            </p>
        </div>
    </div>

    {{-- Cartão principal --}}
    <div class="card bg-dark border-{{ $usuario->tema == 'cyberpunk' ? 'info' : ($usuario->tema == 'sobrenatural' ? 'purple' : 'warning') }} shadow-lg mb-5 pt-5">
        <div class="card-body text-center mt-5">
            {{-- Biografia --}}
            <p class="text-light fst-italic px-3">
                {{ $usuario->biografia ?? 'Este aventureiro ainda não escreveu sua biografia.' }}
            </p>

            {{-- Estatísticas --}}
            <div class="row justify-content-center text-light mt-3">
                <div class="col-md-4 mb-2">
                    <div class="bg-secondary bg-opacity-25 rounded p-2">
                        <strong>Personagens:</strong> {{ $personagemCount }}
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="bg-secondary bg-opacity-25 rounded p-2">
                        <strong>Campanhas Ativas:</strong> {{ $campanhas->count() }}
                    </div>
                </div>
            </div>

            {{-- Botão Editar --}}
            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-{{ $usuario->tema == 'cyberpunk' ? 'info' : ($usuario->tema == 'sobrenatural' ? 'purple' : 'warning') }} mt-3 px-4 py-2 shadow-lg">
                ✏️ Editar Perfil
            </a>
        </div>
    </div>

    {{-- Lista de campanhas --}}
    <div class="card bg-dark border-{{ $usuario->tema == 'cyberpunk' ? 'info' : ($usuario->tema == 'sobrenatural' ? 'purple' : 'warning') }} shadow-lg mb-5">
        <div class="card-header text-center text-{{ $usuario->tema == 'cyberpunk' ? 'info' : ($usuario->tema == 'sobrenatural' ? 'purple' : 'warning') }}">
            <h3>Campanhas Ativas</h3>
        </div>
        <div class="card-body">
            @if($campanhas->isEmpty())
                <p class="text-center text-secondary">Você ainda não participa de nenhuma campanha. ⚔️</p>
            @else
                <div class="table-responsive">
                    <table class="table table-dark table-bordered table-striped align-middle mb-0">
                        <thead class="text-{{ $usuario->tema == 'cyberpunk' ? 'info' : ($usuario->tema == 'sobrenatural' ? 'purple' : 'warning') }}">
                            <tr>
                                <th>Nome</th>
                                <th>Sistema</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campanhas as $campanha)
                                <tr>
                                    <td class="text-light">{{ $campanha->nome }}</td>
                                    <td class="text-light">{{ $campanha->sistemaRPG }}</td>
                                    <td>
                                        @if($campanha->status === 'ativa')
                                            <span class="badge bg-success">Ativa</span>
                                        @elseif($campanha->status === 'pausada')
                                            <span class="badge bg-warning text-dark">Pausada</span>
                                        @else
                                            <span class="badge bg-secondary">Encerrada</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
