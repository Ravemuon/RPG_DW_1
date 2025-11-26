@extends('layouts.app')

@section('title', 'Livro do Aventureiro – Perfil')

@section('content')
<div class="container py-5">

    {{-- Banner Section --}}
    <div class="position-relative mb-5 rounded overflow-hidden shadow" style="height: 350px;">
        {{-- Display Banner using the Accessor --}}
        <div class="w-100 h-100"
             style="background-image: url('{{ $user->banner_url }}'); background-size: cover; background-position: center; filter: brightness(0.65);">
        </div>

        {{-- Upload banner link (only visible to owner) --}}
        @if(Auth::id() === $user->id)
            <label for="bannerUpload"
                   class="position-absolute top-0 end-0 m-3 btn btn-outline-light btn-sm shadow"
                   style="cursor:pointer; z-index: 10;">
                <i class="bi bi-camera-fill"></i> Alterar Banner
            </label>
            <form action="{{ route('usuarios.uploadImagem', 'banner') }}"
                  method="POST" enctype="multipart/form-data" class="d-none" id="formBannerUpload">
                @csrf
                <input type="file" name="arquivo" id="bannerUpload" accept="image/*">
            </form>
        @endif
    </div>

    {{-- Avatar and Info --}}
    <div class="text-center mb-5 position-relative" style="margin-top: -90px;">
        <div class="position-relative d-inline-block">
            {{-- Display Avatar using the Accessor --}}
            <img src="{{ $user->avatar_url }}"
                 alt="Avatar de {{ $user->nome }}"
                 class="rounded-circle border shadow-lg"
                 style="width: 160px; height: 160px; object-fit: cover; border-color: var(--btn-bg) !important; border-width: 3px !important;">

            {{-- Upload avatar link (only visible to owner) --}}
            @if(Auth::id() === $user->id)
                <label for="avatarUpload"
                       class="position-absolute bottom-0 end-0 bg-light rounded-circle p-2 shadow"
                       style="cursor:pointer; border: 2px solid var(--btn-bg);">
                    <i class="bi bi-camera-fill text-dark"></i>
                </label>
                <form action="{{ route('usuarios.uploadImagem', 'avatar') }}"
                      method="POST" enctype="multipart/form-data" class="d-none" id="formAvatarUpload">
                    @csrf
                    <input type="file" name="arquivo" id="avatarUpload" accept="image/*">
                </form>
            @endif
        </div>

        <div class="mt-3">
            <h2 class="fw-bold mb-2" style="color: var(--btn-bg); text-shadow: 0 1px 3px rgba(0,0,0,0.8);">{{ $user->nome }}</h2>
            <p class="card-title mb-3 fw-bold text-light opacity-75">@ {{ $user->username }}</p>
            <p class="card-title mb-3 fw-bold">
                @if($user->papel === 'administrador')
                    <span class="badge bg-danger">👑 Administrador</span>
                @elseif($user->papel === 'mestre')
                    <span class="badge bg-primary">🧙 Mestre</span>
                @else
                    <span class="badge bg-secondary">🎮 Jogador</span>
                @endif
            </p>
        </div>
    </div>

    <hr style="border-color: var(--card-border);">

    {{-- Biografia e Estatísticas --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card h-100 shadow-lg" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold" style="color: var(--btn-bg);">📜 Biografia</h5>
                    <p class="mb-4 text-light" style="line-height: 1.6; font-size: 1.05rem;">
                        {{-- CORRIGIDO: Usando 'bio' do banco de dados --}}
                        {{ $user->bio ?? 'Este aventureiro ainda não escreveu sua biografia.' }}
                    </p>
                    @if(Auth::id() === $user->id)
                        <a href="{{ route('usuarios.edit') }}" class="btn btn-custom mt-3 fw-bold">
                            ✏️ Editar Perfil
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100 shadow-lg" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
                <div class="card-body text-center">
                    <h5 class="card-title mb-4 fw-bold" style="color: var(--btn-bg);">📊 Estatísticas</h5>

                    <div class="d-flex justify-content-around flex-wrap gap-3 mb-4">
                        <div class="rounded p-3 shadow-sm flex-fill text-center border" style="background: rgba(255,255,255,0.05); border-color: var(--card-border) !important;">
                            <strong class="btn btn-warning w-100 mb-3">Personagens</strong>
                            <span class="fw-bold fs-5" style="color: var(--btn-bg);">{{ $personagemCount }}</span>
                        </div>
                        <div class="rounded p-3 shadow-sm flex-fill text-center border" style="background: rgba(255,255,255,0.05); border-color: var(--card-border) !important;">
                            <strong class="btn btn-warning w-100 mb-3">Campanhas</strong>
                            <span class="fw-bold fs-5" style="color: var(--btn-bg);">{{ $campanhas->count() }}</span>
                        </div>
                    </div>

                    {{-- Alterar Tema (Somente para o próprio usuário) --}}
                    @if(Auth::id() === $user->id)
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
                    @endif
                </div>
            </div>
        </div>
    </div>

    <hr style="border-color: var(--card-border);">

    {{-- Campanhas Ativas --}}
    <div class="card shadow-lg mb-5" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
        <div class="card-header text-center py-3" style="border-bottom-color: var(--card-border); background: rgba(0,0,0,0.3);">
            <h3 class="mb-0 fw-bold" style="color: var(--btn-bg);">🏕️ Campanhas Ativas</h3>
        </div>
        <div class="card-body">
            @if($campanhas->isEmpty())
                <p class="text-center text-light opacity-75 py-4">
                    @if(Auth::id() === $user->id)
                        Você ainda não participa de nenhuma campanha. ⚔️
                    @else
                        {{ $user->nome }} ainda não participa de nenhuma campanha. ⚔️
                    @endif
                </p>
            @else
                {{-- Tabela de Campanhas... (Mantida a lógica original) --}}
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
                                    {{-- Assumindo que o relacionamento 'criador' existe --}}
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

{{-- Scripts para Upload Automático --}}
@if(Auth::id() === $user->id)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lógica de upload para o Banner
        const bannerUploadInput = document.getElementById('bannerUpload');
        const formBannerUpload = document.getElementById('formBannerUpload');

        if (bannerUploadInput && formBannerUpload) {
            bannerUploadInput.addEventListener('change', function() {
                // Adiciona um pequeno delay para feedback visual, se necessário, antes de submeter
                setTimeout(() => {
                    formBannerUpload.submit();
                }, 100);
            });
        }

        // Lógica de upload para o Avatar
        const avatarUploadInput = document.getElementById('avatarUpload');
        const formAvatarUpload = document.getElementById('formAvatarUpload');

        if (avatarUploadInput && formAvatarUpload) {
            avatarUploadInput.addEventListener('change', function() {
                setTimeout(() => {
                    formAvatarUpload.submit();
                }, 100);
            });
        }
    });
</script>
@endif
@endsection
