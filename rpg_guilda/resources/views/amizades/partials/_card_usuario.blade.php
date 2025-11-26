<div class="card text-light shadow-lg border-0 h-100 overflow-hidden position-relative amigo-card"
     style="background: linear-gradient(145deg, #1a1a1a, #222); border-radius: 15px; transition: transform .2s ease, box-shadow .2s ease;">

    <div class="position-relative">
        <div class="w-100" style="height: 90px; background: url('{{ $usuario->banner_url }}') center/cover no-repeat; filter: brightness(0.8);"></div>
        <div class="position-absolute top-0 start-0 w-100 h-100"
             style="background: linear-gradient(to bottom, rgba(0,0,0,0.4), transparent 60%);"></div>
    </div>

    <div class="text-center mt-n5">
        <img src="{{ $usuario->avatar_url }}"
             alt="Avatar de {{ $usuario->nome }}"
             class="rounded-circle border shadow-lg"
             style="width: 95px; height: 95px; object-fit: cover; border-color: var(--btn-bg) !important;">
    </div>

    <div class="card-body text-center px-3 pb-4">

        <h6 class="fw-bold mt-2 mb-0 text-highlight">{{ $usuario->nome }}</h6>
        <p class="text-muted small">@ {{ $usuario->username }}</p>

        <div class="d-flex justify-content-center gap-4 mb-3">
            <div>
                <small class="text-muted d-block">🎭 Personagens</small>
                <span class="fw-bold text-light">{{ $usuario->personagens->count() }}</span>
            </div>
            <div>
                <small class="text-muted d-block">🗺️ Campanhas</small>
                <span class="fw-bold text-light">{{ $usuario->campanhas->count() }}</span>
            </div>
        </div>

        <a href="{{ route('amizades.perfilpublico', $usuario->id) }}"
           class="btn btn-outline-light btn-sm w-100 fw-bold mb-2 rounded-pill"
           style="border-color: var(--btn-bg); color: var(--btn-bg);">
            👁️ Ver Perfil
        </a>

        @if(Auth::check() && Auth::id() !== $usuario->id)

            @if(isset($usuario->status_amizade) && $usuario->status_amizade === 'pendente')
                <button class="btn btn-warning btn-sm w-100 fw-bold rounded-pill" disabled
                        style="background-color: #ffc107; border: none;">
                    ⏳ Pendente
                </button>

            @elseif(isset($usuario->status_amizade) && $usuario->status_amizade === 'aceito')
                <button class="btn btn-info btn-sm w-100 fw-bold rounded-pill" disabled
                        style="background-color: #17a2b8; border: none;">
                    ✅ Amigos
                </button>

            @else
                <form action="{{ route('amizades.adicionar', $usuario->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-success btn-sm w-100 fw-bold rounded-pill"
                            style="background-color: var(--btn-bg); border: none;">
                        🤝 Adicionar
                    </button>
                </form>
            @endif

        @endif
    </div>
</div>

<style>
.amigo-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.6);
}
</style>
