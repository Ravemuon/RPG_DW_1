@extends('layouts.app')

@section('title', 'Livro do Aventureiro – Perfil')

@section('content')
<div class="container py-5">

    {{-- Banner --}}
    <div class="position-relative mb-5 rounded overflow-hidden shadow" style="height: 350px;">
        <div class="w-100 h-100"
             style="background-image: url('{{ $user->banner_url }}'); background-size: cover; background-position: center; filter: brightness(0.65);">
        </div>

        {{-- Upload banner --}}
        <label for="bannerUpload"
               class="position-absolute top-0 end-0 m-3 btn btn-outline-light btn-sm shadow"
               style="cursor:pointer; z-index: 10;">
            <i class="bi bi-camera-fill"></i>
        </label>
        <form action="{{ route('usuarios.uploadImagem', 'banner') }}"
              method="POST" enctype="multipart/form-data" class="d-none">
            @csrf
            <input type="file" name="arquivo" id="bannerUpload" accept="image/*" onchange="this.form.submit()">
        </form>
    </div>

    {{-- Avatar e informações --}}
    <div class="text-center mb-5 position-relative" style="margin-top: -90px;">
        <div class="position-relative d-inline-block">
            <img src="{{ $user->avatar_url }}"
                 alt="Avatar de {{ $user->nome }}"
                 class="rounded-circle border shadow-lg"
                 style="width: 160px; height: 160px; object-fit: cover; border-color: var(--btn-bg) !important; border-width: 3px !important;">

            {{-- Upload avatar --}}
            <label for="avatarUpload"
                   class="position-absolute bottom-0 end-0 bg-light rounded-circle p-2 shadow"
                   style="cursor:pointer;">
                <i class="bi bi-camera-fill text-dark"></i>
            </label>
            <form action="{{ route('usuarios.uploadImagem', 'avatar') }}"
                  method="POST" enctype="multipart/form-data" class="d-none">
                @csrf
                <input type="file" name="arquivo" id="avatarUpload" accept="image/*" onchange="this.form.submit()">
            </form>
        </div>

        <div class="mt-3">
            <h2 class="fw-bold mb-2" style="color: var(--btn-bg); text-shadow: 0 1px 3px rgba(0,0,0,0.8);">{{ $user->nome }}</h2>
            <p class="mb-1 text-light opacity-90">ID: {{ $user->id }}</p>
            <p class="mb-0 text-light opacity-90">
                @if($user->is_admin ?? false)
                    👑 Administrador
                @elseif($user->papel === 'mestre')
                    🧙 Mestre
                @else
                    🎮 Jogador
                @endif
            </p>
        </div>
    </div>

    {{-- Biografia e Estatísticas --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card h-100 shadow-lg" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold" style="color: var(--btn-bg);">📜 Biografia</h5>
                    <p class="mb-4 text-light" style="line-height: 1.6; font-size: 1.05rem;">
                        {{ $user->biografia ?? 'Este aventureiro ainda não escreveu sua biografia.' }}
                    </p>
                    <a href="{{ route('usuarios.edit') }}" class="btn btn-custom mt-3 fw-bold">
                        ✏️ Editar Perfil
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100 shadow-lg" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
                <div class="card-body text-center">
                    <h5 class="card-title mb-4 fw-bold" style="color: var(--btn-bg);">📊 Estatísticas</h5>

                    <div class="d-flex justify-content-around flex-wrap gap-3 mb-4">
                        <div class="rounded p-3 shadow-sm flex-fill text-center border" style="background: rgba(255,255,255,0.05); border-color: var(--card-border) !important;">
                            <strong class="d-block text-light mb-1">Personagens</strong>
                            <span class="fw-bold fs-5" style="color: var(--btn-bg);">{{ $personagemCount }}</span>
                        </div>
                        <div class="rounded p-3 shadow-sm flex-fill text-center border" style="background: rgba(255,255,255,0.05); border-color: var(--card-border) !important;">
                            <strong class="d-block text-light mb-1">Campanhas</strong>
                            <span class="fw-bold fs-5" style="color: var(--btn-bg);">{{ $campanhas->count() }}</span>
                        </div>
                    </div>

                    {{-- Alterar Tema --}}
                    <div class="mt-4 pt-3 border-top" style="border-color: var(--card-border) !important;">
                        <h6 class="fw-bold mb-3 text-light">🎨 Alterar Tema</h6>
                        <form action="{{ route('usuarios.tema.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="input-group">
                                <select name="tema" class="form-select">
                                    @foreach(\App\Models\User::TEMAS as $tema)
                                        <option value="{{ $tema }}" {{ $user->tema === $tema ? 'selected' : '' }}>
                                            {{ ucfirst($tema) }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-custom fw-bold">Aplicar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Campanhas Ativas --}}
    <div class="card shadow-lg mb-5" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
        <div class="card-header text-center py-3" style="border-bottom-color: var(--card-border); background: rgba(0,0,0,0.3);">
            <h3 class="mb-0 fw-bold" style="color: var(--btn-bg);">🏕️ Campanhas Ativas</h3>
        </div>
        <div class="card-body">
            @if($campanhas->isEmpty())
                <p class="text-center text-light opacity-75 py-4">Você ainda não participa de nenhuma campanha. ⚔️</p>
            @else
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead>
                            <tr style="background: rgba(0,0,0,0.4);">
                                <th class="fw-bold py-3" style="color: var(--btn-bg);">Nome</th>
                                <th class="fw-bold py-3" style="color: var(--btn-bg);">Sistema</th>
                                <th class="fw-bold py-3" style="color: var(--btn-bg);">Status</th>
                                <th class="fw-bold py-3" style="color: var(--btn-bg);">Mestre</th>
                                <th class="fw-bold py-3" style="color: var(--btn-bg);">Players</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campanhas as $campanha)
                                <tr style="border-bottom: 1px solid var(--card-border);">
                                    <td class="py-3 text-light fw-medium">{{ $campanha->nome }}</td>
                                    <td class="py-3 text-light">{{ $campanha->sistemaRPG }}</td>
                                    <td class="py-3">
                                        @if($campanha->status === 'ativa')
                                            <span class="badge bg-success px-3 py-2 fw-medium">Ativa</span>
                                        @elseif($campanha->status === 'pausada')
                                            <span class="badge bg-warning text-dark px-3 py-2 fw-medium">Pausada</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2 fw-medium">Encerrada</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-light">{{ $campanha->criador->nome ?? 'Desconhecido' }}</td>
                                    <td class="py-3 text-light fw-bold" style="color: var(--btn-bg);">{{ $campanha->jogadores->count() }}</td>
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
